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

class Photo {

    public function __construct($id, $name, $preview) {
        $this->id = $id;
        $this->name = $name;
        $this->preview = $preview;
    }

}

class GalleryService implements iService {

    public function process($params) {
        if (!isset($params["gallery"])) {
            return false;
        }

        $galleryId = $params["gallery"];

        $query = mysql_query("SELECT count(*) AS count FROM `photo` "
                . "WHERE photo.gallery_id = " . mysql_real_escape_string($galleryId) . ";");
        $row = mysql_fetch_array($query);
        $count = $row["count"];
        $perPage = 30;

        $start = isset($params["start"]) ? $params["start"] : 0;
        $query = mysql_query("SELECT photo.id, photo.name, entity.data AS preview FROM  `photo` "
                . "LEFT JOIN  `entity` ON photo.id = entity.photo_id "
                . "WHERE photo.gallery_id = " . mysql_real_escape_string($galleryId) . " AND entity.type = 'preview' "
                . "GROUP BY photo.id "
                . "LIMIT " . mysql_real_escape_string($start) . " , " . $perPage);
        $photos = array();

        while ($row = mysql_fetch_array($query)) {
            $photo = new Photo($row["id"], $row["name"], $row["preview"]);
            array_push($photos, $photo);
        }

        $responce = array();
        $responce["photos"] = $photos;
        $responce["count"] = $count;
        $responce["perPage"] = $perPage;

        return $responce;
    }

}
