<!DOCTYPE html>
<html lang="it-IT" data-bs-theme="<?php echo (isset($_COOKIE['tema']) ? $_COOKIE['tema'] : 'auto'); ?>">
<head>
	<title>La Bi$ca</title>
	<?php include "php/bootstrap.php"; ?>
</head>
<body class="text-center">
	<?php echo head(); ?>
	<div class="container-fluid">
	<img id="imglogo" height="100px" class="toggle-easter-egg"><br><br>
	<div class="row"><div class="col-lg-1"></div>
	<div class="col-sm-8 col-lg-7">
		<h4 style="padding: 0 40 0 40;" class="text-primary">Benvenuti nel sito del <strong class="vivaldi" style="white-space: nowrap;">Giuoco del Due</strong></h4>
		<p style="text-align: justify;">Il Giuoco del Due è una variante della Briscola per cinque giocatori. Questo sito è stato realizzato per archiviare i punteggi della famiglia Barzon, grande appassionata di questo gioco. La consultazione è aperta a tutti!</p>

		<div class="row" style="min-height: 120px;">
			<div class="col text-center pe-0">
				<?php
				$proverbi = array(
				'Se vi sono cinque persone in una casa, saranno divise tre contro due e due contro tre',
				'Fidati di tutti, ma taglia tu il mazzo',
				'Giocare è sperimentare il rischio',
				'Bisogna sempre giocare lealmente quando si hanno in mano carte vincenti',
				'Devo lamentare che le carte sono mischiate male fin quando non ho una buona mano',
				'La sorte distribuisce le carte e noi giuochiamo',
				'L\'amore è come il Giuoco: se non hai un buon partner, meglio che tu abbia una buona mano',
				'È sempre colpa del compagno',
				'Le carte sono una guerra, sotto le mentite spoglie di un gioco',
				'Non ti fidar di me se il cuor ti manca',
				'Se ti perdi tuo danno',
				'Non val saper a chi ha fortuna contrà',
				'Per un punto Martin perse la capa',
				'Spade: chi sta in piè no cade',
				'Bastoni: persege e mełoni',
				'Denari: chi i ga fissi e chi i ga ciari',
				'Coppe: co pì se xe manco se fa',
				'Il giuoco della spada a molti non aggrada',
				'Molto spesso le giuocate van finire a bastonate',
				'Son gli amici molto rari quando non si ha danari',
				'Carta in toea no se discoła',
				'Vede mejo quei che varda de quei che zuga',
				'Chi zuga par bisogno perde par necesità',
				'No metarte a zugar se no te voi pericołar',
				'No importa łe carte che te ghè, importa come che te łe zughi',
				'Acetà un zugo te ghe da acetar le regołe',
				'Co\' te ghe perso, de guadagnar no gh\'è pì verso',
				'Gh\'è tre tipi de zugadori de carte: i poco bòni, i mia bòni e i gnente bòni',
				'Chi vinze ła prima man, el va casa co\' łe braghe in man',
				'Chi ha fortuna in amore, non giochi a carte',
				'Il segreto della vita non è avere delle buone carte, ma giocare bene una cattiva mano',
				'I punti sui punti ce li mettono i tonti',
				'Briscola vecchia un carico aspetta',
				'Carta mancante partita a monte',
				'Le carte son femmine: fanno sempre come vogliono');
				$autori = array(
				'(Lc <font style="font-family: Vivaldi; font-weight: bold; font-size: 22px;">12, 52</font>)',
				'Arthur Bloch',
				'Novalis',
				'Oscar Wilde',
				'Jonathan Swift',
				'Arthur Schopenhauer',
				'Charles Pierce',
				'Arthur Block',
				'Charles Lamb');
				$num = rand(1, count($proverbi)) - 1;
				echo '<p class="lead text-primary m-0" style="font-family: WhiteDream; font-size: 26px; padding: 20px 10px 20px 20px;">' . $proverbi[$num] . '.' . (isset($autori[$num]) ? '<br><font style="font-size: 20px;">' . $autori[$num] . '</font>' : '') . '</p>';
				?>
			</div>
			<div class="col-auto text-end p-0 align-self-end">
				<audio id="fisarmonica_audio"><source src="" type="audio/mp3"></audio>
				<img id="fisarmonica" src="media/egg/fisarmonica.png" style="width: 150px; right: 0; bottom: 0; cursor: pointer;" onclick="toggleFisarmonica();">
			</div>
		</div>
		
		<hr class="mt-0 mb-4">
		
		<?php
		$stampate = array();
		// Ultima partita
		$res = $conn->query("SELECT * FROM partite ORDER BY Data DESC;");
		do {
			$row = $res->fetch_assoc();
		} while ($conn->query("SELECT * FROM mani WHERE Partita = " . $row['IdPartita'] . ";")->num_rows == 0);
		echo '<h4 class="text-primary">L\'ultima bi$ca disputata</h4>';
		echo mostra_partita_breve($row['IdPartita']);
		$stampate[] = $row['IdPartita'];
		
		// Partite disputate questo giorno
		$numeri = array('Zero', 'Un', 'Due', 'Tre', 'Quattro', 'Cinque', 'Sei', 'Sette', 'Otto', 'Nove', 'Dieci', 'Undici', 'Dodici', 'Tredici', 'Quattordici', 'Quindici', 'Sedici', 'Diciassette', 'Diciotto', 'Diciannove', 'Venti', 'Ventuno', 'Ventidue', 'Ventitré', 'Ventiquattro', 'Venticinque', 'Ventisei', 'Ventisette', 'Ventotto', 'Ventinove', 'Trenta');
		$res = $conn->query("SELECT IdPartita, YEAR(Data) AS Anno FROM partite WHERE DAY(Data) = DAY(CURDATE()) AND MONTH(Data) = MONTH(CURDATE()) ORDER BY Data DESC;");
		while ($row = $res->fetch_assoc()) {
			$ago = date("Y") - $row['Anno'];
			if ($ago > 0) {
				echo '<h4 class="text-primary">' . (count($numeri) > $ago ? $numeri[$ago] : $ago) . ' ' . ($ago == 1 ? 'anno' : 'anni') . ' fa...</h4>';
				echo mostra_partita_breve($row['IdPartita']);
				$stampate[] = $row['IdPartita'];
			}
		}

		$res = $conn->query("SELECT count(*) AS turni, partite.IdPartita, partite.Data FROM partite JOIN mani ON mani.Partita = partite.IdPartita GROUP BY partite.IdPartita HAVING turni >= 50 ORDER BY Data DESC, turni DESC;");
		while ($row = $res->fetch_assoc()) {
			if (!in_array($row['IdPartita'], $stampate)) {
				echo '<h4 class="text-primary">L\'ultima epica impresa</h4>';
				echo mostra_partita_breve($row['IdPartita']);
				break;
			}
		}
		?>
		
	</div><div class="col-sm-4 col-lg-3"><hr class="d-sm-none" />
		<div class="card border-primary bg-primary mb-3">
			<h4 class="card-header text-white bg-primary">La Biscarmonica</h4>
			<div class="card-body sfondo m-1" style="border-radius: .4rem;">
				<p class="card-text">Migliora le partite con il giusto accompagnamento musicale e divertenti effetti speciali.<br>Scopri la nostra colonna sonora:</p>
				<a class="btn btn-primary" href="suoni.php"><i class="bi bi-music-note-beamed"></i> Musica</a>
			</div>
		</div>
		
		<a name="segnapunti"></a>
		<div class="card border-info bg-info mb-3">
			<h4 class="card-header text-white bg-info">I segnapunti</h4>
			<div class="card-body sfondo m-1" style="border-radius: .4rem;">
				<p class="card-text">Scarica e stampa le tabelle per segnare i punti su carta:
				<ul style="text-align: justify;">
					<li><a href="media/segnapunti/2023.pdf" target="_blank">Versione 2023</a></li>
					<li><a href="media/segnapunti/2023-breve.pdf" target="_blank">Versione 2023 breve</a></li>
					<li class="linkn"><a href="media/segnapunti/2023-speciale.pdf" target="_blank">Versione 2023 speciale</a></li>
					<li class="linkn"><a href="media/segnapunti/2019.pdf" target="_blank">Versione 2019</a></li>
					<li class="linkn"><a href="media/segnapunti/2019-breve.pdf" target="_blank">Versione 2019 breve</a></li>
					<li class="linkn"><a href="media/segnapunti/2016.2.2.pdf" target="_blank">Versione 2016</a></li>
				</ul>
				<a id="togglesegnapunti" href="#segnapunti" onclick="togglelink();">Mostra tutti</a>
				</p>
			</div>
		</div>

		<script>
			let show = false;
			$('.linkn').hide();

			function togglelink() {
				if (show) {
					$('.linkn').hide();
					$('#togglesegnapunti').html('Mostra tutti');
				} else {
					$('.linkn').show();
					$('#togglesegnapunti').html('Mostra meno');
				}
				show = !show;
			}

			let playing = false;
			let k = 0;
			let fisa = [0,1,2,3,4,5,6,7,8,9].sort(() => Math.random() - 0.5);
			function toggleFisarmonica() {
				let audio = document.getElementById('fisarmonica_audio');
				let img = document.getElementById('fisarmonica');
				if (playing) {
					audio.pause();
					audio.currentTime = 0;
					img.src = "media/egg/fisarmonica.png";
				} else {
					audio.src = "media/egg/fisa" + fisa[k++] + ".mp3";
					if (k >= fisa.length) k = 0;
					audio.play();
					audio.loop = true;
					img.src = "media/egg/fisarmonica.gif";
				}
				playing = !playing;
			}
		</script>
		
		<hr>
		<h4>Impostazioni</h4>
		<?php echo checkalias(); ?>
		<h6 class="mt-3">Tema grafico</h6>
		<?php
		$tema = isset($_COOKIE['tema']) ? $_COOKIE['tema'] : 'auto';
		?>
		<div class="form-check text-start">
			<input class="form-check-input" type="radio" name="tema" id="temalight" <?php if ($tema == 'light') echo 'checked'; ?> onchange="if (this.checked) tema('light');">
			<label class="form-check-label" for="temalight">Tema chiaro</label>
		</div>
		<div class="form-check text-start">
  			<input class="form-check-input" type="radio" name="tema" id="temadark" <?php if ($tema == 'dark') echo 'checked'; ?> onchange="if (this.checked) tema('dark');">
  			<label class="form-check-label" for="temadark">Tema scuro</label>
		</div>
		<div class="form-check text-start">
  			<input class="form-check-input" type="radio" name="tema" id="temaauto" <?php if ($tema == 'auto') echo 'checked'; ?> onchange="if (this.checked) tema('auto');">
  			<label class="form-check-label" for="temaauto">Automatico</label>
		</div>

		<script>
			function tema(t) {
				setCookie('tema', t);
				$('html').attr('data-bs-theme', t);
				updateTheme();
			}
		</script>
		
	</div><div class="col-lg-1"></div></div>
	</div>
<?php include "php/bootstrap2.php"; ?>
</body>
</html>
