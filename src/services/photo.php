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

class Photo {

    public function __construct($name, $image) {
        $this->name = $name;
        $this->image = $image;
    }

}

$photoId = $_GET["id"];
$photo = null;

$query = mysql_query("SELECT photo.name, entity.data AS image FROM  `photo` "
        . "LEFT JOIN  `entity` ON photo.id = entity.photo_id "
        . "WHERE photo.id = '" . mysql_real_escape_string($photoId) . "' AND entity.type = 'normal' "
        . "GROUP BY photo.id "
        . "LIMIT 1");

$row = mysql_fetch_array($query);
if ($row != null) {
    $photo = new Photo($row["name"], $row["image"]);
}

echo json_encode($photo);
