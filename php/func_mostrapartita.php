<?php

function mostra_partita($id, $edit, $nuovariga = false) {
	global $conn, $fmt1, $minimomedaglie;
	$res = $conn->query("SELECT * FROM partite WHERE IdPartita = $id;");
	$row = $res->fetch_assoc();
	$partita = partita($id);
	$out = '';

	// Incipit
	$out .= '<div class="row"><div class="col-md-9"><h5 style="text-align: left;"><small>Bi$ca in occasione di:</small><br /><strong><i id="occasione0">' . $row['Occasione'] . '</i></strong>' . ($edit ? '&nbsp;<button class="btn btn-primary btn-sm" onclick="info();"><i class="bi bi-pencil-fill"></i></button>' : '') . '</h5></div>';
	$out .= '<div class="col-md-3 mt-auto"><h5 style="text-align: right;">' . $fmt1->format(strtotime($row['Data'])) . '</h5><span class="d-none" id="data0">' . date("o-m-d", strtotime($row['Data'])) . '</span></div></div>';
	$out .= '<hr>';
	
	// Giocatori
	$out .= '<div style="position: relative;">';
	$out .= '<div class="sticky-top" style="top: 55px; z-index: 100;"><div class="row m-0" style="background: var(--sfondo);">';
	
	// Prima cella della prima riga dei giocatori: eventuale bottone di toggle degli orari se previsti
	$orari = false;
	foreach ($partita[6] as $codice) {
		if ($codice[5] != null) {
			$orari = true;
			break;
		}
	}
	$out .= '<div class="col-2 col-sm-1 border-end border-primary ps-0 pe-2 text-center" style="height: ' . ($edit ? 31 : 23) . 'px;">';
	if ($orari) {
		$out .= '<input type="checkbox" class="btn-check" id="toggle_orario" autocomplete="off" onclick="toggleOrari($(this).prop(\'checked\'));"' . (isset($_COOKIE['orari']) && $_COOKIE['orari'] == 'true' ? ' checked=""' : '') . '><label for="toggle_orario" class="btn btn-sm btn-outline-primary' . (!$edit ? ' py-0' : '') . '"' . (!$edit ? ' style="margin-top: -5px;"' : '') . '><i class="bi bi-clock"></i></label>';
	} else {
		$out .= '<h6 style="margin: 0px;">&nbsp;</h6>';
	}
	$out .= '</div>';

	foreach ($partita[2][0] as $i => $idg) {
		$out .= '<div class="col pad-alto border border-start-0 border-primary text-truncate" style="background: var(--sfondo); position: relative; height: ' . ($edit ? 31 : 23) . 'px;">';
		if ($idg == null) {
			if ($edit) {
				$out .= '<button class="btn btn-warning p-0" style="width: 95%;" onclick="primogioc(' . ($i + 1) . ', false);"><i class="bi bi-person-plus-fill"></i></button>';
			}
		} else {
			$nome = nomedi($idg);
			$nomi = nomedi($idg, true);
			if ($edit) {
				$out .= '<button class="btn btn-outline-dark btn-sm" style="width: 100%; padding: 2px 0px;" onclick="primogioc(' . ($i + 1) . ', [\'' . addslashes($nomi[0]) . '\', \'' . addslashes($nomi[1]) . '\']);">&nbsp;<span class="longx">' . $nome . '</span></button>';
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
			$out .= '<div class="sticky-top" style="pointer-events: none; top: 55px; z-index: ' . (100 + $i) . '; margin-top: 1px;"><div class="row m-0"><div class="col-2 col-sm-1 pad-alto' . (isset($partita[2][$i][0]) ? ' border-end' : '') . ' border-primary"><h6 style="margin: 0px;">&nbsp;</h6></div>';
			for ($j = 0; $j < 5; $j++) {
				if (isset($partita[2][$i][$j])) {
					$nome = nomedi($partita[2][$i][$j]);
					$nomi = nomedi($partita[2][$i][$j], true);
					$out .= '<div class="col pad-alto border-end border-bottom border-primary text-truncate" style="pointer-events: auto; background: var(--sfondo); position: relative; height: ' . ($edit ? 31 : 23) . 'px;">';
					if ($edit) {
						$out .= '<button class="btn btn-outline-dark btn-sm" style="width: 100%; padding: 2px 0px;" onclick="modalannullacambio(' . ($i + 1) . ', ' . ($j + 1) . ', [\'' . addslashes($nomi[0]) . '\', \'' . addslashes($nomi[1]) . '\']);">&nbsp;<span class="longx">' . $nome . '</span></button>';
					} else {
						$out .= '<h6 style="margin: 0px;"><a class="longx text-tema text-decoration-none" href="giocatori.php?id=' . $partita[2][$i][$j] . '">' . $nome . '</a></h6>';
					}
					$out .= '</div>';
				} else {
					$out .= '<div class="col border-bottom' . (isset($partita[2][$i][$j + 1]) != 0 ? ' border-end' : '') . ' border-primary pad-alto" style="height: ' . ($edit ? 31 : 23) . 'px;">&nbsp;</div>';
				}
			}
			$out .= '</div></div>';
		}
		$out .= '<div id="riga' . ($i + 1) . '" class="row m-0 rigatabella' . ($nuovariga == $i + 1 ? ' nuovariga' : '') . '">';
		
		// Indice di turno o orario
		$out .= '<div class="col-2 col-sm-1 border-end border-primary pad-alto pe-2"><span class="index_turno text-end" style="display: ' . ($orari && isset($_COOKIE['orari']) && $_COOKIE['orari'] == 'true' ? 'none' : 'block') . ';">' . ($edit ? '<button class="btn btn-primary no-pad" style="width: 90%;" onclick="turno(' . ($i + 1) . ');">' : '') . '<i class="bi bi-hash"></i>' . ($i + 1) . ($edit ? '</button>' : '') . '</span>';
		$out .= '<span class="orario_turno" style="display: ' . ($orari && isset($_COOKIE['orari']) && $_COOKIE['orari'] == 'true' ? 'block' : 'none') . ';">' . ($partita[6][$i][5] != null ? substr($partita[6][$i][5], 0, 5) : '--:--') . '</span>';
		$out .= '</div>';
		for ($j = 0; $j < 5; $j++) {
			$totali[$j] += $partita[0][$i][$j];
			$parz = ($partita[0][$i][$j] > 0 ? '+' : '') . $partita[0][$i][$j];
			$tot = ($totali[$j] > 0 ? '+' : '') . $totali[$j];

			$sfondo = '';
			$suono = '';
			if ($partita[6][$i][0] == ($j + 1)) { // È il chiamante
				if ($partita[6][$i][1] == ($j + 1)) { // Si è autochiamato
					if ($partita[6][$i][2] == 1 || $partita[6][$i][2] == null) { // Vittoria o pareggio
						$sfondo = 'cerchi_luce2';
						$suono = '+4';
					} else { // Sconfitta
						$sfondo = 'cimitero';
						$suono = '-4';
					}
				} else { // Chiamata normale
					if ($partita[6][$i][2] == 1 || $partita[6][$i][2] == null) { // Vittoria o pareggio
						$sfondo = 'fuoco2';
						$suono = 'chiamatavinta';
					} else { // Sconfitta
						$sfondo = 'fuoco_blu2';
						$suono = 'chiamatapersa';
					}
				}
			} else if ($partita[6][$i][1] == ($j + 1)) { // È il socio
				if ($partita[6][$i][4] == 1) { // Vecia
					$sfondo = 'fulmini2';
					$suono = 'vecia';
				} else {
					$sfondo = 'cerchi_verdi2';
					$suono = 'socio';
				}
			}
			if ($partita[6][$i][3] == 1 && $suono != '+4' && $suono != '-4' && $suono != 'vecia') { // Cappotto
				$suono = 'cappotto';
			}

			$out .= '<div class="col border-end border-bottom border-primary sfondo' . (!empty($sfondo) ? ' eggcasella" style="background-image: url(\'media/img/gif/' . $sfondo . '.gif\'); cursor: pointer; height: inherit; overflow: hidden;" onclick="tableEgg(this, \'' . $suono . '\');"' : '"') . '>';

				$out .= '<div class="row d-none d-md-flex" style="height: inherit;">';
					$out .= '<div class="col-4 bordo4 pad-alto small pt-1 sfondo"' . (!empty($sfondo) ? ' style="background-image: url(\'media/img/gif/' . $sfondo . '.gif\');"' : '') . '><i class="d-block' . (!empty($sfondo) ? ' ptsfondo' . ($partita[6][$i][3] == 1 ? ' ptcappotto' : '') : '') . '">' . $parz . '</i></div>';
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

	// Mostra eventuali cambi giocatori già in programma
	$turni = count($partita[0]);
	if ($edit && $turni > 0) {
		$gioc = $conn->query("SELECT * FROM partecipazioni WHERE Partita = $id AND Inizio > " . $turni . " ORDER BY Inizio, Colonna;");

		$rowg = $gioc->fetch_assoc();
		while ($rowg != null) {
			$out .= '<div class="sticky-top" style="pointer-events: none; top: 55px; z-index: ' . (100 + $turni + $rowg['Inizio']) . '; margin-top: 1px;"><div class="row m-0"><div class="col-2 col-sm-1 pad-alto' . ($rowg['Colonna'] == 1 ? ' border-end' : '') . ' border-primary"><h6 style="margin: 0px;">&nbsp;</h6></div>';
			$actualrow = $rowg['Inizio'];
			for ($j = 0; $j < 5; $j++) {
				if ($rowg != null && $rowg['Colonna'] == $j + 1 && $actualrow == $rowg['Inizio']) {
					$nome = nomedi($rowg['Giocatore']);
					$nomi = nomedi($rowg['Giocatore'], true);
					$out .= '<div class="col pad-alto border-end border-bottom border-primary text-truncate" style="pointer-events: auto; background: var(--sfondo); position: relative; height: ' . 31 . 'px;">';
					$out .= '<button class="btn btn-outline-dark btn-sm atext-truncate" style="width: 100%; padding: 2px 0px;" onclick="modalannullacambio(' . $rowg['Inizio'] . ', ' . $rowg['Colonna'] . ', [\'' . addslashes($nomi[0]) . '\', \'' . addslashes($nomi[1]) . '\']);">&nbsp;<span class="longx">' . $nome . '</span></button>';
					$out .= '</div>';

					$rowg = $gioc->fetch_assoc();
				} else {
					$out .= '<div class="col border-bottom' . ($rowg != null && $rowg['Colonna'] == $j + 2 ? ' border-end' : '') . ' border-primary pad-alto" style="height: ' . 31 . 'px;">&nbsp;</div>';
				}
			}
			$out .= '</div></div>';
		}
	}
	
	// Conclusioni
	if (count($partita[0]) > 0) {
		$out .= '<div class="row" style="margin: 0px;"><div class="col-2 col-sm-1 bordog pad-alto pe-2 text-end" style="font-size: 20px;"><span class="d-md-none"><strong>Tot.</strong></span><span class="d-none d-md-block"><strong>Totale</strong></span></div>';
		for ($j = 0; $j < 5; $j++) {
			$out .= '<div class="col bordo2g pad-alto" style="font-size: 20px;"><strong>' . ($totali[$j] > 0 ? '+' : '') . $totali[$j] . '</strong></div>';
		}
		$out .= '</div>';
	}
	$out .= '</div>';
	if (count($partita[0]) > 0) {
		$out .= '<div class="row" style="margin: 0px;"><div class="col-2 col-sm-1"></div>';

		for ($j = 0; $j < 5; $j++) {
			$out .= '<div class="col no-pad"><img src="media/img/Medaglia' . $partita[5][$j] . '.png" height="40px"' . (count($partita[0]) < $minimomedaglie ? ' class="img-bn"' : ' onclick="suonomedaglia(' . $partita[5][$j] . ');"') . '></div>';
		}
		$out .= '</div>';
		$out .= '<div class="row mt-4"><div class="col-lg-2"></div><div class="col">';
		$out .= '<div class="form-check form-switch d-md-none" style="text-align: left;"><input class="form-check-input" type="checkbox" value="" id="cparziali" onchange="parziali(this);"' . (isset($_COOKIE['parziali']) ? ($_COOKIE['parziali'] == 'true' ? 'checked=""': '') : 'checked=""') . '><label class="form-check-label" for="cparziali">Punteggi parziali</label></div>';
		$out .= checkalias();
		$out .= '</div><div class="col-auto">';
		$out .= '</div><div class="col-lg-2"></div></div>';
	}
	
	if ($edit) {
		$out .= '<div class="row my-3"><div class="col-lg-2"></div><div class="col-sm-6 col-lg-4"><button class="btn btn-lg btn-primary mb-1" style="width: 95%;" onclick="turno();"><i class="bi bi-patch-plus-fill"></i> Nuovo turno</button></div>';
		$out .= '<div class="col-sm-6 col-lg-4"><button class="btn btn-lg btn-warning mb-1" style="width: 95%;" onclick="cambio();"><i class="bi bi-person-plus-fill"></i> Cambio giocatore</button></div><div class="col-lg-2"></div></div>';
	}
	
	// Note e foto
	$outf = carousel_foto($id);
	$foto = !empty($outf);
	$outf = '<h3 class="text-primary">Foto ricordo</h3>' . $outf;

	$note = (!empty($row['Note']) ? '<h3 class="text-primary">Note sulle giuocate</h3><p id="note0" style="text-align: justify;">' . $row['Note'] . '</p>' : '<span id="note0"></span>');
	
	$out .= '<div class="row mt-4">';
	if ($foto && !empty($row['Note'])) {
		$out .= '<div class="col-lg-8">' . $note . '</div><div class="col-lg-4">' . $outf . '</div>';
	} else if ($foto) {
		$out .= '<div class="col-lg-3"></div><div class="col">' . $outf . $note . '</div><div class="col-lg-3"></div>';
	} else {
		$out .= '<div class="col-lg-2"></div><div class="col">' . $note . '</div><div class="col-lg-2"></div>';
	}
	
	$out .= '</div>';
	$out .= '<div class="row"><div class="col-lg-2"></div><div class="col">';
	
	if (count($partita[0]) > 1) {
		$out .= '<h3 class="text-primary mt-4">Classifiche</h3><hr><div class="row" style="text-align: left;">';

		// Medaglie
		if (count($partita[0]) >= $minimomedaglie) {
			$out .= '<div class="col-sm mb-4">' . mostra_medaglie($partita) . '</div>';
		}

		// Chiamate
		$out .= '<div class="col-sm mb-4"><h5><i class="bi bi-chat-dots"></i> Chiamate</h5><p style="text-align: justify;">Vinte: <strong>' . $partita[4][0] . '</strong><br>Perse: <strong>' . $partita[4][1] . '</strong><br>Patte: <strong>' . $partita[4][2] . '</strong><br>In mano: <strong>' . $partita[4][3] . '</strong><br>Con cappotto: <strong>' . $partita[4][4] . '</strong></p></div>';

		// Estremi dei punteggi
		$out .= '<div class="col-sm"><h5><i class="bi bi-chevron-expand"></i> Estremi dei punteggi</h5><p style="text-align: justify;">Massimo: <strong>+' . $partita[4][6] . '</strong><br>Minimo: <strong>' . $partita[4][5] . '</strong></p></div>';

		$out .= '</div>';

		$out .= '<hr class="mt-0"><div class="row" style="text-align: left;">';
		$out .= mostra_chiamantisoci($partita[3]);
		$out .= '</div>';
		
		$coppie = mostra_coppie($partita[2], $partita[6]);
		if (!empty($coppie)) {
			$out .= '<hr><div class="row" style="text-align: left;">';
			$out .= $coppie;
			$out .= '</div>';
		}
	}
	$out .= '</div><div class="col-lg-2"></div></div>';
	
	return $out;
}

function mostra_chiamantisoci($gstat, $divisi = false) {
	$out1 = '';
	$out2 = '';
	$chiamanti = array();
	$soci = array();

	$bestc = array();
	$bestc_score = 0;
	$bests = array();
	$bests_score = 0;

	foreach ($gstat[1] as $k => $v) {
		$chiamanti[$k] = $gstat[1][$k] + $gstat[2][$k] + $gstat[3][$k];
		$soci[$k] = $gstat[10][$k] + $gstat[11][$k] + $gstat[12][$k];
		
		if ($chiamanti[$k] > 1) {
			if ($gstat[9][$k] > $bestc_score) {
				$bestc = array($k);
				$bestc_score = $gstat[9][$k];
			} else if ($gstat[9][$k] == $bestc_score) {
				$bestc[] = $k;
			}
		}
		
		if ($soci[$k] > 1) {
			if ($gstat[15][$k] > $bests_score) {
				$bests = array($k);
				$bests_score = $gstat[15][$k];
			} else if ($gstat[15][$k] == $bests_score) {
				$bests[] = $k;
			}
		}
	}
	arsort($chiamanti);
	arsort($soci);
	if (count($bestc) > 2)
		$bestc = array();
	if (count($bests) > 2)
		$bests = array();


	$out1 .= '<div class="col-sm mb-4" id="chiamanti" style="position: relative;"><h5><i class="bi bi-trophy-fill"></i> Chiamanti più arditi</h5><p class="mb-0">';
		$prec = null;
		foreach ($chiamanti as $k => $v) {
			if ($v == $prec) {
				$out1 .= ', ';
			} else {
				$prec = $v;
				$out1 .= '</p><p style="padding-left: 2em; text-indent: -1em; text-align: left;" class="mb-1"><i><i class="bi bi-dot"></i>' . ($v == 1 ? '<strong>1</strong> chiamata' : ($v == 0 ? '<strong>Pavidi</strong>' : '<strong>' . $v . '</strong> chiamate')) . ':</i> ';
			}

			$best = in_array($k, $bestc);
			$tooltip = gettooltip($gstat, $k, false, $gstat[9][$k], $best);

			$out1 .= '<span href="#" style="white-space: nowrap;"' . (!empty($tooltip) ? ' data-bs-toggle="tooltip" data-bs-title="' . $tooltip . '" data-container="#chiamanti"' : '') . '>' . ($best ? '<u>' : '') . nomedi($k) . ($best ? '</u>' : '') . '</span>';
		}
	$out1 .= '</p></div>';
	
	$out2 .= '<div class="col-sm" id="soci" style="position: relative;"><h5><i class="bi bi-incognito"></i> Soci più ambiti</h5><p class="mb-0">';
		foreach ($soci as $k => $v) {
			if ($v == $prec) {
				$out2 .= ', ';
			} else {
				$prec = $v;
				$out2 .= '</p><p style="padding-left: 2em; text-indent: -1em; text-align: left;" class="mb-1"><i><i class="bi bi-dot"></i>' . ($v == 1 ? '<strong>1</strong> alleanza' : ($v == 0 ? '<strong>Dissidenti</strong>' : '<strong>' . $v . '</strong> alleanze')) . ':</i> ';
			}
			
			$best = in_array($k, $bests);
			$tooltip = gettooltip($gstat, $k, true, $gstat[15][$k], $best);

			$out2 .= '<span style="white-space: nowrap;"' . (!empty($tooltip) ? ' data-bs-toggle="tooltip" data-bs-title="' . $tooltip . '" data-container="#soci"' : '') . '>' . ($best ? '<u>' : '') . nomedi($k) . ($best ? '</u>' : '') . '</span>';
		}
	$out2 .= '</p></div>';

	return ($divisi ? array($out1, $out2) : $out1 . $out2);
}

function mostra_coppie($cambi, $codici, $posizioni_podio = 2) {
	$out = '';
	$coppie = coppie($cambi, $codici);
	arsort($coppie[1]);
	$migliori = array();
	$m = 0;
	$peggiori = array();
	$p = 0;
	$last = null;

	$partecipazioni_necessarie = 2;

	foreach ($coppie[1] as $coppia => $punti) {
		if ($coppie[0][$coppia] >= $partecipazioni_necessarie) {
			if ($punti > 0) {
				if ($m < $posizioni_podio || $punti == $last) {
					$migliori[] = array($coppia, $punti);
					if ($punti != $last) {
						$last = $punti;
						$m++;
					}
				}
			} else if ($punti < 0) {
				if ($p < $posizioni_podio || $punti == $last) {
					$peggiori[] = array($coppia, $punti);
					if ($punti != $last) {
						$last = $punti;
						$p++;
					}
				}
			}
		}
	}
	$peggiori = array_reverse($peggiori);

	if ($m > 0) {
		$out .= '<div class="col-sm mb-4" id="miglioricoppie" style="position: relative;"><h5><i class="bi bi-arrow-through-heart"></i> Migliori coppie</h5><p style="text-align: left;">';
		foreach ($migliori as $i => $coppia) {
			$giocatori = explode('-', $coppia[0]);
			$tooltip = tooltip_coppia($coppie, $coppia[0]);

			$out .= ($i != 0 ? '<br>' : '') . '<i class="bi bi-dot"></i><span data-bs-toggle="tooltip" data-bs-title="' . $tooltip . '" data-container="#miglioricoppie">' . nomedi($giocatori[0]) . ' <i class="bi bi-x"></i> ' . nomedi($giocatori[1]) . '</span>';
			if (!isset($migliori[$i+1]) || $migliori[$i+1][1] != $coppia[1])
				$out .= '<br>&emsp;&emsp;<i>Punteggio:</i> <strong>' . punti($coppia[1]) . '</strong>';
		}
		$out .= '</p></div>';
	}

	if ($p > 0) {
		$out .= '<div class="col-sm" id="peggioricoppie" style="position: relative;"><h5><i class="bi bi-heartbreak"></i> Peggiori coppie</h5><p style="text-align: left;">';
		foreach ($peggiori as $i => $coppia) {
			$giocatori = explode('-', $coppia[0]);
			$tooltip = tooltip_coppia($coppie, $coppia[0]);

			$out .= ($i != 0 ? '<br>' : '') . '<i class="bi bi-dot"></i><span data-bs-toggle="tooltip" data-bs-title="' . $tooltip . '" data-container="#peggioricoppie">' . nomedi($giocatori[0]) . ' <i class="bi bi-x"></i> ' . nomedi($giocatori[1]) . '</span>';
			if (!isset($peggiori[$i+1]) || $peggiori[$i+1][1] != $coppia[1])
				$out .= '<br>&emsp;&emsp;<i>Punteggio:</i> <strong>' . punti($coppia[1]) . '</strong>';
		}
		$out .= '</p></div>';
	}

	return $out;
}

function mostra_medaglie($partita) {
	$medaglie = medaglie($partita);
	$altri = $partita[3][0];
	$solopartecipanti = true;
	$out = '';

	if (count($medaglie) > 0) {
		$solopartecipanti = false;
		$out .= '<h5><i class="bi bi-award"></i> Medaglie</h5><p style="text-align: justify;">';
		$totali = $partita[1];
		$colonne = $partita[5];
		while (count($medaglie) > 0) {
			$min = min($medaglie);
			$gg = array_keys($medaglie, $min);
			$score = $totali[array_search($min, $colonne)];
			foreach ($gg as $g) {
				$out .= '<span class="badge bg-' . ($score >= 0 ? 'success' : 'danger') . ' ps-0 mb-1 text-end" style="position: relative; width: 52px;" onclick="suonomedaglia(' . $min . ');">';
				$out .= '<img src="media/img/Medaglia' . $min . '.png" height="25px" style="position: absolute; top: 0; left: 0; filter: drop-shadow(1px 2px 2px var(--ombra));" />';
				$actualscore = $partita[3][16][$g];
				$out .= (($score > 0 ? '+' : '') . $score) . '</span>&nbsp;<a href="giocatori.php?id=' . $g . '">' . nomedi($g) . '</a>' . ($actualscore != $score ? ' <small>(' . ($actualscore > 0 ? '+' : '') . $actualscore . ')</small>' : '') . '<br>';
				unset($medaglie[$g]);
				unset($altri[$g]);
			}
		}
		$out .= '</p>';
	}

	if (count($altri) > 0) {
		$score = array();
		foreach($altri as $g => $turni) {
			$altri[$g] = array_sum($turni);
			$score[$g] = $partita[3][16][$g];
		}
		$out .= '<h5><i class="bi bi-people-fill"></i> ' . ($solopartecipanti ? 'Partecipanti' : 'Altri partecipanti') . '</h5><p class="text-justify">';
		while (count($altri) > 0) {
			$maxs = max($score);
			$g = array_keys($score, $maxs)[0];
			$turni = $altri[$g];
			$out .= $turni . ' <i class="bi bi-play-fill"></i>&nbsp;<a href="giocatori.php?id=' . $g . '">' . nomedi($g) . '</a> <small>(' . (($maxs > 0 ? '+' : '') . $maxs) . ')</small><br>';
			unset($altri[$g]);
			unset($score[$g]);
		}
		$out .= '</p>';
	}
	return $out;
}

function gettooltip($gstat, $g, $socio, $score, $best) {
	$offsetsocio = $socio ? 9 : 0;
	$vinte = $gstat[$offsetsocio + 1][$g];
	$perse = $gstat[$offsetsocio + 2][$g];
	$patte = $gstat[$offsetsocio + 3][$g];
	
	$tooltip = '';
	$tooltip .= '<div class=\'text-start\'>';
	$tooltip .= ($vinte > 0 ? '<i class=\'bi bi-hand-thumbs-up\'></i> ' . $vinte . ' vint' . ($vinte == 1 ? 'a' : 'e') . '<br>' : '');
	$tooltip .= ($perse > 0 ? '<i class=\'bi bi-hand-thumbs-down\'></i> ' . $perse . ' pers' . ($perse == 1 ? 'a' : 'e') . '<br>' : '');
	$tooltip .= ($patte > 0 ? '<i class=\'bi bi-dot\'></i> ' . $patte . ' patt' . ($patte == 1 ? 'a' : 'e') . '<br>' : '');
	if (!$socio) {
		$mano = $gstat[4][$g] + $gstat[5][$g] + $gstat[6][$g];
		$tooltip .= ($mano > 0 ? '<i class=\'bi bi-star-fill\'></i> ' . $mano . ' in mano<br>' : '');
	}

	$offsetsocio = $socio ? 6 : 0;
	$cappotto = $gstat[$offsetsocio + 7][$g] + $gstat[$offsetsocio + 8][$g];
	$tooltip .= ($cappotto > 0 ? '<i class=\'bi bi-star-fill\'></i> ' . $cappotto . ' cappotto<br>' : '');

	$tooltip .= '</div>';
	if ($tooltip != '<div class=\'text-start\'></div>') {
		$tooltip .= '<hr class=\'my-1\'>' . punti($score) . ' punt' . (abs($score) == 1 ? 'o' : 'i');
	}
	if ($best) {
		$tooltip .= '<br><i class=\'bi bi-award\'></i> Miglior ' . ($socio ? 'socio' : 'chiamante');
	}

	if ($tooltip == '<div class=\'text-start\'></div>')
		$tooltip = '';
	return $tooltip;
}

function tooltip_coppia($coppie, $coppia) {
	$vinte = $coppie[2][$coppia];
	$perse = $coppie[3][$coppia];
	$patte = $coppie[4][$coppia];
	$cappotto = $coppie[5][$coppia];
	$benedizioni = $coppie[6][$coppia];
	$maledizioni = $coppie[7][$coppia];

	$out = '<i class=\'bi bi-activity\'></i> ' . $coppie[0][$coppia] . ' volt' . ($coppie[1][$coppia] == 1 ? 'a' : 'e') . ' insieme<hr class=\'my-1\'>';
	$out .= '<div class=\'text-start\'>';

	$out .= ($vinte > 0 ? '<i class=\'bi bi-hand-thumbs-up\'></i> ' . $vinte . ' vint' . ($vinte == 1 ? 'a' : 'e') . '<br>' : '');
	$out .= ($perse > 0 ? '<i class=\'bi bi-hand-thumbs-down\'></i> ' . $perse . ' pers' . ($perse == 1 ? 'a' : 'e') . '<br>' : '');
	$out .= ($patte > 0 ? '<i class=\'bi bi-dot\'></i> ' . $patte . ' patt' . ($patte == 1 ? 'a' : 'e') . '<br>' : '');
	$out .= ($cappotto > 0 ? '<i class=\'bi bi-star-fill\'></i> ' . $cappotto . ' cappotto<br>' : '');
	$out .= ($benedizioni > 0 ? '<i class=\'bi bi-brightness-high-fill\'></i> ' . $benedizioni . ' benedizion' . ($benedizioni == 1 ? 'e' : 'i') . '<br>' : '');
	$out .= ($maledizioni > 0 ? '<i class=\'bi bi-lightning-fill\'></i> ' . $maledizioni . ' maledizion' . ($maledizioni == 1 ? 'e' : 'i') . '<br>' : '');

	$out .= '</div>';
	//$out .= '' . punti($coppie[1][$coppia]) . ' punt' . (abs($coppie[1][$coppia]) == 1 ? 'o' : 'i');
	return $out;
}

function carousel_foto($id) {
	$foto = false;
	if (isset($_SESSION['id'])) {
		$outf = '';
		$files;
		if ($files = listafoto($id)) {
			if (count($files) > 0) {
				$foto = true;
				for ($i = 0; $i < count($files); $i++) {
					$outf .= '<div class="carousel-item' . ($i == 0 ? ' active' : '') . '"><img src="media/foto/' . $id . '/' . $files[$i] . '" class="d-block mx-auto" style="max-height: 50vh; max-width: 100%;"></div>';
				}
				$outf = '<div id="carousel' . $id . '" class="carousel slide" style="padding: 15px; padding-bottom: 60px; border: 1px solid #8f8f8f; background: #FFF; filter: drop-shadow(3px 3px 5px var(--ombrascura));"><div id="carousel' . $id . '-inner" class="carousel-inner rounded" style="background-image: linear-gradient(#d1d1d1, #8f8f8f);">' . $outf . '</div>';
				if (count($files) > 1)
					$outf .= '<button class="carousel-control-prev" type="button" data-bs-target="#carousel' . $id . '" data-bs-slide="prev" style="margin-top: 15px; margin-bottom: 60px;">
						<span class="carousel-control-prev-icon" aria-hidden="true"></span>
						<span class="visually-hidden">Precedente</span>
					</button>
					<button class="carousel-control-next" type="button" data-bs-target="#carousel' . $id . '" data-bs-slide="next" style="margin-top: 15px; margin-bottom: 60px;">
						<span class="carousel-control-next-icon" aria-hidden="true"></span>
						<span class="visually-hidden">Successiva</span>
					</button>';
				$outf .= '</div>';
			}
		}
	}

	return $foto ? $outf : '';
}

function mostra_partita_breve($id) {
	global $conn, $fmt1, $minimomedaglie;
	$res = $conn->query("SELECT * FROM partite WHERE IdPartita = $id;");
	$row = $res->fetch_assoc();
	$partita = partita($id);
	$out = '';

	$out .= '<div class="border border-2 rounded p-3 mb-4"><div class="text-start">';
	$out .= '<h5><a href="partite.php?id=' . $id . '">' . (empty($row['Occasione']) || $row['Occasione'] == null ? '<i class="text-muted">Occasione sconosciuta</i>' : $row['Occasione']) . '</a></h5>';
	$out .= '<h6 class="mb-2 text-muted"><i>' . $fmt1->format(strtotime($row['Data'])) . '</i> <i class="bi bi-dot"></i> ' . count($partita[0]) . ' turn' . (count($partita[0]) == 1 ? 'o' : 'i') . ' giocati</h6>';
	$out .= '<p>';


	$outm = mostra_medaglie($partita);
	$outf = carousel_foto($id);

	if (!empty($outf)) {
		$out .= '<div class="row">';
		$out .= '<div class="col-sm-6">' . $outm . '</div>';
		$out .= '<div class="col-sm-6">' . $outf . '</div>';
		$out .= '</div>';
	} else {
		$out .= $outm;
	}
	
	$out .= '</p></div></div>';
	return $out;
}
?>