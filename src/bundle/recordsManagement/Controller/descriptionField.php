<?php

/*
 * Copyright (C) 2015 Maarch
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
 * Managemet of the data dictionnary description field
 *
 * @author Cyril Vazquez <cyril.vazquez@maarch.org>
 */
class descriptionField
{

    protected $sdoFactory;

    /**
     * Constructor
     * @param \dependency\sdo\Factory $sdoFactory The sdo factory
     */
    public function __construct(\dependency\sdo\Factory $sdoFactory)
    {
        $this->sdoFactory = $sdoFactory;
    }

    /**
     *  List
     *
     * @return recordManagement/descriptionField[] The list of retention rules
     */
    public function index()
    {
        $descriptionFields = $this->sdoFactory->find('recordsManagement/descriptionField');

        return $descriptionFields;
    }

    /**
     *  Create
     * @param recordsManagement/descriptionField $descriptionField The description field
     *
     * @return boolean The request result
     */
    public function create($descriptionField)
    {
        if (!empty($descriptionField->enumeration)) {
            $descriptionField->enumeration = json_encode($descriptionField->enumeration);
        }

        try {
            return $this->sdoFactory->create($descriptionField, 'recordsManagement/descriptionField');

        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     *  Read a field
     * @param string $name The field name
     *
     * @return recordManagement/descriptionField The description field
     */
    public function read($name)
    {
        try { 
            $descriptionField = $this->sdoFactory->read('recordsManagement/descriptionField', $name);

            if (!empty($descriptionField->enumeration)) {
                $descriptionField->enumeration = json_decode($descriptionField->enumeration);
            }

            return $descriptionField;
        } catch (\Exception $e) {
            
        }
    }

    /**
     *  Update a description field
     * @param recordsManagement/descriptionField $descriptionField The description field
     *
     * @return boolean The request result
     */
    public function update($descriptionField)
    {
        if (!empty($descriptionField->enumeration)) {
            $descriptionField->enumeration = json_encode($descriptionField->enumeration);
        }

        try {
            $res = $this->sdoFactory->update($descriptionField, 'recordsManagement/descriptionField');
        } catch (\core\Exception $e) {
            throw $e;
        }

        return $res;
    }

    /**
     *  Delete a description field
     * @param string $name The description field name
     *
     * @return boolean The request result
     */
    public function delete($name)
    {
        $descriptionField = $this->sdoFactory->read('recordsManagement/descriptionField', $name);

        if (!$descriptionField) {
            return false;
        }
        try {
            $this->sdoFactory->delete($descriptionField);
        } catch (\core\Exception $e) {
            throw new \bundle\recordsManagement\Exception\descriptionFieldException("Retention rule not deleted.");
        }

        return true;
    }
}
