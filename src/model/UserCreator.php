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

class UserCreator {

    public static $CHECK_OK = 0;
    public static $PASSWORD_TOO_SOFT = 1;
    public static $PASSWORD_IS_WRONG = 2;
    public static $EMAIL_EXISTS = 3;
    public static $EMAIL_IS_WRONG = 4;
    public static $FULL_NAME_IS_WRONG = 5;
    public static $ALIAS_IS_WRONG = 6;
    public static $REGEX_PASSWORD = "/[\w-\.]+/";
    public static $REGEX_EMAIL = "/^[\w-\.]+@([\w-]+\.)+[\w-]{2,4}$/";
    public static $REGEX_FULL_NAME = "/^([a-zA-Z]+(\.)?\s)*[a-zA-Z]+$/"; // TODO: add support for different alphabets
    public static $REGEX_ALIAS = "/^\S+(([\S\s]*\S+)|$)$/"; // has to include full name!
    protected $user;
    protected $password;

    public function __construct($user, $password) {
        $this->user = $user;
        $this->password = $password;
    }

    public function check() {
        if (preg_match(UserCreator::$REGEX_PASSWORD, $this->password) == 0) {
            return UserCreator::$PASSWORD_IS_WRONG;
        } else if (preg_match(UserCreator::$REGEX_EMAIL, $this->user->getEmail()) == 0) {
            return UserCreator::$EMAIL_IS_WRONG;
        } else if (preg_match(UserCreator::$REGEX_FULL_NAME, $this->user->getPerson()->getFullName()) == 0) {
            return UserCreator::$FULL_NAME_IS_WRONG;
        } else if (preg_match(UserCreator::$REGEX_ALIAS, $this->user->getPerson()->getAlias()) == 0) {
            return UserCreator::$ALIAS_IS_WRONG;
        } else if (strlen($this->password) < 8) {
            return UserCreator::$PASSWORD_TOO_SOFT;
        }

        $query = mysql_query("SELECT count(*) AS count FROM `user` "
                . "WHERE email = '" . mysql_real_escape_string($this->user->getEmail()) . "';");
        $row = mysql_fetch_array($query);

        if ($row["count"] > 0) {
            return UserCreator::$EMAIL_EXISTS;
        }

        return UserCreator::$CHECK_OK;
    }

    public function save() {
        $this->savePerson();
        $this->saveUser();
        $this->saveGroups();
    }

    protected function saveGroups() {
        $userId = $this->user->getID();
        $groups = $this->user->getGroups();

        foreach ($groups as $group) {
            $default = $group == $this->user->getDefaultGroup() ? 1 :  0;
            mysql_query("INSERT INTO `user_in_group` (user_id, group_id, is_default) "
                    . "VALUES (" . $userId . ", " . $group->getID() . ", " . $default . ");");

            /*$query = mysql_query("SELECT name FROM `group` WHERE id = " . $group->getID() . " LIMIT 1;");
            if (($row = mysql_fetch_array($query)) != null) {
                $group->setName($row["name"]); //FIXME: move this code to user loader!
            }*/
        }
    }

    protected function saveUser() {
        $personId = $this->user->getPerson()->getID();

        mysql_query("INSERT INTO `user` (person_id, email, password, password_salt) "
                . "VALUES ("
                . "'" . $personId . "', "
                . "'" . mysql_real_escape_string($this->user->getEmail()) . "', "
                . "'" . mysql_real_escape_string($this->user->getPassword()->getHash()) . "', "
                . "'" . mysql_real_escape_string($this->user->getPassword()->getSalt()) . "'"
                . ");");
        $id = mysql_insert_id();
        $this->user->setID($id);

        return $id;
    }

    protected function savePerson() {
        $person = $this->user->getPerson();

        mysql_query("INSERT INTO `person` (full_name, alias) "
                . "VALUES ("
                . "'" . mysql_real_escape_string($person->getFullName()) . "', "
                . "'" . mysql_real_escape_string($person->getAlias()) . "'"
                . ");");
        $id = mysql_insert_id();
        $person->setID($id);

        return $id;
    }
}
