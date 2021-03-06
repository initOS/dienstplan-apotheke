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
$workforce = new workforce();
$user_dialog = new user_dialog();

$employee_id = user_input::get_variable_from_any_input('employee_id', FILTER_SANITIZE_NUMBER_INT, $_SESSION['user_object']->employee_id);
$User_list = read_user_list_from_database();
if (FALSE === in_array($employee_id, array_keys($User_list))) {
    /* This happens if a coworker does not have a user account (yet).
     * He can still be chosen within other pages.
     * Therefore we might get his/her id in the cookie.
     * Now we just change it to someone, who does have a user account:
     */
    $employee_id = min(array_keys($User_list));
}
$user = new user($employee_id);
create_cookie('employee_id', $employee_id, 30);

function insert_user_data_into_database(&$user) {
    global $session;
    if (!$session->user_has_privilege('administration')) {
        return FALSE;
    }

    $employee_id = filter_input(INPUT_POST, 'employee_id', FILTER_SANITIZE_NUMBER_INT);
    $privileges = filter_input(INPUT_POST, 'privilege', FILTER_SANITIZE_STRING, FILTER_REQUIRE_ARRAY);
    if ($_SESSION['user_object']->employee_id == $user->employee_id and $session->user_has_privilege('administration')) {
        /*
         * We want to avoid an administrator loosing the administration privilege by accident.
         * The privilege can only be lost, if an other administrator is taking it away.
         * This way we make sure, that there always is at least one user with administrative privileges.
         */
        if (!in_array('administration', $privileges)) {
            $privileges[] = 'administration';
            global $Error_message;
            $Error_message[] = "An administrative user cannot get rid of the 'administration' privilege himself. Only another administrator can take it away.";
        }
    }
    $user->write_new_privileges_to_database($privileges);
}

if (filter_has_var(INPUT_POST, 'submit_user_data')) {
    insert_user_data_into_database($user);
}

/**
 * Get a list of users
 *
 * @return array of \employee objects
 */
function read_user_list_from_database() {
    $sql_query = "SELECT `employee_id`, `user_name` FROM `users` ORDER BY `employee_id` ASC";
    $result = database_wrapper::instance()->run($sql_query);
    while ($row = $result->fetch(PDO::FETCH_OBJ)) {
        $User_list[$row->employee_id] = $row->user_name;
        $User_list[$row->employee_id] = new employee((int) $row->employee_id, $row->user_name, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL);
    }
    return $User_list;
}

require PDR_FILE_SYSTEM_APPLICATION_PATH . 'head.php';
require PDR_FILE_SYSTEM_APPLICATION_PATH . 'src/php/pages/menu.php';
$session->exit_on_missing_privilege('administration');
echo $user_dialog->build_messages();



echo build_html_navigation_elements::build_select_employee($employee_id, $User_list);

function build_checkbox_permission($privilege, $checked) {
    $privilege_name = pdr_gettext(str_replace('_', ' ', $privilege));
    $text = "<label for='$privilege'>" . $privilege_name . ": </label>";
    $text .= "<input type='checkbox' name='privilege[]' value='$privilege' id='$privilege' ";
    if ($checked) {
        $text .= " checked='checked'";
    }
    $text .= ">";
    return $text;
}
?>
<form method='POST' id='user_management'>
    <input type='text' name='employee_id' id="employee_id" value="<?= $user->employee_id ?>" hidden='true'>
    <p>
        <?php
        foreach (sessions::$Pdr_list_of_privileges as $privilege) {
            echo build_checkbox_permission($privilege, in_array($privilege, $user->privileges));
            echo "<br>";
        }
        ?>
    </p><p>

        <input type=submit id=save_new class='no_print' name=submit_user_data value='Eintragen' form='user_management'>
    </p>

</form>
<?php
require PDR_FILE_SYSTEM_APPLICATION_PATH . 'src/php/fragments/fragment.footer.php';
?>
</body>
</html>
