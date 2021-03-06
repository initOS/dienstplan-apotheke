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
require_once "../../../default.php";
$workforce = new workforce();


if (filter_has_var(INPUT_GET, 'absence_details_json')) {
    /*
     * An existing entry will be edited:
     */
    $absence_details_json_unsafe = filter_input(INPUT_GET, 'absence_details_json', FILTER_UNSAFE_RAW);
    $Absence_details_unsafe = json_decode($absence_details_json_unsafe, TRUE);
    $filters = array(
        'employee_id' => FILTER_SANITIZE_NUMBER_INT,
        'reason' => FILTER_SANITIZE_STRING,
        'comment' => FILTER_SANITIZE_STRING,
        'start' => FILTER_SANITIZE_STRING,
        'end' => FILTER_SANITIZE_STRING,
        'approval' => FILTER_SANITIZE_STRING,
    );
    $Absence_details = filter_var_array($Absence_details_unsafe, $filters);
    $Absence_details['mode'] = "edit";
    $employee_id = $Absence_details['employee_id'];
} elseif (filter_has_var(INPUT_GET, 'highlight_details_json')) {
    /*
     * A new entry will be created:
     */
    $highlight_details_json_unsafe = filter_input(INPUT_GET, 'highlight_details_json', FILTER_UNSAFE_RAW);
    $Highlight_details_unsafe = json_decode($highlight_details_json_unsafe, TRUE);
    $filters = array(
        //'highlight_absence_create_from_date_sql' => FILTER_SANITIZE_STRING,
        //'highlight_absence_create_to_date_sql' => FILTER_SANITIZE_STRING,
        'date_range_min' => FILTER_SANITIZE_STRING,
        'date_range_max' => FILTER_SANITIZE_STRING,
    );
    $Highlight_details = filter_var_array($Highlight_details_unsafe, $filters);
    $employee_id = user_input::get_variable_from_any_input('employee_id', FILTER_SANITIZE_NUMBER_INT, $_SESSION['user_object']->employee_id);
    $Absence_details['employee_id'] = $employee_id;
    $Absence_details['reason'] = gettext('Vacation');
    $Absence_details['start'] = date('Y-m-d', $Highlight_details['date_range_min']);
    $Absence_details['end'] = date('Y-m-d', $Highlight_details['date_range_max']);
    $Absence_details['comment'] = '';
    $Absence_details['mode'] = "create";
} else {
    $employee_id = user_input::get_variable_from_any_input('employee_id', FILTER_SANITIZE_NUMBER_INT, $_SESSION['user_object']->employee_id);
}
?>
<form accept-charset='utf-8' id="input_box_form" method="POST">
    <p><?= gettext("Employee") ?><br><select name="employee_id" id="employee_id_select"></p>
    <?php
    if ($session->user_has_privilege('create_absence')) {
        /*
         * The user is allowed to create an absence for anyone:
         */
        foreach ($workforce->List_of_employees as $employee_id_option => $employee_object) {
            if ($employee_id_option == $employee_id) {
                $option_selected = "selected";
            } else {
                $option_selected = "";
            }
            echo "<option id='employee_id_option_$employee_id_option' value='$employee_id_option' $option_selected>";
            echo "$employee_id_option $employee_object->last_name";
            echo "</option>\n";
        }
    } elseif ($session->user_has_privilege('request_own_absence') and "" === $employee_id) {
        /*
         * The user is allowed to create an absence for himself and
         * This absence is new.
         */
        $session_employee_id = $_SESSION['user_object']->employee_id;
        echo "<option id='employee_id_option_" . $session_employee_id . "' value=" . $session_employee_id . ">";
        echo $session_employee_id . " " . $workforce->List_of_employees[$session_employee_id]->last_name;
        echo "</option>\n";
    } else {
        /*
         * The user is NOT allowed to create any absence
         * or this is an existing absence.
         */
        echo "<option id='employee_id_option_" . $employee_id . "' value=" . $employee_id . ">";
        echo $employee_id . " " . $workforce->List_of_employees[$employee_id]->last_name;
        echo "</option>\n";
    }
    ?>
</select>
<!--
<img src="" style="width: 0" alt=""
     onerror="prefill_input_box_form(); this.parentNode.removeChild(this);"
     data-comment="This element is necessary to allow interaction of javascript with this element. After the execution, it is removed."
     />
-->
<p><?= gettext("Start") ?><br><input type="date" id="input_box_form_start_date" name="start_date" value="<?= $Absence_details['start'] ?>"></p>
<p><?= gettext("End") ?><br><input type="date" id="input_box_form_end_date" name="end_date" value="<?= $Absence_details['end'] ?>"></p>
<p><?= gettext("Reason") ?><br><?= absence::build_reason_input_select($Absence_details['reason'], NULL, 'input_box_form') ?></p>
<p><?= gettext("Comment") ?><br><input type="text" id="input_box_form_comment" name="comment" value="<?= $Absence_details['comment'] ?>"></p>
<?php
if ($session->user_has_privilege('create_absence') and "edit" === $Absence_details['mode']) {
    //TODO: Remove all occurences of "disapprove" and change them to "deny".
    if ("approved" !== $Absence_details['approval']) {
        echo "<button type='submit' value='approved'         name='approve_absence' />" . gettext("Approve") . "</button>";
        echo "<button type='submit' value='not_yet_approved' name='approve_absence' />" . gettext("Pending") . "</button>";
        echo "<button type='submit' value='disapproved'      name='approve_absence' />" . gettext("Deny") . "</button>";
    }
}
if (
        $session->user_has_privilege('create_absence')
        or ( $session->user_has_privilege('request_own_absence')
        and ( $_SESSION['user_object']->employee_id === $employee_id
        or "" === $employee_id)
        )
) {
    ?>
    <p>
        <button type="submit" value="save" name="command" class="button_tight"><?= gettext("Save") ?></button>
        <?php if ("edit" === $Absence_details['mode']) { ?>
            <button type="submit" value="delete" name="command" id="input_box_form_button_delete" class="button_tight"><?= gettext("Delete") ?></button>
        <?php } ?>
    </p>
<?php } ?>

<input type="hidden" id="employee_id_old" name="employee_id_old" value="<?= $Absence_details['employee_id'] ?>">
<input type="hidden" id="input_box_form_start_date_old" name="start_date_old" value="<?= $Absence_details['start'] ?>">
</form>
<a title="<?= gettext("Close"); ?>" href="#" onclick="remove_form_div()">
    <span id="remove_form_div_span">
        x
    </span>
</a>
