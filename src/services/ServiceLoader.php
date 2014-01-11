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

require_once 'services/UploadService.php';
require_once 'services/GalleriesService.php';
require_once 'services/GalleryService.php';
require_once 'services/PhotoService.php';
require_once 'services/LogoutService.php';
require_once 'services/LoginService.php';
require_once 'services/RegisterService.php';

class ServiceLoader {

    public static function getService() {
        $serviceName = $_GET["service"];
        $service = null;

        switch ($serviceName) {
            case "logout":
                $service = new LogoutService();
                break;
            case "upload":
                $service = new UploadService();
                break;
            case "galleries":
                $service = new GalleriesService();
                break;
            case "gallery":
                $service = new GalleryService();
                break;
            case "photo":
                $service = new PhotoService();
                break;
            case "login":
                $service = new LoginService();
                break;
            case "register":
                $service = new RegisterService();
                break;
        }

        return $service;
    }
}
