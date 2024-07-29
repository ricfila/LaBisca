<?php

function partita($id) {
	global $conn;
	$row = $conn->query("SELECT * FROM partite WHERE IdPartita = '$id';")->fetch_assoc();
	
	// Giocatori
	$gioc = $conn->query("SELECT * FROM partecipazioni WHERE Inizio = 1 AND Partita = $id ORDER BY Colonna;");
	$giocatori = array(null, null, null, null, null);
	$gstat = array(array(), // [0][5] turni giocati in ogni colonna,
		array(), array(), array(), // [1] Chiamate vinte, [2] perse, [3] patte,
		array(), array(), array(), // [4] in mano vinte, [5] perse, [6] patte,
		array(), array(), array(), // [7] con cappotto vinte, [8] perse, [9] punteggio chiamante,
		array(), array(), array(),  // [10] Socio vinte, [11] perse, [12] patte,
		array(), array(), array()); // [13] socio con cappotto vinte, [14] perse, [15] punteggio socio
	$matgiocatori = array();
	$matgiocatori[0] = array(null, null, null, null, null);
	while ($rowg = $gioc->fetch_assoc()) {
		$giocatori[$rowg['Colonna'] - 1] = $rowg['Giocatore'];
		$matgiocatori[0][$rowg['Colonna'] - 1] = $rowg['Giocatore'];

		// Inizializzazione delle statistiche per il giocatore
		$gstat[0][$rowg['Giocatore']] = array(0, 0, 0, 0, 0);
		for ($z = 1; $z < count($gstat); $z++)
			$gstat[$z][$rowg['Giocatore']] = 0;
	}
	
	// Mani e punteggi
	$mani = $conn->query("SELECT * FROM mani WHERE Partita = $id ORDER BY Numero;");
	$cambi = $conn->query("SELECT * FROM partecipazioni WHERE Partita = $id AND Inizio > 1 ORDER BY Inizio;");
	$i = 0;
	$parziali = array();
	$totali = array(0, 0, 0, 0, 0);
	$codici = array();
	$rowc = $cambi->fetch_assoc();
	$stat = array(0, 0, 0, 0, 0, 0, 0); //Vinte, perse, patte, in mano, cappotto, min, max
	$colonne = array(array(), array(), array(), array(), array());
	while ($rowm = $mani->fetch_assoc()) {
		if ($rowc != null && $rowc['Inizio'] == ($i + 1)) {
			$matgiocatori[$i] = array();
			while ($rowc != null && $rowc['Inizio'] == ($i + 1)) {
				$matgiocatori[$i][$rowc['Colonna'] - 1] = $rowc['Giocatore'];
				$giocatori[$rowc['Colonna'] - 1] = $rowc['Giocatore'];

				// Inizializzazione del giocatore, se nuovo in questa partita
				if (!array_key_exists($rowc['Giocatore'], $gstat[0])) {
					$gstat[0][$rowc['Giocatore']] = array(0, 0, 0, 0, 0);
					for ($z = 1; $z < count($gstat); $z++)
						$gstat[$z][$rowc['Giocatore']] = 0;
				}
				$rowc = $cambi->fetch_assoc();
			}
		}
		$parziali[] = array(0, 0, 0, 0, 0);
		$codici[] = array($rowm['Chiamante'], $rowm['Socio'], $rowm['Vittoria'], $rowm['Cappotto'], $rowm['Vecia']);
		$palio = 0;
		$palio2 = 0;
		$vittoria = 0;
		if ($rowm['Vittoria'] != null) {
			$palio = ($rowm['Chiamante'] == $rowm['Socio'] ? 4 : 2);
			$palio2 = 1;
			$vittoria = ($rowm['Vittoria'] == 1 ? 1 : ($rowm['Vittoria'] == 0 ? -1 : 0));

			if ($rowm['Vittoria'] == 1) { // Vinta
				$stat[0]++;
				$gstat[1][$giocatori[$rowm['Chiamante'] - 1]]++;

				if ($rowm['Chiamante'] == $rowm['Socio']) { // In mano vinta
					$stat[3]++;
					$gstat[4][$giocatori[$rowm['Chiamante'] - 1]]++;
				} else { // Socio vinta
					$gstat[10][$giocatori[$rowm['Socio'] - 1]]++;
				}
			} else { // Persa
				$stat[1]++;
				$gstat[2][$giocatori[$rowm['Chiamante'] - 1]]++;
				
				if ($rowm['Chiamante'] == $rowm['Socio']) { // In mano persa
					$stat[3]++;
					$gstat[5][$giocatori[$rowm['Chiamante'] - 1]]++;
				} else { // Socio persa
					$gstat[11][$giocatori[$rowm['Socio'] - 1]]++;
				}
			}

			if ($rowm['Cappotto'] == 1) { // Cappotto
				$palio *= 2;
				$palio2 *= 2;

				$stat[4]++;
				$gstat[$rowm['Vittoria'] == 1 ? 7 : 8][$giocatori[$rowm['Chiamante'] - 1]]++;
				if ($rowm['Chiamante'] != $rowm['Socio'])
					$gstat[$rowm['Vittoria'] == 1 ? 13 : 14][$giocatori[$rowm['Socio'] - 1]]++;
			}
		} else { // Pareggio
			$stat[2]++;
			$gstat[3][$giocatori[$rowm['Chiamante'] - 1]]++;

			if ($rowm['Chiamante'] == $rowm['Socio']) { // In mano patta
				$stat[3]++;
				$gstat[6][$giocatori[$rowm['Chiamante'] - 1]]++;
			} else { // Socio patta
				$gstat[12][$giocatori[$rowm['Socio'] - 1]]++;
			}
		}
		
		for ($j = 0; $j < 5; $j++) {
			// Turni giocati
			$gstat[0][$giocatori[$j]][$j]++;

			// Turni alle colonne
			if (!isset($colonne[$j][$giocatori[$j]])) {
				$colonne[$j][$giocatori[$j]] = array(
					'id' => $giocatori[$j],
					'inizio' => $i,
					'turni' => 0,
				);
			}
			$colonne[$j][$giocatori[$j]]['turni']++;
			
			// Punteggi
			if (($j + 1) == $rowm['Chiamante']) {
				$parziali[$i][$j] = $vittoria * $palio;
				$gstat[9][$giocatori[$j]] += $vittoria * $palio;
			} else if (($j + 1) == $rowm['Socio']) {
				$parziali[$i][$j] = $vittoria * $palio2;
				$gstat[15][$giocatori[$j]] += $vittoria * $palio2;
			} else {
				$parziali[$i][$j] = -1 * $vittoria * $palio2;
			}
			$totali[$j] += $parziali[$i][$j];
			if ($totali[$j] > $stat[6])
				$stat[6] = $totali[$j];
			if ($totali[$j] < $stat[5])
				$stat[5] = $totali[$j];
		}
		$i++;
	}
	
	// Conclusioni
	$classifica = array(0, 0, 0, 0, 0);
	if ($i > 0) {
		$totali2 = $totali;
		rsort($totali2);
		$totali2 = array_values(array_unique($totali2));
		for ($j = 0; $j < 5; $j++) {
			if ($totali[$j] == $totali2[0]) {
				$classifica[$j] = 1;
			} else if ($totali[$j] == $totali2[1]) {
				$classifica[$j] = 2;
			} else if ($totali[$j] == $totali2[2]) {
				$classifica[$j] = 3;
			} else if ($totali[$j] == $totali2[3]) {
				$classifica[$j] = 4;
			} else if ($totali[$j] == $totali2[4]) {
				$classifica[$j] = 5;
			}
		}
	}
	
	return array($parziali, $totali, $matgiocatori, $gstat, $stat, $classifica, $codici, $colonne);
	/* Output:
	[0] parziali: matrice [n][5], con n numero di partite
	[1] totali: array[5]
	[2] giocatori: matrice[*][5] con una riga per ogni cambio di giocatore
	[3] gstat: matrice di 10 array associativi, uno per ogni statistica, con 5 o più giocatori al loro interno
		[0] Array[5] con i turni giocati in ogni colonna
		[1] Chiamate vinte
		[2] Chiamate perse
		[3] Chiamate patte
		[4] Chiamate in mano vinte
		[5] Chiamate in mano perse
		[6] Chiamate in mano patte
		[7] Chiamate con cappotto vinte
		[8] Chiamate con cappotto perse
		[9] Punteggio chiamante
		[10] Da socio vinte
		[11] Da socio perse
		[12] Da socio patte
		[13] Da socio con cappotto vinte
		[14] Da socio con cappotto perse
		[15] Punteggio socio
	[4] stat: array di 7 elementi
		[0] Vinte
		[1] Perse
		[2] Patte
		[3] In mano
		[4] Cappotto
		[5] Punteggio minimo
		[6] Punteggio massimo
	[5] classifica: array[5] con la medaglia di ogni colonna
	[6] codici: matrice [n][5], con n numero di partite (chiamante, socio, vittoria, cappotto, Vecia)
	[7] colonne: array[5] di array, con un array associativo per ogni giocatore che vi ha giocato riportante il turno in cui ci è entrato ['inizio'] e il numero di turni ['turni']
	*/
}

