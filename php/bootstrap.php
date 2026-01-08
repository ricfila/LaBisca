<?php
//https://bootswatch.com/minty/

include "func.php";

function head() {
	$out = '<nav class="navbar navbar-expand-sm fixed-top navbar-dark bg-primary" style="height: 56px; z-index: 1050;">
			<div class="container-fluid pe-0">
				<div class="row w-100">
					<div class="col-lg-1"></div>
					<div class="col text-start">
						<a class="navbar-brand" href="index.php" style="margin-left: 15px;"><img src="media/img/Bisca_bianco.png" style="height: 30px;"></a>
					</div>

					<div class="col-auto text-end p-0">
						<ul class="navbar-nav ms-auto justify-content-end d-inline ad-sm-none">
							<li class="nav-item d-inline lead">
								<a class="nav-link' . (basename($_SERVER['PHP_SELF'], '.php') == 'partite' ? ' active' : '') . ' d-inline" style="padding: 0px 15px;" href="partite.php" alt="Almanacco"><i class="bi bi-book"></i><span class="d-none d-md-inline ms-2">Almanacco</span></a>
							</li>
							<li class="nav-item d-inline lead">
								<a class="nav-link' . (basename($_SERVER['PHP_SELF'], '.php') == 'suoni' ? ' active' : '') . ' d-inline" style="padding: 0px 15px;" href="suoni.php" alt="Biscarmonica"><i class="bi bi-music-note-beamed"></i><span class="d-none d-md-inline ms-2">Biscarmonica</span></a>
							</li>
							<li class="nav-item d-inline lead dropdown">
								<a class="nav-link' . (basename($_SERVER['PHP_SELF'], '.php') == 'giocatori' ? ' active' : '') . ' d-inline dropdown" data-bs-toggle="dropdown" style="padding: 0px 15px;" href="#" alt="Giocatori" role="button" aria-haspopup="true" aria-expanded="false"><i class="bi bi-people-fill"></i><span class="d-none d-md-inline ms-2">Giuocatori</span></a>
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

<link rel="icon" type="image/png" href="media/img/Bisca_icon.png">
<link href="bootstrap-5.3.3-dist/css/bootstrap.min.css" rel="stylesheet" />
<link href="css/icons/bootstrap-icons.css" rel="stylesheet" />
<script src="bootstrap-5.3.3-dist/js/bootstrap.bundle.min.js"></script>
<script src="js/jquery-3.7.1.min.js"></script>

<?php $r = rand(); ?>
<link href="css/temi.css?d=<?php echo $r; ?>" rel="stylesheet" />
<link href="css/font.css?d=<?php echo $r; ?>" rel="stylesheet" />
<link href="css/partite.css?d=<?php echo $r; ?>" rel="stylesheet" />
<link href="css/generico.css?d=<?php echo $r; ?>" rel="stylesheet" />

<link href="css/egg.css?d=<?php echo $r; ?>" rel="stylesheet" />
<script src="js/egg.js"></script>
<script>var nomealias = <?php echo ($nomealias == 'Nome' ? '0' : '1'); ?>;</script>
<script src="js/general.js"></script>
