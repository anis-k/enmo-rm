<?php

/*
 * Copyright (C) 2015 Maarch
 *
 * This file is part of maarchRM.
 *
 * maarchRM is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * maarchRM is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with bundle recordsManagement.  If not, see <http://www.gnu.org/licenses/>.
 */

namespace presentation\maarchRM\UserStory\archiveManagement;

/**
 * Interface for archive retrieval
 */
interface retrieveInterface
{
    /**
     * Search form
     *
     * @return recordsManagement/archive/searchForm
     *
     * @uses recordsManagement/archivalProfile/readIndex
     *
     * @throws records/exception/orgException organization/organization/noOriginatorException
     * @throws records/exception/defaultJson Exception
     */
    public function readRecordsmanagementArchivesSearchform();

    /**
     * Search form
     *
     * @uses recordsManagement/archives/read
     *
     * @return recordsManagement/welcome/folderContents
     */
    public function readRecordsmanagementArchivesSearch(
        $archiveId = null,
        $profileReference = null,
        $status = null,
        $archiveName = null,
        $agreementReference = null,
        $archiveExpired = null,
        $finalDisposition = null,
        $originatorOrgRegNumber = null,
        $description = null,
        $text = null
    );

    /**
     * get form to update index
     *
     * @return recordsManagement/archive/fulltextModificationForm
     */
    //public function readRecordsmanagementArchiveIndex_archiveId_modification();

    /**
     * Update archive indexes
     *
     * @uses recordsManagement/archive/updateIndex
     *
     * @return recordsManagement/archive/fulltextModificationResult
     */
    //public function updateRecordsmanagementArchiveModifyindex();

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
     * @param string $description
     * @param string $text
     *
     * @return recordsManagement/archive/search
     * @uses recordsManagement/archives/read
     */
    public function readRecordsmanagementArchives(
        $archiveId = null,
        $profileReference = null,
        $status = null,
        $archiveName = null,
        $agreementReference = null,
        $archiveExpired = null,
        $finalDisposition = null,
        $originatorOrgRegNumber = null,
        $description = null,
        $text = null
    );

    /**
     * View the archive
     *
     * @return recordsManagement/archive/getDescription The recordsManagement/archive object
     * @uses  recordsManagement/archiveDescription/read_archiveId_
     */
    public function readRecordsmanagementArchivedescription_archiveId_();

    /**
     * Retrieve an archive content document (CDO)
     *
     * @return recordsManagement/archive/getContents
     *
     * @uses  recordsManagement/archive/readConsultation_archiveId_DigitalResource_resId_
     */
    public function readRecordsmanagementContents_archiveId__resId_();

    /**
     * Check if archive exists
     * @param string $archiveId The archive identifier
     *
     * @return recordsManagement/archive/exists
     *
     * @uses recordsManagement/archive/read_archiveId_Exists
     */
    public function readRecordsmanagementArchive_archiveId_Exists($archiveId);
}