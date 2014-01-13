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

require_once 'model/StringUtils.php';

/**
 * Class maintaining password. The class uses sha-256 as a hash function.
 */
class Password {

    protected $hash;
    protected $salt;

    public function __construct() {

    }

    /**
     * Initiate password from its original non-hashed version. It generates hash
     * and hash salt.
     *
     * @param String $password
     */
    public function initFromPassword($password) {
        $this->salt = $this->generateSalt();
        $this->hash = hash("sha256", $password . $this->salt);
    }

    /**
     * Init password from known hash and its salt.
     *
     * @param String $hash
     * @param String $salt
     */
    public function initFromHash($hash, $salt) {
        $this->hash = $hash;
        $this->salt = $salt;
    }

    /**
     * Tests if password is the same as the one this object store.
     *
     * @param String $password non-hashed password to test
     * @return boolean true if passwords are equal
     */
    public function test($password) {
        $hash = hash("sha256", $password . $this->salt);
        return $hash == $this->hash;
    }

    /**
     * Returns password hash.
     *
     * @return String
     */
    public function getHash() {
        return $this->hash;
    }

    /**
     * Returns password hash's salt.
     *
     * @return String
     */
    public function getSalt() {
        return $this->salt;
    }

    /**
     * Generates random string which can be used as a salt.
     * 
     * @return String
     */
    protected function generateSalt() {
        return StringUtils::generateRandomString(15);
    }
}
