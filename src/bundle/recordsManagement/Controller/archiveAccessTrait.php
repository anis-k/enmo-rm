<?php

/*
 *  Copyright (C) 2017 Maarch
 * 
 *  This file is part of bundle XXXX.
 *  Bundle recordsManagement is free software: you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation, either version 3 of the License, or
 *  (at your option) any later version.
 * 
 *  Bundle recordsManagement is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 * 
 *  You should have received a copy of the GNU General Public License
 *  along with bundle recordsManagement.  If not, see <http://www.gnu.org/licenses/>.
 */

namespace bundle\recordsManagement\Controller;

/**
 * Archive access controller
 *
 * @author Alexis Ragot <alexis.ragot@maarch.org>
 */
trait archiveAccessTrait
{
    /**
     * Search archives by profile / dates / agreement
     * @param string $archiveId
     * @param string $profileReference
     * @param string $status
     * @param string $archiveName
     * @param string $agreementReference
     * @param string $archiveExpired
     * @param string $finalDisposition
     * @param string $originatorOrgRegNumber
     * @param string $originatorOwnerOrgId
     * @param string $originatorArchiveId
     * @param array  $originatingDate
     * @param string $filePlanPosition
     * @param bool   $hasParent
     * @param string $description
     * @param string $text
     * @param bool   $partialRetentionRule
     * @param string $retentionRuleCode
     * @param string $depositStartDate
     * @param string $depositEndDate
     * @param string $originatingStartDate
     * @param string $originatingEndDate
     * @param string $archiverArchiveId
     * @param string $processingStatus
     * @param bool   $checkAccess
     *
     * @return recordsManagement/archive[] Array of recordsManagement/archive object
     */
    public function search(
        $archiveId = null,
        $profileReference = null,
        $status = null,
        $archiveName = null,
        $agreementReference = null,
        $archiveExpired = null,
        $finalDisposition = null,
        $originatorOrgRegNumber = null,
        $originatorOwnerOrgId = null,
        $originatorArchiveId = null,
        $originatingDate = null,
        $filePlanPosition = null,
        $hasParent = null,
        $description = null,
        $text = null,
        $partialRetentionRule = null,
        $retentionRuleCode = null,
        $depositStartDate = null,
        $depositEndDate = null,
        $originatingStartDate = null,
        $originatingEndDate = null,
        $archiverArchiveId = null,
        $processingStatus = null,
        $checkAccess = true
    ) {
        $archives = [];

        $archiveArgs = [
            'archiveId' => $archiveId,
            'profileReference' => $profileReference,
            'status' => $status,
            'archiveName' => $archiveName,
            'agreementReference' => $agreementReference,
            'archiveExpired' => $archiveExpired,
            'finalDisposition' => $finalDisposition,
            'originatorOrgRegNumber' => $originatorOrgRegNumber,
            'originatorOwnerOrgId' => $originatorOwnerOrgId,
            'originatorArchiveId' => $originatorArchiveId,
            'originatingDate' => $originatingDate,
            'filePlanPosition' => $filePlanPosition,
            'hasParent' => $hasParent,
            'partialRetentionRule' => $partialRetentionRule,
            'retentionRuleCode' => $retentionRuleCode,
            'depositStartDate' => $depositStartDate,
            'depositEndDate' => $depositEndDate,
            'originatingDate' => [$originatingStartDate, $originatingEndDate], // [0] startDate, [1] endDate
            'archiverArchiveId' => $archiverArchiveId,
            'processingStatus' => $processingStatus
        ];

        if (!$filePlanPosition) {
            unset($archiveArgs['filePlanPosition']);
        }

        $searchClasses = [];
        if (!$profileReference) {
            $searchClasses['recordsManagement/description'] = $this->useDescriptionController('recordsManagement/description');

            $descriptionClassController = \laabs::newController('recordsManagement/descriptionClass');

            foreach ($descriptionClassController->index() as $descriptionClass) {
                $searchClasses[$descriptionClass->name] = $this->useDescriptionController($descriptionClass->name);
            }
        } else {
            $archivalProfile = $this->archivalProfileController->getByReference($profileReference);
            if ($archivalProfile->descriptionClass != '') {
                $searchClasses[$archivalProfile->descriptionClass] = $this->useDescriptionController($archivalProfile->descriptionClass);
            } else {
                $searchClasses['recordsManagement/description'] = $this->useDescriptionController('recordsManagement/description');
            }
        }
        foreach ($searchClasses as $descriptionClass => $descriptionController) {
            $archives = array_merge($archives, $descriptionController->search($description, $text, $archiveArgs, $checkAccess));
        }

        return $archives;
    }

