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

class CreateGalleryService implements iService {

    public function process($params) {
        $galleryName = $params["create"];

        mysql_query("INSERT INTO `gallery` (`name`) VALUES ('" . mysql_real_escape_string($galleryName) . "');");
        $galleryId = mysql_insert_id();

        foreach ($_SESSION["galleryFiles"] as $fileId => $files) {
            $this->savePhoto($galleryId, $fileId, $files);
        }

        unset($_SESSION["galleryFiles"]);

        $responce = array();
        $responce["gallery_id"] = $galleryId;

        return $responce;
    }

    function savePhoto($galleryId, $name, $files) {
        mysql_query("INSERT INTO `photo` (`gallery_id`, `name`)"
                . " VALUES (" . $galleryId . ", "
                . "'" . mysql_escape_string($name) . "');");
        $photoId = mysql_insert_id();

        $this->saveEntities($photoId, $files);
    }

    function saveEntities($photoId, $files) {
        $this->saveEntity($photoId, $files['full'], 'full');
        $this->saveEntity($photoId, $files['normal'], 'normal');
        $this->saveEntity($photoId, $files['preview'], 'preview');
    }

    function saveEntity($photoId, $file, $type = "full") {
        mysql_query("INSERT INTO `entity` (`photo_id`, `data`, `type`)"
                . " VALUES (" . $photoId . ", "
                . "'" . mysql_escape_string($file) . "', '" . $type . "');");
    }

}
