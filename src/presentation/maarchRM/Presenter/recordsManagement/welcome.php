<?php
/*
 * Copyright (C) 2017 Maarch
 *
 * This file is part of bundle recordsManagement.
 *
 * Bundle recordsManagement is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Bundle recordsManagement is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with bundle recordsManagement.  If not, see <http://www.gnu.org/licenses/>.
 */
namespace presentation\maarchRM\Presenter\recordsManagement;

/**
 * welcomePage serializer html
 *
 * @author Alexis Ragot <alexis.ragot@maarch.org>
 */
class welcome
{
    use \presentation\maarchRM\Presenter\exceptions\exceptionTrait;

    public $view;
    public $json;

    protected $userArchivalProfiles = [];

    /**
     * Constuctor of welcomePage html serializer
     * @param \dependency\html\Document   $view The view
     * @param \dependency\json\JsonObject $json Json utility
     */
    public function __construct(\dependency\html\Document $view, \dependency\json\JsonObject $json)
    {
        $this->view = $view;
        $this->view->translator->setCatalog('recordsManagement/messages');

        $this->json = $json;
        $this->json->status = true;
    }

    /**
     * Get a welcome page
     *
     * @return string
     */
    public function welcomePage()
    {
        $this->view->addContentFile("dashboard/mainScreen/main.html");

        $this->view->translate();

        $accountToken = \laabs::getToken('AUTH');
        $userAccountController = \laabs::newController('auth/userAccount');
        $user = $userAccountController->get($accountToken->accountId);
        
        // File plan tree
        $filePlanPrivileges = \laabs::callService('auth/userAccount/readHasprivilege', "archiveManagement/filePlan");

        $syncImportPrivilege = \laabs::callService('auth/userAccount/readHasprivilege', "archiveDeposit/deposit");
        $asyncImportPrivilege = \laabs::callService('auth/userAccount/readHasprivilege', "archiveDeposit/transferImport");

        $filePlan = \laabs::callService('filePlan/filePlan/readTree');
        if ($filePlan) {
            $this->getOrgUnitArchivalProfiles($filePlan);

            $filePlan = [$filePlan];
            $this->markTreeLeaf($filePlan);

            $this->view->setSource("filePlan", $filePlan);
            $this->view->setSource("filePlanPrivileges", $filePlanPrivileges);
            $this->view->merge($this->view->getElementById('filePlanTree'));
            $this->view->translate();
        }

        // Retention
        $retentionRules = \laabs::callService('recordsManagement/retentionRule/readIndex');
        for ($i = 0, $count = count($retentionRules); $i < $count; $i++) {
            $retentionRules[$i]->durationText = (string) $retentionRules[$i]->duration;
        }

        // archival profiles for search form
        foreach ($this->userArchivalProfiles as $archivalProfile) {
            $archivalProfile->searchFields = [];
            foreach ($archivalProfile->archiveDescription as $archiveDescription) {
                switch ($archiveDescription->descriptionField->type) {
                    case 'text':
                    case 'name':
                    case 'date':
                    case 'number':
                    case 'boolean':
                        $archivalProfile->searchFields[] = $archiveDescription->descriptionField;
                }
            }
        }
        
        $depositPrivilege = \laabs::callService('auth/userAccount/readHasprivilege', "archiveDeposit/deposit");
        $this->view->translate();

        $this->view->setSource("userArchivalProfiles", $this->userArchivalProfiles);
        $this->view->setSource("depositPrivilege", $depositPrivilege);
        $this->view->setSource("syncImportPrivilege", $syncImportPrivilege);
        $this->view->setSource("asyncImportPrivilege", $asyncImportPrivilege);
        $this->view->setSource("filePlanPrivileges", $filePlanPrivileges);
        

        foreach ($this->view->getElementsByClass('dateRangePicker') as $dateRangePickerInput) {
            $this->view->translate($dateRangePickerInput);
        }

        $this->view->setSource('retentionRules', $retentionRules);
        $this->view->setSource('user', $user);
        $this->view->merge();

        return $this->view->saveHtml();
    }

    /**
     * Show the events search form
     * @param object $filePlan The root orgUnit of user with sub-orgUnits and folders
     *
     * @return string
     */
    public function showTree($filePlan)
    {
        $this->view->addContentFile('filePlan/filePlanTree.html');
        $this->markTreeLeaf([$filePlan]);

        $this->view->setSource("filePlan", [$filePlan]);
        $this->view->merge();
        $this->view->translate();

        return $this->view->saveHtml();
    }

    /**
     * Show a document information
     *
     * @return string
     */
    public function documentInfo()
    {
        $this->view->addContentFile('dashboard/mainScreen/documentInformation.html');
        $this->view->translate();

        if (isset(\laabs::configuration('presentation.maarchRM')['displayableFormat'])) {
            $this->view->setSource("displayableFormat", json_encode(\laabs::configuration('presentation.maarchRM')['displayableFormat']));
        } else {
            $this->view->setSource("displayableFormat", json_encode(array()));
        }

        $this->view->merge();

        return $this->view->saveHtml();
    }
    /**
     * Display error
     * @param object $error
     *
     * @return string
     */
    public function error($error)
    {
        $this->view->addContentFile("dashboard/error.html");

        $this->view->translate();

        $this->view->setSource('error', $error);
        $this->view->merge();

        return $this->view->saveHtml();
    }

