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
require_once "../../../default.php";
require_once PDR_FILE_SYSTEM_APPLICATION_PATH . "/db-lesen-mitarbeiter.php";

if (filter_has_var(INPUT_GET, 'employee_id')) {
    $employee_id = filter_input(INPUT_GET, 'employee_id', FILTER_SANITIZE_NUMBER_INT);
} elseif (filter_has_var(INPUT_COOKIE, 'employee_id')) {
    $employee_id = filter_input(INPUT_COOKIE, 'employee_id', FILTER_SANITIZE_NUMBER_INT);
} else {
    $employee_id = $_SESSION['user_employee_id'];
}
?>
<form id="input_box_form" method="POST" onmousedown="stop_click_propagation();">
    <select name="employee_id" id="employee_id_select">
        <?php
        if ($session->user_has_privilege('create_absence')) {
            foreach ($Mitarbeiter as $employee_id_option => $last_name) {
                if ($employee_id_option === $employee_id) {
                    $option_selected = "selected";
                }
                echo "\t\t<option id='employee_id_option_$employee_id_option' value='$employee_id_option' $option_selected>";
                echo "$employee_id_option $last_name";
                echo "</option>\n";
            }
        } elseif ($session->user_has_privilege('request_own_absence') and "" === $employee_id) {
            echo "\t\t<option id='employee_id_option_" . $_SESSION['user_employee_id'] . "' value=" . $_SESSION['user_employee_id'] . ">";
            echo $_SESSION['user_employee_id'] . " " . $Mitarbeiter[$_SESSION['user_employee_id']];
            echo "</option>\n";
        } else {
            echo "\t\t<option id='employee_id_option_" . $employee_id . "' value=" . $employee_id . ">";
            echo $employee_id . " " . $Mitarbeiter[$employee_id];
            echo "</option>\n";
        }
        ?>
    </select>
    <img src="" style="width: 0" alt=""
         onerror="prefill_input_box_form(); this.parentNode.removeChild(this);"
         comment="This element is necessary to allow interaction of javascript with this element. After the execution, it is removed."
         />
    <input type="date" id="input_box_form_start_date" name="start_date">
    <input type="date" id="input_box_form_end_date" name="end_date">
    <input type="text" id="input_box_form_reason" name="reason" list='reasons'>
    <?php
    if (
            $session->user_has_privilege('create_absence')
            or ( $session->user_has_privilege('request_own_absence')
            and ( $_SESSION['user_employee_id'] === $employee_id
            or "" === $employee_id)
            )
    ) {
        ?>
        <button type="submit" value="save" name="command" class="button_tight">Speichern</button>
        <button type="submit" value="delete" name="command" id="input_box_form_button_delete" class="button_tight">Löschen</button>
    <?php } ?>

    <input type="hidden" id="employee_id_old" name="employee_id_old">
    <input type="hidden" id="input_box_form_start_date_old" name="start_date_old">
</form>
<a title="<?= gettext("Close"); ?>" href="#" onclick="remove_form_div()">
    <span id="remove_form_div_span">
        x
    </span>
</a>
