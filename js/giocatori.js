
function modalaggiorna() {
	modal('<i class="bi bi-pencil-fill"></i>&nbsp;<strong>Modifica informazioni</strong>', '<i class="bi bi-person"></i> Nome:<input type="text" class="form-control" id="nome" onkeyup="if(event.keyCode == 13) aggiorna();" value="' + nome + '">' + 
	'<i class="bi bi-person"></i> Cognome:<input type="text" class="form-control" id="cognome" onkeyup="if(event.keyCode == 13) aggiorna();" value="' + cognome + '">' + 
	'<i class="bi bi-person-exclamation"></i> Alias o epiteto:<input type="text" class="form-control" id="alias" onkeyup="if(event.keyCode == 13) aggiorna();" value="' + alias + '">' + 
	(loginpersonale ?
		'<hr><i class="bi bi-key-fill"></i> Nuova parola chiave:<input type="password" class="form-control" id="pwd1" onkeyup="if(event.keyCode == 13) aggiorna();" value="">' + 
		'<i class="bi bi-key-fill"></i> Conferma parola chiave:<input type="password" class="form-control" id="pwd2" onkeyup="if(event.keyCode == 13) aggiorna();" value="">'
	: '') +
	'<span class="text-danger" id="erroreaggiorna"></span>', '<button type="button" class="btn btn-primary" onclick="aggiorna();"><i class="bi bi-check-circle-fill"></i> Salva</button>');
}

function aggiorna() {
	var nome = document.getElementById("nome").value;
	var cognome = document.getElementById("cognome").value;
	var alias = document.getElementById("alias").value;
	if (document.getElementById("pwd1") != null) {
		var pwd1 = document.getElementById("pwd1").value;
		var pwd2 = document.getElementById("pwd2").value;
	} else {
		var pwd1 = '';
		var pwd2 = '';
	}

	if (nome.length == 0) {
		$('#erroreaggiorna').html('Inserire un nome');
	} else if (pwd1 != pwd2) {
		$('#erroreaggiorna').html('Le due parole chiave inserite non coincidono');
	} else {
		var xhttp = new XMLHttpRequest();
		xhttp.onreadystatechange = function() {
			if (this.readyState == 4 && this.status == 200) {
				if (isNaN(parseInt(this.responseText))) {
					document.getElementById('erroreaggiorna').innerHTML = this.responseText;
				} else {
					location.reload();
				}
			}
		};
		xhttp.open("POST", "php/ajax.php", true);
		xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
		xhttp.send("ajax=aggiornagiocatore&id=" + id + "&nome=" + nome + "&cognome=" + cognome + "&alias=" + alias + (pwd1.length > 0 ? '&pwd=' + pwd1 : ''));
	}
}


function modalpermessi() {
	modal('<i class="bi bi-key-fill"></i>&nbsp;<strong>Modifica permessi</strong>', '<div class="form-check"><input class="form-check-input" type="checkbox" id="login"' + (abilitato ? ' checked=""' : '') + ' onchange="change_login(this.checked);"><label class="form-check-label" for="login">Abilitato all\'accesso</label></div>' +
	'<div class="form-check"><input class="form-check-input" type="checkbox" id="editor"' + (editor ? ' checked=""' : '') + ' onchange="change_editor(this.checked);"><label class="form-check-label" for="editor">Editore</label></div>' +
	'<div class="form-check"><input class="form-check-input" type="checkbox" id="admin"' + (admin ? ' checked=""' : '') + ' onchange="change_admin(this.checked);"><label class="form-check-label" for="admin">Amministratore</label></div>' +
	'<br><button class="btn btn-danger" id="resetpwd" onclick="resetpwd();"><i class="bi bi-x-lg"></i> Resetta parola chiave</button>' +
	'<span class="text-danger" id="erroreaggiornapermessi"></span>', '<button type="button" class="btn btn-primary" onclick="aggiornapermessi();"><i class="bi bi-check-circle-fill"></i> Salva</button>');
}

function change_login(login) {
	$('#editor').prop("disabled", !login).prop("checked", false);
	$('#admin').prop("disabled", true).prop("checked", false);
	$('#resetpwd').prop("disabled", !login);
}

function change_editor(editor) {
	$('#admin').prop("disabled", !editor)
	if (!editor)
		$('#admin').prop("checked", false);
}

function change_admin(admin) {
	if (admin)
		$('#editor').prop("disabled", false).prop("checked", true);
}

function resetpwd() {
	var xhttp = new XMLHttpRequest();
	xhttp.onreadystatechange = function() {
		if (this.readyState == 4 && this.status == 200) {
			if (isNaN(parseInt(this.responseText))) {
				$('#erroreaggiornapermessi').html(this.responseText);
			} else {
				location.reload();
			}
		}
	};
	xhttp.open("POST", "php/ajax_admin.php", true);
	xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
	xhttp.send("ajax=resetpwd&id=" + id);
}

function aggiornapermessi() {
	var xhttp = new XMLHttpRequest();
	xhttp.onreadystatechange = function() {
		if (this.readyState == 4 && this.status == 200) {
			if (isNaN(parseInt(this.responseText))) {
				$('#erroreaggiornapermessi').html(this.responseText);
			} else {
				location.reload();
			}
		}
	};
	xhttp.open("POST", "php/ajax_admin.php", true);
	xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
	xhttp.send("ajax=aggiornapermessi&id=" + id + "'&login=" + ($('#login').is(':checked')?1:0) + "&editor=" + ($('#editor').is(':checked')?1:0) + "&admin=" + ($('#admin').is(':checked')?1:0));
}
