<?php

/*
 * Copyright (C) 2017 Mandelkow
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

/**
 * This class contains functions to check rosters for adherence to legal requirements.
 *
 * @author Mandelkow
 */
class law_and_order {

    public function check_maximum_working_hours($date_sql, $employee_id) {
        /*
         * Germany
         * Arbeitszeitgesetz (ArbZG)
         * § 3 Arbeitszeit der Arbeitnehmer
         * Die werktägliche Arbeitszeit der Arbeitnehmer darf acht Stunden nicht überschreiten.
         * Sie kann auf bis zu zehn Stunden nur verlängert werden,
         *  wenn innerhalb von sechs Kalendermonaten oder innerhalb von 24 Wochen im Durchschnitt acht Stunden werktäglich nicht überschritten werden.
         */
        $sql_query = "SELECT SUM(`Stunden`) AS `sum_of_hours`"
                . " FROM `dienstplan`"
                . " WHERE `Datum` BETWEEN DATE($date_sql) - INTERVAL 24 WEEK AND DATE($date_sql)"
                . " AND `VK` = '$employee_id'";
        $row = mysqli_query_for_single_object($sql_query);
        $sum_of_hours = $row->sum_of_hours;
        $sum_of_days = 0;
        for ($date_unix = strtotime($date_sql); $date_unix > strtotime("-24 weeks", strtotime($date_sql)); $date_unix = strtotime("-1 day", $date_unix)) {
            if (0 != strftime("%w", $date_unix) and ! is_holiday($date_unix)) {
                $sum_of_days++;
                print_debug_variable(date('YYYY-mm-dd', $date_unix));
            }
        }
        $average_working_hours = $sum_of_hours / $sum_of_days;
        if ($average_working_hours > 8) {
            return build_error_message_maximum_working_hours($average_working_hours, $employee_id);
        }
    }

    private function build_error_message_maximum_working_hours($average_working_hours, $employee_id) {
        global $Mitarbeiter;
        $error_message = $Mitarbeiter[$employee_id] . " arbeitet im Durchschnitt " . $average_working_hours 
                . " das ist ein Verstoß gegen <a href='http://www.gesetze-im-internet.de/arbzg/__3.html'>§3 ArbZG</a>!";
        if (!function_exists(build_warning_messages)) {
            require_once 'src/php/build-warning-messages.php';
        }
        return build_warning_messages($error_message);
    }

}