    /**
     * Search archives by profile / dates / agreement
     * @param string $archiveId
     * @param string $profileReference
     * @param string $status
     * @param string $archiveName
     * @param string $agreementReference
     * @param string $archiveExpired
     * @param string $finalDisposition
     * @param string $originatorOrgRegNumber
     * @param string $originatorOwnerOrgId
     * @param string $originatorArchiveId
     * @param array  $originatingDate
     * @param string $filePlanPosition
     * @param bool   $hasParent
     * @param string $description
     * @param string $text
     * @param bool   $partialRetentionRule
     * @param string $retentionRuleCode
     * @param string $depositStartDate
     * @param string $depositEndDate
     * @param string $originatingStartDate
     * @param string $originatingEndDate
     *
     * @return recordsManagement/archive[] Array of recordsManagement/archive object
     */
    public function searchRegistry(
        $archiveId = null,
        $profileReference = null,
        $status = null,
        $archiveName = null,
        $agreementReference = null,
        $archiveExpired = null,
        $finalDisposition = null,
        $originatorOrgRegNumber = null,
        $originatorOwnerOrgId = null,
        $originatorArchiveId = null,
        $originatingDate = null,
        $filePlanPosition = null,
        $hasParent = null,
        $description = null,
        $text = null,
        $partialRetentionRule = null,
        $retentionRuleCode = null,
        $depositStartDate = null,
        $depositEndDate = null,
        $originatingStartDate = null,
        $originatingEndDate = null
    ) {
        $queryParts = array();
        $queryParams = array();

        $currentDate = \laabs::newDate();
        $currentDateString = $currentDate->format('Y-m-d');

        if ($archiveId) {
            $queryParts['archiveId'] = "archiveId = :archiveId";
            $queryParams['archiveId'] = $archiveId;
        } else {
            if ($profileReference) {
                $queryParts['archivalProfileReference'] = "archivalProfileReference = :archivalProfileReference";
                $queryParams['archivalProfileReference'] = $profileReference;
            }

            if ($status) {
                $queryParts['status'] = "status = :status";
                $queryParams['status'] = $status;
            }

            if ($retentionRuleCode) {
                $queryParts['retentionRuleCode'] = "retentionRuleCode = :retentionRuleCode";
                $queryParams['retentionRuleCode'] = $retentionRuleCode;
            }

            if ($filePlanPosition) {
                $queryParts['filePlanPosition'] = "filePlanPosition = :filePlanPosition";
                $queryParams['filePlanPosition'] = $filePlanPosition;
            }

            if ($originatorArchiveId) {
                $queryParts['originatorArchiveId'] = "originatorArchiveId = :originatorArchiveId";
                $queryParams['originatorArchiveId'] = $originatorArchiveId;
            }

            if ($originatorOrgRegNumber) {
                $queryParts['originatorOrgRegNumber'] = "originatorOrgRegNumber = :originatorOrgRegNumber";
                $queryParams['originatorOrgRegNumber'] = $originatorOrgRegNumber;
            }

            if ($finalDisposition) {
                $queryParts['finalDisposition'] = "finalDisposition = :finalDisposition";
                $queryParams['finalDisposition'] = $finalDisposition;
            }

            if ($originatingStartDate && $originatingEndDate) {
                $queryParams['originatingStartDate'] = $originatingStartDate;
                $queryParams['originatingEndDate'] = $originatingEndDate;
                $queryParts['originatingDate'] = "originatingDate >= :originatingStartDate AND originatingDate <= :originatingEndDate";
            } elseif ($originatingStartDate) {
                $queryParams['originatingStartDate'] = $originatingStartDate;
                $queryParts['originatingDate'] = "originatingDate >= :originatingStartDate";

            } elseif ($originatingEndDate) {
                $queryParams['originatingEndDate'] = $originatingEndDate;
                $queryParts['originatingDate'] = "originatingDate <= :originatingEndDate";
            }

            if ($depositStartDate && $depositEndDate) {
                $queryParams['depositStartDate'] = $depositStartDate;
                $queryParams['depositEndDate'] = $depositEndDate;
                $queryParts['depositDate'] = "depositDate >= :depositStartDate AND depositDate <= :depositEndDate";
            } elseif ($depositStartDate) {
                $queryParams['depositStartDate'] = $depositStartDate;
                $queryParts['depositDate'] = "depositDate >= :depositStartDate";

            } elseif ($depositEndDate) {
                $queryParams['depositEndDate'] = $depositEndDate;
                $queryParts['depositDate'] = "depositDate <= :depositEndDate";
            }
            if ($archiveExpired) {
                if ($archiveExpired == "true") {
                    $queryParams['disposalDate'] = $currentDateString;
                    $queryParts['disposalDate'] = "disposalDate <= :disposalDate";
                } elseif ($archiveExpired == "false") {
                    $queryParams['disposalDate'] = $currentDateString;
                    $queryParts['disposalDate'] = "disposalDate >= :disposalDate";
                }
            }

            if ($partialRetentionRule) {
                $queryParts['partialRetentionRule'] = "(retentionDuration=NULL 
                OR retentionStartDate=NULL 
                OR retentionRuleCode=NULL)";
            }

        }

        $queryParams['descriptionClass'] = 'recordsManagement/log';
        $queryParts['descriptionClass'] = "(descriptionClass != :descriptionClass OR descriptionClass=NULL)";

