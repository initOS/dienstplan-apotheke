<?php
require 'default.php';
$mandant = 1;    //Wir zeigen den Dienstplan standardmäßig für die "Hauptapotheke" Mandant 1

$datum = date('Y-m-d'); //Dieser Wert wird überschrieben, wenn "$wochenauswahl und $woche per POST übergeben werden."
$year = date('Y');

//Hole eine Liste aller Mitarbeiter
require 'db-lesen-mitarbeiter.php';
require 'cookie-auswertung.php'; //Auswerten der per COOKIE gespeicherten Daten.
require 'get-auswertung.php'; //Auswerten der per GET übergebenen Daten.
if (isset($mandant)) {
    create_cookie('mandant', $mandant, 30);
}
if (isset($datum)) {
    create_cookie('datum', $datum, 0.5);
}
if (isset($year)) {
    create_cookie('year', $year, 0.5);
}

//Hole eine Liste aller Mandanten (Filialen)
require 'db-lesen-mandant.php';

$sql_query = "SELECT * FROM Notdienst WHERE YEAR(Datum) = $year AND Mandant = $mandant";
$result = mysqli_query_verbose($sql_query);
while ($row = mysqli_fetch_object($result)) {
    $Notdienste['VK'][] = $row->VK;
    $Notdienste['Datum'][] = $row->Datum;
    $Notdienste['Mandant'][] = $row->Mandant;
}
require 'head.php';?>
			<table>
				<tr><td>Datum</td><td>Name</td><td>Ersatz</td></tr>
				<?php
                    foreach ($Notdienste['Datum'] as $key => $datum) {
                        echo "\n\t\t\t\t<tr><td>".date('d.m.Y', strtotime($Notdienste['Datum'][$key])).'</td>';
												echo '<td>';
												echo (isset($List_of_employees[$Notdienste['VK'][$key]])) ? $List_of_employees[$Notdienste['VK'][$key]] : "";
												echo '</td>';
												echo "<td style=width:40%></td></tr>";
                    }
            ?>

		</table>
	</body>
</html>
