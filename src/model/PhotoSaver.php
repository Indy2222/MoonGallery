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

require_once 'model/SimpleImage.php';
require_once 'model/StringUtils.php';

class PhotoSaver {

    protected $tmpFile;
    protected $fullImage;
    protected $normalImage;
    protected $previewImage;


    public function __construct($tmpFile) {
        $this->tmpFile = $tmpFile;
    }

    public function save() {
        $this->generateNames();

        // move full image to rignt location
        rename($this->tmpFile, $this->nameToFullPath($this->fullImage));

        $this->createScaledVersion($GLOBALS["normal_width"], $this->normalImage);
        $this->createScaledVersion($GLOBALS["preview_width"], $this->previewImage);
    }

    public function getImages() {
        $images = array();

        $images["full"] = $this->nameToPath($this->fullImage);
        $images["normal"] = $this->nameToPath($this->normalImage);
        $images["preview"] = $this->nameToPath($this->previewImage);

        return $images;
    }

    protected function createScaledVersion($width, $name) {
        $image = new SimpleImage();
        $image->load($this->nameToFullPath($this->fullImage));
        $image->resizeToWidth($width);
        $image->save($this->nameToFullPath($name));
    }

    protected function nameToFullPath($name) {
        return getcwd() . "/" . $this->nameToPath($name);
    }

    protected function nameToPath($name) {
        return $GLOBALS["files_dir"] . $name;
    }

    protected function generateNames() {
        $this->fullImage = $this->generateName("full");
        $this->normalImage = $this->generateName("normal");
        $this->previewImage = $this->generateName("preview");
    }

    protected function generateName($type) {
        return hash("sha256", $type . StringUtils::generateRandomString(40));
    }
}
