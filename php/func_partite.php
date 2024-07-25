<?php

function partita($id) {
	global $conn;
	$row = $conn->query("select * from partite where IdPartita = '$id';")->fetch_assoc();
	
	// Giocatori
	$gioc = $conn->query("select * from partecipazioni where Inizio = 1 and Partita = $id order by Colonna;");
	$giocatori = array(null, null, null, null, null);
	$gstat = array(array(), // [0] turni giocati,
		array(), array(), array(), array(), array(), // [1] Chiamate vinte, [2] perse, [3] patte, [4] in mano, [5] con cappotto
		array(), array(), array(), array()); // [6] Socio vinte, [7] perse, [8] patte, [9] con cappotto
	$matgiocatori = array();
	$matgiocatori[0] = array();
	while ($rowg = $gioc->fetch_assoc()) {
		$giocatori[$rowg['Colonna'] - 1] = $rowg['Giocatore'];
		$matgiocatori[0][$rowg['Colonna'] - 1] = $rowg['Giocatore'];
		for ($z = 0; $z < count($gstat); $z++)
			$gstat[$z][$rowg['Giocatore']] = 0; // Inizializzazione delle statistiche per il giocatore
	}
	
	// Mani e punteggi
	$mani = $conn->query("select * from mani where Partita = $id order by Numero;");
	$cambi = $conn->query("select * from partecipazioni where Partita = $id and Inizio > 1 order by Inizio;");
	$i = 0;
	$parziali = array();
	$totali = array(0, 0, 0, 0, 0);
	$codici = array();
	$rowc = $cambi->fetch_assoc();
	$stat = array(0, 0, 0, 0, 0, 0, 0); //Vinte, perse, patte, in mano, cappotto, min, max
	while ($rowm = $mani->fetch_assoc()) {
		if ($rowc != null && $rowc['Inizio'] == ($i + 1)) {
			$matgiocatori[$i] = array();
			while ($rowc != null && $rowc['Inizio'] == ($i + 1)) {
				$matgiocatori[$i][$rowc['Colonna'] - 1] = $rowc['Giocatore'];
				$giocatori[$rowc['Colonna'] - 1] = $rowc['Giocatore'];
				if (!array_key_exists($rowc['Giocatore'], $gstat[0])) { // Inizializzazione del giocatore, se nuovo in questa partita
					for ($z = 0; $z < count($gstat); $z++)
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
			if ($rowm['Chiamante'] == $rowm['Socio']) {
				$stat[3]++; // In mano
				$gstat[4][$giocatori[$rowm['Chiamante'] - 1]]++;
			}
			$palio2 = 1;
			$vittoria = ($rowm['Vittoria'] == 1 ? 1 : ($rowm['Vittoria'] == 0 ? -1 : 0));
			if ($rowm['Vittoria'] == 1) {
				$stat[0]++; // Vinta
				$gstat[1][$giocatori[$rowm['Chiamante'] - 1]]++;
				if ($rowm['Chiamante'] != $rowm['Socio'])
					$gstat[6][$giocatori[$rowm['Socio'] - 1]]++;
			} else {
				$stat[1]++; // Persa
				$gstat[2][$giocatori[$rowm['Chiamante'] - 1]]++;
				if ($rowm['Chiamante'] != $rowm['Socio'])
					$gstat[7][$giocatori[$rowm['Socio'] - 1]]++;
			}
			if ($rowm['Cappotto'] == 1) {
				$palio *= 2;
				$palio2 *= 2;
				$stat[4]++; // Cappotto
				$gstat[5][$giocatori[$rowm['Chiamante'] - 1]]++;
				if ($rowm['Chiamante'] != $rowm['Socio'])
					$gstat[9][$giocatori[$rowm['Socio'] - 1]]++;
			}
		} else {
			$stat[2]++; // Pareggio
			$gstat[3][$giocatori[$rowm['Chiamante'] - 1]]++;
			if ($rowm['Chiamante'] != $rowm['Socio'])
				$gstat[8][$giocatori[$rowm['Socio'] - 1]]++;
		}
		for ($z = 0; $z < 5; $z++) // Turni giocati
			$gstat[0][$giocatori[$z]]++;
		
		for ($j = 0; $j < 5; $j++) {
			if (($j + 1) == $rowm['Chiamante'])
				$parziali[$i][$j] = $vittoria * $palio;
			else if (($j + 1) == $rowm['Socio'])
				$parziali[$i][$j] = $vittoria * $palio2;
			else
				$parziali[$i][$j] = -1 * $vittoria * $palio2;
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
	
	return array($parziali, $totali, $matgiocatori, $gstat, $stat, $classifica, $codici);
	/* Output:
	[0] parziali: matrice [n][5], con n numero di partite
	[1] totali: array[5]
	[2] giocatori: array[5] con gli ultimi giocatori che hanno giocato
	[3] gstat: matrice di 10 array associativi, uno per ogni statistica, con 5 o più giocatori al loro interno
		[0] Turni giocati
		[1] Chiamate vinte
		[2] Chiamate perse
		[3] Chiamate patte
		[4] Chiamate in mano
		[5] Chiamate con cappotto
		[6] Da socio vinte
		[7] Da socio perse
		[8] Da socio patte
		[9] Da socio con cappotto
	[4] stat: array di 7 elementi
		[0] Vinte
		[1] Perse
		[2] Patte
		[3] In mano
		[4] Cappotto
		[5] Punteggio minimo
		[6] Punteggio massimo
	[5] classifica: array[5] con la posizione di ogni giocatore
	[6] codici: matrice [n][4], con n numero di partite
	*/
}


function mostra_partita($row, $edit) {
	global $conn, $fmt1;
	$id = $row['IdPartita'];
	$partita = partita($id);
	$out = '';

	// Incipit
	$out .= '<div id="partita">';
	$out .= '<div class="row"><div class="col-md-9"><h5 style="text-align: left;">Bi$ca in occasione di: <strong><i id="occasione0">' . $row['Occasione'] . '</i></strong>' . (isset($_SESSION['id']) && $_SESSION['editor'] ? ($edit ? '&nbsp;<button class="btn btn-primary" onclick="info();"><i class="bi bi-pencil-fill"></i></button>&nbsp;<a href="partite.php?id=' . $id . '" class="btn btn-info btn-sm"><i class="bi bi-eye-fill"></i> Torna alla visualizzazione</a>' : '&nbsp;<a href="partite.php?id=' . $id . '&edit=true" class="btn btn-primary btn-sm"><i class="bi bi-pencil-fill"></i> Modifica la partita</a>') : '') . '</h5></div>';
	$out .= '<div class="col-md-3"><h5 style="text-align: right;">' . $fmt1->format(strtotime($row['Data'])) . '</h5><span class="d-none" id="data0">' . date("o-m-d", strtotime($row['Data'])) . '</span></div></div>';
	$out .= '<hr>';
	
	// Giocatori
	$out .= '<div class="sticky-top" style="top: 56; "><div class="row" style="margin: 0px; background: var(--sfondo);"><div class="col-2 pad-alto border border-primary"><h6 style="margin: 0px;">&nbsp;</h6></div>';
	foreach ($partita[2][0] as $i => $idg) {
		$out .= '<div class="col-2 pad-alto border border-start-0 border-primary text-truncate" style="background: var(--sfondo); z-index: 1020; position: relative;">';
		if ($idg == null) {
			if ($edit) {
				$out .= '<button class="btn btn-warning" style="width: 95%;" onclick="primogioc(' . ($i + 1) . ', false);"><i class="bi bi-person-plus-fill"></i></button>';
			}
		} else {
			$nome = nomedi($idg);
			$nomi = nomedi($idg, true);
			if ($edit) {
				$out .= '<button class="btn btn-outline-dark btn-sm atext-truncate" style="width: 100%; padding: 2px 0px;" onclick="primogioc(' . ($i + 1) . ', [\'' . addslashes($nomi[0]) . '\', \'' . addslashes($nomi[1]) . '\']);">&nbsp;<span class="longx">' . $nome . '</span></button>';
			} else {
				$out .= '<h6 style="margin: 0px; overflow-x: hidden;"><a class="longx text-tema text-decoration-none" href="giocatori.php?id=' . $idg . '">' . $nome . '</a></h6>';
			}
		}
		$out .= '</div>';
	}
	$out .= '</div></div>';
	
	// Mani e punteggi
	$totali = array(0, 0, 0, 0, 0);
	foreach ($partita[0] as $i => $parz) {
		// Cambi di giocatori
		if ($i > 0 && isset($partita[2][$i])) {
			$out .= '<div class="sticky-top" style="pointer-events: none; top: 57; z-index: ' . (1030 + $i) . ';"><div class="row" style="margin: 0px;"><div class="col-2 pad-alto border-bottom' . (isset($partita[2][$i][0]) ? ' border-end' : '') . ' border-primary"><h6 style="margin: 0px;">&nbsp;</h6></div>';
			for ($j = 0; $j < 5; $j++) {
				if (isset($partita[2][$i][$j])) {
					$nome = nomedi($partita[2][$i][$j]);
					$nomi = nomedi($partita[2][$i][$j], true);
					$out .= '<div class="col-2 pad-alto border-end border-bottom border-primary text-truncate" style="pointer-events: auto; background: var(--sfondo); position: relative;">';
					if ($edit) {
						$out .= '<button class="btn btn-outline-dark btn-sm atext-truncate" style="width: 100%; padding: 2px 0px;" onclick="modalannullacambio(' . ($i + 1) . ', ' . ($j + 1) . ', [\'' . addslashes($nomi[0]) . '\', \'' . addslashes($nomi[1]) . '\']);">&nbsp;<span class="longx">' . $nome . '</span></button>';
					} else {
						$out .= '<h6 style="margin: 0px;"><a class="longx text-tema text-decoration-none" href="giocatori.php?id=' . $partita[2][$i][$j] . '">' . $nome . '</a></h6>';
					}
					$out .= '</div>';
				} else {
					$out .= '<div class="col-2 border-bottom' . (isset($partita[2][$i][$j + 1]) != 0 ? ' border-end' : '') . ' border-primary pad-alto"></div>';
				}
			}
			$out .= '</div></div>';
		}
		$out .= '<div class="row" style="margin: 0px;"><div class="col-2 border border-top-0 border-primary pad-alto">' . ($edit ? '<button class="btn btn-primary no-pad" style="width: 90%;" onclick="turno(' . ($i + 1) . ');">' : '') . '<i class="bi bi-hash"></i>' . ($i + 1) . ($edit ? '</button>' : '') . '</div>';
		for ($j = 0; $j < 5; $j++) {
			$totali[$j] += $partita[0][$i][$j];
			$parz = ($partita[0][$i][$j] > 0 ? '+' : '') . $partita[0][$i][$j];
			$tot = ($totali[$j] > 0 ? '+' : '') . $totali[$j];

			$sfondo = '';
			if ($partita[6][$i][0] == ($j + 1)) { // È il chiamante
				if ($partita[6][$i][1] == ($j + 1)) { // Si è autochiamato
					if ($partita[6][$i][2] == 1 || $partita[6][$i][2] == null) { // Vittoria o pareggio
						$sfondo = 'cerchi_luce2';
					} else { // Sconfitta
						$sfondo = 'cimitero';
					}
				} else { // Chiamata normale
					if ($partita[6][$i][2] == 1 || $partita[6][$i][2] == null) { // Vittoria o pareggio
						$sfondo = 'fuoco2';
					} else { // Sconfitta
						$sfondo = 'fuoco_blu2';
					}
				}
			} else if ($partita[6][$i][1] == ($j + 1)) { // È il socio
				if ($partita[6][$i][4] == 1) { // Vecia
					$sfondo = 'fulmini2';
				} else {
					$sfondo = 'cerchi_verdi2';
				}
			}
			$out .= '<div class="col-2 border-end border-bottom border-primary sfondo"' . (!empty($sfondo) ? ' style="background-image: url(\'img/gif/' . $sfondo . '.gif\');"' : '') . '>';

				$out .= '<div class="row d-none d-md-flex">';
					$out .= '<div class="col-4 bordo4 pad-alto small pt-1 sfondo"' . (!empty($sfondo) ? ' style="background-image: url(\'img/gif/' . $sfondo . '.gif\');"' : '') . '><i class="d-block' . (!empty($sfondo) ? ' ptsfondo' . ($partita[6][$i][3] == 1 ? ' ptcappotto' : '') : '') . '">' . $parz . '</i></div>';
					$out .= '<div class="col-8 pad-alto" style="font-size: 20px; background-color: var(--sfondo);"><strong>' . $tot . '</strong></div>';
				$out .= '</div>';
				$out .= '<div class="d-md-none">';
					$out .= '<span class="parziale pad-alto' . (!empty($sfondo) ? ' ptsfondo' . ($partita[6][$i][3] == 1 ? ' ptcappotto' : ''): '') . '" style="display: ' . (isset($_COOKIE['parziali']) ? ($_COOKIE['parziali'] == 'true' ? 'block': 'none') : 'block') . ';"><i>' . $parz . '</i></span>';
					$out .= '<span class="totale pad-alto' . (!empty($sfondo) ? ' ptsfondo' . ($partita[6][$i][3] == 1 ? ' ptcappotto' : '') : '') . '" style="display: ' . (isset($_COOKIE['parziali']) ? ($_COOKIE['parziali'] == 'false' ? 'block': 'none') : 'none') . ';">' . $tot . '</span>';
				$out .= '</div>';

			$out .= '</div>';
		}
		$out .= '</div>';
	}
	
	// Conclusioni
	if (count($partita[0]) > 0) {
		$out .= '<div class="row" style="margin: 0px;"><div class="col-2 bordog pad-alto" style="font-size: 20px;"><span class="d-sm-none"><strong>Tot.</strong></span><span class="d-none d-sm-block"><strong>Totale</strong></span></div>';
		for ($j = 0; $j < 5; $j++) {
			$out .= '<div class="col-2 bordo2g pad-alto" style="font-size: 20px;"><strong>' . ($totali[$j] > 0 ? '+' : '') . $totali[$j] . '</strong></div>';
		}
		$out .= '</div>';
		$out .= '<div class="sticky-top" style="top: ' . ($edit ? 87 : 79) . ';"><div class="row" style="margin: 0px;"><div class="col-2"></div>';
		for ($j = 0; $j < 5; $j++) {
			$out .= '<div class="col-2 no-pad"><img src="img/Medaglia' . $partita[5][$j] . '.png" height=40px></div>';
		}
		$out .= '</div></div>';
		$out .= '<div class="form-check form-switch d-md-none" style="text-align: left;"><br><input class="form-check-input" type="checkbox" value="" id="cparziali" onchange="parziali(this);"' . (isset($_COOKIE['parziali']) ? ($_COOKIE['parziali'] == 'true' ? 'checked=""': '') : 'checked=""') . '><label class="form-check-label" for="cparziali">Punteggi parziali</label></div>';
		$out .= checkalias();
	}
	
	if ($edit) {
		$out .= '<br><div class="row"><div class="col-lg-2"></div><div class="col-sm-6 col-lg-4"><button class="btn btn-lg btn-primary mb-1" style="width: 95%;" onclick="turno();"><i class="bi bi-patch-plus-fill"></i> Nuovo turno</button></div>';
		$out .= '<div class="col-sm-6 col-lg-4"><button class="btn btn-lg btn-warning mb-1" style="width: 95%;" onclick="cambio();"><i class="bi bi-person-plus-fill"></i> Cambio giocatore</button></div><div class="col-lg-2"></div></div><br>';
	}
	
	// Note e foto
	$out .= '<br>';
	$foto = false;
	if (isset($_SESSION['id'])) {
		$outf = '';
		$files;
		if ($files = listafoto($id)) {
			if (count($files) > 0) {
				$foto = true;
				for ($i = 0; $i < count($files); $i++) {
					$outf .= '<div class="carousel-item' . ($i == 0 ? ' active' : '') . '"><img src="foto/' . $id . '/' . $files[$i] . '" class="d-block" style="max-height: 50vh; max-width: 100%;"></div>';
				}
				$outf = '<h3>Foto ricordo</h3><div id="carousel" class="carousel slide" style="padding: 10px; border: 1px solid #8f8f8f;"><div id="carousel-inner" class="carousel-inner" style="background-image: linear-gradient(#d1d1d1, #8f8f8f);">' . $outf . '</div>';
				if (count($files) > 1)
					$outf .= '<button class="carousel-control-prev" type="button" data-bs-target="#carousel" data-bs-slide="prev">
						<span class="carousel-control-prev-icon" aria-hidden="true"></span>
						<span class="visually-hidden">Precedente</span>
						</button>
						<button class="carousel-control-next" type="button" data-bs-target="#carousel" data-bs-slide="next">
						<span class="carousel-control-next-icon" aria-hidden="true"></span>
						<span class="visually-hidden">Successiva</span>
						</button>';
				$outf .= '</div>';
			}
		}
	}
	$note = (!empty($row['Note']) ? '<h3>Note sulle giuocate</h3><p id="note0" style="text-align: justify;">' . $row['Note'] . '</p>' : '<span id="note0"></span>');
	
	$out .= '<div class="row">';
	if ($foto && !empty($row['Note'])) {
		$out .= '<div class="col-lg-8">' . $note . '</div><div class="col-lg-4">' . $outf . '</div>';
	} else if ($foto) {
		$out .= '<div class="col-lg-3"></div><div class="col">' . $outf . $note . '</div><div class="col-lg-3"></div>';
	} else {
		$out .= '<div class="col-lg-2"></div><div class="col">' . $note . '</div><div class="col-lg-2"></div>';
	}
	
	$out .= '</div>';
	$out .= '<div class="row"><div class="col-lg-2"></div><div class="col">';
	
	// Statistiche
	if (count($partita[0]) > 1) {
		$chiamanti = array();
		$soci = array();
		foreach ($partita[3][0] as $k => $v) {
			$chiamanti[$k] = 0;
			$soci[$k] = 0;
		}
		for ($z = 1; $z <= 3; $z++)
			foreach ($partita[3][$z] as $k => $v)
				$chiamanti[$k] += $v;
		for ($z = 6; $z <= 8; $z++)
			foreach ($partita[3][$z] as $k => $v)
				$soci[$k] += $v;
		arsort($chiamanti);
		arsort($soci);
		$out .= '<br><h3>Statistiche</h3><div class="row" style="text-align: left;">';
		$out .= '<div class="col-sm-3"><h5><i class="bi bi-chat-dots"></i> Chiamate</h5><p style="text-align: justify;">Vinte: <strong>' . $partita[4][0] . '</strong><br>Perse: <strong>' . $partita[4][1] . '</strong><br>Patte: <strong>' . $partita[4][2] . '</strong><br>In mano: <strong>' . $partita[4][3] . '</strong><br>Con cappotto: <strong>' . $partita[4][4] . '</strong></p></div>';
		$out .= '<div class="col-sm-3"><h5><i class="bi bi-chevron-expand"></i> Estremi dei punteggi</h5><p style="text-align: justify;">Massimo: <strong>+' . $partita[4][6] . '</strong><br>Minimo: <strong>' . $partita[4][5] . '</strong></p></div>';
		$out .= '<div class="col-sm-3"><h5><i class="bi bi-award"></i> Chiamanti più arditi</h5><p style="text-align: justify;">';
			foreach ($chiamanti as $k => $v) {
				$out .= nomedi($k) . ': <strong>' . $v . '</strong><br>';
			}
			$out .= '</p></div>';
		$out .= '<div class="col-sm-3"><h5><i class="bi bi-compass"></i> Soci più ambiti</h5><p style="text-align: justify;">';
			foreach ($soci as $k=>$v) {
				$out .= nomedi($k) . ': <strong>' . $v . '</strong><br>';
			}
			$out .= '</p></div>';
		$out .= '</div>';
	}
	$out .= '</div><div class="col-lg-2"></div></div>';
	$out .= '</div>';
	
	return $out;
}
?>