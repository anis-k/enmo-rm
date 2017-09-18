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

namespace bundle\recordsManagement\Controller;

/**
 * Trait for archives life cycle logging
 */
trait archiveLifeCycleTrait
{
	/**
     * Log an archive life cycle event
     * @param string 						  $type      	   The eventType
     * @param recordsManagement/archive 	  $archive   	   The archive
     * @param bool  						  $operationResult The event result
     * @param digitalResource/digitalResource $resource   	   The resouce
     * @param array 						  $eventInfo 	   The event information
     *
     * @return mixed The created event or the list of created envent
     */
	protected function logLifeCycleEvent($type, $archive, $operationResult = true, $resource = null, $eventInfo = null)
	{
		$eventItems = $eventInfo ? $eventInfo : [];
		$res = null;

        $eventItems["originatorOwnerOrgRegNumber"] = $archive->originatorOwnerOrgRegNumber;

        if ($resource) {
        	$eventItems['resId'] = $resource->resId;
        	$eventItems['hashAlgorithm'] = $resource->hashAlgorithm;
        	$eventItems['hash'] = $resource->hash;
        	$eventInfo['address'] = $resource->address[0]->path;

        	$res = $this->lifeCycleJournalController->logEvent($type, 'recordsManagement/archive', $archive->archiveId, $eventItems, $operationResult);

        
        } else if ($eventInfo !== false && !empty($archive->digitalResources)) {
        	$res = [];

			foreach ($archive->digitalResources as $digitalResource) {
				$eventItems['resId'] = $digitalResource->resId;
        		$eventItems['hashAlgorithm'] = $digitalResource->hashAlgorithm;
        		$eventItems['hash'] = $digitalResource->hash;
        		$eventInfo['address'] = $digitalResource->address[0]->path;

        		$res[] = $this->lifeCycleJournalController->logEvent($type, 'recordsManagement/archive', $archive->archiveId, $eventItems, $operationResult);
			}

		} else {
			$eventItems= [];
			$eventItems['resId'] = null;
	        $eventItems['hashAlgorithm'] = null;
	        $eventItems['hash'] = null;
	        $eventItems['address'] = $archive->storagePath;

        	$res = $this->lifeCycleJournalController->logEvent($type, 'recordsManagement/archive', $archive->archiveId, $eventItems, $operationResult);
		}

        return $res;
	}



	// DEPOSIT -> archiveEntryTrait
	/**
     * Log an archive deposit
     * @param recordsManagement/archive $archive   	   The archive
     * @param bool  					$operationResult The operation result
     *
     * @return mixed The created event or the list of created envent
     */
	public function logDeposit($archive, $operationResult = true)
	{
		return $this->logLifeCycleEvent('recordsManagement/deposit', $archive, $operationResult);
	}

	// CONSULTATION -> archiveCommunicationTrait
	/**
     * Log an archive consultation
     * @param recordsManagement/archive 	  $archive   	   The archive
     * @param digitalResource/digitalResource $resource   	   The resouce
     * @param bool  						  $operationResult The operation result
     *
     * @return mixed The created event or the list of created envent
     */
	public function logConsultation($archive, $resource, $operationResult = true)
	{
		return $this->logLifeCycleEvent('recordsManagement/consultation',$archive, $operationResult, $resource);
	}


	// DELIVERY -> archiveCommunicationTrait
	/**
     * Log an archive delivery
     * @param recordsManagement/archive $archive   	   The archive
     * @param bool  					$operationResult The operation result
     *
     * @return mixed The created event or the list of created envent
     */
	public function logDelivery($archive, $operationResult = true)
	{
		return $this->logLifeCycleEvent('recordsManagement/delivery',$archive, $operationResult);
	}

	// INTEGRITY -> archiveComplianceTrait
	/**
     * Log an archive resource integrity check
     * @param digitalResource/digitalResource $resource   	   The resouce
     * @param recordsManagement/archive       $archive         The archive
     * @param bool  						  $operationResult The operation result
     *
     * @return mixed The created event or the list of created envent
     */
	public function logIntegrity($resource, $archive, $info, $operationResult = true)
	{
        $currentOrganization = \laabs::getToken("ORGANIZATION");

		$eventInfo = [];
        $eventInfo['requesterOrgRegNumber'] = $currentOrganization->registrationNumber;
        $eventInfo['info'] = $info;

        return $this->logLifeCycleEvent('recordsManagement/integrityCheck', $archive, $operationResult, $resource, $eventInfo);
	}

