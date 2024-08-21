<?php
if (!isset($_POST['ajax'])) {
	exit;
}
chdir("..");
include "func.php";
if (!isset($_SESSION['admin']) || !$_SESSION['admin']) {
	exit;
}

foreach ($_POST as $key => $value) {
	$$key = $conn->real_escape_string(stripslashes($value));
}
$defpwd = '3f8203c18897266bdf30fe6e8b83c469084a79ba4a807edab6fc18e255e54656d631dbde040e30bbdc74a6e46a38d430a1471072eb36d09417bc22fe7affe718';

switch ($ajax) {
	case 'resetpwd':
		if ($conn->query("UPDATE giocatori SET Password = '$defpwd' WHERE IdGiocatore = $id;")) {
			echo 1;
			load_login();
		} else {
			echo $conn->error;
		}
		break;
	case 'aggiornapermessi':
		$res = $conn->query("SELECT * FROM giocatori WHERE IdGiocatore = $id;");
		if ($res->num_rows == 0) {
			echo "Il giocatore non esiste";
		} else {
			$row = $res->fetch_assoc();
			if ($row['Password'] == null && $login)
				$pwd = "Password = '$defpwd',";
			else if ($row['Password'] != null && !$login)
				$pwd = "Password = null,";
			else
				$pwd = "";
			if ($conn->query("UPDATE giocatori SET $pwd Editor = $editor, Admin = $admin WHERE IdGiocatore = $id;")) {
				echo 1;
				load_login();
			} else {
				echo $conn->error;
			}
		}
		break;
	case 'eliminapartita':
		$conn->query("delete from partite where IdPartita = $id;");
		$conn->query("delete from partecipazioni where Partita = $id;");
		$conn->query("delete from mani where Partita = $id;");
		break;
	default:
		exit;
}

?>
