<?php
/*
 * This file is part of nearly every page.
 * But DO NOT include it inside head.php!
 * It is not part and should not be part of e.g. install.php
 */
?>
<nav id="nav" class="no_print">
    <ul id="navigation">
        <li>
            <a href=<?= PDR_HTTP_SERVER_APPLICATION_PATH ?>src/php/pages/roster-week-table.php><?= gettext("Weekly view") ?>
                <img src=<?= PDR_HTTP_SERVER_APPLICATION_PATH ?>img/week_2.svg class="inline-image" alt="week-button" title="Show week">
            </a>
            <ul>
                <li>            <a href=<?= PDR_HTTP_SERVER_APPLICATION_PATH ?>src/php/pages/roster-week-table.php><?= gettext("Weekly table") ?>
                        <img src=<?= PDR_HTTP_SERVER_APPLICATION_PATH ?>img/week_2.svg class="inline-image" alt="week-button" title="Show week">
                    </a>
                </li>
                <li>            <a href=<?= PDR_HTTP_SERVER_APPLICATION_PATH ?>src/php/pages/roster-week-images.php><?= gettext("Weekly images") ?>
                        <img src=<?= PDR_HTTP_SERVER_APPLICATION_PATH ?>img/week_1.svg class="inline-image" alt="week-button" title="Show week roster images">
                    </a>
                </li>
            </ul>

        </li>
        <li>
            <a href=<?= PDR_HTTP_SERVER_APPLICATION_PATH ?>src/php/pages/roster-day-read.php><?= gettext("Daily view") ?>
                <img src=<?= PDR_HTTP_SERVER_APPLICATION_PATH ?>img/day.svg class="inline-image" alt="day-button" title="Show day">
            </a>
            <ul>
                <li>
                    <a href=<?= PDR_HTTP_SERVER_APPLICATION_PATH ?>src/php/pages/roster-day-edit.php><?= gettext("Daily input") ?>
                        <img src=<?= PDR_HTTP_SERVER_APPLICATION_PATH ?>img/pencil-pictogram.svg class="inline-image" alt="edit-button" title="Edit">
                    </a>
                    <a href=<?= PDR_HTTP_SERVER_APPLICATION_PATH ?>src/php/pages/roster-day-read.php><?= gettext("Daily output") ?></a>
                    <a href=<?= PDR_HTTP_SERVER_APPLICATION_PATH ?>src/php/pages/principle-roster-day.php><?= gettext("Principle roster daily") ?>
                        <img src=<?= PDR_HTTP_SERVER_APPLICATION_PATH ?>img/pencil-pictogram.svg class="inline-image" alt="edit-button" title="Edit">
                    </a>
                </li>
            </ul>
        </li>
        <li><a href=<?= PDR_HTTP_SERVER_APPLICATION_PATH ?>src/php/pages/roster-employee-table.php><?= gettext("Employee") ?>
                <img src=<?= PDR_HTTP_SERVER_APPLICATION_PATH ?>img/employee_2.svg class="inline-image" alt="employee-button" title="Show employee">
            </a>
            <ul>
                <li>
                    <a href=<?= PDR_HTTP_SERVER_APPLICATION_PATH ?>src/php/pages/roster-employee-table.php><?= gettext("Roster employee") ?></a>
                </li>
                <li>
                    <a href=<?= PDR_HTTP_SERVER_APPLICATION_PATH ?>src/php/pages/principle-roster-employee.php><?= gettext("Principle roster employee") ?>
                        <img src=<?= PDR_HTTP_SERVER_APPLICATION_PATH ?>img/pencil-pictogram.svg class="inline-image" alt="edit-button" title="Edit">
                    </a>
                </li>
                <li><a href=<?= PDR_HTTP_SERVER_APPLICATION_PATH ?>src/php/pages/marginal-employment-hours-list.php><?= gettext("Marginal employment hours list") ?>
                        <img src=<?= PDR_HTTP_SERVER_APPLICATION_PATH ?>img/employee_2.svg class="inline-image" alt="employee-button" title="Show employee">
                    </a></li>

            </ul>
        <li>
            <a href=<?= PDR_HTTP_SERVER_APPLICATION_PATH ?>src/php/pages/overtime-read.php><?= gettext("Overtime") ?>
                <img src=<?= PDR_HTTP_SERVER_APPLICATION_PATH ?>img/watch_overtime.svg class="inline-image" alt="overtime-button" title="Show overtime">
            </a>
            <ul>
                <li>
                    <a href=<?= PDR_HTTP_SERVER_APPLICATION_PATH ?>src/php/pages/overtime-edit.php><?= gettext("Overtime input") ?>
                        <img src=<?= PDR_HTTP_SERVER_APPLICATION_PATH ?>img/pencil-pictogram.svg class="inline-image" alt="edit-button" title="Edit">
                    </a>
                    <a href=<?= PDR_HTTP_SERVER_APPLICATION_PATH ?>src/php/pages/overtime-read.php><?= gettext("Overtime output") ?></a>
                    <a href=<?= PDR_HTTP_SERVER_APPLICATION_PATH ?>src/php/pages/overtime-overview.php><?= gettext("Overtime overview") ?></a>
                </li>
            </ul>
        </li>
        <li>
            <a href=<?= PDR_HTTP_SERVER_APPLICATION_PATH ?>src/php/pages/absence-read.php title="Urlaub, Krankheit, Abwesenheit"><?= gettext("Absence") ?>
                <img src=<?= PDR_HTTP_SERVER_APPLICATION_PATH ?>img/absence.svg class="inline-image" alt="absence-button" title="Show absence">
            </a>
            <ul>
                <li>
                    <a href=<?= PDR_HTTP_SERVER_APPLICATION_PATH ?>src/php/pages/absence-edit.php title="Urlaub, Krankheit, Abwesenheit"><?= gettext("Absence input") ?>
                        <img src=<?= PDR_HTTP_SERVER_APPLICATION_PATH ?>img/pencil-pictogram.svg class="inline-image" alt="edit-button" title="Edit">
                    </a>
                    <a href=<?= PDR_HTTP_SERVER_APPLICATION_PATH ?>src/php/pages/absence-read.php title="Urlaub, Krankheit, Abwesenheit"><?= gettext("Absence output") ?></a>
                    <a href=<?= PDR_HTTP_SERVER_APPLICATION_PATH ?>src/php/pages/collaborative-vacation-year.php title="Urlaub, Krankheit, Abwesenheit"><?= gettext("Absence annual plan") ?>
                        <img src=<?= PDR_HTTP_SERVER_APPLICATION_PATH ?>img/pencil-pictogram.svg class="inline-image" alt="edit-button" title="Edit">
                    </a>
                    <a href=<?= PDR_HTTP_SERVER_APPLICATION_PATH ?>src/php/pages/collaborative-vacation-month.php title="Urlaub, Krankheit, Abwesenheit"><?= gettext("Absence monthly plan") ?>
                        <img src=<?= PDR_HTTP_SERVER_APPLICATION_PATH ?>img/pencil-pictogram.svg class="inline-image" alt="edit-button" title="Edit">
                        <img src=<?= PDR_HTTP_SERVER_APPLICATION_PATH ?>img/month_1.svg class="inline-image" alt="month-button" title="Edit month">
                    </a>
                </li>
            </ul>
        </li>
        <li><a>
                <?= gettext("Administration") ?>
                <img src=<?= PDR_HTTP_SERVER_APPLICATION_PATH ?>img/settings.png class="inline-image" alt="settings-button" title="Show settings">
            </a>
            <ul>
                <li><a href=<?= PDR_HTTP_SERVER_APPLICATION_PATH ?>src/php/pages/attendance-list.php><?= gettext("Attendance list") ?></a></li>
                <li><a href=<?= PDR_HTTP_SERVER_APPLICATION_PATH ?>src/php/pages/saturday-list.php><?= gettext("Saturday list") ?></a></li>
                <li><a href=<?= PDR_HTTP_SERVER_APPLICATION_PATH ?>src/php/pages/emergency-service-list.php><?= gettext("Emergency service list") ?></a></li>
                <li><a href=<?= PDR_HTTP_SERVER_APPLICATION_PATH ?>src/php/pages/marginal-employment-hours-list.php><?= gettext("Marginal employment hours list") ?>
                        <img src=<?= PDR_HTTP_SERVER_APPLICATION_PATH ?>img/employee_2.svg class="inline-image" alt="employee-button" title="Show employee">
                    </a></li>
                <li><a>&nbsp;</a></li>
                <li><a href=<?= PDR_HTTP_SERVER_APPLICATION_PATH ?>src/php/pages/upload-pep.php><?= gettext("Upload deployment planning") ?></a></li>
                <li><a>&nbsp;</a></li>
                <li><a href=<?= PDR_HTTP_SERVER_APPLICATION_PATH ?>src/php/pages/human-resource-management.php><?= gettext("Human resource management") ?></a></li>
                <li><a href=<?= PDR_HTTP_SERVER_APPLICATION_PATH ?>src/php/pages/branch-management.php><?= gettext("Branch management") ?></a></li>
                <li><a href=<?= PDR_HTTP_SERVER_APPLICATION_PATH ?>src/php/pages/user-management.php><?= gettext("User management") ?></a></li>
                <li><a>&nbsp;</a></li>
                <li><a href=<?= PDR_HTTP_SERVER_APPLICATION_PATH ?>src/php/pages/configuration.php><?= gettext('Configuration') ?><img src=<?= PDR_HTTP_SERVER_APPLICATION_PATH ?>img/settings.png class="inline-image" alt="configuration-button" title="Configuration"></a></li>
                <li><a href=/phpmyadmin>PhpMyAdmin</a></li>
            </ul>
        </li>
        <li>
            <a href="<?= PDR_HTTP_SERVER_APPLICATION_PATH ?>src/php/pages/user-page.php"><?= $_SESSION['user_object']->user_name; ?>&nbsp;
                <img src=<?= PDR_HTTP_SERVER_APPLICATION_PATH ?>img/user_1.svg class="inline-image" alt="user-button" title="Show user">
            </a>
            <ul>
                <li>
                    <a href="<?= PDR_HTTP_SERVER_APPLICATION_PATH ?>src/php/pages/user-page.php">
                        <?= gettext('User page'); ?>
                        <img src=<?= PDR_HTTP_SERVER_APPLICATION_PATH ?>img/user_1.svg class="inline-image" alt="user-button" title="Show user">
                    </a>
                </li>
                <li>
                    <?= $session->build_logout_button(); ?>
                </li>
            </ul>

        </li>
    </ul>
</nav>
