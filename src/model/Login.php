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

require_once 'model/UserLoader.php';
require_once 'model/Password.php';
require_once 'model/StringUtils.php';

/**
 * Maintain info about logging in.
 */
class Login {

    protected $user = null;
    protected $tooken;

    public function __construct() {

    }

    /**
     * Refreshes info about loggin in.
     */
    public function refresh($tooken) {
        global $CSRF_tooken_protection;

        if (isset($_SESSION["user"])) {
            if ($this->testTooken($tooken) || !$CSRF_tooken_protection) {
                $userLoader = new UserLoader($_SESSION["user"]);
                $userLoader->load();
                $this->user = $userLoader->get();
            } else {
                $this->logout();
            }
        } else {
            $this->user = null;
        }
    }

    /**
     * If user is logged in returns him otherwise null.
     *
     * @return User
     */
    public function getUser() {
        return $this->user;
    }

    /**
     * Returns wheter user is logged in.
     *
     * @return boolean
     */
    public function isLoggedIn() {
        return $this->user != null;
    }

    /**
     * Returns tooken which will be tested in another request to keep login.
     *
     * @return String
     */
    public function getTooken() {
        return $this->tooken;
    }

    /**
     * Try to log in the user and returns if succeed. It check user e-mail and
     * password.
     *
     * @param String $email
     * @param String $password
     * @return boolean
     */
    public function login($email, $password) {
        if ($this->isLoggedIn()) {
            return false;
        }

        $userId = $this->getUserIdByEmail($email);

        if ($userId != -1) {
            $loader = new UserLoader($userId);
            $loader->load();
            $user = $loader->get();

            if ($user != null && $user->getPassword()->test($password)) {
                $this->user = $user;
                $_SESSION["user"] = $user->getID();
                $this->generateTooken();
            }
        }

        return $this->isLoggedIn();
    }

    /**
     * Log out the user and returns if succeed. Fails if noone is logged in.
     *
     * @return boolean
     */
    public function logout() {
        if ($this->isLoggedIn()) {
            $this->user = null;
            unset($_SESSION["user"]);
            $this->destroyTooken();
            return true;
        } else {
            return false;
        }
    }

    /**
     * Returns ID of user with specified e-mail.
     *
     * @param String $email
     * @return integer ID of the user or -1 if there is not such user
     */
    protected function getUserIdByEmail($email) {
        $id = -1;

        $query = mysql_query("SELECT id FROM `user` "
                . "WHERE email = '" . mysql_real_escape_string($email) . "' "
                . "LIMIT 1;");

        if (($row = mysql_fetch_array($query)) != null) {
            $id = $row["id"];
        }

        return $id;
    }

    protected function destroyTooken() {
        unset($_SESSION["tooken"]);
        $this->tooken = null;
    }

    protected function generateTooken() {
        $this->tooken = StringUtils::generateRandomString(10);
        $_SESSION["tooken"] = $this->tooken;
    }

    protected function loadTooken() {
        $this->tooken = $_SESSION["tooken"];
    }

    protected function testTooken($tooken) {
        $this->loadTooken();
        return $tooken != null && $this->tooken != null && $tooken === $this->tooken;
    }

}
