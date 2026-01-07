<?php
if (!isset($_POST['ajax'])) {
	exit;
}
chdir("..");
include "func.php";
if (!isset($_SESSION['id']) && $_POST['ajax'] != 'cercagiocatorilogin') {
	exit;
}

foreach ($_POST as $key => $value) {
	$$key = $conn->real_escape_string(stripslashes($value));
}

switch ($ajax) {
	case 'nuovogiocatore':
		$parti = explode(' ', $nome);
		$nome = $parti[0];
		$cognome = '';
		for ($i = 1; $i < count($parti); $i++) {
			$cognome .= $parti[$i] . ($i != count($parti) - 1 ? ' ' : '');
		}
		if ($conn->query("SELECT * FROM giocatori WHERE Nome = '$nome' AND Cognome = '$cognome';")->num_rows == 0) {
			if ($conn->query("INSERT INTO giocatori (Nome, Cognome, Alias) VALUES ('$nome', '$cognome', '');")) {
				echo $conn->insert_id;
			} else {
				http_response_code(500);
				echo 'Errore durante l\'inserimento';
			}
		} else {
			http_response_code(500);
			echo 'Un giocatore con questo nome esiste già!';
		}
		break;
	case 'aggiornagiocatore':
		if ($conn->query("SELECT * FROM giocatori WHERE IdGiocatore = $id;")->num_rows == 1) {
			if ($conn->query("UPDATE giocatori SET Nome = '$nome', Cognome = '$cognome', Alias = '$alias'" . (isset($pwd) ? ", Password = '" . hash("sha512", $pwd) . "'" : "") . " WHERE IdGiocatore = $id;")) {
				echo 1;
				load_login();
			} else {
				echo 'Errore durante l\'aggiornamento';
			}
		} else {
			echo 'Impossibile trovare il giocatore richiesto!';
		}
		break;
	case 'nuovapartita':
		if ($conn->query("insert into partite (Data) values (now());")) {
			echo $conn->insert_id;
		} else {
			echo 'Errore durante l\'inserimento';
		}
	break;
	case 'salvainfopartita':
		if ($conn->query("update partite Set Occasione = '$occasione', Data = '$data', Note = '$note' where IdPartita = $id;")) {
			echo mostra_partita($id, true);
		} else {
			http_response_code(500);
			echo 'Impossibile aggiornare.' . ($_SESSION['admin'] ? ' ' . $conn->error : '');
		}
		break;
	case 'cercagiocatori':
		$res = $conn->query("SELECT * FROM giocatori WHERE Nome LIKE '%$testo%' OR Alias LIKE '%$testo%';");
		while ($row = $res->fetch_assoc()) {
			echo '<button class="btn btn-outline-dark" style="width: 50%;" onclick="salvagioc(' . $row['IdGiocatore'] . ');"><i class="bi bi-person-fill"></i> ' . nomedi($row['IdGiocatore']) . '</button>';
		}
		if (strlen($testo) > 2) {
			echo '<button class="btn btn-outline-primary" onclick="nuovogioc(\'' . $testo . '\');">Crea nuovo giocatore: <i class="bi bi-person-fill"></i> ' . $testo . '</button>';
		}
		break;
	case 'cercagiocatorilogin':
		$res = $conn->query("SELECT * FROM giocatori WHERE Nome LIKE '%$testo%' OR Alias LIKE '%$testo%';");
		echo $res->num_rows . '&';
		while ($row = $res->fetch_assoc()) {
			echo '<button class="btn btn-outline-dark" style="width: 50%;" onclick="scegligioc(' . $row['IdGiocatore'] . ', \'' . nomecognome($row['Nome'], $row['Cognome']) . '\', ' . ($row['Password'] != null) . ');"><i class="bi bi-person-fill"></i> ' . nomedi($row['IdGiocatore']) . '</button>';
		}
		break;
	case 'salvagioc':
		$colonna2 = null;
		if ($idg == -1) {// Cancellazione al turno e colonna
			$conn->query("DELETE FROM partecipazioni WHERE Partita = $id AND Inizio = $inizio AND Colonna = $colonna;");
		} else {// Inserimento o sostituzione
			if ($conn->query("SELECT * FROM partecipazioni WHERE Partita = $id AND Inizio = $inizio AND Colonna = $colonna;")->num_rows > 0) {// Rimpiazza qualcuno nello stesso inizio e stessa colonna
				$res = $conn->query("SELECT * FROM partecipazioni WHERE Partita = $id AND Inizio = $inizio AND Giocatore = $idg;");
				if ($res->num_rows > 0) {// Il giocatore da inserire è già nella riga, scambiarlo di posto
					$conn->query("DELETE FROM partecipazioni WHERE Partita = $id AND Inizio = $inizio AND Giocatore = $idg;");
					$row = $res->fetch_assoc();
					$row2 = $conn->query("SELECT * FROM partecipazioni WHERE Partita = $id AND Inizio = $inizio AND Colonna = $colonna;")->fetch_assoc();
					$conn->query("UPDATE partecipazioni SET Giocatore = $idg WHERE Partita = $id AND Inizio = $inizio AND Colonna = $colonna;");
					$colonna2 = $row['Colonna'];
					$conn->query("INSERT INTO partecipazioni (Giocatore, Partita, Inizio, Colonna) VALUES (" . $row2['Giocatore'] . ", $id, $inizio, $colonna2);");
				} else {// Il giocatore da inserire non è nella riga, aggiornare il posto...
					$ingioco = false;
					$presenze = $conn->query("SELECT * FROM partecipazioni WHERE Partita = $id AND Giocatore = $idg ORDER BY Inizio;");
					while ($rowp = $presenze->fetch_assoc()) {
						if ($rowp['Inizio'] < $inizio) {// Controllare se non è attualmente in gioco altrove
							if ($conn->query("SELECT * FROM partecipazioni WHERE Partita = $id AND Colonna = " . $rowp['Colonna'] . " AND Inizio > " . $rowp['Inizio'] . " AND Inizio <= $inizio AND Giocatore <> $idg;")->num_rows == 0) {
								$ingioco = true;
							}
						} else {// Controllare se prima della sua partecipazione futura verrà rimpiazzato qui
							if ($conn->query("SELECT * FROM partecipazioni WHERE Partita = $id AND Colonna = $colonna AND Inizio > $inizio AND Inizio <= " . $rowp['Inizio'] . " AND Giocatore <> $idg;")->num_rows == 0) {
								$ingioco = true;
							}
						}
					}
					if (!$ingioco) {// ...Se in ogni sua partecipazione è già stato soppiantato
						$conn->query("UPDATE partecipazioni SET Giocatore = $idg WHERE Partita = $id AND Inizio = $inizio AND Colonna = $colonna;");
					}
				}
			} else {// Inserisci qualcuno...
				if ($conn->query("SELECT * FROM partecipazioni WHERE Partita = $id AND Inizio = $inizio AND Giocatore = $idg;")->num_rows == 0) {// Se non è già presente nella stessa riga
					$ingioco = false;
					$presenze = $conn->query("SELECT * FROM partecipazioni WHERE Partita = $id AND Giocatore = $idg order by Inizio;");
					while ($rowp = $presenze->fetch_assoc()) {
						if ($conn->query("SELECT * FROM partecipazioni WHERE Partita = $id AND Colonna = " . $rowp['Colonna'] . " AND Inizio > " . $rowp['Inizio'] . " AND Inizio <= $inizio AND Giocatore <> $idg;")->num_rows == 0) {
							$ingioco = true;
						}
					}
					if (!$ingioco) {// ...Se in ogni sua partecipazione è già stato soppiantato
						$conn->query("INSERT INTO partecipazioni (Giocatore, Partita, Inizio, Colonna) VALUES ($idg, $id, $inizio, $colonna);");
					}
				}
			}
			$cols = array($colonna, $colonna2);
			foreach ($cols as $i => $col) {
				if ($col == null) continue;
				$res = $conn->query("SELECT * FROM partecipazioni WHERE Partita = $id AND Colonna = $col order by Inizio;");
				$gioc = 0;
				while ($row = $res->fetch_assoc()) {
					if ($row['Giocatore'] == $gioc) {
						$conn->query("DELETE FROM partecipazioni WHERE Partita = $id AND Colonna = $col AND Inizio = " . $row['Inizio'] . ";");
					} else {
						$gioc = $row['Giocatore'];
					}
				}
			}
		}
		echo mostra_partita($id, true);
		break;
	case 'modalturno':
		$nuovo = false;
		if ($numero == 'null') {
			$nuovo = true;
			$numero = $conn->query("SELECT * FROM mani WHERE Partita = $id;")->num_rows + 1;
		}
		$resg = $conn->query("SELECT * FROM partecipazioni WHERE Partita = $id AND Inizio <= $numero ORDER BY Inizio DESC;");
		if ($resg->num_rows > 4) {
			$gioc = array(null, null, null, null, null);
			while (in_array(null, $gioc)) {
				$rowg = $resg->fetch_assoc();
				if ($rowg == null)
					break;
				if ($gioc[$rowg['Colonna'] - 1] == null) {
					$gioc[$rowg['Colonna'] - 1] = $rowg['Giocatore'];
				}
			}
			if (!$nuovo) {
				$mano = $conn->query("SELECT * FROM mani WHERE Partita = $id AND Numero = $numero;")->fetch_assoc();
				$nuovo = $mano == null;
			}
			echo '<div class="row mb-2"><div class="col">';
			echo '<strong>Chiamante:</strong><br><div class="btn-group-vertical mt-1 w-100" role="group">';
			foreach ($gioc as $i => $idg) {
				echo '<input type="radio" class="btn-check chiamante" name="chiamante" id="c' . $idg . '" autocomplete="off"' . (!$nuovo ? ($mano['Chiamante'] == ($i + 1) ? ' checked=""' : '') : '') . ' onchange="checkcodice(\'' . ($i + 1) . 'nnn\');"><label class="btn btn-outline-dark btn-sm" for="c' . $idg . '">' . nomedi($idg) . '</label>';
			}
			echo '</div></div><div class="col">';
			echo '<strong>Socio:</strong><br><div class="btn-group-vertical mt-1 w-100" role="group">';
			foreach ($gioc as $i => $idg) {
				echo '<input type="radio" class="btn-check socio" name="socio" id="s' . $idg . '" autocomplete="off"' . (!$nuovo ? ($mano['Socio'] == ($i + 1) ? ' checked=""' : '') : '') . ' onchange="checkcodice(\'n' . ($i + 1) . 'nn\');"><label class="btn btn-outline-dark btn-sm" for="s' . $idg . '">' . nomedi($idg) . '</label>';
			}
			echo '</div></div></div>';
			
			echo '<strong>La chiamata è stata:</strong><div class="row">';

			// Colonna sinistra: vinta, persa o patta
			echo '<div class="col my-auto">';
			echo '<div class="form-check"><label class="form-check-label"><input type="radio" class="form-check-input" name="vittoria" id="vinta" value="1"' . (!$nuovo ? ($mano['Vittoria'] == 1 ? ' checked=""' : '') : '') . ' onchange="checkcodice(\'nn1n\');">Vinta</label></div>';
			echo '<div class="form-check"><label class="form-check-label"><input type="radio" class="form-check-input" name="vittoria" id="persa" value="0"' . (!$nuovo ? ($mano['Vittoria'] == 0 ? ' checked=""' : '') : '') . ' onchange="checkcodice(\'nn0n\');">Persa</label></div>';
			echo '<div class="form-check"><label class="form-check-label"><input type="radio" class="form-check-input" name="vittoria" id="pareggiata" value="-"' . (!$nuovo ? ($mano['Vittoria'] == null ? ' checked=""' : '') : '') . ' onchange="checkcodice(\'nn--\');">Pareggiata</label></div>';
			echo '</div>';

			// Colonna destra: con o senza cappotto
			echo '<div class="col my-auto">';
			echo '<div class="form-check"><label class="form-check-label"><input type="radio" class="form-check-input" name="cappotto" id="concappotto" value="1"' . (!$nuovo ? ($mano['Cappotto'] == 1 ? ' checked=""' : '') : '') . ' onchange="checkcodice(\'nnn1\');">Con cappotto</label></div>';
			echo '<div class="form-check"><label class="form-check-label"><input type="radio" class="form-check-input" name="cappotto" id="senzacappotto" value="0"' . (!$nuovo ? ($mano['Cappotto'] == 0 || $mano['Cappotto'] == null ? ' checked=""' : '') : '') . ' onchange="checkcodice(\'nnn0\');">Senza cappotto</label></div>';

			// Altre opzioni
			echo '<hr class="my-2">';
			echo '<div class="form-check"><input class="form-check-input" type="checkbox" id="salva_orario" onchange="salva_orario(this.checked);"' . (!$nuovo && $mano['Ora'] == null ? '' : ' checked=""') . '><label class="form-check-label" for="salva_orario">Salva orario</label></div>';
			echo '<div class="form-check"><input class="form-check-input" type="checkbox" id="vecia" onchange="vecia(this.checked);"' . (!$nuovo && $mano['Vecia'] == 1 ? ' checked=""' : '') . '><label class="form-check-label" for="vecia">Fante di Spade</label></div>';
			echo '</div>';
			echo '</div><br>';
			
			echo '<div class="row"><div class="col-auto my-auto"><strong>Codice:</strong></div>';
			echo '<div class="col"><input type="text" class="form-control m-0" id="codice"' . (!$nuovo ? ' value="' . $mano['Chiamante'] . $mano['Socio'] . ($mano['Vittoria'] == null ? '--' : $mano['Vittoria'] . $mano['Cappotto']) . '"' : '') . ' onkeyup="if(event.keyCode == 13) salvaturno(' . ($nuovo ? '\'nuovo\'' : $numero) . ');"></div></div>';

			if (!$nuovo) {
				echo '<hr>';
				echo '<div class="row mb-3"><div class="col-auto my-auto"><strong>Orario di salvataggio:</strong></div>';
				echo '<div class="col"><input type="time" class="form-control m-0" id="orario" value="' . ($mano['Ora'] != null ? $mano['Ora'] : date('H:i')) . '"' . ($mano['Ora'] == null ? ' disabled' : '') . '></div></div>';

				echo '<div class="input-group mb-3"><button class="btn btn-sm btn-info" onclick="spostaturno(' . $numero . ');"><i class="bi bi-arrow-down-up"></i> Sposta in posizione</button><input class="form-control" type="number" min="1" max="' . $conn->query("SELECT * FROM mani WHERE Partita = $id;")->num_rows . '" id="posizione" style="margin: 0px;" value="' . $numero . '"></div>';

				echo '<button class="btn btn-danger" onclick="modalelimina('. $numero . ');"><i class="bi bi-trash"></i> Elimina questo turno</button><br>';
			}
			echo '<span class="text-danger" id="erroreturno"></span>';
		} else {
			http_response_code(500);
			echo 'Definire prima i cinque giocatori partecipanti.';
		}
		break;
	case 'salvaturno':
		$chiamante = substr($codice, 0, 1);
		$socio = substr($codice, 1, 1);
		$vittoria = (substr($codice, 2, 1) == '-' ? "null" : substr($codice, 2, 1));
		$cappotto = (substr($codice, 3, 1) == '-' ? "null" : substr($codice, 3, 1));
		$orario = ($orario == 'false' ? "null" : ($orario == 'true' ? "DATE_FORMAT(NOW(), '%H:%i')" : "'" . $orario . "'"));
		if ($numero == 'nuovo') {
			$numero = $conn->query("SELECT * FROM mani WHERE Partita = $id;")->num_rows + 1;
			if ($conn->query("INSERT INTO mani (Partita, Numero, Chiamante, Socio, Vittoria, Cappotto, Vecia, Ora) VALUES ($id, " . $numero . ", $chiamante, $socio, $vittoria, $cappotto, $vecia, $orario);")) {
				echo json_encode(array('numero' => $numero, 'partita' => mostra_partita($id, true, $numero)));
			} else {
				http_response_code(500);
				echo $conn->error;
			}
		} else {
			if ($conn->query("UPDATE mani SET Chiamante = $chiamante, Socio = $socio, Vittoria = $vittoria, Cappotto = $cappotto, Vecia = $vecia, Ora = $orario WHERE Partita = $id AND Numero = $numero;")) {
				echo json_encode(array('numero' => $numero, 'partita' => mostra_partita($id, true, $numero)));
			} else {
				http_response_code(500);
				echo $conn->error;
			}
		}
		break;
	case 'salvaturni':
		$turni = explode(" ", $codici);
		$ok = true;
		foreach ($turni as $i => $codice) {
			$chiamante = substr($codice, 0, 1);
			$socio = substr($codice, 1, 1);
			$vittoria = (substr($codice, 2, 1) == '-' ? "null" : substr($codice, 2, 1));
			$cappotto = (substr($codice, 3, 1) == '-' ? "null" : substr($codice, 3, 1));
			if (!$conn->query("INSERT INTO mani (Partita, Numero, Chiamante, Socio, Vittoria, Cappotto) VALUES ($id, " . ($conn->query("SELECT * FROM mani WHERE Partita = $id;")->num_rows + 1) . ", $chiamante, $socio, $vittoria, $cappotto);")) {
				$ok = false;
				break;
			}
		}
		if ($ok) {
			echo mostra_partita($id, true);
		} else {
			http_response_code(500);
			echo $conn->error;
		}
		break;
	case 'spostaturno':
		$turni = $conn->query("SELECT * FROM mani WHERE Partita = $id;")->num_rows;
		if ($posizione <= $turni && $posizione != $numero) {
			$conn->query("UPDATE mani SET Numero = " . ($turni + 1) . " WHERE Partita = $id AND Numero = $numero;");
			if ($posizione < $numero) {
				for ($i = $numero; $i > $posizione; $i--) {
					$conn->query("UPDATE mani SET Numero = $i WHERE Partita = $id AND Numero = " . ($i - 1) . ";");
				}
			} else {
				for ($i = $numero; $i < $posizione; $i++) {
					$conn->query("UPDATE mani SET Numero = $i WHERE Partita = $id AND Numero = " . ($i + 1) . ";");
				}
			}
			$conn->query("UPDATE mani SET Numero = $posizione WHERE Partita = $id AND Numero = " . ($turni + 1) . ";");
		}
		echo mostra_partita($id, true);
		break;
	case 'eliminaturno':
		$turni = $conn->query("SELECT * FROM mani WHERE Partita = $id;")->num_rows;
		$conn->query("DELETE FROM mani WHERE Partita = $id AND Numero = $numero;");
		for ($i = $numero; $i < $turni; $i++) {
			$conn->query("UPDATE mani SET Numero = $i WHERE Partita = $id AND Numero = " . ($i + 1) . ";");
		}
		echo mostra_partita($id, true);
		break;
	case 'modalcambio':
		$resg = $conn->query("SELECT * FROM partecipazioni WHERE Partita = $id;");
		if ($resg->num_rows > 4) {
			$turno = $conn->query("SELECT * FROM mani WHERE Partita = $id;")->num_rows + 1;
			echo '<div class="row"><div class="col-4">Al turno</div><div class="col"><input type="number" class="form-control" id="cturno" min="2" max="' . $turno . '" value="' . $turno . '"></div></div>';
			echo '<div class="row"><div class="col-4">Nella colonna <strong id="outcolonna">1</strong></div><div class="col"><input type="range" class="form-range" id="ccolonna" min="1" max="5" step="1" value="1" onchange="aggiornaoutcolonna();"></div></div>';
			echo '<div class="row"><div class="col-4">Entra in gioco</div><div class="col"><input class="form-control" type="text" id="primogioc" placeholder="Cerca..." onkeyup="cercagiocatori(this.value);" autofocus></div></div>';
			echo '<div id="listag"></div>';
		} else {
			echo '<span class="text-danger">Definire prima i cinque giocatori partecipanti</span>';
		}
		break;
	case 'annullacambio':
		if ($conn->query("DELETE FROM partecipazioni WHERE Partita = $id AND Inizio = $inizio AND Colonna = $colonna;")) {
			$res = $conn->query("SELECT * FROM partecipazioni WHERE Partita = $id AND Inizio < $inizio ORDER BY Inizio;");
			$gioc = array(null, null, null, null, null);
			while ($row = $res->fetch_assoc()) {
				$gioc[$row['Colonna'] - 1] = $row['Giocatore'];
			}
			$res = $conn->query("SELECT * FROM partecipazioni WHERE Partita = $id AND Inizio = $inizio;");
			while ($row = $res->fetch_assoc()) {
				if (in_array($row['Giocatore'], $gioc)) {
					$conn->query("DELETE FROM partecipazioni WHERE Partita = $id AND Inizio = $inizio AND Giocatore = " . $row['Giocatore'] . ";");
				}
			}
			echo mostra_partita($id, true);
		} else {
			http_response_code(500);
			echo $conn->error;
		}
		break;
	case 'caricafoto':
		if (isset($_FILES['files'])) {
			$directory = 'media/foto/' . $id;
			if (!is_dir($directory)) {
				mkdir($directory);
			}
			
			$ok = true;
			$esitoupload = '';
			if (is_array($_FILES['files']['name'])) {
				foreach ($_FILES['files']['name'] as $key => $name) {
					$last = processafile(array(
					'name' => $_FILES['files']['name'][$key],
					'type' => $_FILES['files']['type'][$key],
					'tmp_name' => $_FILES['files']['tmp_name'][$key],
					'error' => $_FILES['files']['error'][$key],
					'size' => $_FILES['files']['size'][$key]), $directory);
					$ok = ($ok && $last);
				}
			} else {
				$ok = processafile($_FILES['files'], $directory);
			}
			
			if ($ok)
				echo '1';
			else
				echo $esitoupload;
		}
		break;
	case 'listafile':
		if ($files = listafoto($id)) {
			if (count($files) == 0) {
				echo '0';
			} else {
				foreach ($files as $k => $value) {
					echo $value . "\n";
				}
			}
		} else {
			echo 'null';
		}
		break;
	case 'eliminafoto':
		if ($files = listafoto($id)) {
			if (unlink('media/foto/' . $id . '/' . $files[$indice]))
				echo '1';
		} else {
			echo 'Nessun file trovato';
		}
		break;
	case 'rinominafoto':
		if ($files = listafoto($id)) {
			if (rename('media/foto/' . $id . '/' . $files[$indice], 'media/foto/' . $id . '/' . $nome))
				echo '1';
		} else {
			echo 'Nessun file trovato';
		}
		break;
	default:
		exit;
}

?>
