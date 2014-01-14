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
require_once 'services/UploadService.php';

$database = new Database();
try {
    $database->connect();
} catch (Exception $e) {
    echo json_encode(null);
    return;
}

$params = processParams();

$login = new Login();
$login->refresh($params["tooken"]);

$responce = createResponce($params);

$database->disconnect();

echo $responce;

function createResponce($params) {
    $responce = array();

    $serviceResponce = processService($params);
    $responce["service"] = $serviceResponce;

    $login = $GLOBALS["login"];
    if ($login->isLoggedIn()) {
        $loginResponce = array();

        $loginResponce["tooken"] = $login->getTooken();

        $loginResponce["alias"] = $login->getUser()->getPerson()->getAlias();
        $loginResponce["email"] = $login->getUser()->getEmail();

        //TODO: move it elsewhere
        $loginResponce["rights"] = array();
        $loginResponce["rights"]["upload"] = UploadService::canUpload();

        $responce["loggedIn"] = $loginResponce;
    }

    return json_encode($responce);
}

function processService($params) {
    $responce = false;
    $service = ServiceLoader::getService();

    if ($service != null) {
        $responce = $service->process($params);
    }

    return $responce;
}

function processParams() {
    $params = array();

    //$requestPayload = json_decode(file_get_contents("php://input"));

    //foreach ($requestPayload as $key => $value) {
    //    $params[$key] = $value;
    //}
    foreach ($_POST as $key => $value) {
        $params[$key] = $value;
    }
    foreach ($_GET as $key => $value) {
        $params[$key] = $value;
    }

    return $params;
}
