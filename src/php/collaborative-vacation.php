<!DOCTYPE html>
<!--
Copyright (C) 2017 Mandelkow

This program is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program.  If not, see <http://www.gnu.org/licenses/>.
-->
<html>
    <head>
        <meta charset="UTF-8">
        <title></title>
        <style>
            div{
                /*border: solid red thin;*/
            }
            div.year_container{
                width: 120em;
            }
            div.month_container{
                font-family: monospace;
                width: 12em;
                float: left;
                /*display: inline-block;*/
            }
            p.day_paragraph{
                border: solid black thin;
                margin-bottom: 0em;
                margin-top: 0em;
                /*-webkit-margin-before: 0em;*/
            }
            p.weekday{
                background-color: transparent;
            }
            p.weekend{
                background-color: lightgray;
            }
            span.absent_employee_container{
                /*border: solid red thin;*/
                float: right;
                width: 2em;
                background-color: #B4B4B4;
            }
            span.Apotheker{
                background-color: #73AC22;
            }
            span.PI{
                background-color: #73AC22;
            }
            span.PTA{
                background-color: #BDE682;
            }
            span.PKA{
                background-color: #B4B4B4;
            }

        </style>
        <script type="text/javascript">
            function get_element_below_pointer() {
                var x = event.clientX,
                        y = event.clientY,
                        element_mouse_is_over = document.elementFromPoint(x, y);
                document.getElementById('script_test_container').innerHTML = element_mouse_is_over;
                insert_div(element_mouse_is_over);
                return element_mouse_is_over;
            }
            function insert_div(element_mouse_is_over) {
                var existing_div = document.getElementById('test');
                if (existing_div) {
                    existing_div.parentNode.removeChild(existing_div);
                }

                var div = document.createElement('div');
                element_mouse_is_over.appendChild(div);
                var rect = element_mouse_is_over.getBoundingClientRect();
                div.style.left = rect.left;
                div.style.top = rect.top;
                div.style.position = 'absolute';
                div.style.backgroundColor = 'inherit';
                div.id = 'test';
                div.class = 'input_box_div'
                div.innerHTML = '<span class="msg">Hello world.</span>';
            }
        </script>
    </head>
    <body>
        <?php
        if (isset($_POST["year"])) {
            $year = filter_input(INPUT_POST, "year", FILTER_SANITIZE_NUMBER_INT);
        } else {
            $year = date("Y");
        }

        $start_date = mktime(0, 0, 0, 1, 1, $year);
        $current_month = date("n", $start_date);
        $current_month_name = date("F", $start_date);
        $current_year = date("Y", $start_date);

        echo "<div id='script_test_container'></div>\n";
        echo "<div class=year_container onclick=get_element_below_pointer()>\n";
        echo $current_year . "<br>\n";
        echo "<div class=month_container>";
        echo $current_month_name . "<br>\n";
        for ($date_unix = $start_date; $date_unix < strtotime("+ 1 year", $start_date); $date_unix += 24 * 60 * 60) {
            list($Abwesende, $Urlauber, $Kranke) = db_lesen_abwesenheit($date_unix);
            if ($current_month < date("n", $date_unix)) {
                $current_month = date("n", $date_unix);
                $current_month_name = date("F", $date_unix);
                echo "</div>";
                echo "<div class=month_container>";
                echo $current_month_name . "<br>\n";
            }
            $date_text = date("D d", $date_unix);
            $current_week_day_number = date("N", $date_unix);
            if ($current_week_day_number < 6) {
                $paragraph_weekday_class = "weekday";
                if (isset($Abwesende)) {
                    unset($absent_employees_containers);
                    foreach ($Abwesende as $employee_id => $reason) {
                        $absent_employees_containers .= "<span class='absent_employee_container $Ausbildung_mitarbeiter[$employee_id]'>";
                        $absent_employees_containers .= $employee_id;
                        $absent_employees_containers .= "</span>\n";
                    }
                } else {
                    $absent_employees_containers = "";
                }
                echo "<p class='day_paragraph "
                . $paragraph_weekday_class
                . "'>"
                . $date_text
                . " "
                . $absent_employees_containers
                . "</p>\n";
            } else {
                $paragraph_weekday_class = "weekend";
                echo "<p class='day_paragraph "
                . $paragraph_weekday_class
                . "'>"
                . $date_text
                . "\n"
                . "</p>\n";
            }
        }
        echo "\n</div>\n";
        echo "</div>\n";
        ?>
    </body>
</html>
