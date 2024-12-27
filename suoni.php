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
		
		<!--audio id="inizio"><source src="media/suoni/Inizio.mp3" type="audio/mp3"></audio>
		<audio id="punti"><source src="media/suoni/Punti.mp3" type="audio/mp3"></audio>
		<audio id="applausi"><source src="media/suoni/Applausi.mp3" type="audio/mp3"></audio>
		<audio id="clacson"><source src="media/suoni/Clacson.mp3" type="audio/mp3"></audio>
		
		<audio id="delusione"><source src="media/suoni/Delusione.mp3" type="audio/mp3"></audio>
		<audio id="fallimento"><source src="media/suoni/Fallimento.mp3" type="audio/mp3"></audio>
		<audio id="schiaffo"><source src="media/suoni/Schiaffo.mp3" type="audio/mp3"></audio>
		<audio id="trombone"><source src="media/suoni/Trombone.mp3" type="audio/mp3"></audio>
		
		<audio id="preparazione"><source src="media/suoni/Preparazione.mp3" type="audio/mp3"></audio>
		<audio id="psyco"><source src="media/suoni/Psyco.mp3" type="audio/mp3"></audio>
		<audio id="tantantan"><source src="media/suoni/Tan_tan_tan.mp3" type="audio/mp3"></audio>
		
		<audio id="impatto"><source src="media/suoni/Impatto.mp3" type="audio/mp3"></audio>
		<audio id="cadutabomba"><source src="media/suoni/Caduta_bomba.mp3" type="audio/mp3"></audio>
		<audio id="tuono"><source src="media/suoni/Tuono.mp3" type="audio/mp3"></audio>
		<audio id="allarme"><source src="media/suoni/Allarme.mp3" type="audio/mp3"></audio>
		
		<audio id="carica"><source src="media/suoni/Carica.mp3" type="audio/mp3"></audio>
		<audio id="ohno"><source src="media/suoni/Oh_no.mp3" type="audio/mp3"></audio>
		<audio id="eccomiqua"><source src="media/suoni/Eccomi_qua.mp3" type="audio/mp3"></audio>
		<audio id="tombola"><source src="media/suoni/Tombola.mp3" type="audio/mp3"></audio>
		<audio id="miodio"><source src="media/suoni/Mio_Dio.mp3" type="audio/mp3"></audio>
		<audio id="tuttomio"><source src="media/suoni/Tutto_mio.mp3" type="audio/mp3"></audio>
		<audio id="classico"><source src="media/suoni/Classico.mp3" type="audio/mp3"></audio>
		<audio id="demone"><source src="media/suoni/Un_demone.mp3" type="audio/mp3"></audio>
		<audio id="mostro"><source src="media/suoni/Un_mostro.mp3" type="audio/mp3"></audio>
		<audio id="disgrazie"><source src="media/suoni/Disgrazie.mp3" type="audio/mp3"></audio>
		<audio id="cavallo"><source src="media/suoni/Cavallo.mp3" type="audio/mp3"></audio>
		<audio id="nientediniente"><source src="media/suoni/Niente_di_niente.mp3" type="audio/mp3"></audio>
		<audio id="rilevante"><source src="media/suoni/Rilevante.mp3" type="audio/mp3"></audio>
		<audio id="minatore"><source src="media/suoni/Minatore.mp3" type="audio/mp3"></audio>
		<audio id="mangiafuoco"><source src="media/suoni/Mangiafuoco.mp3" type="audio/mp3"></audio>
		<audio id="disonore"><source src="media/suoni/Disonore.mp3" type="audio/mp3"></audio>
		<audio id="bisbigliare"><source src="media/suoni/Bisbigliare.mp3" type="audio/mp3"></audio-->

		
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
					'Inizio', 'Punti', 'Applausi', 'Clacson', 'Delusione', 'Fallimento', 'Schiaffo', 'Trombone', 'Cavallo'
				),
				'allarmi' => array(
					'Preparazione', 'Psyco', 'Tan_tan_tan', 'Caduta_bomba', 'Impatto', 'Tuono', 'Allarme', 'Un_demone', 'Un_mostro', 'Mio_Dio'
				),
				'film' => array(
					'Carica', 'Tutto_mio', 'Tombola', 'Bisbigliare', 'Oh_no', 'Disgrazie', 'Eccomi_qua', 'Classico', 'Disonore', 'Niente_di_niente', 'Rilevante', 'Minatore', 'Mangiafuoco'
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
					//echo '<div class="col-3">';
					echo '<audio id="' . $s . '"><source src="media/suoni/' . $s . '.mp3" type="audio/mp3"></audio>';
					echo '<img src="media/suoni/img/' . $s . '.jpg" onclick="suono(\'' . $s . '\');" class="img_suono" alt="' . $s . '" title="' . $s . '">';
					//echo '</div>';
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
			var audio = document.getElementById(nome);
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
