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

class LoginService implements iService {

    public function process($params) {
        global $login;

        $email = $params["email"];
        $password = $params["password"];

        if ($login->login($email, $password)) {
            $person = $login->getUser()->getPerson();

            $responce = array();
            $responce["email"] = $login->getUser()->getEmail();
            $responce["alias"] = $person->getAlias();

            return $responce;
        } else if ($login->isLoggedIn()) {
            $responce = array();
            $responce["error"] = "already-logged-in";
            return $responce;
        } else {
            return false;
        }
    }

}
