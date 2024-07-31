<?php
//https://bootswatch.com/minty/

include "func.php";
setlocale(LC_TIME, 'it_IT');
$fmt1 = new IntlDateFormatter('it_IT', IntlDateFormatter::LONG, IntlDateFormatter::NONE);
$fmt2 = new IntlDateFormatter('it_IT', IntlDateFormatter::MEDIUM, IntlDateFormatter::NONE, null, null, 'd MMM');
$fmt3 = new IntlDateFormatter('it_IT', IntlDateFormatter::MEDIUM, IntlDateFormatter::NONE, null, null, 'd MMMM');
//$conn->set_charset("utf-8");

function head() {
	$out = '<nav class="navbar navbar-expand-sm fixed-top navbar-dark bg-primary" style="height: 56px; z-index: 1050;">
			<div class="container-fluid pe-0">
				<div class="row w-100">
					<div class="col-lg-1"></div>
					<div class="col text-start">
						<a class="navbar-brand" href="index.php" style="margin-left: 15px;"><img src="img/Bisca_bianco.png" style="height: 30px;"></a>
					</div>

					<div class="col text-end pe-0">
						<ul class="navbar-nav ms-auto justify-content-end d-inline ad-sm-none">
							<li class="nav-item d-inline lead">
								<a class="nav-link' . (basename($_SERVER['PHP_SELF'], '.php') == 'partite' ? ' active' : '') . ' d-inline" style="padding: 0px 15px;" href="partite.php" alt="Almanacco"><i class="bi bi-book"></i><span class="d-none d-md-inline"> Almanacco</span></a>
							</li>
							<li class="nav-item d-inline lead dropdown">
								<a class="nav-link' . (basename($_SERVER['PHP_SELF'], '.php') == 'giocatori' ? ' active' : '') . ' d-inline dropdown" data-bs-toggle="dropdown" style="padding: 0px 15px;" href="#" alt="Giocatori" role="button" aria-haspopup="true" aria-expanded="false"><i class="bi bi-people-fill"></i><span class="d-none d-md-inline"> Giuocatori</span></a>
								<div class="dropdown-menu position-absolute dropdown-menu-end mt-2" data-bs-popper="static">
									<a class="dropdown-item" href="giocatori.php"><i class="bi bi-people-fill"></i> Tutti i giuocatori</a>
									<hr class="dropdown-divider">';
									if (isset($_SESSION['id'])) {
										$out .= '<a class="dropdown-item" href="giocatori.php?id=' . $_SESSION['id'] . '"><i class="bi bi-person-circle"></i> ' . $_SESSION['nome'] . '</a>
										<a class="dropdown-item" href="logout.php"><i class="bi bi-door-open"></i> Sortisci dal sito</a>';
									} else {
										$out .= '<a class="dropdown-item" href="login.php"><i class="bi bi-door-open"></i> Accedi al sito</a>';
									}
									$out .= '
								</div>
							</li>
						</ul>
					</div>
					<div class="col-lg-1"></div>
				</div>
			</div>
			</nav>';
	return $out;
}

//https://bootswatch.com/minty/
?>

<meta charset="utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">

<link rel="icon" type="image/png" href="img/Bisca_icon.png">
<link href="bootstrap-5.3.3-dist/css/bootstrap.min.css" rel="stylesheet" />
<link href="css/icons/bootstrap-icons.css" rel="stylesheet" />
<script src="bootstrap-5.3.3-dist/js/bootstrap.bundle.min.js"></script>
<script src="js/jquery-3.7.1.min.js"></script>

<link href="css/temi.css" rel="stylesheet" />
<link href="css/font.css" rel="stylesheet" />
<link href="css/partite.css" rel="stylesheet" />
<link href="css/generico.css" rel="stylesheet" />

<script>
var nomealias = <?php echo ($nomealias == 'Nome' ? '0' : '1'); ?>;

function modal(titolo, corpo, azione) {
	var m = document.getElementById("modal-content");
	m.innerHTML = '<div class="modal-header">' + titolo + '<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Chiudi"><span aria-hidden="true"></span></button></div>';
	m.innerHTML += '<div class="modal-body">' + corpo + '</div>';
	if (azione != false && azione != null)
		m.innerHTML += '<div class="modal-footer"><button class="btn btn-danger" type="button" onclick="chiudi();"><i class="bi bi-x-diamond-fill"></i> Annulla</button>' + azione + '</div>';
	apri();
}

function apri() {
	$('#modal').modal('show');
}

function chiudi() {
	$('#modal').modal('hide');
}

function setCookie(cname, cvalue, exdays) {
	var d = new Date();
	d.setTime(d.getTime() + (exdays * 24 * 60 * 60 * 1000));
	var expires = "expires=" + d.toUTCString();
	document.cookie = cname + "=" + cvalue + ";" + expires + ";path=/";
}

function alias(check) {
	setCookie("alias", check.checked, 365);
	nomealias = (check.checked ? 1 : 0);
	var nomi = document.getElementsByClassName('nome');
	var alias = document.getElementsByClassName('alias');
	for (var i = 0; i < nomi.length; i++) {
		nomi[i].style.display = (check.checked ? 'none' : 'inline');
	}
	for (var i = 0; i < alias.length; i++) {
		alias[i].style.display = (check.checked ? 'inline' : 'none');
	}
	if (typeof marquee === "function")
		marquee();
}

;(function () {
	const htmlElement = document.querySelector("html")
	if(htmlElement.getAttribute("data-bs-theme") === 'auto') {
		function updateTheme() {
			document.querySelector("html").setAttribute("data-bs-theme",
			window.matchMedia("(prefers-color-scheme: dark)").matches ? "dark" : "light")
		}
		window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', updateTheme)
		updateTheme()
	}
})()

</script>
