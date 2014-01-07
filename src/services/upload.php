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

$FILES_DIR = "files/";
$FILES_DIR_FULL = getcwd() . "/" . $FILES_DIR;

$galleryName = $_GET["create"];
$fileId = $_GET["id"];
$fileStart = $_GET["start"];
$fileSize = $_GET["size"];

if (isset($galleryName)) {
    $query = "INSERT INTO `gallery` (`name`) VALUES ('" . mysql_escape_string($galleryName) . "');";
    mysql_query($query);
    $galleryId = mysql_insert_id();

    foreach ($_SESSION["fileName"] as $fileId => $fileName) {
        $query = "INSERT INTO `moongallery`.`photo` (`gallery_id`, `name`)"
                . " VALUES (" . $galleryId . ", "
                . "'" . mysql_escape_string($fileId) . "');";
        mysql_query($query);
        $photoId = mysql_insert_id();

        $fileName = $FILES_DIR . $fileName;
        $query = "INSERT INTO `moongallery`.`entity` (`photo_id`, `data`, `type`)"
                . " VALUES (" . $photoId . ", "
                . "'" . mysql_escape_string($fileName) . "', 'full');";
        mysql_query($query);
    }

    unset($_SESSION["fileName"]);
} else {
    if (!isset($_SESSION['fileName'])) {
        $_SESSION['fileName'] = array();
    }

    if (isset($_SESSION['fileName'][$fileId])) {
        $fileName = $_SESSION['fileName'][$fileId];
    } else {
        $fileName = $_SESSION['fileName'][$fileId] = sha1($fileId + session_id() + time());
    }
    $fileName = $FILES_DIR_FULL . $fileName;

    $file = $_FILES["file"];
    $lastChunk = ($file["size"] + $fileStart) >= $fileSize;

    $tmp = $FILES_DIR_FULL . time();
    move_uploaded_file($file["tmp_name"], $tmp);
    exec("cat " . $tmp . " >> " . $fileName); // TODO: this is not nice!
    unlink($tmp);
}

echo json_encode(true);
