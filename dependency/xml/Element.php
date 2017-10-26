<?php
/*
 * Copyright (C) 2015 Maarch
 *
 * This file is part of dependency xml.
 *
 * Dependency xml is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Dependency xml is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with dependency xml.  If not, see <http://www.gnu.org/licenses/>.
 */
namespace dependency\xml;
class Element
    extends \DOMElement
{
    /* -------------------------------------------------------------------------
    - Properties
    ------------------------------------------------------------------------- */
    /* -------------------------------------------------------------------------
    - Methods
    ------------------------------------------------------------------------- */
    public function __call($method, $args) {
        // If method exists magic method is not called by DOM
        if (($extension = $this->ownerDocument->getNodeExtension($this)) && is_callable("$extension::$method"))
            return call_user_func_array("$extension::$method", array_merge(array($this), $args));

        return parent::$method($this);
    }
}