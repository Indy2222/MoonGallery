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

session_start();

require 'config.php';
require_once 'model/Database.php';
require_once 'model/Login.php';
require_once 'services/ServiceLoader.php';

$database = new Database();
$database->connect();

$login = new Login();
$login->refresh();

$responce = createResponce();

$database->disconnect();

echo $responce;

function createResponce() {
    $responce = array();

    $serviceResponce = processService();
    $responce["service"] = $serviceResponce;

    $login = $GLOBALS["login"];
    if ($login->isLoggedIn()) {
        $responce["logged_in"]["alias"] = $login->getUser()->getPerson()->getAlias();
        $responce["logged_in"]["email"] = $login->getUser()->getEmail();
    }

    return json_encode($responce);
}

function processService() {
    $responce = false;
    $service = ServiceLoader::getService();
    $params = array();

    foreach ($_POST as $key => $value) {
        $params[$key] = $value;
    }
    foreach ($_GET as $key => $value) {
        $params[$key] = $value;
    }

    if ($service != null) {
        $responce = $service->process($params);
    }

    return $responce;
}
