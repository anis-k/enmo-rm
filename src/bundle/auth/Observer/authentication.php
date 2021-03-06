<?php
/*
 * Copyright (C) 2015 Maarch
 *
 * This file is part of bundle user.
 *
 * Bundle user is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Bundle user is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with bundle user.  If not, see <http://www.gnu.org/licenses/>.
 */
namespace bundle\auth\Observer;

/**
 * Service for authentication check
 *
 * @package User
 * @author  Maarch Cyril  VAZQUEZ <cyril.vazquez@maarch.org>
 */
class authentication
{
    protected $sdoFactory;

    protected $ignoreRoutes;

    /**
     * Construct the observer
     * @param object $sdoFactory   The user model
     * @param array  $ignoreRoutes An array of route patterns to ignore when checking authentication
     */
    public function __construct(\dependency\sdo\Factory $sdoFactory, $ignoreRoutes = array('auth/authentication/*'))
    {
        $this->sdoFactory = $sdoFactory;

        $this->ignoreRoutes = $ignoreRoutes;
    }

    /**
     * Observer for user authentication
     * @param \core\Reflection\Command &$servicePath
     * @param array                    &$args
     *
     * @return auth/credential
     *
     * @subject LAABS_SERVICE_PATH
     */
    public function check(&$servicePath, array &$args = null)
    {
        foreach ($this->ignoreRoutes as $pattern) {
            if (fnmatch($pattern, $servicePath->domain.LAABS_URI_SEPARATOR.$servicePath->interface)) {
                return true;
            }
        }

        if (!\laabs::isServiceClient()) {
            return true;
        }
        $token = null;
        if ($accountToken = \laabs::getToken('AUTH')) {

            $account = $this->sdoFactory->read("auth/account", $accountToken->accountId);

        } elseif (\core\Kernel\abstractKernel::get()->request->authentication) {
            $username = $_SERVER['PHP_AUTH_USER'];
            $account = $this->sdoFactory->read("auth/account", array("accountName" => $username));

            $password = $_SERVER['PHP_AUTH_PW'];

            $userAuthenticationController = \laabs::newController('auth/userAuthentication');
            $userAuthenticationController->login($username, $password);
        }

        if (!isset($account)) {
            throw \laabs::newException("auth/authenticationException", "Missing authentication credential", 401);
        }

        if ((!$account->enabled) || ($account->locked)) {
            throw \laabs::newException("auth/authenticationException", "Missing authentication credential", 401);
        }

        if ($account->accountType == "service") {
            $token = new \core\token($accountToken, 0);
            $jsonToken = \json_encode($token);
            $cryptedToken = \laabs::encrypt($jsonToken, \laabs::getCryptKey());
            $cookieToken = base64_encode($cryptedToken);

            if ($account->password != $cookieToken) {
                throw \laabs::newException("auth/authenticationException", "Missing authentication credential", 401);
            }

            $servicePositionController = \laabs::newController("organization/servicePosition");
            $servicePosition = $servicePositionController->getPosition($account->accountId);

            if ($servicePosition != null) {
                \laabs::setToken("ORGANIZATION", $servicePosition->organization);
            }
        } else {
            $organization = \laabs::getToken("ORGANIZATION");

            $userPositionController = \laabs::newController("organization/userPosition");
            $userPositions = $userPositionController->getMyPositions();
            
            if (!empty($organization)) {
                $isUserPosition = false;
                
                foreach ($userPositions as $position) {
                    if ($position->orgId == $organization->orgId) {
                        $isUserPosition = true;
                    }
                }

                if (!$isUserPosition) {
                    \laabs::unsetToken("AUTH");
                    \laabs::unsetToken("ORGANIZATION");
                    \laabs::newException("auth/authenticationException", "Missing authentication credential", 403);

                    return false;
                }
            }
        }

        return $account;
    }
}
