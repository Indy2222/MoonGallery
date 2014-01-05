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

include '../config.php';

class Database {

    /**
     * Establishes connection to configured database server and select configured database.
     */
    function connect() {
        $this->connection = mysql_connect($db_server, $db_user, $db_password);
        mysql_select_db($db_database_name, $this->connection);
    }

    /**
     * Disconnect from database.
     */
    function disconnect() {
        mysql_close($this->connection);
    }

}
