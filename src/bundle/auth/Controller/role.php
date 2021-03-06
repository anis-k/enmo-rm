<?php

/*
 * Copyright (C) 2015 Maarch
 *
 * This file is part of bundle auth.
 *
 * Bundle auth is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Bundle auth is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with bundle auth.  If not, see <http://www.gnu.org/licenses/>.
 */

namespace bundle\auth\Controller;

use bundle\digitalSafe\Exception\Exception;

/**
 * Controler for the role
 *
 * @package Auth
 * @author  Alexis Ragot <alexis.ragot@maarch.org>
 */
class role
{

    public $sdoFactory;
    protected $csv;

    /**
     * Constructor of adminRole class
     * @param \dependency\sdo\Factory $sdoFactory The factory
     * @param \dependency\csv\Csv     $csv        The dependency csv
     */
    public function __construct(\dependency\sdo\Factory $sdoFactory, \dependency\csv\Csv $csv)
    {
        $this->sdoFactory = $sdoFactory;
        $this->csv = $csv;
        $this->hasSecurityLevel = isset(\laabs::configuration('auth')['useSecurityLevel']) ? (bool) \laabs::configuration('auth')['useSecurityLevel'] : false;
    }

    /**
     * List roles
     *
     * @return array Array of auth/role object
     */
    public function getAll()
    {
        return $this->sdoFactory->find("auth/role");
    }

    /**
     * List roles
     *
     * @param integer $limit Maximal number of results to dispay
     *
     * @return array Array of auth/role object
     */
    public function index($limit = null)
    {
        $roleMemberController = \laabs::newController("auth/roleMember");
        $roleMembers = $roleMemberController->readByUserAccount(\laabs::getToken("AUTH")->accountId);

        $query = [];
        foreach ($roleMembers as $roleMember) {
            $role = $this->sdoFactory->read("auth/role", $roleMember->roleId);
            if ($this->hasSecurityLevel) {
                switch ($role->securityLevel) {
                    case $role::SECLEVEL_GENADMIN:
                        $query[] = "securityLevel ='". $role::SECLEVEL_GENADMIN ."'";
                        $query[] = "securityLevel ='". $role::SECLEVEL_FUNCADMIN ."'";
                        $query[] = "securityLevel = null";
                        break;
                    case $role::SECLEVEL_FUNCADMIN:
                        $query[] = "securityLevel ='". $role::SECLEVEL_FUNCADMIN ."'";
                        $query[] = "securityLevel ='". $role::SECLEVEL_USER ."'";
                        $query[] = "securityLevel = null";
                        break;
                    case $role::SECLEVEL_USER:
                        $query[] = "securityLevel ='". $role::SECLEVEL_USER ."'";
                        $query[] = "securityLevel = null";
                }
            }
        }
        $roles = $this->sdoFactory->find("auth/role", \laabs\implode(' OR ', array_unique($query)), null, null, null, $limit);

        return $roles;
    }

    /**
     * Prepare an empty role object
     *
     * @return auth/role The empty role object
     */
    public function newRole()
    {
        return \laabs::newInstance('auth/role');
    }

    /**
     * Prepares a role object for update
     * @param string $roleId The identifier of the role
     *
     * @return auth/role The requested role
     */
    public function edit($roleId)
    {
        $role = $this->sdoFactory->read("auth/role", $roleId);
        $role->privileges = array();

        foreach ($this->sdoFactory->readChildren("auth/privilege", $role) as $privilege) {
            $role->privileges[] = $privilege->userStory;
        }

        $role->roleMembers = array();

        foreach ($this->sdoFactory->readChildren("auth/roleMember", $role) as $roleMember) {
            $role->roleMembers[] = $roleMember->userAccountId;
        }

        return \laabs::castMessage($role, 'auth/role');
    }

    /**
     * Record a new role
     * @param auth/role $role The role object to create
     *
     * @return string The new role id
     */
    public function create($role)
    {
        $transactionControl = !$this->sdoFactory->inTransaction();

        if ($transactionControl) {
            $this->sdoFactory->beginTransaction();
        }

        try {
            $roleInstance = \laabs::newInstance("auth/role");
            $roleInstance->roleId = \laabs::newId();
            $roleInstance->roleName = $role->roleName;
            $roleInstance->description = $role->description;
            $roleInstance->securityLevel = $role->securityLevel;
            $roleInstance->enabled = $role->enabled;

            $this->sdoFactory->create($roleInstance);

            if (!empty($role->privileges)) {
                foreach ($role->privileges as $userStory) {
                    $this->addPrivilege($roleInstance, $userStory);
                }
            }

            if (!empty($role->roleMembers)) {
                foreach ($role->roleMembers as $userAccountId) {
                    $roleMemberController = \laabs::newController('auth/roleMember');
                    $roleMemberController->create($roleInstance->roleId, $userAccountId);
                }
            }

        } catch (\Exception $exception) {
            if ($transactionControl) {
                $this->sdoFactory->rollback();
            }

            throw \laabs::newException("auth/adminRoleException", "Role not created");
        }

        if ($transactionControl) {
            $this->sdoFactory->commit();
        }

        return $roleInstance->roleId;
    }

