<?php

/*
 * Copyright (C) 2018 Dr. rer. nat. M. Mandelkow <netbeans-pdr@martin-mandelkow.de>
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

/**
 * Description of class
 *
 * @author Dr. rer. nat. M. Mandelkow <netbeans-pdr@martin-mandelkow.de>
 */
abstract class roster {
    /*
     * Read the roster data from the database.
     * @param $start_date_sql string A string representation in the form of 'Y-m-d'. The first day, that is to be read.
     * @param $end_date_sql string A string representation in the form of 'Y-m-d'. The last day, that is to be read.
     */

    public static function read_roster_from_database($branch_id, $date_sql_start, $date_sql_end = NULL) {
        if (NULL === $date_sql_end) {
            $date_sql_end = $date_sql_start;
        }
        $date_unix_start = strtotime($date_sql_start);
        $date_unix_end = strtotime($date_sql_end);
        $Roster = array();
        for ($date_unix = $date_unix_start; $date_unix <= $date_unix_end; $date_unix += PDR_ONE_DAY_IN_SECONDS) {
            $date_sql = date('Y-m-d', $date_unix);
            $sql_query = 'SELECT DISTINCT Dienstplan.* '
                    . 'FROM `Dienstplan` '
                    . 'WHERE Dienstplan.Mandant = "' . $branch_id . '" AND `Datum` = "' . $date_sql . '" '
                    . 'ORDER BY `Dienstbeginn` ASC, `Dienstende` ASC, `Mittagsbeginn` ASC;';
            $result = mysqli_query_verbose($sql_query);

            $roster_row_iterator = 0;
            while ($row = mysqli_fetch_object($result)) {
                $Roster[$date_unix][$roster_row_iterator] = new roster_item($row->Datum, $row->VK, $row->Dienstbeginn, $row->Dienstende, $row->Mittagsbeginn, $row->Mittagsende, $row->Kommentar);
                $roster_row_iterator++;
            }
            /*
             * We mark empty roster days as empty:
             */
            if (!isset($Roster[$date_unix])) {
                $Roster[$date_unix]["empty"] = TRUE;
            }
        }
        return $Roster;
    }

    public static function read_principle_roster_from_database($branch_id, $date_sql_start, $date_sql_end = NULL) {
        global $List_of_employees;
        if (NULL === $date_sql_end) {
            $date_sql_end = $date_sql_start;
        }
        $date_unix_start = strtotime($date_sql_start);
        $date_unix_end = strtotime($date_sql_end);
        $Roster = array();
        for ($date_unix = $date_unix_start; $date_unix <= $date_unix_end; $date_unix += PDR_ONE_DAY_IN_SECONDS) {
            $date_sql = date('Y-m-d', $date_unix);
            $Absentees = db_lesen_abwesenheit($date_sql);
            /*
             * TODO: Make sure, that these two repair calls are not necessary anymore:
             */
            mysqli_query_verbose("UPDATE `Apotheke`.`Grundplan` SET `Mittagsbeginn` = NULL WHERE `Grundplan`.`Mittagsbeginn` = '0:00:00'");
            mysqli_query_verbose("UPDATE `Apotheke`.`Grundplan` SET `Mittagsende` = NULL WHERE `Grundplan`.`Mittagsende` = '0:00:00'");
            $sql_query = "SELECT * FROM `Grundplan`"
                    . "WHERE `Wochentag` = '" . date("N", $date_unix) . "'"
                    . "AND `Mandant` = '$branch_id'"
                    . "ORDER BY `Dienstbeginn` + `Dienstende`, `Dienstbeginn`";

            $result = mysqli_query_verbose($sql_query);
            $roster_row_iterator = 0;
            while ($row = mysqli_fetch_object($result)) {
                //Mitarbeiter, die im Urlaub/Krank sind, werden gar nicht erst beachtet.
                //TODO: This should be put somewhere else as a seperate function!
                if (isset($Absentees[$row->VK])) {
                    continue 1;
                }
                if (isset($List_of_employees) AND array_search($row->VK, array_keys($List_of_employees)) === false) {
                    //$Fehlermeldung[]=$List_of_employees[$row->VK]." ist nicht angestellt.<br>\n";
                    continue 1;
                }
                $Roster[$date_unix][$roster_row_iterator] = new roster_item($date_sql, $row->VK, $row->Dienstbeginn, $row->Dienstende, $row->Mittagsbeginn, $row->Mittagsende);
                $roster_row_iterator++;
                //TODO: Make sure, that real NULL values are inserted into the database! By every php-file that inserts anything into the grundplan!
            }
        }
        //TODO: call determine_lunch_breaks here perhaps
        return $Roster;
    }

    /*
     * This function determines the optimal lunch breaks.
     *
     * It considers the principle lunch breaks.
     * @return array $Roster
     */

