<!DOCTYPE html>
<html lang="it-IT" data-bs-theme="auto">
<head>
	<title>Sortita - La Bi$ca</title>
	<?php
	include 'php/bootstrap.php';
	setcookie('login', '1', time() - 3600, "/");
	setcookie('pwd', '1', time() - 3600, "/");
	$_SESSION = [];
	header("Location: index.php");
	?>
</head>
<body><center>
	<?php echo head(); ?>
	<h4>Sortita dal sito effettuata</h4>
	<a href="index.php" class="btn btn-primary">Home page</a>
</center></body>
</html>
