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

#Diese Seite wird den kompletten Dienstplan eines einzelnen Tages anzeigen.
require '../../../default.php';
$tage = 1; //Dies ist eine Tagesansicht für einen einzelnen Tag.
$tag = 0;

//$employee_id = user_input::get_variable_from_any_input("employee_id", FILTER_SANITIZE_NUMBER_INT);
//$year = user_input::get_variable_from_any_input("year", FILTER_SANITIZE_NUMBER_INT);
$branch_id = user_input::get_variable_from_any_input("mandant", FILTER_SANITIZE_NUMBER_INT, min(array_keys($List_of_branch_objects)));
create_cookie("mandant", $branch_id, 30);

$date_sql = user_input::get_variable_from_any_input("datum", FILTER_SANITIZE_STRING, date('Y-m-d'));
create_cookie("datum", $date_sql, 0.5);
$date_unix = strtotime($date_sql);
$workforce = new workforce($date_sql);
if (filter_has_var(INPUT_POST, 'Roster')) {
    $Roster = user_input::get_Roster_from_POST_secure();
    if (filter_has_var(INPUT_POST, 'submit_roster') && $session->user_has_privilege('create_roster')) {
        user_input::roster_write_user_input_to_database($Roster, $branch_id);
    }
}

$Abwesende = absence::read_absentees_from_database($date_sql);
$holiday = holidays::is_holiday($date_unix);
$Roster = roster::read_roster_from_database($branch_id, $date_sql);
if ((filter_has_var(INPUT_POST, 'submit_approval') or filter_has_var(INPUT_POST, 'submit_disapproval')) && count($Roster) > 0 && $session->user_has_privilege('approve_roster')) {
    user_input::old_write_approval_to_database($branch_id, $Roster);
}
$Principle_roster = roster::read_principle_roster_from_database($branch_id, $date_sql, NULL, array(roster::OPTION_CONTINUE_ON_ABSENCE));
if (!isset($Roster[$date_unix]) and FALSE === $holiday) { //No plans on holidays.
    if (!empty($Principle_roster)) {
        //Wir wollen eine automatische Dienstplanfindung beginnen.
        //Mal sehen, wie viel die Maschine selbst gestalten kann.
        $Fehlermeldung[] = "Kein Plan in der Datenbank, dies ist ein Vorschlag!";
        $Roster = $Principle_roster;
    } elseif (6 == strftime('%u', $date_unix)) {
        try {
            $saturday_rotation = new saturday_rotation($date_sql, $branch_id);
            $Roster = $saturday_rotation->fill_roster();
        } catch (Exception $exception) {
            error_log($exception->getMessage());
        }
    }
}
if ("7" !== date('N', $date_unix) and ! holidays::is_holiday($date_unix)) {
    $examine_roster = new examine_roster($Roster, $date_unix, $branch_id);
    $examine_roster->check_for_overlap($date_sql, $Fehlermeldung);
    $examine_roster->check_for_sufficient_employee_count($Fehlermeldung, 2);
    $examine_roster->check_for_sufficient_goods_receipt_count($Warnmeldung);
    $examine_roster->check_for_sufficient_qualified_pharmacist_count($Fehlermeldung);
}

if (FALSE !== pharmacy_emergency_service::having_emergency_service($date_sql)) {
    $Warnmeldung[] = "An den Notdienst denken!";
}




$VKmax = max(array_keys($workforce->List_of_employees));

//Wir schauen, on alle Anwesenden anwesend sind und alle Kranken und Siechenden im Urlaub.
examine_attendance::check_for_absent_employees($Roster, $Principle_roster, $Abwesende, $date_unix, $Warnmeldung);
examine_attendance::check_for_attendant_absentees($Roster, $date_sql, $Abwesende, $Fehlermeldung);



//Produziere die Ausgabe
require PDR_FILE_SYSTEM_APPLICATION_PATH . 'head.php';
require PDR_FILE_SYSTEM_APPLICATION_PATH . 'src/php/pages/menu.php';
$session->exit_on_missing_privilege('create_roster');
$html_text = "";

//Hier beginnt die Normale Ausgabe.
$html_text .= "<div id=main-area>\n";

//Here we put the output of errors and warnings. We display the errors, which we collected in $Fehlermeldung and $Warnmeldung:
$html_text .= build_warning_messages($Fehlermeldung, $Warnmeldung);

