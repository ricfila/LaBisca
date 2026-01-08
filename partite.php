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
		if (isset($_GET['id'])) {
			$id = $conn->real_escape_string(stripslashes($_GET['id']));
			$res = $conn->query("SELECT * FROM partite WHERE IdPartita = $id;");
			if ($res->num_rows == 1) {
				$logged = isset($_SESSION['id']) && $_SESSION['editor'];
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
							?>
							<div style="position: fixed; bottom: 20px; right: 20px; z-index: 900;">
								<a class="btn btn-lg btn-<?php echo ($edit ? 'info' : 'success'); ?>" href="partite.php?id=<?php echo $id . (!$edit ? '&edit=true' : ''); ?>" style="width: 60px; height: 60px; line-height: 45px; border-radius: 50%; box-shadow: var(--ombra) 3px 3px 10px; font-size: 1.5em;"><i class="bi bi-<?php echo ($edit ? 'check-lg' : 'pencil-fill'); ?>"></i></a>
							</div>
							<?php
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
			$res = $conn->query("SELECT partite.IdPartita, partite.Data, partite.Occasione, COUNT(mani.Numero) AS Turni FROM partite LEFT JOIN mani ON partite.IdPartita = mani.Partita GROUP BY partite.IdPartita ORDER BY partite.Data desc, IdPartita DESC;");
			if ($res->num_rows > 0) {
				echo '<div class="row"><div class="col-lg-2"></div><div class="col-lg">';
				$anno = false;
				while ($row = $res->fetch_assoc()) {
					if (substr($row['Data'], 0, 4) != $anno) {
						$anno = substr($row['Data'], 0, 4);
						echo '<h3 class="mt-3 text-center"><a href="anni.php?anno=' . $anno . '">' . $anno . '</a></h3><hr>';
					}
					echo '<a class="dropdown-item" href="partite.php?id=' . $row['IdPartita'] . '"><div class="row">';
					echo '<div class="col-1 no-pad text-end">' . ($row['Turni'] < 10 ? '&nbsp;&nbsp;' : '') . $row['Turni'] . '<i class="bi bi-play-fill"></i></div>';
					echo '<div class="col text-start d-inline-block text-truncate">' . (empty($row['Occasione']) ? '<span class="chiaro"><i>Occasione sconosciuta</i></span>' : $row['Occasione']) . '</div>';
					echo '<div class="col-auto text-end px-0 px-sm-3"><small class="chiaro"><i class="d-block d-sm-none">' . $fmt2->format(strtotime($row['Data'])) . '</i><i class="d-none d-sm-block">' . $fmt3->format(strtotime($row['Data'])) . '</i></small></div></div></a>';
				}
				echo '</div><div class="col-lg-2"></div></div><br>';
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
