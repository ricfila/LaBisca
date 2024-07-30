<!DOCTYPE html>
<html lang="it-IT" data-bs-theme="auto">
<head>
	<title>Accesso - La Bi$ca</title>
	<?php
	include "conn.php";
	if (isset($_POST['giocatore']) && isset($_POST['password'])) {
		$id = $conn->real_escape_string(stripslashes($_POST['giocatore']));
		$pwd = hash("sha512", $conn->real_escape_string(stripslashes($_POST['password'])));
		$res = $conn->query("SELECT * FROM giocatori WHERE IdGiocatore = $id;");
		if ($res->num_rows != 1) {
			$err = "Giuocatore non trovato";
		} else {
			if ($res->fetch_assoc()['Password'] != $pwd) {
				$err = '<img src="img/gif/vecia.gif"><br><strong>Parola chiave erronea</strong>';
			} else {
				$durata = time() + (86400 * 365);
				$posizione = "/";
				setcookie('login', $id, $durata, $posizione);
				setcookie('pwd', $pwd, $durata, $posizione);
				header("Location: index.php");
			}
		}
	}

	include 'php/bootstrap.php';
	?>
</head>
<body>
	<?php echo head(); ?>
	<div class="container-fluid">
		<div class="row">
			<div class="col-md-3 col-lg-4"></div>
			<div class="col">
				<h4>Accesso al sito</h4><hr class="mb-3">
				
				<input id="inputg" type="text" class="form-control" placeholder="Nome del giuocatore" onkeyup="cercagiocatori(this.value);" autocomplete="off" />
				<button id="btng" class="btn btn-outline-dark w-100 mb-2" style="display: none;" onclick="ripristinainput();"></button>
				<div id="giocatori" class="mb-2" zstyle="max-height: 100vh; overflow-y: scroll;"></div>

				<form id="formlogin" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST">
					<input name="giocatore" id="giocatore" class="d-none" value="0" />
					<input name="password" id="password" type="password" class="form-control" placeholder="Parola chiave" style="display: none;" onkeyup="if (event.keyCode == 13) login();" />
				</form>
				<br>
				<p id="err" class="text-danger text-center"><?php if (isset($err)) echo $err; ?></p>

				<div style="text-align: right;">
					<button id="btnaccedi" class="btn btn-primary" onclick="login();" style="display: none;">Accedi</button>
				</div>
				
			</div>
			<div class="col-md-3 col-lg-4"></div>
		</div>
	</div>

	<script>

	function cercagiocatori(testo) {
		var xhttp = new XMLHttpRequest();
		xhttp.onreadystatechange = function() {
			if (this.readyState == 4 && this.status == 200) {
				let resp = this.responseText.split('&');
				if (parseInt(resp[0]) == 0)
					$('#giocatori').html('Nessun giuocatore con questo nome');
				else {
					$('#giocatori').html(resp[1]);
				}
			}
		};
		xhttp.open("POST", "php/ajax.php", true);
		xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
		xhttp.send("ajax=cercagiocatorilogin&testo=" + testo);
	}

	function scegligioc(id, nome, abilitato) {
		if (abilitato) {
			$('#inputg').hide();
			$('#giocatori').hide();
			$('#btng').html('<div class="row"><div class="col"><i class="bi bi-person-fill"></i> ' + nome + '</div><div class="col-auto my-auto"><i class="bi bi-x-lg"></i></div></div>').show();
			$('#giocatore').val(id);
			$('#password').show().focus();
			$('#btnaccedi').show();
			$('#err').html('');
		} else {
			$('#err').html('<img src="img/gif/vecia.gif"><br><strong>Non sei autorizzato ad accedere al sito</strong>');
		}
	}

	function ripristinainput() {
		$('#inputg').val('').show();
		$('#giocatori').html('').show().css("height", 0);
		$('#btng').hide();
		$('#inputg').focus();
		$('#password').hide().val();
		$('#btnaccedi').hide();
		$('#err').html('');
	}

	function login() {
		$('#formlogin').submit();
	}
	</script>
</body>
</html>
