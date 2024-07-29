<!DOCTYPE html>
<html>
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
				<div class="col-lg-1"></div>
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
								?>
								<button class="btn btn-primary btn-sm mb-1" onclick="modalaggiorna();"><i class="bi bi-pencil-fill"></i> Modifica informazioni</button><br>
								<?php
							}
							if ($_SESSION['admin']) {
								?>
								<button class="btn btn-warning btn-sm mb-1" onclick="modalpermessi();"><i class="bi bi-key-fill"></i> Modifica permessi</button><br>
								<?php
							}
							if ($_SESSION['id'] == $id) {
								?>
								<a class="btn btn-secondary btn-sm mb-1" href="logout.php"><i class="bi bi-door-closed"></i> Sortisci dal sito</a><br>
								<?php
							}
						}
					?>
				</div>
				<div class="col-lg-1"></div>
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
			
			<br>
			<div class="row"><div class="col-lg-2"></div><div class="col-lg">
				<?php
				$res2 = $conn->query("SELECT * FROM partecipazioni JOIN partite ON partecipazioni.Partita = partite.IdPartita WHERE partecipazioni.Giocatore = $id GROUP BY partite.IdPartita ORDER BY partite.Data desc;");
				if ($res2->num_rows > 0) {
					echo '<h4 class="text-center"><strong>' . ($res2->num_rows == 1 ? 'La bi$ca' : 'Le ' . $res2->num_rows . ' bi$che') . ' che ha disputato:</strong></h4>';
					$anno = false;
					$partite = array();
					while ($row2 = $res2->fetch_assoc()) {
						$partita = partita($row2['IdPartita']);
						$numturni = $partita[4][0] + $partita[4][1] + $partita[4][2];
						
						if (substr($row2['Data'], 0, 4) != $anno) { // Cambio anno
							$anno = substr($row2['Data'], 0, 4);
							echo '<br><h3 class="text-center">' . $anno . '</h3><hr>';
						}
						
						echo '<a class="dropdown-item" href="partite.php?id=' . $row2['IdPartita'] . '"><div class="row">';
						echo '<div class="col d-inline-block my-auto" style="text-align: right;">' . array_sum($partita[3][0][$id]) . '<i class="bi bi-play-fill"></i></div>'; // Turni che ha giocato
						echo '<div class="col-8 no-pad d-inline-block"><div class="row" style="max-width: 100%;">';
							echo '<div class="col-md no-pad text-truncate" style="text-align: left;">' . (empty($row2['Occasione']) ? '<span class="chiaro"><i>Occasione sconosciuta</i></span>' : $row2['Occasione']) . '</div>'; // Occasione
							echo '<div class="col-md-3" style="text-align: left;"><small class="chiaro d-block d-md-none" style="line-height: 15px;"><i>&nbsp;' . $fmt3->format(strtotime($row2['Data'])) . '</i></small><small class="chiaro d-none d-md-block"><i>&nbsp;' . $fmt3->format(strtotime($row2['Data'])) . '</i></small></div>'; // Data
						echo '</div></div>';
						
						// Punteggio realizzato ed eventuale medaglia
						echo '<div class="col no-pad my-auto" style="text-align: right;">';
						$completo = array_sum($partita[3][0][$id]) == $numturni;
						echo ($completo ? '<strong>' : '') . punti($partita[3][16][$id]) . ($completo ? '</strong>' : '');
						$medaglie = medaglie($partita);
						if (isset($medaglie[$id])) {
							echo '<img src="img/Medaglia' . $medaglie[$id] . '.png" height=25px>';
						} else if ($numturni >= $minimomedaglie) {
							echo '&nbsp;<i class="bi bi-door-open"></i>&nbsp;';
						} else {
							echo '<img src="img/Medaglia0.png" height=25px>';
						}
						echo '</div>';
						echo '<div class="col-md-1"></div>';

						echo '</div></a>';
					}
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
		<h1 class="text-center">I giuocatori della tavola rotonda</h2>
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
