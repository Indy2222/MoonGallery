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

require_once 'model/Password.php';
require_once 'model/Person.php';
require_once 'model/Group.php';

class User {

    protected $id;
    protected $email;
    protected $password;

    protected $person;
    protected $defaultGroup;
    protected $groups = [];


    public function __construct($id, $email, $password) {
        $this->id = $id;
        $this->email = $email;
        $this->password = $password;
    }

    public function setPerson($person) {
        $this->person = $person;
    }

    public function addGroup($group, $default = false) {
        array_push($this->groups, $group);
        if ($default) {
            $this->defaultGroup = $group;
        }
    }
}