$minimomedaglie = 6;
function medaglie($partita) {
	global $minimomedaglie;
	$medaglie = array();
	$classifica = $partita[5];

	// Medaglie assegnate solo se è stato raggiunto il minimo di turni
	if ($partita[4][0] + $partita[4][1] + $partita[4][2] >= $minimomedaglie) {
		$assegnate = array(0, 0, 0, 0, 0);
		$colonne = $partita[7];
		$turnimedaglia = array();
		$iniziomedaglia = array();
		$colonnamedaglia = array();
		$attesa = array(array(), array(), array(), array(), array());

		$i = 1;
		while (count(array_filter($assegnate, function($a) {return $a == 0;})) != 0) {
			for ($j = 0; $j < 5; $j++) {
				if ($assegnate[$j] == 0 && count($colonne[$j]) == $i) { // Se la colonna contiene il numero totale di giocatori che si sta cercando
					$lastmax = 0;
					while (count($colonne[$j]) > 0) {
						// Recupero il giocatore con il maggior numero di turni nella colonna
						$turni = array_column($colonne[$j], 'turni', 'id');
						$max = max($turni);
						$g = array_keys($turni, $max)[0];

						$turnifatti = $colonne[$j][$g]['turni'];
						$iniziocolonna = $colonne[$j][$g]['inizio'];
						// Se ha già ricevuto una medaglia, controllo se deve ottenere invece quella della colonna in esame
						if (isset($medaglie[$g])) {
							if ($turnifatti >= $turnimedaglia[$g] && $iniziocolonna > $iniziomedaglia[$g]) {
								$prec = $colonnamedaglia[$g];

								// Se la colonna ha una lista d'attesa o un'altra medaglia assegnata, posso annullare la medaglia ricevuta
								if (count($attesa[$prec]) > 0 || $assegnate[$prec] > 1) {
									unset($medaglie[$g]);
									unset($turnimedaglia[$g]);
									unset($iniziomedaglia[$g]);
									unset($colonnamedaglia[$g]);
									$assegnate[$prec]--;
									
									// Solo se la colonna aveva solo quella medaglia, richiamo altri nella lista d'attesa
									if ($assegnate[$prec] == 0) {
										// Assegno la medaglia al prossimo (o i prossimi) aventi diritto
										$t = $attesa[$prec][0]['turni'];
										while (count($attesa[$prec]) > 0 && $attesa[$prec][0]['turni'] == $t) {
											$next = array_shift($attesa[$prec]);
											$medaglie[$next['id']] = $classifica[$prec];
											$turnimedaglia[$g] = $next['turni'];
											$iniziomedaglia[$g] = $next['inizio'];
											$colonnamedaglia[$g] = $prec;
											$assegnate[$prec]++;
										}
									}
								}
							}
						}

						// Se non è stata annullata la medaglia precedente il giocatore non concorre per questa
						if (!isset($medaglie[$g])) {
							if ($lastmax == 0) { // Il primo giocatore avente diritto definisce il numero di turni da avere per competere con lui
								$lastmax = $max;
							}

							if ($max == $lastmax) { // Se è il primo giocatore o ha il suo stesso numero di turni, ottiene la medaglia
								$medaglie[$g] = $classifica[$j];
								$turnimedaglia[$g] = $turnifatti;
								$iniziomedaglia[$g] = $iniziocolonna;
								$colonnamedaglia[$g] = $j;
								$assegnate[$j]++;
							} else { // Se questo giocatore ha giocato meno del precedente non concorre, ma finisce nella lista d'attesa della colonna
								$attesa[$j][] = $colonne[$j][$g];
							}
						}
						unset($colonne[$j][$g]);
					}
				}
			}
			$i++;
		}
	}

	return $medaglie;
}

function coppie($partita) {
	$coppie = array();
	$punti = array();

	$giocatori = $partita[2][0];
	foreach ($partita[6] as $i => $cod) {
		// Cambi di giocatori
		if ($i > 0 && isset($partita[2][$i])) {
			for ($j = 0; $j < 5; $j++) {
				if (isset($partita[2][$i][$j])) {
					$giocatori[$j] = $partita[2][$i][$j];
				}
			}
		}
		if ($cod[0] == $cod[1])
			continue;

		if ($giocatori[$cod[0]-1] < $giocatori[$cod[1]-1])
			$c = $giocatori[$cod[0]-1] . '-' . $giocatori[$cod[1]-1];
		else
			$c = $giocatori[$cod[1]-1] . '-' . $giocatori[$cod[0]-1];
		
		if (isset($coppie[$c])) {
			$coppie[$c]++;
		} else {
			$coppie[$c] = 1;
			$punti[$c] = 0;
		}
		$punti[$c] += (2 * ($cod[2] == null ? 0 : ($cod[2] * 2) - 1) * ($cod[3] == 1 ? 2 : 1)) + $cod[4];
	}

	return array($coppie, $punti);
}
?>