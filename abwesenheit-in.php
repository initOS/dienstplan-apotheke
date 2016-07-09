<?php
require 'default.php';
?>
<html>
<?php require 'head.php';?>
		<script>
			window.setTimeout(leavePage, 900000); //Leave the page after x milliseconds of waiting. 900'000 = 15 Minutes.
		</script>
	<body>
		<?php
            require 'db-verbindung.php';
            //Hole eine Liste aller Mitarbeiter
            require 'db-lesen-mitarbeiter.php';
            //$VKmax = max(array_keys($Mitarbeiter)); //Wir suchen die höchste VK-Nummer.
            //Hole eine Liste aller Mandanten (Filialen)
            require 'db-lesen-mandant.php';
            if (isset($_POST['auswahl_mitarbeiter'])) {
                $auswahl_mitarbeiter = $_POST['auswahl_mitarbeiter'];
            } elseif (isset($_GET['auswahl_mitarbeiter'])) {
                $auswahl_mitarbeiter = $_GET['auswahl_mitarbeiter'];
            } elseif (isset($_COOKIE['auswahl_mitarbeiter'])) {
                $auswahl_mitarbeiter = $_COOKIE['auswahl_mitarbeiter'];
            } else {
                $auswahl_mitarbeiter = 1;
            }

            if (isset($auswahl_mitarbeiter)) {
                create_cookie('auswahl_mitarbeiter', $auswahl_mitarbeiter, 30);
            }

            //Wir löschen Datensätze, wenn dies befohlen wird.
            if (isset($_POST['loeschen'])) {
                foreach ($_POST['loeschen'] as $vk => $Beginne) {
                    foreach ($Beginne as $beginn => $X) {
                        $abfrage = "DELETE FROM `Abwesenheit`
						WHERE `VK` = '$vk' AND `Beginn` = '$beginn'";
                //		echo "$abfrage";
                        $ergebnis = mysqli_query($verbindungi, $abfrage) or die("Error: $abfrage <br>".mysqli_error($verbindungi));
                    }
                }
                $auswahl_mitarbeiter = $vk;
            }
            //Wir fügen neue Datensätze ein, wenn ALLE Daten übermittelt werden. (Leere Daten klappen vielleicht auch.)
            if (isset($_POST['submitStunden']) and isset($_POST['auswahl_mitarbeiter']) and isset($_POST['beginn']) and isset($_POST['ende']) and isset($_POST['tage']) and isset($_POST['grund'])) {
                for ($tag = strtotime($_POST['beginn']); $tag <= strtotime($_POST['ende']); $tag = strtotime('+1 day', strtotime($datum))) {
                    $datum = date('Y-m-d', $tag);
//					echo "$datum<br>\n";
                    require 'db-lesen-feiertag.php';
                    //Jetzt werden die Feiertage abgezogen, die nicht auf ein Wochenende fallen.
                    //Samstage und Sonntage wurden vorher bereits im Javascript abgezogen.
                    if (isset($feiertag) and strftime('%u', strtotime($datum)) < 6) {
                        echo "$feiertag ist ein Feiertag ($datum).<br>\n";
                        --$_POST['tage'];
                    }
                }
                $abfrage = 'INSERT INTO `Abwesenheit`
					(VK, Beginn, Ende, Tage, Grund)
					VALUES ('.$_POST['auswahl_mitarbeiter'].", '".$_POST['beginn']."', '".$_POST['ende']."', '".$_POST['tage']."', '".$_POST['grund']."')";
//				echo "$abfrage";
                $ergebnis = mysqli_query($verbindungi, $abfrage) or die("Error: $abfrage <br>".mysqli_error($verbindungi));
            }
            $vk = $auswahl_mitarbeiter;
            $abfrage = 'SELECT * FROM `Abwesenheit`
				WHERE `VK` = '.$vk.'
				ORDER BY `Beginn` ASC
				';
            $ergebnis = mysqli_query($verbindungi, $abfrage) or die("Error: $abfrage <br>".mysqli_error($verbindungi));
            $number_of_rows = mysqli_num_rows($ergebnis);
            $tablebody = ''; $i = 1;
            while ($row = mysqli_fetch_object($ergebnis)) {
                $tablebody .= "\t\t\t<tr>\n";
                $tablebody .= "\t\t\t\t<td>\n\t\t\t\t\t";
                $tablebody .= date('d.m.Y', strtotime($row->Beginn))." <input class=no-print type=submit name=loeschen[$vk][$row->Beginn] value='X' title='Diesen Datensatz löschen'>";
                $tablebody .= "\n\t\t\t\t</td>\n";
                $tablebody .= "\t\t\t\t<td>\n\t\t\t\t\t";
                $tablebody .= date('d.m.Y', strtotime($row->Ende));
                $tablebody .= "\n\t\t\t\t</td>\n";
                if ($i == $number_of_rows) {
                    $tablebody .= "\t\t\t\t<td id=letzterGrund>\n\t\t\t\t\t";
                } else {
                    $tablebody .= "\t\t\t\t<td>\n\t\t\t\t\t";
                }
                $tablebody .= "$row->Grund";
                $tablebody .= "\n\t\t\t\t</td>\n";
                $tablebody .= "\t\t\t\t<td>\n\t\t\t\t\t";
                $tablebody .= "$row->Tage";
                $tablebody .= "\n\t\t\t\t</td>\n";
                $tablebody .= "\n\t\t\t</tr>\n";
                ++$i;
            }
            $abfrage = 'SELECT DISTINCT `Grund` FROM `Abwesenheit` ORDER BY `Grund` ASC';
            $ergebnis = mysqli_query($verbindungi, $abfrage) or die("Error: $abfrage <br>".mysqli_error($verbindungi));
            $datalist = "<datalist id='gruende'>\n";
            while ($row = mysqli_fetch_object($ergebnis)) {
                $datalist .= "\t<option value='$row->Grund'>\n";
            }
            $datalist .= "</datalist>\n";

//Hier beginnt die Ausgabe
require 'navigation.php';
echo "<div class=no-image>\n";
echo "\t\t<form method=POST>\n";
echo "\t\t\t<select name=auswahl_mitarbeiter class='no-print large' onChange=document.getElementById('submitAuswahlMitarbeiter').click()>\n";
foreach ($Mitarbeiter as $vk => $name)
{
	if($vk == $auswahl_mitarbeiter)
	{
		echo "\t\t\t\t<option value=$vk selected>".$vk." ".$Mitarbeiter[$vk]."</option>,\n";
	}
	else {
		echo "\t\t\t\t<option value=$vk>".$vk." ".$Mitarbeiter[$vk]."</option>,\n";
	}
}
echo "\t\t\t</select>\n";
$submit_button = "\t\t\t<input type=submit value=Auswahl name='submitAuswahlMitarbeiter' id='submitAuswahlMitarbeiter' class=no-print>\n"; echo $submit_button; //name ist für die $_POST-Variable relevant. Die id wird für den onChange-Event im select benötigt.
echo "\t\t</form>\n";
echo "\t\t\t<H1>".$Mitarbeiter[$auswahl_mitarbeiter]."</H1>\n";
echo "<a class=no-print href=abwesenheit-out.php?auswahl_mitarbeiter=$auswahl_mitarbeiter>[Lesen]</a>";
echo "\t\t<form onsubmit='return confirmDelete()' method=POST>\n";
            echo "\t\t<table border=1>\n";
//Überschrift
            echo "\t\t\t<tr>\n
				\t\t\t\t<th>\n
				\t\t\t\t\tBeginn\n
				\t\t\t\t</th>\n
				\t\t\t\t<th>\n
				\t\t\t\t\tEnde\n
				\t\t\t\t</th>\n
				\t\t\t\t<th>\n
				\t\t\t\t\tGrund\n
				\t\t\t\t</th>\n
				\t\t\t\t<th>\n
				\t\t\t\t\tTage\n
				\t\t\t\t</th>\n
				\t\t\t</tr>\n";
//Ausgabe
            echo "$tablebody";
            echo "\t\t</form>\n";
//Eingabe. Der Saldo wird natürlich berechnet.
            echo "\t\t<form method=POST>\n";
            echo "<input type=hidden name=auswahl_mitarbeiter value=$auswahl_mitarbeiter>";
            echo "\t\t\t<tr class=no-print>\n";
            echo "\t\t\t\t<td>\n\t\t\t\t\t";
            echo '<input type=date onchange=updateTage() id=beginn name=beginn value='.date('Y-m-d').'>';
            echo "\n\t\t\t\t</td>\n";
            echo "\t\t\t\t<td>\n\t\t\t\t\t";
            echo '<input type=date onchange=updateTage() id=ende name=ende value='.date('Y-m-d').'>';
            echo "\n\t\t\t\t</td>\n";
            echo "\t\t\t\t<td>\n\t\t\t\t\t";
            echo "<input list='gruende' name=grund>";
            echo "$datalist";
            echo "\n\t\t\t\t</td>\n";
            echo "\t\t\t\t<td>\n\t\t\t\t\t";
            echo "<input readonly type=number id=tage name=tage title='Feiertage werden anschließend automatisch vom Server abgezogen.'>";
            echo "\n\t\t\t\t</td>\n";
            echo "\n\t\t\t</tr>\n";
            echo "\t\t</table>\n";
            echo "<input type=submit class=no-print name=submitStunden value='Eintragen'>";
            echo "\t</form>";
//echo "<pre>"; var_dump($_POST); echo "</pre>";
            echo "</div>\n";
						require 'contact-form.php';
        ?>
	</body>
</html>
