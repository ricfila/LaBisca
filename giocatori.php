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

								<script>
									function modalaggiorna() {
										modal('<i class="bi bi-pencil-fill"></i>&nbsp;<strong>Modifica informazioni</strong>', '<i class="bi bi-person"></i> Nome:<input type="text" class="form-control" id="nome" onkeyup="if(event.keyCode == 13) aggiorna();" value="<?php echo addslashes($row['Nome']); ?>">' + 
										'<i class="bi bi-person"></i> Cognome:<input type="text" class="form-control" id="cognome" onkeyup="if(event.keyCode == 13) aggiorna();" value="<?php echo addslashes($row['Cognome']); ?>">' + 
										'<i class="bi bi-person-exclamation"></i> Alias o epiteto:<input type="text" class="form-control" id="alias" onkeyup="if(event.keyCode == 13) aggiorna();" value="<?php echo addslashes($row['Alias']); ?>">' + 
										<?php if ($_SESSION['id'] == $id) { ?>
										'<hr><i class="bi bi-key-fill"></i> Nuova parola chiave:<input type="password" class="form-control" id="pwd1" onkeyup="if(event.keyCode == 13) aggiorna();" value="">' + 
										'<i class="bi bi-key-fill"></i> Conferma parola chiave:<input type="password" class="form-control" id="pwd2" onkeyup="if(event.keyCode == 13) aggiorna();" value="">' +
										<?php } ?>
										'<span class="text-danger" id="erroreaggiorna"></span>', '<button type="button" class="btn btn-primary" onclick="aggiorna();"><i class="bi bi-check-circle-fill"></i> Salva</button>');
									}
									
									function aggiorna() {
										var nome = document.getElementById("nome").value;
										var cognome = document.getElementById("cognome").value;
										var alias = document.getElementById("alias").value;
										if (document.getElementById("pwd1") != null) {
											var pwd1 = document.getElementById("pwd1").value;
											var pwd2 = document.getElementById("pwd2").value;
										} else {
											var pwd1 = '';
											var pwd2 = '';
										}

										if (nome.length == 0) {
											$('#erroreaggiorna').html('Inserire un nome');
										} else if (pwd1 != pwd2) {
											$('#erroreaggiorna').html('Le due parole chiave inserite non coincidono');
										} else {
											var xhttp = new XMLHttpRequest();
											xhttp.onreadystatechange = function() {
												if (this.readyState == 4 && this.status == 200) {
													if (isNaN(parseInt(this.responseText))) {
														document.getElementById('erroreaggiorna').innerHTML = this.responseText;
													} else {
														location.reload();
													}
												}
											};
											xhttp.open("POST", "php/ajax.php", true);
											xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
											xhttp.send("ajax=aggiornagiocatore&id=<?php echo $id; ?>&nome=" + nome + "&cognome=" + cognome + "&alias=" + alias + (pwd1.length > 0 ? '&pwd=' + pwd1 : ''));
										}
									}

									</script>
								<?php
							}
							if ($_SESSION['admin']) {
								?>
								<button class="btn btn-warning btn-sm mb-1" onclick="modalpermessi();"><i class="bi bi-key-fill"></i> Modifica permessi</button><br>

								<script>
									function modalpermessi() {
										modal('<i class="bi bi-key-fill"></i>&nbsp;<strong>Modifica permessi</strong>', '<div class="form-check"><input class="form-check-input" type="checkbox" id="login"<?php if ($row['Password'] != null) echo ' checked=""'; ?> onchange="change_login(this.checked);"><label class="form-check-label" for="login">Abilitato all\'accesso</label></div>' +
										'<div class="form-check"><input class="form-check-input" type="checkbox" id="editor"<?php if ($row['Editor'] == 1) echo ' checked=""'; ?> onchange="change_editor(this.checked);"><label class="form-check-label" for="editor">Editore</label></div>' +
										'<div class="form-check"><input class="form-check-input" type="checkbox" id="admin"<?php if ($row['Admin'] == 1) echo ' checked=""'; ?> onchange="change_admin(this.checked);"><label class="form-check-label" for="admin">Amministratore</label></div>' +
										'<br><button class="btn btn-danger" id="resetpwd" onclick="resetpwd();"><i class="bi bi-x-lg"></i> Resetta parola chiave</button>' +
										'<span class="text-danger" id="erroreaggiornapermessi"></span>', '<button type="button" class="btn btn-primary" onclick="aggiornapermessi();"><i class="bi bi-check-circle-fill"></i> Salva</button>');
									}

									function change_login(login) {
										$('#editor').prop("disabled", !login).prop("checked", false);
										$('#admin').prop("disabled", true).prop("checked", false);
										$('#resetpwd').prop("disabled", !login);
									}
									
									function change_editor(editor) {
										$('#admin').prop("disabled", !editor)
										if (!editor)
											$('#admin').prop("checked", false);
									}
									
									function change_admin(admin) {
										if (admin)
											$('#editor').prop("disabled", false).prop("checked", true);
									}
									
									function resetpwd() {
										var xhttp = new XMLHttpRequest();
										xhttp.onreadystatechange = function() {
											if (this.readyState == 4 && this.status == 200) {
												if (isNaN(parseInt(this.responseText))) {
													$('#erroreaggiornapermessi').html(this.responseText);
												} else {
													location.reload();
												}
											}
										};
										xhttp.open("POST", "php/ajax_admin.php", true);
										xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
										xhttp.send("ajax=resetpwd&id=<?php echo $id; ?>");
									}

									function aggiornapermessi() {
										var xhttp = new XMLHttpRequest();
										xhttp.onreadystatechange = function() {
											if (this.readyState == 4 && this.status == 200) {
												if (isNaN(parseInt(this.responseText))) {
													$('#erroreaggiornapermessi').html(this.responseText);
												} else {
													location.reload();
												}
											}
										};
										xhttp.open("POST", "php/ajax_admin.php", true);
										xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
										xhttp.send("ajax=aggiornapermessi&id=<?php echo $id; ?>&login=" + ($('#login').is(':checked')?1:0) + "&editor=" + ($('#editor').is(':checked')?1:0) + "&admin=" + ($('#admin').is(':checked')?1:0));
									}
								</script>
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
			
			<br>
			<div class="row"><div class="col-lg-2"></div><div class="col-lg">
				<?php
				$res2 = $conn->query("SELECT * from partecipazioni join partite on partecipazioni.Partita = partite.IdPartita where partecipazioni.Giocatore = $id group by partite.IdPartita order by partite.Data desc;");
				if ($res2->num_rows > 0) {
					echo '<h4 class="text-center"><strong>' . ($res2->num_rows == 1 ? 'La bi$ca' : 'Le ' . $res2->num_rows . ' bi$che') . ' che ha disputato:</strong></h4>';
					$anno = false;
					$partite = array();
					while ($row2 = $res2->fetch_assoc()) {
						$partita = partita($row2['IdPartita']);
						$numturni = $partita[4][0] + $partita[4][1] + $partita[4][2];
						
						if (substr($row2['Data'], 0, 4) != $anno) {
							$anno = substr($row2['Data'], 0, 4);
							echo '<br><h3 class="text-center">' . $anno . '</h3><hr>';
						}
						
						echo '<a class="dropdown-item" href="partite.php?id=' . $row2['IdPartita'] . '"><div class="row">';
						echo '<div class="col-2 d-inline-block my-auto" style="text-align: right;">' . $partita[3][0][$id] . '<i class="bi bi-play-fill"></i></div>'; // Turni che ha giocato
						echo '<div class="col-8 no-pad d-inline-block"><div class="row" style="max-width: 100%;">';
							echo '<div class="col-md no-pad text-truncate" style="text-align: left; aline-height: 18px;">' . (empty($row2['Occasione']) ? '<span class="chiaro"><i>Occasione sconosciuta</i></span>' : $row2['Occasione']) . '</div>'; // Occasione
							echo '<div class="col-md-3" style="text-align: left;"><small class="chiaro d-block d-md-none" style="line-height: 15px;"><i>&nbsp;' . $fmt3->format(strtotime($row2['Data'])) . '</i></small><small class="chiaro d-none d-md-block"><i>&nbsp;' . $fmt3->format(strtotime($row2['Data'])) . '</i></small></div>'; // Data
						echo '</div></div>';
						//echo '<div class="col-2 no-pad text-left" style="text-align: left;"><span class="d-block d-sm-none">' . $fmt2->format(strtotime($row2['Data'])) . '</span><span class="d-none d-sm-block">' . $fmt3->format(strtotime($row2['Data'])) . '</span></div>'; // Data
						
						// Classifica e punteggio finale
						if ($partita[3][0][$id] == $numturni) { // Ha iniziato e finito
							$puntiseri = $partita[1][$row2['Colonna'] - 1];
						} else { // Un conteggio per ogni intervallo intermedio:
							$rg1 = $conn->query("select * from partecipazioni where Partita = " . $row2['IdPartita'] . " and Giocatore = $id order by Inizio;");
							$punti = 0; // Punti fino alla fine del primo intervento
							$puntiseri = 0; // Punti di tutti gli interventi
							$j = 0;
							$i = 0;
							while ($ro1 = $rg1->fetch_assoc()) {
								if ($j == 0) {
									for (; $i < $ro1['Inizio'] - 1; $i++) {
										$punti += $partita[0][$i][$ro1['Colonna'] - 1];
									}
									$primoinizio = $ro1['Inizio'];
								}
								
								$rg2 = $conn->query("select * from partecipazioni where Partita = " . $row2['IdPartita'] . " and Colonna = " . $ro1['Colonna'] . " and Inizio > " . $ro1['Inizio'] . " and Giocatore <> $id order by Inizio;");
								$fine = ($rg2->num_rows == 0 ? $numturni + 1 : $rg2->fetch_assoc()['Inizio']);
								for ($i = $ro1['Inizio'] - 1; $i < $fine - 1; $i++) {
									if ($j == 0)
										$punti += $partita[0][$i][$ro1['Colonna'] - 1];
									$puntiseri += $partita[0][$i][$ro1['Colonna'] - 1];
								}
								$j++;
							}
						}
						
						echo '<div class="col-2 no-pad my-auto" style="text-align: left;">';
						if (($partita[3][0][$id] / $numturni) >= 0.5) { // Ha giocato almeno metà dei turni, medaglia
							echo '<img src="img/Medaglia' . $partita[5][$row2['Colonna'] - 1] . '.png" height=25px>';
						} else { // Ha giocato poco, icona porta
							echo '&nbsp;<i class="bi bi-door-open"></i>&nbsp;';
						}
						echo ($partita[3][0][$id] == $numturni ? '<strong>' : '') . ($puntiseri > 0 ? '+' : '') . $puntiseri . ($partita[3][0][$id] == $numturni ? '</strong>' : '');
						echo '</div></div></a>';
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
