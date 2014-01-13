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

/**
 * Tool which can scale an image and save it to the file system.
 */
class PhotoSaver {

    protected $tmpFile;
    protected $fullImage;
    protected $normalImage;
    protected $previewImage;

    /**
     * Initiate the tool from original file path.
     *
     * @param String $tmpFile location of the original file in the file system. It should be absolute path.
     */
    public function __construct($tmpFile) {
        $this->tmpFile = $tmpFile;
    }

    /**
     * Removes original file and creates full and scaled versioins with names
     * choosen accordingly to saving rules.
     */
    public function save() {
        $this->generateNames();

        // move full image to rignt location
        rename($this->tmpFile, $this->nameToFullPath($this->fullImage));

        $this->createScaledVersion($GLOBALS["normal_width"], $this->normalImage);
        $this->createScaledVersion($GLOBALS["preview_width"], $this->previewImage);
    }

    /**
     * Returns generated file relative paths.
     *
     * @return HashMap contains paths to full, normal and preview image
     */
    public function getImages() {
        $images = array();

        $images["full"] = $this->nameToPath($this->fullImage);
        $images["normal"] = $this->nameToPath($this->normalImage);
        $images["preview"] = $this->nameToPath($this->previewImage);

        return $images;
    }

    /**
     * Creates a file (scaled version of original file).
     *
     * @param int $width scaled image width
     * @param String $name scaled image name
     */
    protected function createScaledVersion($width, $name) {
        $image = new SimpleImage();
        $image->load($this->nameToFullPath($this->fullImage));
        $image->resizeToWidth($width);
        $image->save($this->nameToFullPath($name));
    }

    /**
     * Return absolute path to file with specified name.
     *
     * @param String $name
     * @return String
     */
    protected function nameToFullPath($name) {
        return getcwd() . "/" . $this->nameToPath($name);
    }

    /**
     * Return relative path to file with specified name.
     *
     * @param String $name
     * @return String
     */
    protected function nameToPath($name) {
        return $GLOBALS["files_dir"] . $name;
    }

    /**
     * Generates names for files to create.
     */
    protected function generateNames() {
        $this->fullImage = $this->generateName("full");
        $this->normalImage = $this->generateName("normal");
        $this->previewImage = $this->generateName("preview");
    }

    /**
     * Genarates name of a file to create.
     *
     * @param String $type file type (full, normal, preview)
     * @return String
     */
    protected function generateName($type) {
        return hash("sha256", $type . StringUtils::generateRandomString(40));
    }
}
