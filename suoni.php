<!DOCTYPE html>
<html lang="it-IT" data-bs-theme="<?php echo $_COOKIE['tema'] ?: 'auto'; ?>">
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
		
		<audio id="inizio"><source src="media/suoni/mixkit-bonus-extra-in-a-video-game-2064.mp3" type="audio/mp3"></audio>
		<audio id="punti"><source src="media/suoni/mixkit-player-recharging-in-video-game-2041.mp3" type="audio/mp3"></audio>
		<audio id="applausi"><source src="media/suoni/mixkit-ending-show-audience-clapping-478.mp3" type="audio/mp3"></audio>
		<audio id="clacson"><source src="media/suoni/Clacson.mp3" type="audio/mp3"></audio>
		
		<audio id="delusione"><source src="media/suoni/mixkit-arcade-space-shooter-dead-notification-272.mp3" type="audio/mp3"></audio>
		<audio id="fallimento"><source src="media/suoni/mixkit-player-losing-or-failing-2042.mp3" type="audio/mp3"></audio>
		<audio id="schiaffo"><source src="media/suoni/mixkit-axe-hits-to-a-plate-2774.mp3" type="audio/mp3"></audio>
		<audio id="trombone"><source src="media/suoni/Trombone.mp3" type="audio/mp3"></audio>
		
		<audio id="preparazione"><source src="media/suoni/mixkit-arcade-rising-231.mp3" type="audio/mp3"></audio>
		<audio id="psyco"><source src="media/suoni/Psyco.mp3" type="audio/mp3"></audio>
		<audio id="tantantan"><source src="media/suoni/Tan_tan_tan.mp3" type="audio/mp3"></audio>
		
		<audio id="impatto"><source src="media/suoni/mixkit-dramatic-metal-explosion-impact-1687.mp3" type="audio/mp3"></audio>
		<audio id="cadutabomba"><source src="media/suoni/mixkit-bomb-drop-impact-2804.mp3" type="audio/mp3"></audio>
		<audio id="esplosione"><source src="media/suoni/mixkit-low-explosion-indoors-2187.mp3" type="audio/mp3"></audio>
		<audio id="tuono"><source src="media/suoni/mixkit-distant-thunder-explosion-1278.mp3" type="audio/mp3"></audio>
		<audio id="allarme"><source src="media/suoni/mixkit-facility-alarm-sound-999.mp3" type="audio/mp3"></audio>
		
		<audio id="six3"><source src="media/suoni/Si_si_e_si.mp3" type="audio/mp3"></audio>
		<audio id="siii"><source src="media/suoni/Siii.mp3" type="audio/mp3"></audio>
		<audio id="carica"><source src="media/suoni/Carica.mp3" type="audio/mp3"></audio>
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
		
		<h4>Effetti sonori</h4>
		<p style="text-align: left;">
			<button class="btn btn-sm btn-success mb-1" onclick="suono('inizio');"><i class="bi bi-play-fill"></i> Inizio</button>
			<button class="btn btn-sm btn-success mb-1" onclick="suono('punti');"><i class="bi bi-play-fill"></i> Punti</button>
			<button class="btn btn-sm btn-success mb-1" onclick="suono('applausi');"><i class="bi bi-play-fill"></i> Applausi</button>
			<button class="btn btn-sm btn-success mb-1" onclick="suono('clacson');"><i class="bi bi-play-fill"></i> Clacson</button>
			<br>
			<button class="btn btn-sm btn-warning mb-1" onclick="suono('delusione');"><i class="bi bi-play-fill"></i> Delusione</button>
			<button class="btn btn-sm btn-warning mb-1" onclick="suono('fallimento');"><i class="bi bi-play-fill"></i> Fallimento</button>
			<button class="btn btn-sm btn-warning mb-1" onclick="suono('schiaffo');"><i class="bi bi-play-fill"></i> Schiaffo</button>
			<button class="btn btn-sm btn-warning mb-1" onclick="suono('trombone');"><i class="bi bi-play-fill"></i> Trombone</button>
			<br>
			<button class="btn btn-sm btn-secondary mb-1" onclick="suono('preparazione');"><i class="bi bi-play-fill"></i> Preparazione</button>
			<button class="btn btn-sm btn-secondary mb-1" onclick="suono('psyco');"><i class="bi bi-play-fill"></i> Psyco</button>
			<button class="btn btn-sm btn-secondary mb-1" onclick="suono('tantantan');"><i class="bi bi-play-fill"></i> Tan tan tan</button>
			<br>
			<button class="btn btn-sm btn-danger mb-1" onclick="suono('cadutabomba');"><i class="bi bi-play-fill"></i> Caduta bomba</button>
			<button class="btn btn-sm btn-danger mb-1" onclick="suono('esplosione');"><i class="bi bi-play-fill"></i> Esplosione</button><br>
			<button class="btn btn-sm btn-dark mb-1" onclick="suono('impatto');"><i class="bi bi-play-fill"></i> Impatto</button>
			<button class="btn btn-sm btn-dark mb-1" onclick="suono('tuono');"><i class="bi bi-play-fill"></i> Tuono</button>
			<button class="btn btn-sm btn-dark mb-1" onclick="suono('allarme');"><i class="bi bi-play-fill"></i> Allarme</button>
		</p>
		<h4>Voci e versi</h4>
		<p style="text-align: left;">
			<button class="btn btn-sm btn-success mb-1" onclick="suono('six3');"><i class="bi bi-play-fill"></i> Sì... sì e sì</button>
			<button class="btn btn-sm btn-success mb-1" onclick="suono('siii');"><i class="bi bi-play-fill"></i> Sììì</button>
			<button class="btn btn-sm btn-success mb-1" onclick="suono('carica');"><i class="bi bi-play-fill"></i> Carica</button>
			<button class="btn btn-sm btn-success mb-1" onclick="suono('tuttomio');"><i class="bi bi-play-fill"></i> Tutto mio</button>
			<button class="btn btn-sm btn-success mb-1" onclick="suono('tombola');"><i class="bi bi-play-fill"></i> Tombola</button>
			<br>
			<button class="btn btn-sm btn-info mb-1" onclick="suono('disgrazie');"><i class="bi bi-play-fill"></i> Disgrazie</button>
			<button class="btn btn-sm btn-info mb-1" onclick="suono('miodio');"><i class="bi bi-play-fill"></i> Mio Dio</button>
			<button class="btn btn-sm btn-info mb-1" onclick="suono('eccomiqua');"><i class="bi bi-play-fill"></i> Eccomi qua</button>
			<button class="btn btn-sm btn-info mb-1" onclick="suono('classico');"><i class="bi bi-play-fill"></i> È un classico</button>
			<br>
			<button class="btn btn-sm btn-danger mb-1" onclick="suono('nientediniente');"><i class="bi bi-play-fill"></i> Niente di niente</button>
			<button class="btn btn-sm btn-danger mb-1" onclick="suono('rilevante');"><i class="bi bi-play-fill"></i> Rilevante</button>
			<button class="btn btn-sm btn-danger mb-1" onclick="suono('minatore');"><i class="bi bi-play-fill"></i> Minatore</button>
			<br>
			<button class="btn btn-sm btn-warning mb-1" onclick="suono('cavallo');"><i class="bi bi-play-fill"></i> Cavallo</button>
			<button class="btn btn-sm btn-warning mb-1" onclick="suono('mangiafuoco');"><i class="bi bi-play-fill"></i> Mangiafuoco</button>
			<button class="btn btn-sm btn-dark mb-1" onclick="suono('demone');"><i class="bi bi-play-fill"></i> Un demone</button>
			<button class="btn btn-sm btn-dark mb-1" onclick="suono('mostro');"><i class="bi bi-play-fill"></i> Un mostro</button>
			
			<br><br><button class="btn btn-outline-dark" onclick="stop();"><i class="bi bi-stop-fill"></i> Ferma tutto</button>
		</p>
		
		
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
