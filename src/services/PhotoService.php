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

class PhotoService implements iService {

    public function process($params) {

        $photoId = $params["id"];
        $photo = null;

        $query = mysql_query("SELECT photo.gallery_id, photo.name, entity.data AS image FROM  `photo` "
                . "LEFT JOIN  `entity` ON photo.id = entity.photo_id "
                . "WHERE photo.id = " . mysql_real_escape_string($photoId) . " AND entity.type = 'normal' "
                . "GROUP BY photo.id "
                . "LIMIT 1");

        if (($row = mysql_fetch_array($query)) != null) {
            $galleryId = $row["gallery_id"];

            $photo = array();
            $photo["galleryId"] = $galleryId;
            $photo["name"] = $row["name"];
            $photo["image"] = $row["image"];

            $query = mysql_query("SELECT data AS image FROM `entity` "
                    . "WHERE photo_id = " . mysql_real_escape_string($photoId) . " AND entity.type = 'full' "
                    . "LIMIT 1");
            if (($row = mysql_fetch_array($query)) != null) {
                $photo["fullImage"] = $row["image"];
            }


            $query = mysql_query("SELECT id FROM `photo` "
                    . "WHERE id > " . mysql_real_escape_string($photoId) . " "
                    . "AND gallery_id = " . $galleryId . " "
                    . "ORDER BY id LIMIT 1;");
            if (($row = mysql_fetch_array($query)) != null) {
                $photo["next"] = $row["id"];
            }
            $query = mysql_query("SELECT id FROM `photo` "
                    . "WHERE id < " . mysql_real_escape_string($photoId) . " "
                    . "AND gallery_id = " . $galleryId . " "
                    . "ORDER BY id DESC LIMIT 1;");
            if (($row = mysql_fetch_array($query)) != null) {
                $photo["previous"] = $row["id"];
            }
        }

        return $photo;
    }

}
