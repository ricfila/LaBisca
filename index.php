<!DOCTYPE html>
<html lang="it-IT" data-bs-theme="<?php echo $_COOKIE['tema'] ?: 'auto'; ?>">
<head>
	<title>La Bi$ca</title>
	<?php include "php/bootstrap.php"; ?>
</head>
<body class="text-center">
	<?php echo head(); ?>
	<div class="container-fluid">
	<img id="imglogo" height="100px"><br><br>
	<div class="row"><div class="col-lg-1"></div>
	<div class="col-sm-8 col-lg-7">
		<h4 style="padding: 0 40 0 40;">Benvenuti nel sito del <strong style="font-family: Vivaldi; white-space: nowrap;">Giuoco del Due</strong></h4>
		<p style="text-align: justify;">Il Giuoco del Due è una variante della Briscola per cinque giocatori. Questo sito è stato realizzato per archiviare i punteggi della famiglia Barzon, grande appassionata di questo gioco. La consultazione è aperta a tutti!</p>
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
		echo '<p class="lead text-primary" style="font-family: WhiteDream; font-size: 26px; padding: 20px;">' . $proverbi[$num] . '.' . (isset($autori[$num]) ? '<br><font style="font-size: 20px;">' . $autori[$num] . '</font>' : '') . '</p>';
		?>
		
		<hr>
		<h4>Le partite</h4>
		<?php
		$res = $conn->query("SELECT * FROM partite ORDER BY Data desc;");
		do {
			$row = $res->fetch_assoc();
		} while ($conn->query("SELECT * FROM mani WHERE Partita = " . $row['IdPartita'] . ";")->num_rows == 0);
		echo '<p style="text-align: justify;">Nell\'archivio sono presenti ' . $res->num_rows . ' partite. L\'ultima di queste è stata disputata il ' . $fmt1->format(strtotime($row['Data'])) . ' in occasione di <i>' . $row['Occasione'] . '</i>.</p> ';
		$partita = partita($row['IdPartita']);
		$totali = $partita[1];
		$max = max($totali);
		$vincitori = array();
		for ($i = 0; $i < 5; $i++) {
			if ($totali[$i] == $max) {
				$vincitori[] = $i;
			}
		}
		/*echo 'Ha' . (count($vincitori) == 1 ? '' : 'nno') . ' vinto ';
		foreach ($vincitori as $i => $g) {
			echo '<a href="giocatori.php?id=' . $partita[2][$vincitori[$i]] . '">' . nomedi($partita[2][$vincitori[$i]]) . '</a>' . ($i == count($vincitori) - 1 ? '' : ($i == count($vincitori) - 2 ? ' e ' : ', '));
		}
		echo ' con <strong>' . $max . '</strong> punti.</p>';*/
		echo '<a href="partite.php?id=' . $row['IdPartita'] . '" class="btn btn-primary"><i class="bi bi-arrow-right-circle-fill"></i> Vai alla partita</a>';
		?>
		
	</div><div class="col-sm-4 col-lg-3"><hr class="d-sm-none" />
		<div class="card border-primary bg-primary mb-3">
			<h4 class="card-header text-white bg-primary">Colonna sonora</h4>
			<div class="card-body sfondo m-1" style="border-radius: .4rem;">
				<p class="card-text">Migliora le partite con il giusto accompagnamento musicale.<br>Clicca sul pulsante per accedere alla nostra colonna sonora:</p>
				<a class="btn btn-primary" href="suoni.php"><i class="bi bi-music-note-beamed"></i> Musica</a>
			</div>
		</div>
		
		<a name="segnapunti"></a>
		<div class="card border-info bg-info mb-3">
			<h4 class="card-header text-white bg-info">Segnapunti</h4>
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
				<a href="#segnapunti" onclick="togglelink();">Mostra tutti</a>
				</p>
			</div>
		</div>

		<script>
			let show = false;
			$('.linkn').hide();

			function togglelink() {
				if (show)
					$('.linkn').hide();
				else
					$('.linkn').show();
				show = !show;
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
