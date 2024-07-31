<!DOCTYPE html>
<html lang="it-IT" data-bs-theme="auto">
<head>
	<title>I giocatori della Bi$ca</title>
	<?php include "php/bootstrap.php"; ?>
</head>
<body>
	<?php echo head(); ?>
	<div class="container-fluid">
	<?php
	if (isset($_GET['id'])) {
		$id = $conn->real_escape_string(stripslashes($_GET['id']));
		$res = $conn->query("SELECT * FROM giocatori WHERE IdGiocatore = '$id';");
		if ($res->num_rows == 1) {
			$row = $res->fetch_assoc();
			?>
			<div class="row">
				<div class="col-lg-2"></div>
				<div class="col-md">
					<h1><strong><?php echo nomecognome($row['Nome'], $row['Cognome']); ?></strong></h1>
					<?php
					if (!empty($row['Alias'])) {
						echo '<h3>' . $row['Alias'] . '</h3>';
					}
					?>
				</div>
				<div class="col-md-auto" style="text-align: right;">
					<?php
					if (isset($_SESSION['id'])) {
						if ($_SESSION['admin'] || $_SESSION['id'] == $id) {
							?><button class="btn btn-primary btn-sm mb-1" onclick="modalaggiorna();"><i class="bi bi-pencil-fill"></i> Modifica informazioni</button><br><?php
						}
						if ($_SESSION['admin']) {
							?><button class="btn btn-warning btn-sm mb-1" onclick="modalpermessi();"><i class="bi bi-key-fill"></i> Modifica permessi</button><br><?php
						}
						if ($_SESSION['id'] == $id) {
							?><a class="btn btn-secondary btn-sm mb-1" href="logout.php"><i class="bi bi-door-closed"></i> Sortisci dal sito</a><br><?php
						}
					}
					?>
				</div>
				<div class="col-lg-2"></div>
			</div>

			<script>
				var id = <?php echo $id; ?>;
				var loginpersonale = <?php echo $_SESSION['id'] == $id ? 'true' : 'false'; ?>;
				var nome = '<?php echo addslashes($row['Nome']); ?>';
				var cognome = '<?php echo addslashes($row['Cognome']); ?>';
				var alias = '<?php echo addslashes($row['Alias']); ?>';
				var abilitato = <?php echo $row['Password'] != null ? 'true' : 'false'; ?>;
				var editor = <?php echo $row['Editor'] == 1 ? 'true' : 'false'; ?>;
				var admin = <?php echo $row['Admin'] == 1 ? 'true' : 'false'; ?>;
			</script>
			<script src="js/giocatori.js"></script>
			
			<div class="row"><div class="col-lg-2"></div><div class="col-lg">
				<?php
				$res2 = $conn->query("SELECT * FROM partecipazioni JOIN partite ON partecipazioni.Partita = partite.IdPartita WHERE partecipazioni.Giocatore = $id GROUP BY partite.IdPartita ORDER BY partite.Data desc;");
				if ($res2->num_rows > 0) {
					
					$anno = false;
					$partite = array();
					$out = '';
					$outanno = '';
					$last = false;
					$medaglie = array(0, 0, 0, 0, 0);
					$medaglietot = array(0, 0, 0, 0, 0);
					$chiamate = array(0, 0, 0, 0, 0, 0, 0, 0); // Chiamate [0] vinte, [1] perse, [2] patte, [3] in mano, [4] socio, [5] cappotti (sia chiamante che socio), [6] punteggio, [7] turni giocati
					$chiamatetot = array(0, 0, 0, 0, 0, 0, 0, 0);
					while (($row2 = $res2->fetch_assoc()) || !$last) {
						if ($row2) {
							$partita = partita($row2['IdPartita']);
							$numturni = $partita[4][0] + $partita[4][1] + $partita[4][2];
						}
						
						if (($row2 == null) || substr($row2['Data'], 0, 4) != $anno) { // Cambio anno
							if ($anno != null) {
								$out .= '<div class="riquadroanno alert-success mt-3 mb-0">';

								$out .= '<div class="row">';
								$out .= '<div class="col-auto text-start"><h3 class="mb-sm-0">' . $anno . '</h3></div>';
								$out .= '<div class="col text-end">';
								$out .= '<h6>Punteggio: <strong>' . punti($chiamate[6]) . '</strong></h6>';
								$out .= '</div></div>';
								
								$out .= '<div class="text-end">' . lista_stat_giocatore($chiamate) . '</div>';

								$out .= '</div>';

								$out .= '<div class="text-end mb-3 pe-md-5" style="margin-top: -2px;">' . medagliere_giocatore($medaglie) . '</div>';
								$out .= $outanno;

								$medaglietot = array_map(function (...$arrays) {
									return array_sum($arrays);
								}, $medaglietot, $medaglie);
								$chiamatetot = array_map(function (...$arrays) {
									return array_sum($arrays);
								}, $chiamatetot, $chiamate);

								if (!$row2)
									break;
							}

							// Azzeramento
							$outanno = '';
							$anno = substr($row2['Data'], 0, 4);
							$medaglie = array(0, 0, 0, 0, 0);
							$chiamate = array(0, 0, 0, 0, 0, 0, 0, 0);
						}
						
						$outanno .= '<a class="dropdown-item" href="partite.php?id=' . $row2['IdPartita'] . '"><div class="row">';
						$outanno .= '<div class="col-1 no-pad d-inline-block my-auto text-end">' . array_sum($partita[3][0][$id]) . '<i class="bi bi-play-fill"></i></div>'; // Turni che ha giocato
						$outanno .= '<div class="col-9 ano-pad d-inline-block"><div class="row" style="max-width: 100%;">';
							$outanno .= '<div class="col-md ano-pad text-truncate text-start">' . (empty($row2['Occasione']) ? '<span class="chiaro"><i>Occasione sconosciuta</i></span>' : $row2['Occasione']) . '</div>'; // Occasione
							$outanno .= '<div class="col-md-3 text-start"><small class="chiaro d-block d-md-none" style="line-height: 15px;"><i>&nbsp;' . $fmt3->format(strtotime($row2['Data'])) . '</i></small><small class="chiaro d-none d-md-block"><i>&nbsp;' . $fmt3->format(strtotime($row2['Data'])) . '</i></small></div>'; // Data
						$outanno .= '</div></div>';
						
						// Punteggio realizzato ed eventuale medaglia
						$outanno .= '<div class="col no-pad my-auto text-end">';
						$turnifatti = array_sum($partita[3][0][$id]);
						$completo = $turnifatti == $numturni;
						$punti = $partita[3][16][$id];
						$outanno .= ($completo ? '<strong>' : '') . punti($punti) . ($completo ? '</strong>' : '');
						$med = medaglie($partita);
						if (isset($med[$id])) {
							$outanno .= '<img src="img/Medaglia' . $med[$id] . '.png" height=25px>';
							$medaglie[$med[$id] - 1]++;
						} else if ($numturni >= $minimomedaglie) {
							$outanno .= '&nbsp;<i class="bi bi-door-open"></i>&nbsp;';
						} else {
							$outanno .= '<img src="img/Medaglia0.png" height=25px>';
						}

						$chiamate[0] += $partita[3][1][$id];
						$chiamate[1] += $partita[3][2][$id];
						$chiamate[2] += $partita[3][3][$id];
						$chiamate[3] += $partita[3][4][$id] + $partita[3][5][$id] + $partita[3][6][$id];
						$chiamate[4] += $partita[3][10][$id] + $partita[3][11][$id] + $partita[3][12][$id];
						$chiamate[5] += $partita[3][7][$id] + $partita[3][8][$id] + $partita[3][13][$id] + $partita[3][14][$id];
						$chiamate[6] += $punti;
						$chiamate[7] += $turnifatti;

						$outanno .= '</div>';
						$outanno .= '<div class="col-md-1"></div>';

						$outanno .= '</div></a>';
					}
					?>
					
					<hr>
					<h2 class="vivaldi text-center">La carriera del Giuocatore</h2>
					<div class="row">
						<div class="col-md my-auto mb-4">
							<div class="row">
								<div class="col text-end">
									Bi$che disputate<br>
									Punteggio realizzato
								</div>
								<div class="col">
									<strong><?php echo $res2->num_rows; ?></strong><br>
									<strong><?php echo ($chiamatetot[6] >= 0 ? '<span class="text-success">' : '<span class="text-danger">') . punti($chiamatetot[6]) . '</span>'; ?></strong>
								</div>
							</div>
							<div class="text-center">Medaglie<br><?php echo medagliere_giocatore($medaglietot); ?></div>
						</div>
						<div class="col-md my-auto">
							<div class="row">
								<div class="col text-end">Partite giocate</div>
								<div class="col">
									<strong class="text-info">
										<i class="bi bi-play-fill"></i>&nbsp;<?php echo $chiamatetot[7]; ?>
									</strong>
								</div>
							</div>
							<div class="row">
								<div class="col text-end my-auto">Chiamate</div>
								<div class="col">
									<strong class="text-success">
										<i class="bi bi-hand-thumbs-up"></i> <?php echo $chiamatetot[0]; ?>
									</strong>&nbsp;&nbsp;
									<strong class="text-danger">
										<i class="bi bi-hand-thumbs-down"></i>&nbsp;<?php echo $chiamatetot[1]; ?>
									</strong>
									<?php if ($chiamatetot[2] > 0 || $chiamatetot[3] > 0) echo '<br>';
									if ($chiamatetot[2] > 0) {?>
										<strong class="text-warning"><i class="bi bi-arrows-collapse"></i>&nbsp;<?php echo $chiamatetot[2]; ?></strong>
									<?php } ?>
									<?php if ($chiamatetot[3] > 0) {?>
										&nbsp;&nbsp;<strong class="text-info"><i class="bi bi-person-bounding-box"></i>&nbsp;<?php echo $chiamatetot[3]; ?></strong>
									<?php } ?><br>
								</div>
							</div>
							<div class="row">
								<div class="col text-end">Alleanze</div>
								<div class="col">
									<i class="bi bi-incognito"></i>&nbsp;<strong><?php echo $chiamatetot[4]; ?></strong>
								</div>
							</div>
							<?php if ($chiamatetot[5] > 0) {?>
								<div class="row">
									<div class="col text-end">Cappotti</div>
									<div class="col">
										<strong class="text-warning">
											<i class="bi bi-star-fill"></i>&nbsp;<?php echo $chiamatetot[5]; ?>
										</strong>
									</div>
								</div>
							<?php } ?>
						</div>
					</div>

					<hr>
					<?php
					echo '<h2 class="vivaldi text-center">' . ($res2->num_rows == 1 ? 'La bi$ca' : 'Le bi$che') . ' che ha disputato</h2>';

					echo $out;


					echo '<br>';
				} else {
					echo '<i>' . $row['Nome'] . ' non ha disputato nessuna bi$ca.</i>';
				}
				?>
			</div>
			<div class="col-lg-2"></div>
			</div>
			
			<?php
			
		} else {
			echo 'Il giocatore cercato non esiste.';
		}
	} else {
		?>
		<h1 style="font-family: Vivaldi; font-size: 40px;" class="text-center mb-0"><span style="white-space: nowrap;">i Giuocatori</span> <span style="white-space: nowrap;">della tavola rotonda</span></h1>
		<p class="text-center">Di chi vuoi conoscere le epiche gesta?</p>
		
		<?php
		if (isset($_SESSION['id']) && $_SESSION['editor']) {
			?>
			<br><div class="text-center"><button class="btn btn-primary" onclick="aggiungi();"><i class="bi bi-person-plus-fill"></i> Aggiungi un giuocatore</button></div><br><br>
					
			<script>
			function aggiungi() {
				modal('Aggiungi un giuocatore', 'Nome:<input type="text" class="form-control" id="nome" onkeyup="if(event.keyCode == 13) nuovo();">Alias:<input type="text" class="form-control" id="alias" onkeyup="if(event.keyCode == 13) nuovo();"><span class="text-danger" id="errorenuovo"></span>', '<button type="button" class="btn btn-primary" onclick="nuovo();"><i class="bi bi-check-circle-fill"></i> Salva</button>');
			}
			
			function nuovo() {
				var nome = document.getElementById("nome").value;
				if (nome.length > 0) {
					var alias = document.getElementById("alias").value;
					var xhttp = new XMLHttpRequest();
					xhttp.onreadystatechange = function() {
						if (this.readyState == 4 && this.status == 200) {
							if (isNaN(parseInt(this.responseText))) {
								document.getElementById('errorenuovo').innerHTML = this.responseText;
							} else {
								window.location.href = 'giocatori.php?id=' + this.responseText;
							}
						}
					};
					xhttp.open("POST", "php/ajax.php", true);
					xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
					xhttp.send("ajax=nuovogiocatore&nome=" + nome + "&alias=" + alias);
				} else {
					document.getElementById('errorenuovo').innerHTML = 'Inserire un nome';
				}
			}

			</script>
			<?php
		}
		$res = $conn->query("SELECT * FROM giocatori;");
		if ($res->num_rows > 0) {
			echo '<div class="row"><div class="col-lg"></div><div class="col-lg-6">';
			while ($row = $res->fetch_assoc()) {
				$res2 = $conn->query("SELECT * FROM partecipazioni JOIN partite ON partecipazioni.Partita = partite.IdPartita WHERE partecipazioni.Giocatore = " . $row['IdGiocatore'] . " GROUP BY partite.IdPartita ORDER BY partite.Data desc;");
				echo '<a class="dropdown-item" href="giocatori.php?id=' . $row['IdGiocatore'] . '"><div class="row">';
				echo '<div class="col-2 no-pad" style="text-align: right;">' . $res2->num_rows . '<i class="bi bi-play-fill"></i></div>';
				echo '<div class="col" style="text-align: left;">' . nomecognome($row['Nome'], $row['Cognome']) . (!empty($row['Alias']) ? ' – <i class="chiaro">' . $row['Alias'] : '') . '</i></div>';
				echo '</div></a>';
			}
			echo '<br></div><div class="col-lg"></div></div>';
		}
	}
	?>
	
	</div>
<?php include "php/bootstrap2.php"; ?></body>
</html>
