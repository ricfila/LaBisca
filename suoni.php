<!DOCTYPE html>
<html lang="it-IT" data-bs-theme="<?php echo (isset($_COOKIE['tema']) ? $_COOKIE['tema'] : 'auto'); ?>">
<head>
	<title>La Bi$ca</title>
	<?php include "php/bootstrap.php"; ?>
</head>
<body onload="load();" class="text-center">
	<?php echo head(); ?>
	<div class="container-fluid">
	<img id="imglogo" height="100px"><br><br>
	<div class="row"><div class="col-lg-1"></div>
	<div class="col-sm-8 col-lg-7">
		
		<h4>Effetti sonori</h4>
		<div class="text-end">
			<button class="btn btn-sm btn-outline-dark" onclick="stop();"><i class="bi bi-stop-fill"></i> Ferma tutto</button>
		</div>

		<ul class="nav nav-tabs" role="tablist">
			<li class="nav-item" role="presentation">
				<a class="nav-link active" data-bs-toggle="tab" href="#suoni" aria-selected="true" role="tab"><i class="bi bi-volume-up-fill"></i></a>
			</li>
			<li class="nav-item" role="presentation">
				<a class="nav-link" data-bs-toggle="tab" href="#allarmi" aria-selected="false" role="tab" tabindex="-1"><i class="bi bi-exclamation-triangle-fill"></i></a>
			</li>
			<li class="nav-item" role="presentation">
				<a class="nav-link" data-bs-toggle="tab" href="#film" aria-selected="false" role="tab" tabindex="-1"><i class="bi bi-chat-dots"></i></a>
			</li>
			<?php if (isset($_SESSION['id'])) { ?>
			<li class="nav-item" role="presentation">
				<a class="nav-link" data-bs-toggle="tab" href="#voci" aria-selected="false" role="tab" tabindex="-1"><i class="bi bi-mic-fill"></i></a>
			</li>
			<?php } ?>
		</ul>

		<div class="tab-content">
			<?php
			$suoni = array(
				'suoni' => array(
					'Inizio', 'Punti', 'Applausi', 'Clacson', 'Delusione', 'Fallimento', 'Schiaffo', 'Trombone', 'Cavallo', 'Attesa'
				),
				'allarmi' => array(
					'Preparazione', 'Caduta_bomba', 'Impatto', 'Tuono', 'Allarme', 'Un_demone', 'Un_mostro', 'Mio_Dio', 'Psyco', 'Tan_tan_tan'
				),
				'film' => array(
					'Carica', 'Oh_no', 'Classico', 'Disonore', 'Eccomi_qua', 'Tombola', 'Mangiafuoco', 'Tutto_mio', 'Disgrazie', 'Niente_di_niente', 'Rilevante', 'Bisbigliare',  'Minatore'
				)
			);

			if (isset($_SESSION['id'])) {
				$suoni['voci'] = array(
					'Si_si_e_si', 'Siii', 'Zolia_carte', 'Che_vedo', 'Falso', 'Par_carita', 'Piacere'
				);
			}

			foreach ($suoni as $categoria => $lista) {
				echo '<div class="tab-pane fade text-start pt-2' . ($categoria == 'suoni' ? ' active show' : '') . '" id="' . $categoria . '" role="tabpanel">';
				foreach ($lista as $s) {
					echo '<audio id="' . $s . '"><source src="media/suoni/' . $s . '.mp3" type="audio/mp3"></audio>';
					echo '<img src="media/suoni/img/' . $s . '.jpg" onclick="suono(\'' . $s . '\');" class="img_suono" alt="' . $s . '" title="' . $s . '">';
				}
				echo '</div>';
			}
			?>
		</div>
		
		<script>
		function load() {
			$('audio').each(function() {
				this.preload = "auto";
			});
		}

		function suono(nome) {
			let audio = document.getElementById(nome);
			audio.currentTime = 0;
			audio.play();
		}
		
		function stop() {
			$('audio').each(function() {
				this.pause();
				this.currentTime = 0;
			});
		}
		</script>
		
	</div><div class="col-sm-4 col-lg-3"><hr class="d-sm-none" />
		<h4>La Biscarmonica</h4>
		<iframe src="https://open.spotify.com/embed/playlist/29iClBvg7BDzvLg4AcccFK" width="100%" height="380" frameBorder="0" allowtransparency="true" allow="encrypted-media"></iframe>
		
		
	</div><div class="col-lg-1"></div></div>
	</div>
<?php include "php/bootstrap2.php"; ?>
</body>
</html>