    public static function determine_lunch_breaks($Roster) {
        global $List_of_employee_lunch_break_minutes;
        $lunch_break_length_standard = 30 * 60;
        foreach (array_keys($Roster) as $date_unix) {
            if (empty($Roster[$date_unix])) {
                return FALSE;
            }
            foreach ($Roster[$date_unix] as $roster_item_object) {
                $break_start_taken_int[] = $roster_item_object->break_start_int;
                $break_end_taken_int[] = $roster_item_object->break_end_int;
            }
            $lunch_break_start = roster_item::convert_time_to_seconds('11:30:00');
            foreach ($Roster[$date_unix] as $roster_item_object) {
                $employee_id = $roster_item_object->employee_id;
                if (!empty($List_of_employee_lunch_break_minutes[$employee_id]) AND ! ($roster_item_object->break_start_int > 0) AND ! ($roster_item_object->break_end_int > 0)) {
                    //Zunächst berechnen wir die Stunden, damit wir wissen, wer überhaupt eine Mittagspause bekommt.
                    $duty_seconds_with_a_break = $roster_item_object->duty_end_int - $roster_item_object->duty_start_int - $List_of_employee_lunch_break_minutes[$employee_id] * 60;
                    if ($duty_seconds_with_a_break >= 6 * 3600) {
                        //echo "Mehr als 6 Stunden, also gibt es Mittag!";
                        //Wer länger als 6 Stunden Arbeitszeit hat, bekommt eine Mittagspause.
                        $lunch_break_end = $lunch_break_start + $List_of_employee_lunch_break_minutes[$employee_id] * 60;
                        for ($number_of_trys = 0; $number_of_trys < 3; $number_of_trys++) {
                            if (FALSE !== array_search($lunch_break_start, $break_start_taken_int) OR FALSE !== array_search($lunch_break_end, $break_end_taken_int)) {
                                //Zu diesem Zeitpunkt startet schon jemand sein Mittag. Wir warten 30 Minuten (1800 Sekunden)
                                $lunch_break_start += $lunch_break_length_standard;
                                $lunch_break_end += $lunch_break_length_standard;
                                continue;
                            } else {
                                break;
                            }
                        }
                        $roster_item_object->break_start_int = $lunch_break_start;
                        $roster_item_object->break_start_sql = roster_item::format_time_integer_to_string($lunch_break_start);
                        $roster_item_object->break_end_int = $lunch_break_end;
                        $roster_item_object->break_end_sql = roster_item::format_time_integer_to_string($lunch_break_end);
                        /*
                         * Preparartion for the next iteration:
                         */
                        $lunch_break_start = $lunch_break_end;
                    }
                } elseif (!empty($employee_id) AND ! empty($roster_item_object->break_start_int) AND empty($roster_item_object->break_end_int)) {
                    $roster_item_object->break_end_int = $roster_item_object->break_start_int + $List_of_employee_lunch_break_minutes[$employee_id];
                    $roster_item_object->break_end_sql = roster_item::format_time_integer_to_string($roster_item_object->break_end_int);
                } elseif (!empty($employee_id) AND empty($roster_item_object->break_start_int) AND ! empty($roster_item_object->break_end_int)) {
                    $roster_item_object->break_start_int = $roster_item_object->break_end_int - $List_of_employee_lunch_break_minutes[$employee_id];
                    $roster_item_object->break_start_sql = roster_item::format_time_integer_to_string($roster_item_object->break_start_int);
                }
            }
        }
        return NULL;
    }

    public static function calculate_changing_times($Roster) {
        foreach ($Roster as $roster_day) {
            foreach ($roster_day as $roster_item_object) {
                $Changing_times[] = $roster_item_object->duty_start_int;
                $Changing_times[] = $roster_item_object->duty_end_int;
                $Changing_times[] = $roster_item_object->break_start_int;
                $Changing_times[] = $roster_item_object->break_end_int;
            }
        }
        sort($Changing_times);
        $Unique_changing_times = array_unique($Changing_times);
        //Remove empty and null values from the array:
        $Clean_changing_times = array_filter($Unique_changing_times, 'strlen');
        return $Clean_changing_times;
    }

    public static function get_employee_id_from_roster($Roster, $day_iterator, $roster_row_iterator) {
        return $Roster[$day_iterator][$roster_row_iterator]->employee_id;
    }

    public static function get_duty_start_from_roster($Roster, $day_iterator, $roster_row_iterator) {
        return roster_item::format_time_integer_to_string($Roster[$day_iterator][$roster_row_iterator]->duty_start_int);
    }

    public static function get_duty_end_from_roster($Roster, $day_iterator, $roster_row_iterator) {
        return roster_item::format_time_integer_to_string($Roster[$day_iterator][$roster_row_iterator]->duty_end_int);
    }

    public static function get_break_start_from_roster($Roster, $day_iterator, $roster_row_iterator) {
        return roster_item::format_time_integer_to_string($Roster[$day_iterator][$roster_row_iterator]->break_start_int);
    }

    public static function get_break_end_from_roster($Roster, $day_iterator, $roster_row_iterator) {
        return roster_item::format_time_integer_to_string($Roster[$day_iterator][$roster_row_iterator]->break_end_int);
    }

    public static function get_comment_from_roster($Roster, $day_iterator, $roster_row_iterator) {
        return $Roster[$day_iterator][$roster_row_iterator]->comment;
    }

}