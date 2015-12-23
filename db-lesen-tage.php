<?php
//Argumente hinter dem .. sind optional.
function db_lesen_tage($tage, $mandant, $VKmandant='[0-9]*') 
{
global $datum, $verbindungi, $Mitarbeiter;
	//Abruf der gespeicherten Daten aus der Datenbank
	//$tage ist die Anzahl der Tage. 5 Tage = Woche; 1 Tag = 1 Tag.
	//$mandant 1 ist der Marienplatz, 2 ist die Helenenstraße. Mandant 0 wird für den Chef, Frau Zapel, Frau Köhler und andere genutzt, die nicht jeden Tag im Plan stehen sollen.
	$tag=$datum;
	for ($i=0; $i<$tage; $i++)
	{
		$tag=date('Y-m-d', strtotime("+$i days", strtotime($datum)));
		$abfrage='SELECT Dienstplan.* FROM `Dienstplan` LEFT JOIN Mitarbeiter ON Dienstplan.VK=Mitarbeiter.VK WHERE Dienstplan.Mandant = "'.$mandant.'" AND `Datum` = "'.$tag.'" AND Mitarbeiter.Mandant REGEXP "^'.$VKmandant.'$" ORDER BY `Dienstbeginn` ASC, `Mittagsbeginn` ASC;';

//		$abfrage='SELECT * FROM `Dienstplan` WHERE `Datum` = "'.$tag.'" AND `Mandant` = "'.$mandant.'" ORDER BY `Dienstbeginn` ASC, `Mittagsbeginn` ASC;';
		$ergebnis = mysqli_query($verbindungi, $abfrage) OR die ("Error: $abfrage <br>".mysqli_error($verbindungi));
		$dienstplanCSV="";
		while($row = mysqli_fetch_object($ergebnis))
		{
			$Dienstplan[$i]["Datum"][]=$row->Datum;
			$Dienstplan[$i]["VK"][]=$row->VK;
			$Dienstplan[$i]["Dienstbeginn"][]=$row->Dienstbeginn;
			$Dienstplan[$i]["Dienstende"][]=$row->Dienstende;
			$Dienstplan[$i]["Mittagsbeginn"][]=$row->Mittagsbeginn;
			$Dienstplan[$i]["Mittagsende"][]=$row->Mittagsende;
			$Dienstplan[$i]["Stunden"][]=$row->Stunden;
			$Dienstplan[$i]["Kommentar"][]=$row->Kommentar;
			//Und jetzt schreiben wir die Daten noch in eine Datei, damit wir sie mit gnuplot darstellen können.
			if(empty($mittagsbeginn)){$mittagsbeginn="0:00";}
			if(empty($mittagsende)){$mittagsende="0:00";}
			$dienstplanCSV.=$Mitarbeiter[$row->VK].", $row->VK, $row->Datum";
			$dienstplanCSV.=", ".$row->Dienstbeginn;
			$dienstplanCSV.=", ".$row->Dienstende;
			$dienstplanCSV.=", ".$row->Mittagsbeginn;
			$dienstplanCSV.=", ".$row->Mittagsende;  
			$dienstplanCSV.=", ".$row->Stunden;  
			$dienstplanCSV.=", ".$row->Mandant."\n";  
		}
		$filename = "tmp/Dienstplan.csv";
		$myfile = fopen($filename, "w") or die("Unable to open file!");
		fwrite($myfile, $dienstplanCSV);
		fclose($myfile);
		$dienstplanCSV="";
		$command=('./Dienstplan_image.sh '.escapeshellcmd("m".$mandant."_".$datum));
		exec($command, $kommandoErgebnis); // Kann dies Fehler verursachen?
		//Wir rufen die Funktion mehrmals mit verschiedenen Parametern auf. Kann dem Filial-Plan-Bild dabei etwas zustoßen?

//		echo "<pre>";	var_export($kommandoErgebnis);    	echo "</pre>"; // Hier kann der aus der Datenbank gelesene Datensatz zu Debugging-Zwecken angesehen werden.
		//Wir füllen komplett leere Tage mit Werten, damit trotzdem eine Anzeige entsteht.
		if ( !isset($Dienstplan[$i]) )
		{
			$Dienstplan[$i]["Datum"][]=$tag;
/*
			$Dienstplan[$i]["VK"][]="";
			$Dienstplan[$i]["Dienstbeginn"][]="";
			$Dienstplan[$i]["Dienstende"][]="";
			$Dienstplan[$i]["Mittagsbeginn"][]="";
			$Dienstplan[$i]["Mittagsende"][]="";
			$Dienstplan[$i]["Stunden"][]="";
			$Dienstplan[$i]["Kommentar"][]="";
*/
		}
		//echo "Ich sehe ".count($Dienstplan)." Tage."."<br>";
	}
	if (isset($Dienstplan))
	{
		return $Dienstplan;
	}
	else
	{
		return 0;
	}
}
?>
