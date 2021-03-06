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

require '../../../default.php';

$employee_id = (int) user_input::get_variable_from_any_input('employee_id', FILTER_SANITIZE_NUMBER_INT, $_SESSION['user_object']->employee_id);
$branch_id = (int) user_input::get_variable_from_any_input('mandant', FILTER_SANITIZE_NUMBER_INT, min(array_keys($List_of_branch_objects)));
$weekday = (int) user_input::get_variable_from_any_input('weekday', FILTER_SANITIZE_NUMBER_INT, 1);
$alternating_week_id = (int) user_input::get_variable_from_any_input('alternating_week_id', FILTER_SANITIZE_NUMBER_INT, alternating_week::get_min_alternating_week_id());

$chosen_history_date_valid_from = NULL;
if (filter_has_var(INPUT_GET, 'chosen_history_date_valid_from')) {
    $chosen_history_date_valid_from = new DateTime(filter_input(INPUT_GET, 'chosen_history_date_valid_from', FILTER_SANITIZE_STRING));
}

if (!in_array($alternating_week_id, alternating_week::get_alternating_week_ids())) {
    $alternating_week_id = alternating_week::get_min_alternating_week_id();
}
$alternating_week = new alternating_week($alternating_week_id);
$date_object = $alternating_week->get_monday_date_for_alternating_week($chosen_history_date_valid_from);
if ($weekday > 1) {
    $date_object->add(new DateInterval('P' . ($weekday - 1) . 'D'));
}
create_cookie('mandant', $branch_id, 30);
/*
 * weekday
 */
create_cookie('alternating_week_id', $alternating_week_id, 1);
create_cookie('weekday', $weekday, 1);
$workforce = new workforce($date_object->format('Y-m-d'));
if (filter_has_var(INPUT_POST, 'submit_roster')) {
    if (!$session->user_has_privilege(sessions::PRIVILEGE_CREATE_ROSTER)) {
        return FALSE;
    }

    if (isset($_SESSION['Principle_roster_from_prompt'])) {
        $Principle_roster_new = $_SESSION['Principle_roster_from_prompt'];
        $List_of_differences = $_SESSION['List_of_differences'];
        unset($_SESSION['Principle_roster_from_prompt']);
        unset($_SESSION['List_of_differences']);
        $valid_from_input = new DateTime(filter_input(INPUT_POST, 'valid_from', FILTER_SANITIZE_STRING));
        /*
         * Find a correct date for the change:
         *     It should be the first monday in the relevant alternating_week_id week, after the given date.
         */
        $some_date_from_input = (new DateTime())->setTimestamp(min(array_keys($Principle_roster_new))); //This should probably be a monday.
        $valid_from = ( new alternating_week(
                alternating_week::get_alternating_week_for_date($some_date_from_input))
                )->get_monday_date_for_alternating_week($valid_from_input);
        principle_roster::insert_changed_entries_into_database($Principle_roster_new, $List_of_differences, $valid_from->format('Y-m-d'));
    }
}
if (filter_has_var(INPUT_POST, 'principle_roster_copy_from')) {
    $principle_roster_copy_from = filter_input(INPUT_POST, 'principle_roster_copy_from', FILTER_SANITIZE_STRING);
    user_input::principle_roster_copy_from($principle_roster_copy_from);
}
if (filter_has_var(INPUT_POST, 'principle_roster_delete')) {
    $principle_roster_delete = filter_input(INPUT_POST, 'principle_roster_delete', FILTER_SANITIZE_STRING);
    user_input::principle_roster_delete($principle_roster_delete);
}

$Principle_roster = principle_roster::read_current_principle_roster_from_database($branch_id, $date_object, $date_object);
/*
 * TODO: Build this page for the new valid_from approach!;
 */

//Produziere die Ausgabe
require PDR_FILE_SYSTEM_APPLICATION_PATH . 'head.php';
require PDR_FILE_SYSTEM_APPLICATION_PATH . 'src/php/pages/menu.php';
$user_dialog = new user_dialog;
echo $user_dialog->build_messages();
//Hier beginnt die Normale Ausgabe.
echo "<H1>" . gettext('Principle roster daily') . "</H1>\n";
echo "<div id=main-area>\n";
echo build_html_navigation_elements::build_select_branch($branch_id, $date_object->format('Y-m-d'));
//Auswahl des Wochentages
echo build_html_navigation_elements::build_select_weekday($weekday);
echo build_html_navigation_elements::build_select_alternating_week($alternating_week_id, $weekday, $date_object);
echo build_html_navigation_elements::build_button_principle_roster_copy($alternating_week_id);
echo build_html_navigation_elements::build_button_principle_roster_delete($alternating_week_id);
echo build_html_navigation_elements::build_button_show_principle_roster_history($alternating_week_id, $employee_id, $weekday, $branch_id, $date_object);
echo "<div id=navigation_elements>";
/*
 * TODO: Make it work:
 */

echo build_html_navigation_elements::build_button_submit('principle_roster_form');
echo "</div>\n";
$html_text = '';
$html_text .= "<form accept-charset='utf-8' id=principle_roster_form method=post action='../fragments/fragment.prompt_before_safe.php'>\n";
$html_text .= "<script> "
        . " var Roster_array = " . json_encode($Principle_roster) . ";\n"
        . " var List_of_employee_names = " . json_encode($workforce->get_list_of_employee_names()) . ";\n"
        . "</script>\n";
$html_text .= "<table>\n";
$max_employee_count = roster::calculate_max_employee_count($Principle_roster);
$day_iterator = $date_object->getTimestamp(); //Just in case the loop does not define it for build_html_roster_views::build_roster_input_row_add_row
for ($table_input_row_iterator = 0; $table_input_row_iterator < $max_employee_count; $table_input_row_iterator++) {
    $html_text .= "<tr>\n";
    foreach (array_keys($Principle_roster) as $day_iterator) {
        $html_text .= build_html_roster_views::build_roster_input_row($Principle_roster, $day_iterator, $table_input_row_iterator, $max_employee_count, $branch_id, array('add_select_employee'));
    }
    $html_text .= "</tr>\n";
}
$html_text .= build_html_roster_views::build_roster_input_row_add_row($day_iterator, $table_input_row_iterator, $max_employee_count, $branch_id);

$html_text .= "</table>\n";
$html_text .= "</form>\n";
echo $html_text;
if (!empty($Principle_roster)) {
    echo "<div class=image_group_container>\n";
    echo "<div class=image>\n";
    $roster_image_bar_plot = new roster_image_bar_plot($Principle_roster);
    echo $roster_image_bar_plot->svg_string;
    echo "<br>\n";
    $Changing_times = roster::calculate_changing_times($Principle_roster);
    $Attendees = roster_headcount::headcount_roster($Principle_roster, $Changing_times);
    echo roster_image_histogramm::draw_image_histogramm($Principle_roster, $branch_id, $Attendees, $date_object->getTimestamp());
    echo "</div>\n";
    echo "</div>\n";
}
echo '</div>';

require PDR_FILE_SYSTEM_APPLICATION_PATH . 'src/php/fragments/fragment.footer.php';

echo "</body>\n";
echo '</html>';
