<html>
<head>
	<?php
	include "php/bootstrap.php";
	$title = 'L\'almanacco della Bi$ca';
	if (isset($_GET['id'])) {
		$id = $conn->real_escape_string(stripslashes($_GET['id']));
		$res = $conn->query("select * from partite where IdPartita = '$id';");
		if ($res->num_rows == 1) {
			$title = $res->fetch_assoc()['Occasione'] . ' - La Bi$ca';
		}
	}
	echo '<title>' . $title . '</title>';
	?>
</head>
<body onload="marquee();" onresize="marquee();"><center>
	<?php echo head(); ?>
	<div class="container-fluid">
		<?php
		if (isset($_GET['id'])) {
			$id = $conn->real_escape_string(stripslashes($_GET['id']));
			$res = $conn->query("SELECT * FROM partite WHERE IdPartita = $id;");
			if ($res->num_rows == 1) {
				?>
				<div class="row">
					<div class="col-lg-1"></div>
					<div class="col">
						<h1 style="font-family: Vivaldi; font-weight: bold; font-size: 70px;" class="d-none d-md-block">il Giuoco del Due</h1>
						<h1 style="font-family: Vivaldi; font-weight: bold; font-size: 60px;" class="d-none d-sm-block d-md-none">il Giuoco del Due</h1>
						<h1 style="font-family: Vivaldi; font-weight: bold; font-size: 40px;" class="d-sm-none">il Giuoco del Due</h1>
						<?php
						$edit = isset($_SESSION['id']) && $_SESSION['editor'] && isset($_GET['edit']);
						echo mostra_partita($res->fetch_assoc(), $edit);

						// Modifiche
						if ($edit) {
							?>
								<hr>
								<button class="btn btn-info mb-1" onclick="modalfoto();"><i class="bi bi-camera-fill"></i> Foto allegate</button>&nbsp;
								<button class="btn btn-primary mb-1" onclick="multiturno();"><i class="bi bi-patch-plus-fill"></i> Aggiungi più turni</button>&nbsp;
							<?php
							if ($_SESSION['admin']) {
								?>
								<button class="btn btn-danger mb-1" onclick="modaleliminapartita();"><i class="bi bi-trash"></i> Elimina la partita</button>
								<?php
							}
							echo '<br>';
						}
						?>
						<br>
					</div>
					<div class="col-lg-1"></div>
				</div>
				<script>
					var id = <?php echo $id; ?>;
				</script>
				<script src="js/partite.js"></script>
				<?php
			} else {
				echo 'La partita cercata non esiste.';
			}
		} else {
			?>
			<h1 style="font-family: Vivaldi; font-size: 50px;" class="mb-0">Almanacco</h1>
			<p>L'archivio dei tornei al Giuoco del Due</p>
			
			<?php
			if (isset($_SESSION['id']) && $_SESSION['editor']) {
				?>
				<button class="btn btn-primary" onclick="nuova();"><i class="bi bi-journal-plus"></i> Nuova partita</button>
				<br><br>
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
			$res = $conn->query("select * from partite order by Data desc;");
			if ($res->num_rows > 0) {
				echo '<div class="row"><div class="col-lg-2"></div><div class="col-lg">';
				$anno = false;
				while ($row = $res->fetch_assoc()) {
					if (substr($row['Data'], 0, 4) != $anno) {
						$anno = substr($row['Data'], 0, 4);
						echo '<br><h3>' . $anno . '</h3><hr>';
					}
					$partita = partita($row['IdPartita']);
					$turni = ($partita[4][0] + $partita[4][1] + $partita[4][2]);
					echo '<a class="dropdown-item" href="partite.php?id=' . $row['IdPartita'] . '"><div class="row">';
					echo '<div class="col-1 no-pad" style="text-align: right;">' . ($turni < 10 ? '&nbsp;&nbsp;' : '') . $turni . '<i class="bi bi-play-fill"></i></div>';
					echo '<div class="col d-inline-block text-truncate" style="text-align: left;">' . (empty($row['Occasione']) ? '<span class="chiaro"><i>Occasione sconosciuta</i></span>' : $row['Occasione']) . '</div>';
					echo '<div class="col-auto text-left" style="text-align: right;"><small class="chiaro"><i class="d-block d-sm-none">' . $fmt2->format(strtotime($row['Data'])) . '</i><i class="d-none d-sm-block">' . $fmt3->format(strtotime($row['Data'])) . '</i></small></div></div></a>';
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
	<div id="fulminevecia" style="display: none; width: 100vw; height: 100vh; top: 0; right: 0; z-index: 2000; position: fixed; background-color: #000; background-image: url('img/gif/fulmini_vecia.gif'); background-repeat: no-repeat; background-attachment: fixed; background-position: center; background-size: cover;">
		<!--img id="imgfulminevecia" src="img/gif/fulmine_vecia2.gif" style="height: 100%;" /-->
	</div>
</center><?php include "php/bootstrap2.php"; ?></body>
</html>
