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

class Password {

    protected $hash;
    protected $salt;

    public function __construct() {

    }

    public function initFromPassword($password) {
        $this->salt = $this->generateSalt();
        $this->hash = hash("sha256", $password . $this->salt);
    }

    public function initFromHash($hash, $salt) {
        $this->hash = $hash;
        $this->salt = $salt;
    }

    public function test($password) {
        $hash = hash("sha256", $password . $this->salt);
        return $hash == $this->hash;
    }

    public function getHash() {
        return $this->hash;
    }

    public function getSalt() {
        return $this->salt;
    }

    protected function generateSalt() {
        return StringUtils::generateRandomString(15);
    }
}