        $accessRuleAssert = $this->getAccessRuleAssert($currentDateString);

        if ($accessRuleAssert) {
            $queryParts[] = $accessRuleAssert;
        }

        $queryString = \laabs\implode(' AND ', $queryParts);
        $archives = $this->sdoFactory->find('recordsManagement/archive', $queryString, $queryParams, false, false, 300);

        foreach ($archives as $archive) {
            if (!empty($archive->disposalDate) && $archive->disposalDate <= \laabs::newDate()) {
                $archive->disposable = true;
            }
        }

        return $archives;
    }

    /**
     * Get archives list
     * @param string  $originatorOrgRegNumber The organization registration number
     * @param string  $filePlanPosition       The file plan position
     * @param boolean $archiveUnit            List the archive unit
     *
     * @return array recordsManagement/archive
     */
    public function index($originatorOrgRegNumber, $filePlanPosition = null, $archiveUnit = false)
    {
        $queryParts = array();
        $queryParams = array();

        $currentDate = \laabs::newDate();
        $currentDateString = $currentDate->format('Y-m-d');

        $queryParts['status'] = "status != :status";
        $queryParams['status'] = 'disposed';

        if ($originatorOrgRegNumber) {
            $queryParts['originatorOrgRegNumber'] = "originatorOrgRegNumber = :originatorOrgRegNumber";
            $queryParams['originatorOrgRegNumber'] = $originatorOrgRegNumber;
        }

        if ($filePlanPosition) {
            $queryParts['filePlanPosition'] = "filePlanPosition = :filePlanPosition";
            $queryParams['filePlanPosition'] = $filePlanPosition;
        } else {
            $queryParts['filePlanPosition'] = "filePlanPosition = null";
        }

        if ($archiveUnit == false) {
            $queryParts['parentArchiveId'] = "parentArchiveId = null";
        }

        if ($archiveUnit == true) {
            $queryParts['parentArchiveId'] = "parentArchiveId != null";
        }

        $accessRuleAssert = $this->getAccessRuleAssert($currentDateString);

        if ($accessRuleAssert) {
            $queryParts[] = $accessRuleAssert;
        }
        
        $queryString = \laabs\implode(' AND ', $queryParts);
        $archives = $this->sdoFactory->find('recordsManagement/archive', $queryString, $queryParams, false, false, 300);

        foreach ($archives as $archive) {
            if (!empty($archive->disposalDate) && $archive->disposalDate <= \laabs::newDate()) {
                $archive->disposable = true;
            }
        }

        return $archives;
    }

    /**
     * Get archive metadata
     * @param string $archiveId   The archive identifier
     * 
     * @return recordsManagement/archive The archive metadata
     */
    public function getMetadata($archiveId, $checkAccess = true)
    {
        if (is_scalar($archiveId)) {
            $archive = $this->sdoFactory->read('recordsManagement/archive', $archiveId);
        } else {
            $archive = $archiveId;
        }
        $this->getAccessRule($archive);

        if ($checkAccess) {
            $this->checkRights($archive);
        }

        $descriptionController = $this->useDescriptionController($archive->descriptionClass);

        $archive->descriptionObject = $descriptionController->read($archive->archiveId);

        return $archive;
    }

    /**
     * Get the related information of an archive
     * @param string $archiveId   The identifier of the archive or the archive itself
     * @param bool   $checkAccess Check access for originator or archiver. if false, caller MUST control access before or after
     * 
     * @return recordsManagement/archive
     */
    public function getRelatedInformation($archiveId, $checkAccess = true)
    {
        if (is_scalar($archiveId)) {
            $archive = $this->sdoFactory->read('recordsManagement/archive', $archiveId);
        } else {
            $archive = $archiveId;
        }

        if ($checkAccess) {
            $this->checkRights($archive);
        }

        $archive->lifeCycleEvent = $this->getArchiveLifeCycleEvent($archive->archiveId);
        $archive->relationships = $this->getArchiveRelationship($archive->archiveId);

        return $archive;
    }

    /**
     * Get the children of an archive as an index
     * @param string $archiveId         The identifier of the archive or the archive itself
     * @param bool   $loadResourcesInfo Load the resources info
     * @param bool   $loadBinary        Load the resources binary
     *
     * @return array recordsManagement/archive
     */
    public function listChildrenArchive($archiveId, $loadResourcesInfo = false, $loadBinary = false, $checkAccess = true)
    {
        if (is_scalar($archiveId)) {
            $archive = $this->sdoFactory->read('recordsManagement/archive', $archiveId);
        } else {
            $archive = $archiveId;
        }

        $archive->digitalResources = $this->getDigitalResources($archive->archiveId);

        if ($archive->digitalResources) {
            if ($loadBinary) {
                foreach ($archive->digitalResources as $i => $digitalResource) {
                    $archive->digitalResources[$i] = $this->digitalResourceController->retrieve($digitalResource->resId);
                }

            } elseif ($loadResourcesInfo) {
                foreach ($archive->digitalResources as $i => $digitalResource) {
                    $archive->digitalResources[$i] = $this->digitalResourceController->info($digitalResource->resId);
                }
            }
        }

        $archive->contents = $this->sdoFactory->find(
            "recordsManagement/archive",
            "parentArchiveId='".(string) $archive->archiveId."'"
        );

        if ($archive->contents) {
            foreach ($archive->contents as $child) {
                $this->listChildrenArchive($child, $loadResourcesInfo, $loadBinary);
            }
        }

        return $archive;
    }

    /**
     * Retrieve an archive resource contents
     * @param string $archiveId   The archive identifier
     * @param bool   $checkAccess Check access for originator or archiver. if false, caller MUST control access before or after
     * 
     * @return digitalResource/digitalResource[] Array of digitalResource/digitalResource object
     */
    public function getDigitalResources($archiveId, $checkAccess = true)
    {
        $archive = $this->sdoFactory->read('recordsManagement/archive', $archiveId);

        if ($checkAccess) {
            $this->checkRights($archive);
        }

        return $this->digitalResourceController->getResourcesByArchiveId($archiveId);
    }

    /**
     * Retrieve an archive resource contents
     *
     * @param string $archiveId   The archive identifier
     * @param string $resId       The resource identifier
     * @param bool   $checkAccess Check access for originator or archiver. if false, caller MUST control access before or after
     * 
     * @return digitalResource/digitalResource Archive resource contents
     */
    public function consultation($archiveId, $resId, $checkAccess = true)
    {
        $archive = $this->sdoFactory->read('recordsManagement/archive', $archiveId);

        if ($checkAccess) {
            $this->checkRights($archive);
        }

        try {
            $digitalResource = $this->digitalResourceController->retrieve($resId);

            $resourceIntegrity = true;
            foreach ($digitalResource->address as $address) {
                if (!$address->integrityCheckResult) {
                    $resourceIntegrity = false;
                }
            }

            if (!$resourceIntegrity) {
                $this->logIntegrityCheck($archive, "Invalid resource", $digitalResource, false);
            }

            if (($checkAccess && !$this->accessVerification($archive)) || $digitalResource->archiveId != $archiveId) {
                throw \laabs::newException('recordsManagement/accessDeniedException', "Permission denied");
            }

            $this->logConsultation($archive, $digitalResource);

        } catch (\Exception $e) {
            $this->logConsultation($archive, $digitalResource, false);

            throw $e;
        }

        $binaryDataObject = \laabs::newInstance("recordsManagement/BinaryDataObject");
        $binaryDataObject->attachment = new \stdClass();
        $binaryDataObject->attachment->data = base64_encode($digitalResource->getContents());
        $binaryDataObject->attachment->uri = "";
        $binaryDataObject->attachment->filename = $digitalResource->fileName;

        if (!empty($digitalResource->fileExtension)) {
            $digitalResource->fileName = $digitalResource->fileName . $digitalResource->fileExtension;
        }

        $binaryDataObject->format = $digitalResource->puid;
        $binaryDataObject->mimetype = $digitalResource->mimetype;
        $binaryDataObject->size = $digitalResource->size;

        $binaryDataObject->messageDigest = new \stdClass();
        $binaryDataObject->messageDigest->value = $digitalResource->hash;
        $binaryDataObject->messageDigest->algorithm = $digitalResource->hashAlgorithm;

        return $binaryDataObject;
    }

    /**
     * Retrieve an archive by its id
     *
     * @param string $archiveId   The archive identifier
     * @param bool   $withBinary  Retrieve contents or only metadata
     * @param bool   $checkAccess Check access for originator or archiver. if false, caller MUST control access before or after
     * @throws
     * @return recordsManagement/archive object
     */
    public function retrieve($archiveId, $withBinary = false, $checkAccess = true)
    {
        if (is_scalar($archiveId)) {
            $archive = $this->sdoFactory->read('recordsManagement/archive', $archiveId);
        } else {
            $archive = $archiveId;
        }

        if ($checkAccess) {
            $this->checkRights($archive);
        }

        $this->getMetadata($archive, $checkAccess);
        $archive->originatorOrg = $this->organizationController->getOrgByRegNumber($archive->originatorOrgRegNumber);

        if (!empty($archive->archiverOrgRegNumber)) {
            $archive->archiverOrg = $this->organizationController->getOrgByRegNumber($archive->archiverOrgRegNumber);
        }
        if (!empty($archive->depositorOrgRegNumber)) {
            $archive->depositorOrg = $this->organizationController->getOrgByRegNumber($archive->depositorOrgRegNumber);
        }
        $this->getRelatedInformation($archive, $checkAccess);
        $this->listChildrenArchive($archive, true, $withBinary, $checkAccess);

        $this->getParentArchive($archive);

        if (!empty($archive->contents)) {
            foreach ($archive->contents as $child) {
                $this->retrieve($child, $withBinary, $checkAccess);
            }
        }

        $archive->communicability = true;
        if ($checkAccess) {
            $archive->communicability = $this->accessVerification($archive);
        }

        if (\laabs::hasBundle('medona')) {
            $archive->messages = $this->getMessageByArchiveid($archive->archiveId);
        }

        return $archive;
    }

    /**
     * Get an archive life cycle event
     * @param string $archiveId The archive identifier
     *
     * @return array lifeCycle/event
     */
    public function getArchiveLifeCycleEvent($archiveId)
    {
        return $this->lifeCycleJournalController->getObjectEvents($archiveId, 'recordsManagement/archive');
    }

    /**
     * Get an archive relationship
     * @param string $archiveId The archive identifier
     *
     * @return array recordsManagement/archiveRelationship
     */
    public function getArchiveRelationship($archiveId)
    {
        $res = [];
        $res['childrenRelationships'] = $this->archiveRelationshipController->getByArchiveId($archiveId);
        $res['parentRelationships'] = $this->archiveRelationshipController->getByRelatedArchiveId($archiveId);

        return $res;
    }

    /**
     * Validate archive access
     *
     * @param string $archiveId The archive identifier
     *
     * @return boolean The result of the authorization access
     */
    public function accessVerification($archiveId)
    {
        $archive = $this->sdoFactory->read('recordsManagement/archive', $archiveId);

        $comDateAccess = $this->accessComDateVerification($archive);

        $currentService = \laabs::getToken("ORGANIZATION");
        if (!$currentService) {
            return false;
        }

        $userServiceOrgRegNumbers = array_merge(
            array($currentService->registrationNumber),
            $this->userPositionController->readDescandantService((string) $currentService->orgId)
        );

        foreach ($userServiceOrgRegNumbers as $userServiceOrgRegNumber) {
            $userService = $this->organizationController->getOrgByRegNumber($userServiceOrgRegNumber);

            // User orgUnit is owner
            if (isset($userService->orgRoleCodes) && (strpos((string) $userService->orgRoleCodes, 'owner') !== false)) {
                return true;
            }

            // Archiver or Originator
            if ($userServiceOrgRegNumber == (string) $archive->archiverOrgRegNumber
                || $userServiceOrgRegNumber == (string) $archive->originatorOrgRegNumber) {
                return true;
            }

            // If date is in the past, public communication is allowed
            if ($userService->ownerOrgId == $archive->originatorOwnerOrgId && $comDateAccess) {
                return true;
            }
        }
    }

    /**
     * Verification of the communication date for access
     *
     * @param recordsManagement/archive $archive The archive to verify
     *
     * @return boolean The access right
     */
    private function accessComDateVerification($archive)
    {
        $access = true;

        if ($archive->accessRuleComDate) {
            $communicationDelay = $archive->accessRuleComDate->diff(\laabs::newTimestamp());
            $access = $communicationDelay->invert == 0 ? true : false;
        }

        return $access;
    }

    /**
     * Get archive assert
     * @param array $args
     * @param array $queryParams
     * @param bool  $checkAccess
     *
     * @return string Query
     */
    public function getArchiveAssert($args, &$queryParams, $checkAccess = true)
    {
        // Args on archive
        $currentDate = \laabs::newDate();
        $currentDateString = $currentDate->format('Y-m-d');

        $queryParts = [];
        if (!empty($args['archiveName'])) {
            $queryParts[] = "archiveName='*".$args['archiveName']."*'";
        }
        if (!empty($args['profileReference'])) {
            $queryParts['archivalProfileReference'] = "archivalProfileReference = :archivalProfileReference";
            $queryParams['archivalProfileReference']=$args['profileReference'];
        }
        if (!empty($args['agreementReference'])) {
            $queryParts['archivalAgreementReference'] = "archivalAgreementReference=:archivalAgreementReference";
            $queryParts['archivalAgreementReference'] = $args['agreementReference'];
        }
        if (!empty($args['archiveId'])) {
            $queryParts['archiveId'] = "archiveId=:archiveId";
            $queryParams['archiveId'] = $args['archiveId'];
        }
        if (!empty($args['status'])) {
            $queryParts['status'] = "status=:status";
            $queryParams['status'] = $args['status'];
        }
        if (!empty($args['retentionRuleCode'])) {
            $queryParts[] = "retentionRuleCode=:retentionRuleCode";
            $queryParams['retentionRuleCode'] = $args['retentionRuleCode'];
        }
        if (!empty($args['archiveExpired']) && $args['archiveExpired'] == "true") {
            $queryParts['disposalDate'] = "disposalDate<= :disposalDate";
            $queryParams['disposalDate'] = $currentDateString;
        }
        if (!empty($args['archiveExpired']) && $args['archiveExpired'] == "false") {
            $queryParts['disposalDate'] = "disposalDate>= :disposalDate";
            $queryParams['disposalDate'] = $currentDateString;
        }
        if (!empty($args['partialRetentionRule']) && $args['partialRetentionRule'] == "true") {
            $queryParts['partialRetentionRule'] = "(
            retentionDuration=NULL 
            OR retentionStartDate=NULL 
            OR retentionRuleCode=NULL
            )";
        }
        if (!empty($args['finalDisposition'])) {
            $queryParts['finalDisposition'] = "finalDisposition= :finalDisposition";
            $queryParams['finalDisposition'] =$args['finalDisposition'];
        }
        if (!empty($args['originatorOrgRegNumber'])) {
            $queryParts[] = "originatorOrgRegNumber= :originatorOrgRegNumber";
            $queryParams['originatorOrgRegNumber'] = $args['originatorOrgRegNumber'];
        }
        if (!empty($args['originatorArchiveId'])) {
            $queryParts['originatorArchiveId'] = "originatorArchiveId= :originatorArchiveId";
            $queryParams['originatorArchiveId'] = $args['originatorArchiveId'];
        }
        if (!empty($args['archiverArchiveId'])) {
            $queryParts['archiverArchiveId'] = "archiverArchiveId= :archiverArchiveId";
            $queryParams['archiverArchiveId'] = $args['archiverArchiveId'];
        }
        if (!empty($args['originatingDate'])) {
            if (!empty($args['originatingDate'][0]) && is_string($args['originatingDate'][0])) {
                $args['originatingDate'][0] = \laabs::newDate($args['originatingDate'][0]);
            }
            if (!empty($args['originatingDate'][1]) && is_string($args['originatingDate'][1])) {
                $args['originatingDate'][1] = \laabs::newDate($args['originatingDate'][1]);
            }

            if (!empty($args['originatingDate'][0])) { // originatingStartDate
                $args['originatingDate'][0] = $args['originatingDate'][0]->format('Y-m-d');
                $queryParts['originatingDate'] = "originatingDate>= :originatingDate";
                $queryParams['originatingDate'] =$args['originatingDate'][0];
            }
            if (!empty($args['originatingDate'][1])) { // originatingEndDate;
                $args['originatingDate'][1] = $args['originatingDate'][1]->format('Y-m-d');
                $queryParts['originatingDate'] = "originatingDate<= :originatingDate";
                $queryParams['originatingDate'] = $args['originatingDate'][1];
            }
        }

        if (!empty($args['depositStartDate']) && is_string($args['depositStartDate'])) {
            $args['depositStartDate'] = \laabs::newDate($args['depositStartDate']);
        }
        if (!empty($args['depositEndDate']) && is_string($args['depositEndDate'])) {
            $args['depositEndDate'] = \laabs::newDate($args['depositEndDate']);
        }

        if (!empty($args['depositStartDate']) && !empty($args['depositEndDate'])) {
            $args['depositStartDate'] = $args['depositStartDate']->format('Y-m-d').'T00:00:00';
            $args['depositEndDate'] = $args['depositEndDate']->format('Y-m-d').'T23:59:59';
            $queryParts['depositDate'] = "depositDate <= :depositEndDate AND depositDate >= :depositStartDate";
            $queryParams['depositEndDate'] = $args['depositEndDate'];
            $queryParams['depositStartDate'] = $args['depositStartDate'];
        } elseif (!empty($args['depositStartDate'])) {
            $args['depositStartDate'] = $args['depositStartDate']->format('Y-m-d').'T00:00:00';
            $queryParts['depositDate'] = "depositDate >= :depositStartDate";
            $queryParams['depositStartDate'] = $args['depositStartDate'];
        } elseif (!empty($args['depositEndDate'])) {
            $args['depositEndDate'] = $args['depositEndDate']->format('Y-m-d').'T23:59:59';
            $queryParts['depositDate'] = "depositDate <= :depositEndDate";
            $queryParams['depositEndDate'] = $args['depositEndDate'];
        }

        if (!empty($args['depositorOrgRegNumber'])) {
            $queryParts['depositorOrgRegNumber'] = "depositorOrgRegNumber= :depositorOrgRegNumber";
            $queryParams['depositorOrgRegNumber'] = $args['depositorOrgRegNumber'];
        }
        if (!empty($args['filePlanPosition'])) {
            $foldersId = $this->getDescendantFolder($args['filePlanPosition']);
            $queryParts['filePlanPosition'] = "filePlanPosition=['".implode("', '", $foldersId)."']";
        }
        if (isset($args['hasParent'])) {
            if ($args['hasParent'] == true) {
                $queryParts['parentArchiveId'] = "parentArchiveId!=null";
            } elseif ($args['hasParent']  === false) {
                $queryParts['hasParent'] = "parentArchiveId=null";
            }
        }
        
        if (isset($args['processingStatus'])) {
            if ($args['processingStatus'] === true) {
                $queryParts['processingStatus'] = "processingStatus!=null";
            } elseif ($args['processingStatus'] === false) {
                $queryParts['processingStatus'] = "processingStatus=null";
            } elseif (is_string($args['processingStatus'])) {
                $queryParts['processingStatus'] = "processingStatus= :processingStatus";
                $queryParams['processingStatus'] = $args['processingStatus'];
            }
        }

        if ($checkAccess) {
            $accessRuleAssert = $this->getAccessRuleAssert($currentDateString);

            if ($accessRuleAssert) {
                $queryParts[] = $accessRuleAssert;
            }
        }

        return implode(' and ', $queryParts);
    }

    /**
     * Get the query assert for access rule
     * @param string $currentDateString the date
     *
     * @return string Query
     */
    public function getAccessRuleAssert($currentDateString)
    {
        $currentService = \laabs::getToken("ORGANIZATION");
        if (!$currentService) {
            return "true=false";
        }

        $userServiceOrgRegNumbers = array_merge(
            array($currentService->registrationNumber),
            $this->userPositionController->readDescandantService((string) $currentService->orgId)
        );

        $owner = false;
        foreach ($userServiceOrgRegNumbers as $userServiceOrgRegNumber) {
            $userService = $this->organizationController->getOrgByRegNumber($userServiceOrgRegNumber);
            if (isset($userService->orgRoleCodes) && $userService->orgRoleCodes->contains('owner')) {
                return;
            }
        }

        $queryParts['originator'] = "originatorOrgRegNumber=['".implode("', '", $userServiceOrgRegNumbers)."']";
        $queryParts['archiver'] = "archiverOrgRegNumber=['".implode("', '", $userServiceOrgRegNumbers)."']";
        $queryParts['user'] = "(userOrgRegNumbers = '".$currentService->registrationNumber."' OR userOrgRegNumbers = '".$currentService->registrationNumber." *' OR userOrgRegNumbers = '* ".$currentService->registrationNumber." *' OR userOrgRegNumbers = '* ".$currentService->registrationNumber."')";
        //$queryParts['depositor'] = "depositorOrgRegNumber=['". implode("', '", $userServiceOrgRegNumbers) ."']";

        $queryParts['accessRule'] = "(originatorOwnerOrgId = '".$currentService->ownerOrgId
            ."' AND (accessRuleComDate <= '$currentDateString'))";

        return "(".implode(" OR ", $queryParts).")";
    }

    /**
     * Get the parent archive
     * @param recordsManagement/archive $archive The archive
     *
     * @return recordsManagement/archive Parent archive
     */
    protected function getParentArchive($archive)
    {
        if (isset($archive->parentArchiveId)) {
            $archive->parentArchive = $this->sdoFactory->read("recordsManagement/archive", $archive->parentArchiveId);
        }

        return $archive;
    }

    /**
     * Change the status of an archive
     * @param mixed  $archiveIds Identifiers of the archives to update
     * @param string $status     New status to set
     *
     * @return array Archives ids separate by successfully updated archives ['success']
     * and not updated archives ['error']
     */
    public function setStatus($archiveIds, $status)
    {
        $statusList = [];
        $statusList['preserved'] = array('frozen', 'disposable', 'error');
        $statusList['frozen'] = array('preserved', 'disposable');
        $statusList['disposable'] = array('preserved');
        $statusList['disposed'] = array('disposable');
        $statusList['restituted'] = array('restituable');
        $statusList['error'] = array('preserved', 'frozen', 'disposable', 'disposed');

        if (!is_array($archiveIds)) {
            $archiveIds = array($archiveIds);
        }

        $res = array('success' => array(), 'error' => array());

        if (!isset($statusList[$status])) {
            $res['error'] = $archiveIds;

            return $res;
        }
        foreach ($archiveIds as $archiveId) {
            $archiveStatus = $this->sdoFactory->read('recordsManagement/archiveStatus', $archiveId);

            if (!in_array($archiveStatus->status, $statusList[$status])) {
                array_push($res['error'], $archiveId);
            } else {
                $archiveStatus->status = $status;
                $archiveStatus->lastModificationDate = \laabs::newTimestamp();
                
                $childrenArchives = $this->sdoFactory->index('recordsManagement/archive', "archiveId", "parentArchiveId = '$archiveId'");
                $this->setStatus($childrenArchives, $status);

                $this->sdoFactory->update($archiveStatus);
                array_push($res['success'], $archiveId);
            }
        }

        return $res;
    }

    /**
     * Change the processing status of an archive
     * @param mixed  $archiveIds   Identifiers of the archives to update
     * @param string $targetStatus New processing status to set
     *
     * @return array Archives ids separate by successfully updated archives ['success']
     * and not updated archives ['error']
     */
    public function setProcessingStatus($archiveIds, $targetStatus)
    {
        if (!is_array($archiveIds)) {
            $archiveIds = array($archiveIds);
        }

        $res = array('success' => array(), 'error' => array());

        foreach ($archiveIds as $archiveId) {
            $archiveProcessingStatus = $this->sdoFactory->read('recordsManagement/archiveProcessingStatus', $archiveId);
            $currentStatus = $archiveProcessingStatus->processingStatus;

            $archiveProcessingStatus->processingStatus = $targetStatus;
            $this->sdoFactory->update($archiveProcessingStatus);
            array_push($res['success'], $archiveId);
        }

        return $res;
    }

    /**
     * Calculate the communication date of an archive
     * @param timestamp $startDate The start date
     * @param duration  $duration  The duration
     *
     * @return date The communication date of an archive
     */
    public function calculateDate($startDate, $duration)
    {
        if (empty($startDate) || empty($duration)) {
            return null;
        }

        return $startDate->shift($duration);
    }

    /**
     * Check if the current user have the rights on an archive
     *
     * @param recordsManagement/archive $archive The archive object
     * @throws
     * @return boolean THe result of the operation
     */
    public function checkRights($archive)
    {
        $currentUserService = \laabs::getToken("ORGANIZATION");
        
        if (!$currentUserService) {
            return false;
        }

        $userPositionController = \laabs::newController('organization/userPosition');
        $userServices = $userPositionController->readDescandantService($currentUserService->orgId);
        $userServices[] = $currentUserService->registrationNumber;
        
        // OWNER access
        if (!is_null($currentUserService->orgRoleCodes)
            && \laabs\in_array('owner', $currentUserService->orgRoleCodes)) {
            return true;
        }

        // ARCHIVER access
        if (!is_null($currentUserService->orgRoleCodes)
            && \laabs\in_array('archiver', $currentUserService->orgRoleCodes)
            && $archive->archiverOrgRegNumber === $currentUserService->registrationNumber) {
            return true;
        }

        // ORIGINATOR ACCESS
        if (\laabs\in_array($archive->originatorOrgRegNumber, $userServices)) {
            return true;
        }

        // USER ACCESS
        if (!empty($archive->userOrgRegNumbers)) {
            foreach ($archive->userOrgRegNumbers as $userOrgRegNumber) {
                if (\laabs\in_array($userOrgRegNumber, $userServices)) {
                    return true;
                }
            }
        }

        throw \laabs::newException('recordsManagement/accessDeniedException', "Permission denied");
    }

    /**
     * Calculate access rule from archive
     * @param recordsManagement/archive         $archive The archive object
     * @param recordsManagement/archivalProfile $archivalProfile The archiveProfile object
     *
     */
    public function getAccessRule($archive, $archivalProfile = false)
    {
        $accessRules = array();
        if (!empty($archive->accessRuleCode)) {
            $accessRuleCode = $archive->accessRuleCode;
        } elseif (!empty($archive->archivalProfileReference)) {
            $archivalProfile = $this->archivalProfileController->getByReference($archive->archivalProfileReference);
            $accessRuleCode = $archivalProfile->accessRuleCode;
        } else {
            return;
        }
        if (!empty($accessRuleCode)) {
            $archive->accessRule = $this->accessRuleController->edit($accessRuleCode);
        }
    }

    /**
     * Check if archive exists
     * @param string $archiveId The archive identifier
     *
     * @return object Object with archiveId and a boolean 'exist'
     */
    public function exists($archiveId)
    {
        $result = new \stdClass();
        $result->archiveId = $archiveId;
        $result->exist = false;
        if ($this->sdoFactory->exists("recordsManagement/archive", array("archiveId" => $archiveId))) {
            $result->exist = true;
        }

        return $result;
    }

    /**
     * Count the archives for an organization
     * @param string $orgRegNumber The organization registration number
     *
     * @return int The number of archives with this organization
     */
    public function countByOrg($orgRegNumber)
    {
        $queryString = [];
        $queryString[] = "archiverOrgRegNumber='$orgRegNumber'";
        $queryString[] = "originatorOrgRegNumber='$orgRegNumber'";

        $count = $this->sdoFactory->count("recordsManagement/archive", \laabs\implode(" OR ", $queryString));

        return $count;
    }

    /**
     * list archive message
     * @param string $archiveId The archive identifier
     *
     * @return message[] Array of message object
     */
    protected function getMessageByArchiveid($archiveId)
    {

        $queryString = [];
        $unitIdentifiers = $this->sdoFactory->find('medona/unitIdentifier', "objectId='$archiveId'");

        foreach ($unitIdentifiers as $unitIdentifier) {
            $queryString [] ="messageId='$unitIdentifier->messageId'";
        }

        if (count($unitIdentifiers) != 0) {
            $messages = $this->sdoFactory->find('medona/message', \laabs\implode(" OR ", $queryString));
        } else {
            $messages = null;
        }

        return $messages;
    }

    /**
     * @param string $positionId
     *
     * @return array $filePlanPosition
     */
    protected function getDescendantFolder($positionId)
    {
        $filePlanPosition = [];
        $folders = $this->sdoFactory->find('filePlan/folder', "parentFolderId='".$positionId."'");

        $filePlanPosition[] = $positionId;
        foreach ($folders as $folder) {
            $filePlanPosition = array_merge($filePlanPosition, $this->getDescendantFolder($folder->folderId));
        }

        return $filePlanPosition;
    }

    /**
     * Restitute an archive
     * @param string $archiveId The idetifier of the archive
     *
     * @return recordsManagement/archive The restitue archive
     */
    public function communicate($archiveId)
    {
        $this->verifyIntegrity($archiveId);

        $archive = $this->retrieve((string) $archiveId, true);

        $this->logDelivery($archive);

        return $archive;
    }
}
