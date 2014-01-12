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

class Gallery {

    function __construct($id, $name, $preview) {
        $this->id = $id;
        $this->name = $name;
        $this->preview = $preview;
    }

}

class Galleries {

    public function __construct($count, $perPage) {
        $this->perPage = $perPage;
        $this->count = $count;
        $this->galleries = array();
    }

    public function addGallery($gallery) {
        array_push($this->galleries, $gallery);
    }

}

class GalleriesService implements iService {

    public function process($params) {
        $query = mysql_query("SELECT count(*) AS count FROM `gallery`;");
        $row = mysql_fetch_array($query);
        $count = $row["count"];
        $perPage = 10;

        $start = isset($params["start"]) ? $params["start"] : 0;
        $query = mysql_query("SELECT gallery.id, gallery.name, entity.data AS preview FROM  `gallery` "
                . "LEFT JOIN  `photo` ON gallery.id = photo.gallery_id "
                . "LEFT JOIN  `entity` ON photo.id = entity.photo_id "
                . "WHERE entity.type = 'preview' "
                . "GROUP BY gallery.id "
                . "LIMIT " . mysql_real_escape_string($start) . " , " . $perPage);

        $galleries = new Galleries($count, $perPage);
        while ($row = mysql_fetch_array($query)) {
            $gallery = new Gallery($row["id"], $row["name"], $row["preview"]);
            $galleries->addGallery($gallery);
        }

        return $galleries;
    }
}
