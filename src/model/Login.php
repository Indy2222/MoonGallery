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

class Login {

    protected $user = null;

    public function refresh() {
        if (isset($_SESSION["user"])) {
            $userLoader = new UserLoader($_SESSION["user"]);
            $userLoader->load();
            $this->user = $userLoader->get();
        } else {
            $this->user = null;
        }
    }

    public function getUser() {
        return $this->user;
    }

    public function isLoggedIn() {
        return $this->user != null;
    }
}
