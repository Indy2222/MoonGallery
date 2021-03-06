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

require_once 'services/iService.php';
require_once 'model/UserCreator.php';
require_once 'model/User.php';
require_once 'model/Person.php';
require_once 'model/Group.php';

class RegisterService implements iService {

    public function process($params) {
        if (!isset($params["email"]) || !isset($params["password"]) || !isset($params["full_name"])) {
            return false;
        }

        $email = $params["email"];
        $password = $params["password"];
        $fullName = $params["full_name"];
        $alias = isset($params["alias"]) ? $params["alias"] : null;

        $passwordObject = new Password();
        $passwordObject->initFromPassword($password);
        $newUser = new User(0, $email, $passwordObject);
        $person = new Person(0, $fullName, $alias);
        $newUser->setPerson($person);

        $group = new Group(Group::$GROUP_ID_ALL, "");
        $newUser->addGroup($group, true);
        if ($this->isThisFirstRegistration()) {
            // first registered user is considered as admin
            $group = new Group(Group::$GROUP_ID_ADMIN, "");
            $newUser->addGroup($group, true);
        }

        $userCreator = new UserCreator($newUser, $password);
        $check = $userCreator->check();

        if ($check == UserCreator::$CHECK_OK) {
            $userCreator->save();
            return true;
        } else {
            $responce = array();
            $responce["error_code"] = $check;

            switch ($check) {
                case UserCreator::$ALIAS_IS_WRONG:
                    $responce["error"] = "wrong-alias";
                    break;
                case UserCreator::$EMAIL_EXISTS:
                    $responce["error"] = "email-exists";
                    break;
                case UserCreator::$EMAIL_IS_WRONG:
                    $responce["error"] = "wrong-email";
                    break;
                case UserCreator::$FULL_NAME_IS_WRONG:
                    $responce["error"] = "wrong-full-name";
                    break;
                case UserCreator::$PASSWORD_TOO_SOFT:
                    $responce["error"] = "password-too-soft";
                    break;
                case UserCreator::$PASSWORD_IS_WRONG:
                    $responce["error"] = "wrong-password";
                    break;
                default:
                    $responce["error"] = "unspecified-error";
            }

            return $responce;
        }
    }

    protected function isThisFirstRegistration() {
        $query = mysql_query("SELECT count(*) AS count FROM `user`;");
        $row = mysql_fetch_array($query);
        return $row["count"] == 0;
    }

}
