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

class Gallery {

    function __construct($id, $name, $preview) {
        $this->id = $id;
        $this->name = $name;
        $this->preview = $preview;
    }
}

$query = mysql_query("SELECT gallery.id, gallery.name, entity.data AS preview FROM  `gallery` "
        . "LEFT JOIN  `photo` ON gallery.id = photo.gallery_id "
        . "LEFT JOIN  `entity` ON photo.id = entity.photo_id "
        . "WHERE entity.type = 'preview' "
        . "GROUP BY gallery.id "
        . "LIMIT 0 , 30");
$galleries = array();

while ($row = mysql_fetch_array($query)) {
    $gallery = new Gallery($row["id"], $row["name"], $row["preview"]);
    array_push($galleries, $gallery);
}

echo json_encode($galleries);
