<?php

/*
 * Copyright (C) 2014 Martin Indra <martin.indra at mgn.cz>
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

/**
 * Object storing info about any person.
 */
class Person {

    protected $id;
    protected $fullName;
    protected $alias;

    public function __construct($id, $fullName, $alias) {
        if (!isset($alias) || strlen($alias) == 0) {
            $alias = $fullName;
        }

        $this->id = $id;
        $this->alias = $alias;
        $this->fullName = $fullName;
    }

    public function setID($id) {
        $this->id = $id;
    }

    public function getID() {
        return $this->id;
    }

    public function getAlias() {
        return $this->alias;
    }

    public function getFullName() {
        return $this->fullName;
    }
}
