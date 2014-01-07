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

$fileId = $_GET["id"];
$fileStart = $_GET["start"];
$fileSize = $_GET["size"];

if (!isset($_SESSION['fileName'])) {
    $_SESSION['fileName'] = array();
}

if (isset($_SESSION['fileName'][$fileId])) {
    $fileName = $_SESSION['fileName'][$fileId];
} else {
    $fileName = generateFileName($fileId);
    $_SESSION['fileName'][$fileId] = $fileName;
}
$fullPathFilesDir = getcwd() . "/" . $files_dir;
$fileName = $fullPathFilesDir . $fileName;

$file = $_FILES["file"];
//$lastChunk = ($file["size"] + $fileStart) >= $fileSize;

$tmp = $fullPathFilesDir . round(microtime(true) * 1000);

var_dump($tmp);
var_dump($fileName);

move_uploaded_file($file["tmp_name"], $tmp);
exec("cat " . $tmp . " >> " . $fileName); // TODO: this is not nice!
unlink($tmp);

function generateFileName($fileId) {
    return sha1($fileId + session_id() + microtime());
}
