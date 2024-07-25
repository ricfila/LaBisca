<?php
include_once "conn.php";
include "func_partite.php";

// Gestione (e ripristino) del login
session_start();
if (isset($_COOKIE['login']) && isset($_COOKIE['pwd'])) {
	if (!isset($_SESSION['id'])) {
		load_login();
	}
}

function load_login() {
	global $conn;
	$id = $conn->real_escape_string(stripslashes($_COOKIE['login']));
	$pwd = $conn->real_escape_string(stripslashes($_COOKIE['pwd']));
	$res = $conn->query("SELECT * FROM giocatori WHERE IdGiocatore = $id AND Password = '$pwd';");
	if ($res->num_rows == 1) {
		$_SESSION['id'] = $id;
		$row = $res->fetch_assoc();
		$_SESSION['nome'] = nomecognome($row['Nome'], $row['Cognome']);
		$_SESSION['editor'] = ($row['Editor'] == 1) || ($row['Admin'] == 1);
		$_SESSION['admin'] = $row['Admin'] == 1;
	}
}

$nomealias = (isset($_COOKIE['alias']) ? ($_COOKIE['alias'] == 'true' ? 'Alias': 'Nome') : 'Nome');
$esitoupload = '';

function nomedi($id, $array = false) {
	global $conn;
	global $nomealias;
	$res = $conn->query("select * from giocatori where IdGiocatore = $id;");
	if ($res->num_rows != 1)
		return null;
	$row = $res->fetch_assoc();
	$nome = nomecognome($row['Nome'], $row['Cognome']);
	$alias = $row['Alias'];
	if (empty($alias))
		$alias = $nome;
	if ($array)
		return array($nome, $alias);
	else
		return '<span class="nome" style="display: ' . ($nomealias == 'Nome' ? 'inline' : 'none') . ';">' . $nome . '</span><span class="alias" style="display: ' . ($nomealias == 'Alias' ? 'inline' : 'none') . ';">' . $alias . '</span>';
}

function nomecognome($nome, $cognome) {
	return $nome . (empty($cognome) ? '' : ' ' . (isset($_SESSION['id']) ? $cognome : substr($cognome, 0, 1) . '.'));
}

function checkalias() {
	$out = '<div class="form-check form-switch" style="text-align: left;"><input class="form-check-input" type="checkbox" value="" id="calias" onchange="alias(this);"' . (isset($_COOKIE['alias']) ? ($_COOKIE['alias'] == 'true' ? 'checked=""': '') : '') . '><label class="form-check-label" for="calias">Visualizza gli alias</label></div>';
	return $out;
}

function processafile($file, $directory, $nomefile = null, $estensioni = null) {
	global $esitoupload;
	if ($file['error'] != 0) {
		$esitoupload .= 'Errore ' . $file['error'] . ' nel caricamento di ' . $file['name'] . '<br>';
		return false;
	}
	if ($file['size'] <= 0) {
		$esitoupload .= 'Errore nel caricamento di ' . $file['name'] . ': file troppo piccolo<br>';
		return false;
	}
	/*if ($file['size'] > (5 * 1024 * 1024)) {
		$esitoupload .= 'Errore nel caricamento di ' . $file['name'] . ': file più grande di 5 MB<br>';
		return false;
	}*/
	$ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
	if ($estensioni != null) {
		if (!in_array($ext, $estensioni)) {
			$esitoupload .= 'Errore nel caricamento di ' . $file['name'] . ': formato ' . $ext . ' non consentito<br>';
			return false;
		}
	}
	if ($nomefile == null) {
		$nomefile = $file['name'];
	}
	if (!is_dir($directory)) {
		mkdir($directory);
	}
	if (move_uploaded_file($file['tmp_name'], $directory . '/' . $nomefile)) {
		if ($ext == 'zip') {
			$zip = new ZipArchive;
			$ok = false;
			if ($zip->open($directory . '/' . $nomefile) === TRUE) {
				if ($zip->extractTo($directory)) {
					$esitoupload .= 'Tutti i file di ' . $file['name'] . ' sono stati estratti con successo<br>';
					$ok = true;
				} else {
					$esitoupload .= 'Errore durante l\'estrazione di ' . $file['name'] . '<br>';
				}
				$zip->close();
			} else {
				$esitoupload .= 'Errore durante l\'estrazione di ' . $file['name'] . '<br>';
			}
			unlink($directory . '/' . $nomefile);
			return $ok;
		}
	} else {
		$esitoupload .= 'Errore nel salvataggio di ' . $file['name'] . '<br>';
		return false;
	}
	return true;
}

function listafoto($id) {
	if (is_dir('foto/' . $id)) {
		$files = scandir('foto/' . $id);
		array_shift($files);
		array_shift($files);
		return $files;
	} else {
		return false;
	}
}

?>