	// CONVERSION -> archiveConversionTrait
	/**
     * Log an archive resource integrity check
     * @param digitalResource/digitalResource $originalResource  The resouce
     * @param digitalResource/digitalResource $convertedResource The resouce
     * @param recordsManagement/archive       $archive           The archive
     * @param bool  						  $operationResult   The operation result
     *
     * @return mixed The created event or the list of created envent
     */
	public function logConvertion($originalResource, $convertedResource, $archive, $operationResult = true)
	{
		$eventInfo = [];
        if ($convertedResource) {
        	if (!empty($convertedResource->address)) {
            	$eventInfo['convertedAddress'] = $convertedResource->address[0]->path;
        	}

            if (!empty($convertedResource->resId)) {
                $eventInfo['convertedResId'] = $convertedResource->resId;
            }

            $eventInfo['convertedHashAlgorithm'] = $convertedResource->hashAlgorithm;
            $eventInfo['convertedHash'] = $convertedResource->hash;
            $eventInfo['software'] = $convertedResource->softwareName.' '.$convertedResource->softwareVersion;
		}

        return $this->logLifeCycleEvent('recordsManagement/conversion', $archive, $operationResult, $originalResource, $eventInfo);
	}

	// ELIMINATION -> archiveDestructionTrait
	/**
     * Log an archive elimination
     * @param recordsManagement/archive $archive   	     The archive
     * @param bool  					$operationResult The operation result
     *
     * @return mixed The created event or the list of created envent
     */
	public function logElimination($archive, $operationResult = true)
	{
		return $this->logLifeCycleEvent('recordsManagement/elimination', $archive, $operationResult);
	}

	// DESTRUCTION -> archiveDestructionTrait
	/**
     * Log an archive destruction
     * @param recordsManagement/archive $archive   	     The archive
     * @param bool  					$operationResult The operation result
     *
     * @return mixed The created event or the list of created envent
     */
	public function logDestruction($archive, $operationResult = true)
	{
		return $this->logLifeCycleEvent('recordsManagement/detruction', $archive, $operationResult);
	}

	// RESTITUTION -> archiveRestitutionTrait
	/**
     * Log an archive restitution
     * @param recordsManagement/archive $archive   	     The archive
     * @param bool  					$operationResult The operation result
     *
     * @return mixed The created event or the list of created envent
     */
	public function logRestitution($archive, $operationResult = true)
	{
        return $this->logLifeCycleEvent('recordsManagement/restitution', $archive, $operationResult);
	}

	// RETENTION RULE MODIFICATION -> archiveModificationTrait
	/**
     * Log an archive retention rule modification
     * @param recordsManagement/archive $archive   	     The archive
     * @param object 					$retentionRule   The retention rule object
     * @param bool  					$operationResult The operation result
     *
     * @return mixed The created event or the list of created envent
     */
	public function logRetentionRuleModification($archive, $retentionRule, $operationResult = true)
	{
	  	$eventInfo = array(
            'originatorOrgRegNumber' => $archive->originatorOrgRegNumber,
            'archiverOrgRegNumber' => $archive->archiverOrgRegNumber,
            'retentionStartDate' => (string) $retentionRule->retentionStartDate,
            'retentionDuration' => (string) $retentionRule->retentionDuration,
            'finalDisposition' => (string) $retentionRule->finalDisposition,
            'previousStartDate' => (string) $retentionRule->previousStartDate,
            'previousDuration' => (string) $retentionRule->previousDuration,
            'previousFinalDisposition' => (string) $retentionRule->previousFinalDisposition,
        );

		return $this->logLifeCycleEvent('recordsManagement/retentionRuleModification', $archive, $operationResult, false, $eventInfo);
	}