    /**
     * Show a folder content
     * @param array $archives
     *
     * @return string
     */
    public function folderContents($archives)
    {
        $organizations = \laabs::callService('organization/organization/readIndex');
        $orgsName = [];

        foreach ($organizations as $organization) {
            $orgsName[$organization->registrationNumber] = $organization->displayName;
        }

        $profiles = \laabs::callService('recordsManagement/archivalProfile/readIndex');
        $profilesName = [];

        foreach ($profiles as $profile) {
            $profilesName[$profile->reference] = $profile->name;
        }

        foreach ($archives as $archive) {
            $archive->originatorOrgName = $orgsName[$archive->originatorOrgRegNumber];
            if (!empty($archive->archivalProfileReference)) {
                $archive->archivalProfileName = $profilesName[$archive->archivalProfileReference];
            }
        }

        $this->json->archives = $archives;

        return $this->json->save();
    }

    /**
     * Show the result of movin²g an archive into a folder
     * @param int $result
     *
     * @return string
     */
    public function moveArchivesToFolder($result)
    {
        $this->view->translator->setCatalog('filePlan/messages');
        if ($result == 1) {
            $this->json->message = "The archive was moved.";
            $this->json->message = $this->view->translator->getText($this->json->message);
            
        } else {
            $this->json->message = '%1$s archives were moved.';
            $this->json->message = $this->view->translator->getText($this->json->message);
            $this->json->message = sprintf($this->json->message, $result);
        }

        return $this->json->save();
    }

    /**
     * Mark leaf for html merging
     * @param object $tree The tree
     */
    protected function markTreeLeaf($tree)
    {
        foreach ($tree as $node) {
            if (!isset($node->organization) && !isset($node->folder)) {
                $node->isLeaf = true;
            } else {
                if (isset($node->organization)) {
                    $this->markTreeLeaf($node->organization);
                }
                if (isset($node->folder)) {
                    $this->updateFolderPath($node->folder, $node->displayName);
                }
            }
        }
    }

    /**
     * Add owner organization name in folder path
     * @param object $tree      The tree
     * @param string $ownerName The owner organizaiton name
     */
    protected function updateFolderPath($tree, $ownerName)
    {
        foreach ($tree as $node) {
            $node->path = $ownerName.'/'.$node->path;
            if ($node->subFolders) {
                $this->updateFolderPath($node->subFolders, $ownerName);
            }
        }
    }

    protected function getOrgUnitArchivalProfiles($orgUnit)
    {
        $orgUnit->archivalProfiles = \laabs::callService('organization/organization/readOrgunitprofiles', $orgUnit->registrationNumber, true);

        foreach ($orgUnit->archivalProfiles as $i => $archivalProfile) {
            if ($archivalProfile == "*") {
                $orgUnit->acceptArchiveWithoutProfile = true;
                unset($orgUnit->archivalProfiles[$i]);
            } else {
                $this->userArchivalProfiles[$archivalProfile->reference] = $archivalProfile;
            }
        }

        $orgUnit->archivalProfiles = array_values($orgUnit->archivalProfiles);

        foreach ($orgUnit->archivalProfiles as $archivalProfile) {
            foreach ($archivalProfile->archiveDescription as $archiveDescription) {
                // Get scheme for array of objects, limit to one level for scheme recusions
                if ($archiveDescription->descriptionField->type == 'array'
                    && isset($archiveDescription->descriptionField->itemType)
                    && $archiveDescription->descriptionField->itemType[0] == '#') {
                    $objectType = new \StdClass();
                    $objectType->type = 'object';

                    $className = substr($archiveDescription->descriptionField->itemType, 1);
                    $objectType->properties = \laabs::callService('recordsManagement/descriptionScheme/read_name_Descriptionfields', $className);

                    $archiveDescription->descriptionField->itemType = $objectType;
                }

                // Get key-value pairs for select if enum has enumNames
                if (isset($archiveDescription->descriptionField->enumeration)) {
                    if (isset($archiveDescription->descriptionField->enumNames)) {
                        $archiveDescription->descriptionField->choice = array_combine($archiveDescription->descriptionField->enumeration, $archiveDescription->descriptionField->enumNames);
                    } else {
                        $archiveDescription->descriptionField->choice = array_combine($archiveDescription->descriptionField->enumeration, $archiveDescription->descriptionField->enumeration);
                    }
                }
            }           
        }

        if (!empty($orgUnit->organization)) {
            foreach ($orgUnit->organization as $subOrgUnit) {
                $this->getOrgUnitArchivalProfiles($subOrgUnit);
            }
        }
    }
}
