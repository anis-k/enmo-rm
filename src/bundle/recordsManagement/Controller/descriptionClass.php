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
 * Control of the recordsManagement descriptionClass
 *
 * @package recordsManagement
 * @author  Alexandre Morin <alexandre.morin@maarch.org> 
 */
class descriptionClass
{
    protected $descriptionSchemes;

    /**
     * Constructor
     * @param array $descriptionSchemes
     *
     * @return void
     */
    public function __construct($descriptionSchemes)
    {
        $this->descriptionSchemes = get_object_vars(json_decode(json_encode($descriptionSchemes)));
    }

    /**
     * Get the description classes
     *
     * @return array The list of organization's roles
     */
    public function index()
    {
        return $this->descriptionSchemes;
    }

    /**
     * Get the description class
     * @param string $name
     *
     * @return object
     */
    public function read($name)
    {
        if (isset($this->descriptionSchemes[$name])) {
            return $this->descriptionSchemes[$name];
        }
    }

    /**
     * Get the description class properties
     * @param string $name
     *
     * @return array
     */
    public function getDescriptionFields($name)
    {
        if (!isset($this->descriptionSchemes[$name])) {
            return [];
        }

        $fields = [];

        $descriptionSchemeConfig = $this->descriptionSchemes[$name];
        switch ($descriptionSchemeConfig->type) {
            case 'php':
                $descriptionScheme = \laabs::getClass($descriptionSchemeConfig->uri);
                $keyfields = [];
                if ($key = $descriptionScheme->getPrimaryKey()) {
                    $keyfields = $key->getFields();
                }
                foreach ($descriptionScheme->getProperties() as $descriptionSchemeProperty) {
                    if (in_array($descriptionSchemeProperty->name, $keyfields)) {
                        continue;
                    }

                    $fields[$descriptionSchemeProperty->name] = $this->getDescriptionFieldFromPhpClass($descriptionSchemeProperty);
                }
                break;
        }

        //var_dump($fields);
        return $fields;
    }

    protected function getDescriptionFieldFromPhpClass($schemeProperty)
    {
        $descriptionField = \laabs::newInstance('recordsManagement/descriptionField');
        $descriptionField->name = $schemeProperty->name;

        if (!empty($schemeProperty->summary)) {
            $descriptionField->label = $schemeProperty->summary;
        } else {
            $descriptionField->label = $schemeProperty->name;
        }

        $type = $schemeProperty->getType();
        $descriptionField->default = $schemeProperty->getDefault();

        if (isset($schemeProperty->enumeration)) {
            $descriptionField->enumeration = $schemeProperty->enumeration;
        }

        switch (true) {
            case substr($type, -2) == []:
                $descriptionField->type = 'array';
                $descriptionField->itemType = susbtr($type, 0, -2);
                break;

            case $type == 'string':
                if (isset($schemeProperty->tags['scheme'])
                    || isset($schemeProperty->tags['uses'])
                    || isset($schemeProperty->enumeration)
                    || isset($schemeProperty->index)) {
                    $descriptionField->type = 'name';
                } else {
                    $descriptionField->type = 'text';
                }
                break;

            case $type == 'int':
            case $type == 'integer':
            case $type == 'float':
            case $type == 'real':
            case $type == 'double':
                $descriptionField->type = 'number';
                break;

            case $type == 'bool':
            case $type == 'boolean':
                $descriptionField->type = 'boolean';
                break;

            case $type == 'timestamp':
            case $type == 'datetime':
            case $type == 'date':
                $descriptionField->type = 'date';
                break;

            case strpos('/', $type) !== false:
                $descriptionField->type = 'object';
                break;

            default:
                $descriptionField->type = $type;
        }

        return $descriptionField;
    }
}