	// ACCESS RULE MODIFICATION -> archiveModificationTrait
	/**
     * Log an archive access rule modification
     * @param recordsManagement/archive $archive   	     The archive
     * @param object 					$accessRule      The access rule object
     * @param bool  					$operationResult The operation result
     *
     * @return mixed The created event or the list of created envent
     */
	public function logAccessRuleModification($archive, $accessRule, $operationResult = true)
	{
		$eventInfo = array(
            'originatorOrgRegNumber' => $archive->originatorOrgRegNumber,
            'archiverOrgRegNumber' => $archive->archiverOrgRegNumber,
            'accessRuleStartDate' => (string) $accessRule->accessRuleStartDate,
            'accessRuleDuration' => (string) $accessRule->accessRuleDuration,
            'previousAccessRuleStartDate' => (string) $accessRule->previousAccessRuleStartDate,
            'previousAccessRuleDuration' => (string) $accessRule->previousAccessRuleDuration,
        );

		return $this->logLifeCycleEvent('recordsManagement/accessRuleModification', $archive, $operationResult, false, $eventInfo);
	}

	// FREEZE -> archiveModificationTrait
	/**
     * Log an archive freeze
     * @param recordsManagement/archive $archive   	     The archive
     * @param bool  					$operationResult The operation result
     *
     * @return mixed The created event or the list of created envent
     */
	public function logFreeze($archive, $operationResult = true)
	{
		return $this->logLifeCycleEvent('recordsManagement/freeze', $archive, $operationResult);
	}

	// UNFREEZE -> archiveModificationTrait
	/**
     * Log an archive unfreeze
     * @param recordsManagement/archive $archive   	     The archive
     * @param bool  					$operationResult The operation result
     *
     * @return mixed The created event or the list of created envent
     */
	public function logUnfreeze($archive, $operationResult = true)
	{
		return $this->logLifeCycleEvent('recordsManagement/unfreeze', $archive, $operationResult);
	}

	// METADATA -> archiveModificationTrait
	/**
     * Log an archive metadata modification
     * @param recordsManagement/archive $archive   	     The archive
     * @param bool  					$operationResult The operation result
     *
     * @return mixed The created event or the list of created envent
     */
	public function logMetadataModification($archive, $operationResult = true)
	{
		$eventInfo = array(
            'originatorOrgRegNumber' => $archive->originatorOrgRegNumber,
            'archiverOrgRegNumber' => $archive->archiverOrgRegNumber,
        );

		return $this->logLifeCycleEvent('recordsManagement/metadata', $archive, $operationResult, false, $eventInfo);
	}

	// ADD RELATIONSHIP -> archiveModificationTrait
	/**
     * Log an archive relatationship adding
     * @param recordsManagement/archive $archive   	         The archive
     * @param object 					$archiveRelationship The access rule object
     * @param bool  					$operationResult     The operation result
     *
     * @return mixed The created event or the list of created envent
     */
	public function logRelationshipAdding($archive, $archiveRelationship, $operationResult = true)
	{
		$eventInfo = array(
            'originatorOrgRegNumber' => $archive->originatorOrgRegNumber,
            'archiverOrgRegNumber' => $archive->archiverOrgRegNumber,
            'relatedArchiveId' => $archiveRelationship->relatedArchiveId
        );

		return $this->logLifeCycleEvent('recordsManagement/addRelationship', $archive, $operationResult, false, $eventInfo);
	}

	// ADD RELATIONSHIP -> archiveModificationTrait
	/**
     * Log an archive relatationship deleting
     * @param recordsManagement/archive $archive   	         The archive
     * @param object 					$archiveRelationship The access rule object
     * @param bool  					$operationResult     The operation result
     *
     * @return mixed The created event or the list of created envent
     */
	public function logRelationshipDeleting($archive, $archiveRelationship, $operationResult = true)
	{
		$eventInfo = array(
            'originatorOrgRegNumber' => $archive->originatorOrgRegNumber,
            'archiverOrgRegNumber' => $archive->archiverOrgRegNumber,
            'relatedArchiveId' => $archiveRelationship->relatedArchiveId
        );

		return $this->logLifeCycleEvent('recordsManagement/deleteRelationship',$archive, $operationResult, false, $eventInfo);
	}

}
