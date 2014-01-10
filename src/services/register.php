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

require_once 'model/UserCreator.php';
require_once 'model/User.php';
require_once 'model/Person.php';
require_once 'model/Group.php';

$DEFAULT_GROUP_ID = 1; //FIXME

$email = $_GET["email"];
$password = $_GET["password"];
$fullName = $_GET["full_name"];
$alias = $_GET["alias"];

$newUser = new User(0, $email, $password);
$person = new Person(0, $fullName, $alias);
$newUser->setPerson($person);
$group = new Group($DEFAULT_GROUP_ID, "");
$newUser->addGroup($group, true);

$userCreator = new UserCreator($user);
//TODO: check if data are correct
$userCreator->save();
