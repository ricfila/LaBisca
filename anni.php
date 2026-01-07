<!DOCTYPE html>
<html lang="it-IT" data-bs-theme="<?php echo (isset($_COOKIE['tema']) ? $_COOKIE['tema'] : 'auto'); ?>">
<head>
	<?php
	if (!isset($_GET['anno'])) {
		header("Location: partite.php");
		exit;
	}
	include "php/bootstrap.php";
	$anno = $conn->real_escape_string(stripslashes($_GET['anno']));
	echo '<title>Riepilogo ' . $anno . ' - La Bi$ca</title>';
	?>
</head>
<body>
	<?php echo head(); ?>
	<div class="container-fluid">
		<div class="row">
			<div class="col-lg-2"></div>
			<div class="col">
				<div class="row">
					<div class="col"><h1 class="mb-3"><strong>Riepilogo <?php echo $anno; ?></strong></h1></div>
					<div class="col-auto">
						<?php
							if ($conn->query("SELECT * FROM partite WHERE Data LIKE '" . ($anno-1) . "%';")->num_rows > 0) {
								echo '<a id="link_prevyear" href="anni.php?anno=' . ($anno-1) . '" class="btn btn-primary me-2"><i class="bi bi-caret-left-fill"></i></a>';
							}
							$anno_dopo = ($conn->query("SELECT * FROM partite WHERE Data LIKE '" . ($anno+1) . "%';")->num_rows > 0);
							echo '<a id="link_nextyear" href="anni.php?anno=' . ($anno+1) . '" class="btn btn-primary"' . (!$anno_dopo ? ' style="opacity: 0; pointer-events: none;"' : '') . '><i class="bi bi-caret-right-fill"></i></a>';
						?>
					</div>
				</div>

				<?php
				$res = $conn->query("SELECT * FROM partite WHERE Data LIKE '" . $anno . "%' ORDER BY Data;");
				
				$num_bische = $res->num_rows;
				if ($num_bische > 0) {
					$num_partite = 0;
					$stat_chiamate = array(0, 0, 0); // Vinte, perse, patte
					$stat_chiamate_inmano = array(0, 0, 0); // In mano vinte, in mano perse, in mano patte
					$stat_chiamate_cappotto = array(0, 0); // Cappotto vinte, cappotto perse
					$punteggio_max = 0;
					$punteggio_min = 0;
					$max_realizzato = 0;
					$min_realizzato = 0;
					$giocatore_max = null;
					$giocatore_min = null;
					$partita_max = null;
					$partita_min = null;
					$in_mano_vinte = array(); // Lista di id partite con chiamate in mano vinte
					$in_mano_perse = array(); // Lista di id partite con chiamate in mano perse
					$in_mano_patte = array(); // Lista di id partite con chiamate in mano patte
					$cappotto_vinte = array(); // Lista di id partite con cappotti vinte
					$cappotto_perse = array(); // Lista di id partite con cappotti perse
					$gstat = array(); // Corrisponde a $partita[3]
					$medaglie = array(); // Array associativo [id_giocatore] => array[5]

					// Array per la fusione delle partite
					$cambi_giocatore = array(); // Corrisponde a $partita[2]
					$codici = array(); // Corrisponde a $partita[6]
					$offset = 0;

					while ($row = $res->fetch_assoc()) {
						$id = $row['IdPartita'];
						$partita = partita($id);

						$num_partite += count($partita[0]);
						$stat_chiamate[0] += $partita[4][0];
						$stat_chiamate[1] += $partita[4][1];
						$stat_chiamate[2] += $partita[4][2];
						foreach ($partita[3][4] as $g => $c) {
							$stat_chiamate_inmano[0] += $c;
							if ($c > 0 && !in_array($id, $in_mano_vinte)) $in_mano_vinte[] = $id;
						}
						foreach ($partita[3][5] as $g => $c) {
							$stat_chiamate_inmano[1] += $c;
							if ($c > 0 && !in_array($id, $in_mano_perse)) $in_mano_perse[] = $id;
						}
						foreach ($partita[3][6] as $g => $c) {
							$stat_chiamate_inmano[2] += $c;
							if ($c > 0 && !in_array($id, $in_mano_patte)) $in_mano_patte[] = $id;
						}
						foreach ($partita[3][7] as $g => $c) {
							$stat_chiamate_cappotto[0] += $c;
							if ($c > 0 && !in_array($id, $cappotto_vinte)) $cappotto_vinte[] = $id;
						}
						foreach ($partita[3][8] as $g => $c) {
							$stat_chiamate_cappotto[1] += $c;
							if ($c > 0 && !in_array($id, $cappotto_perse)) $cappotto_perse[] = $id;
						}

						// Aggiorna punteggio massimo e minimo raggiunti
						if ($partita[4][5] < $punteggio_min) {
							$punteggio_min = $partita[4][5];
						}
						if ($partita[4][6] > $punteggio_max) {
							$punteggio_max = $partita[4][6];
						}

						foreach ($partita[3][16] as $g => $p) {
							if ($p > $max_realizzato) {
								$max_realizzato = $p;
								$giocatore_max = $g;
								$partita_max = $id;
							}
							if ($p < $min_realizzato) {
								$min_realizzato = $p;
								$giocatore_min = $g;
								$partita_min = $id;
							}
						}

						// Aggiorna statistiche e medaglie giocatori
						for ($i = 1; $i <= 16; $i++) {
							if (!isset($gstat[$i]))
								$gstat[$i] = array();

							foreach ($partita[3][$i] as $g => $v) {
								$gstat[$i][$g] = (isset($gstat[$i][$g]) ? $gstat[$i][$g] : 0) + $v;
							}
						}

						$medaglie_p = medaglie($partita);
						foreach ($medaglie_p as $id_g => $med) {
							if (!isset($medaglie[$id_g]))
								$medaglie[$id_g] = array(0, 0, 0, 0, 0);

							$medaglie[$id_g][$med - 1]++;
						}

						// Fusione delle partite per il calcolo delle statistiche sulle coppie
						// matrice[*][5] con una riga per ogni cambio di giocatore
						foreach ($partita[2] as $i => $cambio) {
							$cambi_giocatore[$offset + $i] = $cambio;
						}
						
						// codici: matrice [n][5], con n numero di partite (chiamante, socio, vittoria, cappotto, Vecia)
						foreach ($partita[6] as $r) {
							$codici[] = $r;
						}
						$offset += count($partita[0]);
					}

					$partita_max_data = ($partita_max != null ? $conn->query("SELECT Data FROM partite WHERE IdPartita = $partita_max;")->fetch_assoc()['Data'] : null);

					$partita_min_data = ($partita_min != null ? $conn->query("SELECT Data FROM partite WHERE IdPartita = $partita_min;")->fetch_assoc()['Data'] : null);

					?>

					<ul class="nav nav-tabs" role="tablist">
						<li class="nav-item" role="presentation">
							<a class="nav-link" data-bs-toggle="tab" href="#riepilogo" aria-selected="true" role="tab"><i class="bi bi-clipboard-pulse"></i></a>
						</li>
						<li class="nav-item" role="presentation">
							<a class="nav-link" data-bs-toggle="tab" href="#medagliere" aria-selected="false" role="tab" tabindex="-1"><i class="bi bi-award"></i></a>
						</li>
						<li class="nav-item" role="presentation">
							<a class="nav-link" data-bs-toggle="tab" href="#classifica" aria-selected="false" role="tab" tabindex="-1"><i class="bi bi-bar-chart-line-fill"></i></a>
						</li>
						<li class="nav-item" role="presentation">
							<a class="nav-link" data-bs-toggle="tab" href="#chiamanti" aria-selected="false" role="tab" tabindex="-1"><i class="bi bi-trophy-fill"></i></a>
						</li>
						<li class="nav-item" role="presentation">
							<a class="nav-link" data-bs-toggle="tab" href="#soci" aria-selected="false" role="tab" tabindex="-1"><i class="bi bi-incognito"></i></a>
						</li>
						<li class="nav-item" role="presentation">
							<a class="nav-link" data-bs-toggle="tab" href="#coppie" aria-selected="false" role="tab" tabindex="-1"><i class="bi bi-arrow-through-heart"></i></a>
						</li>
					</ul>

					<div class="tab-content">
						<!-- RIEPILOGO -->
						<div class="tab-pane fade text-start py-3" id="riepilogo" role="tabpanel">
							<div class="row">
								<div class="col-md mb-4">
									<div class="row mb-3">
										<div class="col">Bi$che disputate:</div>
										<div class="col my-auto"><strong><?php echo $num_bische; ?></strong></div>
									</div>
									<div class="row mb-3">
										<div class="col">Partite giocate:</div>
										<div class="col my-auto"><strong><i class="bi bi-play-fill"></i>&nbsp;<?php echo $num_partite; ?></strong></div>
									</div>
								</div>
								<div class="col-md mb-4">
									<?php
									echo specchietto_chiamate($stat_chiamate);
									
									if (($stat_chiamate_inmano[0] + $stat_chiamate_inmano[1] + $stat_chiamate_inmano[2]) > 0) {
										echo specchietto_chiamate_inmano($stat_chiamate_inmano, true);
									}
									if (($stat_chiamate_cappotto[0] + $stat_chiamate_cappotto[1]) > 0) {
										echo specchietto_cappotti($stat_chiamate_cappotto, true);
									}
									?>
								</div>
								<div class="col-md mb-4">
									<div class="row mb-3">
										<div class="col">Estremi di punteggio:</div>
										<div class="col my-auto">
											<strong class="text-danger"><?php echo $punteggio_min; ?></strong>&emsp;
											<i class="bi bi-three-dots"></i>&emsp;
											<strong class="text-success">+<?php echo $punteggio_max; ?></strong>
										</div>
									</div>
									<?php
									if ($giocatore_max != null) {
										?>
										<div class="mb-3">
											Punteggio massimo realizzato: <strong class="text-success">+<?php echo $max_realizzato; ?></strong><br>
											<div class="ms-4">da <a href="giocatori.php?id=<?php echo $giocatore_max; ?>"><?php echo nomedi($giocatore_max); ?></a>, il <a href="partite.php?id=<?php echo $partita_max; ?>"><?php echo $fmt3->format(strtotime($partita_max_data)); ?></a></div>
										</div>
										<?php
									}
									if ($giocatore_min != null) {
										?>
										<div class="mb-3">
											Punteggio minimo realizzato: <strong class="text-danger"><?php echo $min_realizzato; ?></strong><br>
											<div class="ms-4">da <a href="giocatori.php?id=<?php echo $giocatore_min; ?>"><?php echo nomedi($giocatore_min); ?></a>, il <a href="partite.php?id=<?php echo $partita_min; ?>"><?php echo $fmt3->format(strtotime($partita_min_data)); ?></a></div>
										</div>
										<?php
									}
									?>
								</div>
							</div>
						</div>

						<!-- MEDAGLIERE -->
						<div class="tab-pane fade text-start py-3" id="medagliere" role="tabpanel">
							<h5><i class="bi bi-award"></i> Medagliere</h5>
							<?php
							$i = 1;
							while (count($medaglie) > 0) {
								$max_g = [];
								$max_m = null;
								foreach ($medaglie as $g => $m) {
									if ($max_m === null ||
										$m[0] > $max_m[0] ||
										($m[0] == $max_m[0] && $m[1] > $max_m[1]) ||
										($m[0] == $max_m[0] && $m[1] == $max_m[1] && $m[2] > $max_m[2]) ||
										($m[0] == $max_m[0] && $m[1] == $max_m[1] && $m[2] == $max_m[2] && $m[3] > $max_m[3]) ||
										($m[0] == $max_m[0] && $m[1] == $max_m[1] && $m[2] == $max_m[2] && $m[3] == $max_m[3] && $m[4] > $max_m[4])) {
										$max_g = [$g];
										$max_m = $m;
									} elseif ($m[0] == $max_m[0] && $m[1] == $max_m[1] && $m[2] == $max_m[2] && $m[3] == $max_m[3] && $m[4] == $max_m[4]) {
										// Pareggio
										$max_g[] = $g;
									}
								}

								echo '<hr class="my-1">';
								echo '<div class="row">';
								echo '<div class="col-2 col-md-1 p-0 my-auto text-nowrap text-end"><i>' . $i . '</i><i class="bi bi-dot"></i></div>';
								echo '<div class="col p-0">';
								$k = 0;
								foreach ($max_g as $g) {
									echo ($k > 0 ? ', ' : '') . '<a href="giocatori.php?id=' . $g . '">' . nomedi($g) . '</a>';
									unset($medaglie[$g]);
									$k++;
								}
								echo '</div>';
								echo '<div class="col-5 col-md-3 ps-0 my-auto text-nowrap text-end">' . medagliere_giocatore($max_m, 3) . '</div>';
								echo '</div>';
								$i++;
							}
							echo '<hr class="my-1">';
							?>
						</div>

						<!-- CLASSIFICA -->
						<div class="tab-pane fade text-start py-3" id="classifica" role="tabpanel">
							<h5><i class="bi bi-bar-chart-line-fill"></i> Classifica</h5>
							<div class="px-3">
								<?php
								$punteggi = $gstat[16];
								$i = 1;
								while (count($punteggi) > 0) {
									$max = max($punteggi);
									echo '<div class="row">';
									echo '<div class="col-4 col-md-2 row">';
										echo '<div class="col p-0 text-nowrap text-end"><i>' . $i . '</i>&nbsp;<i class="bi bi-dot"></i></div>';
										echo '<div class="col pe-0 text-nowrap"><strong>' . ($max > 0 ? '+' : '') . $max . '</strong></div>';
									echo '</div>';
									echo '<div class="col">';
									$k = 0;
									foreach (array_keys($punteggi, $max) as $g) {
										echo ($k > 0 ? ', ' : '') . '<a href="giocatori.php?id=' . $g . '">' . nomedi($g) . '</a>';
										unset($punteggi[$g]);
										$k++;
									}
									echo '</div>';
									echo '</div>';
									$i++;
								}
								?>
							</div>
						</div>

						<!-- CHIAMANTI -->
						<?php $chiamanti_soci = mostra_chiamantisoci($gstat, true); ?>
						<div class="tab-pane fade text-start py-3" id="chiamanti" role="tabpanel">
							<?php echo $chiamanti_soci[0]; ?>
						</div>

						<!-- SOCI -->
						<div class="tab-pane fade text-start py-3" id="soci" role="tabpanel">
							<?php echo $chiamanti_soci[1]; ?>
						</div>

						<!-- COPPIE -->
						<div class="tab-pane fade text-start py-3" id="coppie" role="tabpanel">
							<?php echo mostra_coppie($cambi_giocatore, $codici, 5); ?>
						</div>
					</div>
				<?php
				} else {
					echo 'Nessuna Bi$ca disputata nel ' . $anno;
				}
				?>
			</div>
			<div class="col-lg-2"></div>
		</div>
	</div>

	<div class="offcanvas offcanvas-start" tabindex="-1" id="dettagliopartite" aria-labelledby="dettagliopartite-titolo">
		<div class="offcanvas-header">
			<h5 class="offcanvas-title" id="dettagliopartite-titolo"></h5>
			<button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
		</div>
		<div class="offcanvas-body px-0" id="dettagliopartite-corpo"></div>
	</div>
	<script>
		<?php
		function link_partita($row) {
			global $fmt2;
			$out = '<a class="dropdown-item" href="partite.php?id=' . $row['IdPartita'] . '"><div class="row">';
			$out .= '<div class="col d-inline-block text-truncate">' . (empty($row['Occasione']) ? '<span class="chiaro"><i>Occasione sconosciuta</i></span>' : $row['Occasione']) . '</div>';
			$out .= '<div class="col-auto text-end ps-0 px-sm-3"><small class="chiaro"><i>' . $fmt2->format(strtotime($row['Data'])) . '</i></small></div>';
			$out .= '</div></a>';

			return $out;
		}

		$in_mano_vinte_out = '';
		$in_mano_perse_out = '';
		$in_mano_patte_out = '';
		$cappotto_vinte_out = '';
		$cappotto_perse_out = '';
		foreach ($in_mano_vinte as $p)
			$in_mano_vinte_out .= link_partita($conn->query("SELECT * FROM partite WHERE IdPartita = $p;")->fetch_assoc());
		foreach ($in_mano_perse as $p)
			$in_mano_perse_out .= link_partita($conn->query("SELECT * FROM partite WHERE IdPartita = $p;")->fetch_assoc());
		foreach ($in_mano_patte as $p)
			$in_mano_patte_out .= link_partita($conn->query("SELECT * FROM partite WHERE IdPartita = $p;")->fetch_assoc());
		foreach ($cappotto_vinte as $p)
			$cappotto_vinte_out .= link_partita($conn->query("SELECT * FROM partite WHERE IdPartita = $p;")->fetch_assoc());
		foreach ($cappotto_perse as $p)
			$cappotto_perse_out .= link_partita($conn->query("SELECT * FROM partite WHERE IdPartita = $p;")->fetch_assoc());
		?>
		let titoli_dettagliopartite = ['Partite con<br>chiamate in mano vinte', 'Partite con<br>chiamate in mano perse', 'Partite con<br>chiamate in mano patte', 'Partite con cappotti<br>vinti', 'Partite con cappotti<br>persi'];
		let out_partite = ['<?php echo addslashes($in_mano_vinte_out); ?>', '<?php echo addslashes($in_mano_perse_out); ?>', '<?php echo addslashes($in_mano_patte_out); ?>', '<?php echo addslashes($cappotto_vinte_out); ?>', '<?php echo addslashes($cappotto_perse_out); ?>'];

		function mostra_dettagliopartite(id) {
			if (out_partite[id].length == 0) return;
			$('#dettagliopartite-titolo').html(titoli_dettagliopartite[id]);
			$('#dettagliopartite-corpo').html(out_partite[id]);
			var offcanvas = new bootstrap.Offcanvas(document.getElementById('dettagliopartite'));
			offcanvas.show();
		}

		var tabEl = document.querySelectorAll('a[data-bs-toggle="tab"]');
		tabEl.forEach(function (tab) {
			tab.addEventListener('shown.bs.tab', function (event) {
				let tabname = event.target.getAttribute('href').substring(1);
				history.replaceState(null, null, '#' + tabname);
				$('#link_prevyear').attr('href', 'anni.php?anno=<?php echo ($anno-1); ?>' + '#' + tabname);
				$('#link_nextyear').attr('href', 'anni.php?anno=<?php echo ($anno+1); ?>' + '#' + tabname);
			})
		})

		$(document).ready(function() {
			// Attiva la tab in base all'anchor in URL
			let hash = window.location.hash;
			if (!hash)
				hash = '#riepilogo';
			let triggerEl = document.querySelector('a[href="' + hash + '"]');
			if (triggerEl) {
				let tab = new bootstrap.Tab(triggerEl);
				tab.show();
			}
		});
	</script>
<?php include "php/bootstrap2.php"; ?></body>
</html>