    /**
     * Updates a role
     * @param id        $roleId The role identifier
     * @param auth/role $role   The role info to update
     *
     * @return boolean The status of the query
     */
    public function update($roleId, $role)
    {
        $this->sdoFactory->beginTransaction();

        try {
            $this->sdoFactory->update($role, 'auth/role');

            $this->sdoFactory->deleteChildren("auth/privilege", $role, "auth/role");
            if (!empty($role->privileges)) {
                foreach ($role->privileges as $userStory) {
                    $this->addPrivilege($role, $userStory);
                }
            }

            $this->sdoFactory->deleteChildren("auth/roleMember", $role, "auth/role");

            if (!empty($role->roleMembers)) {
                foreach ($role->roleMembers as $member) {
                    $roleMemberController = \laabs::newController('auth/roleMember');
                    $roleMemberController->create($roleId, $member);
                }
            }
        } catch (\Exception $exception) {
            $this->sdoFactory->rollback();
            throw \laabs::newException("auth/adminRoleException", "Role not updated");
        }

        $this->sdoFactory->commit();

        return true;
    }

    /**
     * Lock or unlock a role
     * @param auth/role $roleId The role object to update
     * @param boolean   $status The new status of the role
     *
     * @return boolean The status of the query
     */
    public function changeStatus($roleId, $status)
    {
        $role = $this->sdoFactory->read("auth/role", $roleId);
        if ($status == "true") {
            $role->enabled = 1;
        } else {
            $role->enabled = 0;
        }
        $result = $this->sdoFactory->update($role, 'auth/role');

        return $result;
    }

    /**
     * Delete an auth role
     * @param string $roleId The role identifier to delete
     *
     * @return boolean The status of the query
     */
    public function delete($roleId)
    {
        $res = false;
        $this->sdoFactory->beginTransaction();
        try {
            $role = $this->sdoFactory->read("auth/role", $roleId);
            $this->sdoFactory->deleteChildren("auth/privilege", $role);
            $this->sdoFactory->deleteChildren("auth/roleMember", $role);
            $this->sdoFactory->delete($role);
            $this->sdoFactory->commit();

            $res = true;
        } catch (\Exception $exception) {
            $this->sdoFactory->rollback();

            throw \laabs::newException("auth/adminRoleException", "Role not deleted");
        }

        return $res;
    }

    /**
     * Get the list of available authorization groups
     * @param string $query A query string of tokens
     *
     * @return array The list of groups
     */
    public function queryRoles($query)
    {
        $queryTokens = \laabs\explode(" ", $query);
        $queryTokens = array_unique($queryTokens);

        $queryProperties = array("roleName");
        $queryPredicats = array();
        foreach ($queryProperties as $queryProperty) {
            foreach ($queryTokens as $queryToken) {
                $queryPredicats[] = $queryProperty."="."'*".$queryToken."*'";
            }
        }
        $queryString = implode(" OR ", $queryPredicats);

        $result = $this->sdoFactory->find('auth/role', $queryString);

        return $result;
    }

    /**
     * Get the list of user story
     * @param string $roleId The identifier of the role
     *
     * @return array The list of privileges
     */
    public function getPrivilege($roleId)
    {
        $role = $this->sdoFactory->read("auth/role", $roleId);
        $userStories = array();
        $privileges = $this->sdoFactory->readChildren("auth/privilege", $role);
        foreach ($privileges as $privilege) {
            $userStories[] = $privilege->userStory;
        }

        return $userStories;
    }

    /**
     * Create privileges
     * @param role     $role   The role where privilege is added
     * @param string $userStory Privilege userStory
     *
     * @return boolean The operation result
     */
    public function addPrivilege($role, $userStory)
    {
        if (isset(\laabs::configuration('auth')['privileges'])
            && isset(\laabs::configuration('auth')['securityLevel'])
            && $this->hasSecurityLevel
        ) {
            $privileges = \laabs::configuration('auth')['privileges'];
            $privilegesSecurityLevel = \laabs::configuration('auth')['securityLevel'];

            if ($privilegesSecurityLevel[$role->securityLevel] === '0') {
                $bitmask = ['1', '2', '4'];
            } elseif ($privilegesSecurityLevel[$role->securityLevel] === '3') {
                $bitmask = ['1', '2'];
            } elseif ($privilegesSecurityLevel[$role->securityLevel] === '6') {
                $bitmask = ['4', '2'];
            } else {
                $bitmask = [$privilegesSecurityLevel[$role->securityLevel]];
            }

            $domain = strtok($userStory, LAABS_URI_SEPARATOR);
            foreach ($bitmask as $i) {
                if (in_array($domain . '/', $privileges[$i])) {
                    continue;
                }

                foreach ($privileges[$i] as $privilege) {
                    if (fnmatch($privilege, $userStory)) {
                        continue 2;
                    }

                    $domainPrivileges = strtok($privilege, LAABS_URI_SEPARATOR);
                    if ($domain . '/*' == $userStory
                        && $domain == $domainPrivileges
                    ) {
                        continue 2;
                    }
                }
                return false;
            }
        }

        $privilege = \laabs::newInstance("auth/privilege");
        $privilege->userStory = $userStory;
        $privilege->roleId = $role->roleId;

        return $this->sdoFactory->create($privilege);
    }

