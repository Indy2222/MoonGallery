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

include "model/SimpleImage.php";

$galleryName = $_GET["create"];

mysql_query("INSERT INTO `gallery` (`name`) VALUES ('" . mysql_escape_string($galleryName) . "');");
$galleryId = mysql_insert_id();

foreach ($_SESSION["fileName"] as $fileId => $fileName) {
    savePhoto($galleryId, $fileId, $fileName);
}

unset($_SESSION["fileName"]);

function savePhoto($galleryId, $name, $file) {
    mysql_query("INSERT INTO `photo` (`gallery_id`, `name`)"
            . " VALUES (" . $galleryId . ", "
            . "'" . mysql_escape_string($name) . "');");
    $photoId = mysql_insert_id();

    saveEntities($photoId, $file);
}

function saveEntities($photoId, $file) {
    saveEntity($photoId, $GLOBALS["files_dir"] . $file); // save full sized image
    saveEntityScaled($photoId, $file, "normal");
    saveEntityScaled($photoId, $file, "preview");
}

function saveEntityScaled($photoId, $original, $type) {
    if ($type == "normal") {
        $width = $GLOBALS["normal_width"];
    } else if ($type == "preview") {
        $width = $GLOBALS["preview_width"];
    } else {
        $width = $GLOBALS["preview_width"];
        $type = "preview";
    }

    $scaledName = sha1($fileId + session_id() + microtime() + $type);

    $directory = getcwd() . "/";
    $image = new SimpleImage();
    $image->load($directory . $GLOBALS["files_dir"] . $original);
    $image->resizeToWidth($width);
    $image->save($directory . $GLOBALS["files_dir"] . $scaledName);

    saveEntity($photoId, $GLOBALS["files_dir"] . $scaledName, $type);
}

function saveEntity($photoId, $file, $type = "full") {
    mysql_query("INSERT INTO `entity` (`photo_id`, `data`, `type`)"
            . " VALUES (" . $photoId . ", "
            . "'" . mysql_escape_string($file) . "', '" . $type . "');");
}
