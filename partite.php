<!DOCTYPE html>
<html lang="it-IT" data-bs-theme="<?php echo (isset($_COOKIE['tema']) ? $_COOKIE['tema'] : 'auto'); ?>">
<head>
	<?php
	include "php/bootstrap.php";
	$title = 'L\'almanacco della Bi$ca';
	if (isset($_GET['id'])) {
		$id = $conn->real_escape_string(stripslashes($_GET['id']));
		$res = $conn->query("SELECT * FROM partite WHERE IdPartita = '$id';");
		if ($res->num_rows == 1) {
			$title = $res->fetch_assoc()['Occasione'] . ' - La Bi$ca';
		}
	}
	echo '<title>' . $title . '</title>';
	?>
</head>
<body onload="marquee();" onresize="marquee();">
	<?php echo head(); ?>
	<div class="container-fluid">
		<?php
		$logged = isset($_SESSION['id']) && $_SESSION['editor'];

		if (isset($_GET['id'])) {
			$id = $conn->real_escape_string(stripslashes($_GET['id']));
			$res = $conn->query("SELECT * FROM partite WHERE IdPartita = $id;");
			if ($res->num_rows == 1) {
				$edit = $logged && isset($_GET['edit']);
				?>
				<div class="row">
					<div class="col-lg-1"></div>
					<div class="col" style="margin-bottom: <?php echo $logged ? '4rem;' : '2rem;'; ?>">
						<span class="toggle-easter-egg text-center">
							<h1 style="font-family: Vivaldi; font-weight: bold; font-size: 70px;" class="d-none d-md-block">il Giuoco del Due</h1>
							<h1 style="font-family: Vivaldi; font-weight: bold; font-size: 60px;" class="d-none d-sm-block d-md-none">il Giuoco del Due</h1>
							<h1 style="font-family: Vivaldi; font-weight: bold; font-size: 40px;" class="d-sm-none">il Giuoco del Due</h1>
						</span>

						<div id="partita" class="mb-4">
							<?php echo mostra_partita($id, $edit); ?>
						</div>

						<?php
						// Pulsante modifica/torna in visualizzazione
						if ($logged) {
							echo btn_angolo($edit ? 'info' : 'success', 'partite.php?id=' . $id . (!$edit ? '&edit=true' : ''), $edit ? 'check-lg' : 'pencil-fill');
						}

						// Modifiche
						if ($edit) {
							?>
							<hr>
							<div class="row mb-2">
								<div class="col-sm p-1"><button class="btn btn-info h-100 w-100" onclick="modalfoto();"><i class="bi bi-camera-fill"></i> Foto allegate</button></div>
								<div class="col-sm p-1"><button class="btn btn-primary h-100 w-100" onclick="multiturno();"><i class="bi bi-patch-plus-fill"></i> Aggiungi più turni</button></div>
								<?php
								if ($_SESSION['admin']) {
									$row = $res->fetch_assoc();
									echo '<script>var puntegginascosti = ' . ($row['PuntiNascosti'] ? 'true' : 'false') . ';</script>';
									?>
									<div class="col-sm p-1"><button id="btnmostrapunteggi" class="btn btn-warning h-100 w-100" onclick="mostrapunteggi();"><?php echo ($row['PuntiNascosti'] ? '<i class="bi bi-unlock-fill"></i> Svela i punteggi' : '<i class="bi bi-lock-fill"></i> Cela i punteggi'); ?></button></div>
									<div class="col-sm p-1"><button class="btn btn-danger h-100 w-100" onclick="modaleliminapartita();"><i class="bi bi-trash"></i> Elimina la partita</button></div>
									<?php
								}
								?>
							</div>
							<?php
						}
						?>
					</div>
					<div class="col-lg-1"></div>
				</div>
				<script>
					var id = <?php echo $id; ?>;
					var edit = <?php echo ($edit ? 'true' : 'false'); ?>;
				</script>
				<script src="js/partite.js"></script>
				<script src="js/eggpartite.js"></script>
				<?php
			} else {
				echo 'La partita cercata non esiste.';
			}
		} else {
			?>
			<div class="text-center">
				<h1 style="font-family: Vivaldi; font-size: 50px;" class="mb-0 toggle-easter-egg">Almanacco</h1>
				<p>L'archivio dei tornei al Giuoco del Due</p>
			
				<?php
				if (isset($_SESSION['id']) && $_SESSION['editor']) {
					?>
					<button class="btn btn-primary mb-3" onclick="nuova();"><i class="bi bi-journal-plus"></i> Nuova partita</button>
					<script>
					function nuova() {
						var xhttp = new XMLHttpRequest();
						xhttp.onreadystatechange = function() {
							if (this.readyState == 4 && this.status == 200) {
								if (isNaN(parseInt(this.responseText))) {
									modal('Errore', this.responseText, false);
								} else {
									window.location.href = 'partite.php?id=' + this.responseText + '&edit=true';
								}
							}
						};
						xhttp.open("POST", "php/ajax.php", true);
						xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
						xhttp.send("ajax=nuovapartita");
					}
					</script>
					<?php
				}
				?>
			</div>
			<?php
			$res = $conn->query("SELECT partite.IdPartita, partite.Data, partite.Occasione, partite.Note, COUNT(mani.Numero) AS Turni FROM partite LEFT JOIN mani ON partite.IdPartita = mani.Partita GROUP BY partite.IdPartita ORDER BY partite.Data desc, IdPartita DESC;");
			if ($res->num_rows > 0) {
				echo '<div class="row"><div class="col-lg-2"></div><div class="col-lg">';
				echo '<div class="position-relative">';
				echo '<input type="text" id="cerca_partita" class="form-control w-100 my-4 mx-auto" placeholder="Cerca una partita..." onkeyup="cerca_partita();" />';
				echo '<button class="btn btn-sm btn-outline-secondary h-100" onclick="$(\'#cerca_partita\').val(\'\'); cerca_partita();" style="position: absolute; top: 0px; right: 0px;"><i class="bi bi-x-lg"></i></button>';
				echo '</div>';

				$anno = false;
				$partite = array();
				$partite_anno = array();
				while ($row = $res->fetch_assoc()) {
					if (substr($row['Data'], 0, 4) != $anno) {
						$anno = substr($row['Data'], 0, 4);
						$partite_anno[$anno] = 0;
						echo '<div id="anno_' . $anno . '"><h3 class="mt-3 text-center"><a href="anni.php?anno=' . $anno . '">' . $anno . '</a></h3><hr></div>';
					}
					$partite[] = array('id' => $row['IdPartita'], 'occasione' => $row['Occasione'], 'note' => $row['Note'], 'anno' => $anno);
					$partite_anno[$anno]++;

					echo '<a id="partita_' . $row['IdPartita'] . '" class="dropdown-item" href="partite.php?id=' . $row['IdPartita'] . '"><div class="row">';
					echo '<div class="col-1 no-pad text-end">' . ($row['Turni'] < 10 ? '&nbsp;&nbsp;' : '') . $row['Turni'] . '<i class="bi bi-play-fill"></i></div>';
					echo '<div class="col text-start d-inline-block text-truncate">' . (empty($row['Occasione']) ? '<span class="chiaro"><i>Occasione sconosciuta</i></span>' : $row['Occasione']) . '</div>';
					echo '<div class="col-auto text-end px-0 px-sm-3"><small class="chiaro"><i class="d-block d-sm-none">' . $fmt2->format(strtotime($row['Data'])) . '</i><i class="d-none d-sm-block">' . $fmt3->format(strtotime($row['Data'])) . '</i></small></div></div></a>';
				}
				echo '</div><div class="col-lg-2"></div></div><br>';
				?>
				<script>
				let partite = <?php echo json_encode($partite); ?>;
				let partite_anno = <?php echo json_encode($partite_anno); ?>;

				function cerca_partita() {
					let input = $('#cerca_partita').val().toLowerCase();
					$.each(partite_anno, function(anno) {
						partite_anno[anno] = 0;
					});
					partite.forEach(function(p) {
						let elem = $('#partita_' + p.id);
						if (p.occasione.toLowerCase().includes(input) || (p.note != null && p.note.toLowerCase().includes(input))) {
							elem.show();
							partite_anno[p.anno]++;
						} else {
							elem.hide();
						}
					});
					$.each(partite_anno, function(anno) {
						if (partite_anno[anno] == 0) {
							$('#anno_' + anno).hide();
						} else {
							$('#anno_' + anno).show();
						}
					});
				}
				</script>
				<?php
			}
		}
		?>
	</div>
	<script>
	function marquee() {
		$('.longx').each(function() {
			if ($(this).width() > $(this).parent().width())
				$(this).addClass('marquee');
			else if ($(this).hasClass('marquee'))
				$(this).removeClass('marquee');
		});
	}
	</script>
	<div id="fulminevecia" style="display: none; width: 100vw; height: 100vh; top: 0; right: 0; z-index: 2000; position: fixed; background-color: #000; background-image: url('media/img/gif/fulmini_vecia.gif'); background-repeat: no-repeat; background-attachment: fixed; background-position: center; background-size: cover;">
		<!--img id="imgfulminevecia" src="media/img/gif/fulmine_vecia2.gif" style="height: 100%;" /-->
	</div>
<?php include "php/bootstrap2.php"; ?></body>
</html>
