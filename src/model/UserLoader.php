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

require_once 'model/User.php';
require_once 'model/Password.php';
require_once 'model/Person.php';
require_once 'model/Group.php';

/**
 * Tool which construct user by his id
 */
class UserLoader {

    protected $id;
    protected $user;

    /**
     * Initiate loader
     *
     * @param int $id user ID
     */
    public function __construct($id) {
        $this->id = $id;
    }

    /**
     * Loads and construct user from database
     */
    public function load() {
        $this->loadUser();
        $this->loadGroups();
    }

    /**
     * Return loaded user or null.
     *
     * @return User
     */
    public function get() {
        return $this->user;
    }

    /**
     * Load info about user
     */
    protected function loadUser() {
        $query = mysql_query("SELECT "
                . "user.password, user.password_salt, user.id AS user_id, "
                . "user.email AS user_email, person.id AS person_id, "
                . "person.full_name, person.alias "
                . "FROM `user` "
                . "LEFT JOIN `person` ON person.id = user.person_id "
                . "WHERE user.id = " . $this->id . " "
                . "GROUP BY user.id "
                . "LIMIT 1;");

        if (($row = mysql_fetch_array($query)) != null) {
            $password = new Password();
            $password->initFromHash($row["password"], $row["password_salt"]);

            $this->user = new User($row["user_id"], $row["user_email"], $password);
            $person = new Person($row["person_id"], $row["full_name"], $row["alias"]);
            $this->user->setPerson($person);
        }
    }

    /**
     * Load info about users groups
     */
    protected function loadGroups() {
        if ($this->user != null) {
            $query = mysql_query("SELECT "
                    . "user_in_group.is_default, group.name, group.id "
                    . "FROM `user_in_group`, `group` "
                    . "WHERE user_in_group.user_id = " . mysql_real_escape_string($this->id) . " "
                    . "AND user_in_group.group_id = group.id;");

            while (($row = mysql_fetch_array($query)) != null) {
                $default = $row["is_default"];
                $group = new Group($row["id"], $row["name"]);
                $this->user->addGroup($group, $default);
            }
        }
    }
}
