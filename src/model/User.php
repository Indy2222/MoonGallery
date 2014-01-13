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

    public function setID($id) {
        $this->id = $id;
    }

    public function setPerson($person) {
        $this->person = $person;
    }

    /**
     * Sets that this user belongs to specified group.
     *
     * @param Group $group
     * @param boolean $default true if the group is users default group
     */
    public function addGroup($group, $default = false) {
        array_push($this->groups, $group);
        if ($default) {
            $this->defaultGroup = $group;
        }
    }

    public function getGroups() {
        return $this->groups;
    }

    public function getDefaultGroup() {
        return $this->defaultGroup;
    }

    /**
     * Return group with specified ID or null if there is no group with this ID
     * to which user belongs.
     *
     * @param int $id
     * @return Group
     */
    public function getGroup($id) {
        foreach ($this->groups as $group) {
            if ($group->getID() == $id) {
                return $group;
            }
        }

        return null;
    }

    public function getID() {
        return $this->id;
    }

    /**
     *
     * @return Password
     */
    public function getPassword() {
        return $this->password;
    }

    /**
     * Returns person which represents this user.
     *
     * @return Person
     */
    public function getPerson() {
        return $this->person;
    }

    public function getEmail() {
        return $this->email;
    }

}
