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

$db_server = "localhost"; // MySQL database adress
$db_user = "moongallery"; // MySQL database user
$db_password = "naguheslo"; // MySQL database password
$db_database_name = "moongallery"; // MySQL database name

$files_dir = "files/"; // directory to which upload photos

$preview_width = 250; // preview image width
$normal_width = 1024; // normal image width

$CSRF_tooken_protection = false; // protection against Cross-site request forgery by secreet tooken
