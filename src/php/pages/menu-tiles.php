<?php
/*
 * Copyright (C) 2017 Mandelkow
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */
require_once '../../../default.php';
require_once PDR_FILE_SYSTEM_APPLICATION_PATH . 'head.php';
?>
<nav id="nav_tiles" class="no-print">
    <ul id="navigation_tiles">
        <li>
            <a href=<?= PDR_HTTP_SERVER_APPLICATION_PATH ?>src/php/pages/roster-week-table.php title="Woche"><img src="<?= PDR_HTTP_SERVER_APPLICATION_PATH ?>img/week_2.svg" class="image_tiles"></a>
        </li>
        <li>
            <a href=<?= PDR_HTTP_SERVER_APPLICATION_PATH ?>src/php/pages/roster-day-read.php title="Tag"><img src="<?= PDR_HTTP_SERVER_APPLICATION_PATH ?>img/day.svg" class="image_tiles"></a>
        </li>
        <li>
            <a href=<?= PDR_HTTP_SERVER_APPLICATION_PATH ?>src/php/pages/roster-employee-table.php title="Mitarbeiter"><img src="<?= PDR_HTTP_SERVER_APPLICATION_PATH ?>img/employee_2.svg" class="image_tiles"></a>
        <li>
            <a href=<?= PDR_HTTP_SERVER_APPLICATION_PATH ?>src/php/pages/overtime-read.php title="Überstunden"><img src="<?= PDR_HTTP_SERVER_APPLICATION_PATH ?>img/watch_overtime.svg" class="image_tiles"></a>
        </li>
        <li>
            <a href=<?= PDR_HTTP_SERVER_APPLICATION_PATH ?>src/php/pages/absence-read.php title="Abwesenheit"><img src="<?= PDR_HTTP_SERVER_APPLICATION_PATH ?>img/absence.svg" class="image_tiles"></a>
        </li>
    </ul>
</nav>