    /**
     * Delete privileges
     * @param auth/privilege $privilege Privilege object
     *
     * @throws Exception
     *
     * @return boolean The result of the operation
     */
    public function deletePrivilege($privilege)
    {
        $transactionControl = !$this->sdoFactory->inTransaction();

        if ($transactionControl) {
            $this->sdoFactory->beginTransaction();
        }

        $privilege = \laabs::castObject($privilege, "auth/privilege");

        try {
            $this->sdoFactory->delete($privilege);
        } catch (\Exception $exception) {
            if ($transactionControl) {
                $this->sdoFactory->rollback();
            }
            throw \laabs::newException("auth/sdoException");
        }

        if ($transactionControl) {
            $this->sdoFactory->commit();
        }

        return true;
    }

    public function exportCsv($limit = null) {
        $roles = $this->sdoFactory->find('auth/role', null, null, null, null, $limit);
        $roles = \laabs::castMessageCollection($roles, 'auth/roleImportExport');

        foreach ($roles as $role) {
            $privileges = $this->getPrivilege($role->roleId);
            if (!empty($privileges)) {
                $lastIndex = count($privileges) -1;
                foreach ($privileges as $index => $privilege) {
                    $role->privileges .= $privilege;

                    if ($lastIndex !== $index) {
                        $role->privileges .= ";";
                    }
                }
            }
        }

        $handler = fopen('php://temp', 'w+');
        $this->csv->writeStream($handler, (array) $roles, 'auth/roleImportExport', true);
        return $handler;
    }

    public function import($data, $isReset = false)
    {
        $transactionControl = !$this->sdoFactory->inTransaction();

        if ($transactionControl) {
            $this->sdoFactory->beginTransaction();
        }

        try {
            if ($isReset) {
                $roles = $this->getAll();

                foreach ($roles as $role) {
                    $roleMembers = $this->sdoFactory->find('auth/roleMember', "roleId='$role->roleId'");
                    foreach ($roleMembers as $roleMember) {
                        $this->sdoFactory->delete($roleMember, 'auth/roleMember');
                    }

                    $privileges = $this->sdoFactory->find('auth/privilege', "roleId='$role->roleId'");
                    foreach ($privileges as $privilege) {
                        $this->sdoFactory->delete($privilege, 'auth/privilege');
                    }
                    $this->sdoFactory->delete($role, 'auth/role');
                }
            }

            $roles = $this->csv->readStream($data, 'auth/roleImportExport', true);
            foreach ($roles as $key => $role) {
                if ($isReset
                    || !$this->sdoFactory->exists('auth/role', $role->roleId)
                ) {
                    $newRole = \laabs::newInstance('auth/role');
                    $newRole->roleId = $role->roleId;
                    $newRole->roleName = $role->roleName;
                    $newRole->description = $role->description;
                    $newRole->enabled = (bool) $role->enabled;
                    $newRole->securityLevel = $role->securityLevel;

                    $this->sdoFactory->create($newRole, 'auth/role');
                } else {
                    $changedRole = $this->sdoFactory->read('auth/role', $role->roleId);
                    $changedRole->roleName = $role->roleName;
                    $changedRole->description = $role->description;
                    $changedRole->enabled = (bool) $role->enabled;
                    $changedRole->securityLevel = $role->securityLevel;
                    $this->sdoFactory->update($changedRole, 'auth/role');

                    $privileges = $this->sdoFactory->find('auth/privilege', "roleId='$role->roleId'");
                    foreach ($privileges as $privilege) {
                        $this->sdoFactory->delete($privilege, 'auth/privilege');
                    }
                }

                $userStories = explode(';', $role->privileges);
                foreach ($userStories as $userStory) {
                    $privilege = \laabs::newInstance('auth/privilege');
                    $privilege->roleId = $role->roleId;
                    $privilege->userStory = $userStory;

                    $this->sdoFactory->create($privilege, 'auth/privilege');
                }
            }
        } catch (\Exception $e) {
            if ($transactionControl) {
                $this->sdoFactory->rollback();
            }
            throw $e;
        }

        if ($transactionControl) {
            $this->sdoFactory->commit();
        }

        return true;
    }
}