$html_text .= "" . strftime(gettext("calendar week") . ' %V', $date_unix) . "<br>";
$html_text .= "<div class=only-print><b>" . $List_of_branch_objects[$branch_id]->name . "</b></div><br>\n";
$html_text .= build_html_navigation_elements::build_select_branch($branch_id, $date_sql);


$html_text .= "<div id=navigation_elements>";
$html_text .= build_html_navigation_elements::build_button_day_backward($date_unix);
$html_text .= build_html_navigation_elements::build_button_day_forward($date_unix);
$html_text .= build_html_navigation_elements::build_button_submit('roster_form');
if ($session->user_has_privilege('approve_roster')) {
    $html_text .= build_html_navigation_elements::build_button_approval();
    $html_text .= build_html_navigation_elements::build_button_disapproval();
}
$html_text .= build_html_navigation_elements::build_button_open_readonly_version('src/php/pages/roster-day-read.php', array('datum' => $date_sql));
$html_text .= "</div>\n";
$html_text .= build_html_navigation_elements::build_input_date($date_sql);
$html_text .= "<form accept-charset='utf-8' id='roster_form' method=post>\n";
$html_text .= "<table>\n";
$html_text .= "<tr>\n";
$html_text .= "<td>";
$html_text .= "<input type=hidden name=datum value=" . $date_sql . ">";
$html_text .= "<input type=hidden name=mandant value=" . htmlentities($branch_id) . ">";
$html_text .= strftime('%d.%m. ', $date_unix);
//Wochentag
$html_text .= strftime('%A ', $date_unix);
if (FALSE !== $holiday) {
    $html_text .= " " . $holiday . " ";
}
$having_emergency_service = pharmacy_emergency_service::having_emergency_service($date_sql);
if (isset($having_emergency_service['branch_id'])) {
    if (isset($workforce->List_of_employees[$having_emergency_service['employee_id']])) {
        $html_text .= "<br>NOTDIENST<br>" . $workforce->List_of_employees[$having_emergency_service['employee_id']]->last_name . " / " . $List_of_branch_objects[$having_emergency_service['branch_id']]->name;
    } else {
        $html_text .= "<br>NOTDIENST<br>??? / " . $List_of_branch_objects[$having_emergency_service['branch_id']]->name;
    }
}
$html_text .= "</td>\n";
$html_text .= "</tr>\n";
$max_employee_count = roster::calculate_max_employee_count($Roster);
if (array() !== $Roster) {
    for ($table_input_row_iterator = 0; $table_input_row_iterator < $max_employee_count; $table_input_row_iterator++) {
        $html_text .= "<tr>\n";
        foreach (array_keys($Roster) as $day_iterator) {
            $html_text .= build_html_roster_views::build_roster_input_row($Roster, $day_iterator, $table_input_row_iterator, $max_employee_count, $date_unix, $branch_id);
        }
        $html_text .= "</tr>\n";
    }
} else {
    /*
     * Write an empty line in case the roster is empty:
     */
    $html_text .= "<tr>\n";
    $html_text .= build_html_roster_views::build_roster_input_row($Roster, $date_unix, 0, $max_employee_count, $date_unix, $branch_id);
    $html_text .= "</tr>\n";
    $html_text .= "<tr>\n";
    $html_text .= build_html_roster_views::build_roster_input_row($Roster, $date_unix, 1, $max_employee_count, $date_unix, $branch_id);
    $html_text .= "</tr>\n";
}


//Wir werfen einen Blick in den Urlaubsplan und schauen, ob alle da sind.
$html_text .= build_html_roster_views::build_absentees_row($Abwesende);
$html_text .= "</table>\n";
$html_text .= "</form>\n";


if (!empty($Roster)) {
    $html_text .= "<div class=image>\n";
    $roster_image_bar_plot = new roster_image_bar_plot($Roster);
    $html_text .= $roster_image_bar_plot->svg_string;
    $html_text .= "<br>\n";
    $html_text .= roster_image_histogramm::draw_image_histogramm($Roster, $branch_id, $examine_roster->Anwesende, $date_unix);
    $html_text .= "</div>\n";
}
$html_text .= "</div>";
echo "$html_text";

require PDR_FILE_SYSTEM_APPLICATION_PATH . 'contact-form.php';

echo "</body>\n";
echo "</html>";
?>