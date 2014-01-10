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

require_once 'model/PhotoSaver.php';
require_once 'model/StringUtils.php';

$fileId = $_GET["id"];
$fileStart = $_GET["start"];
$fileSize = $_GET["size"];

if (isset($_SESSION['fileName'])) {
    $fileName = $_SESSION['fileName'];
} else {
    $fileName = randomFile();
    $_SESSION['fileName'] = $fileName;
}

$file = $_FILES["file"];
$tmp = randomFile();
move_uploaded_file($file["tmp_name"], $tmp);
exec("cat " . $tmp . " >> " . $fileName); // TODO: this is not nice!
unlink($tmp);

$lastChunk = ($file["size"] + $fileStart) >= $fileSize;
if ($lastChunk == 1) {
    unset($_SESSION["fileName"]);
    $photoSaver = new PhotoSaver($fileName);
    $photoSaver->save();

    if (!isset($_SESSION['galleryFiles'])) {
        $_SESSION['galleryFiles'] = array();
    }
    $_SESSION['galleryFiles'][$fileId] = $photoSaver->getImages();
}

//TODO: better answer
echo json_encode(true);

function randomFile() {
    return getcwd() . "/" . $GLOBALS["files_dir"] . StringUtils::generateRandomString(25);
}
