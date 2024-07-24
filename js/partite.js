
var carousel = document.getElementById('carousel');
carousel.addEventListener('slide.bs.carousel', function (e) {
	var nextH = $(e.relatedTarget).height();
	$('#carousel-inner').animate({
		height: nextH
	}, 600);
});

function parziali(check) {
	setCookie('parziali', check.checked, 365);
	var parziali = document.getElementsByClassName('parziale');
	var totali = document.getElementsByClassName('totale');
	for (var i = 0; i < parziali.length; i++) {
		parziali[i].style.display = (check.checked ? 'block' : 'none');
	}
	for (var i = 0; i < totali.length; i++) {
		totali[i].style.display = (check.checked ? 'none' : 'block');
	}
}

function info() {
	modal('Modifica informazioni', 'Bi$ca in occasione di:<input class="form-control" type="text" id="occasione" value="' + document.getElementById('occasione0').innerHTML + '" onkeyup="if(event.keyCode == 13) salvainfo();">Data:<input type="date" class="form-control" id="data" value="' + document.getElementById('data0').innerHTML + '" onkeyup="if(event.keyCode == 13) salvainfo();">Note:<textarea class="form-control" id="note" rows="5">' + document.getElementById('note0').innerHTML + '</textarea><span class="text-danger" id="erroreinfo"></span>', '<button class="btn btn-success" onclick="salvainfo();"><i class="bi bi-check-circle-fill"></i> Salva</button>');
}

function salvainfo() {
	var occasione = document.getElementById('occasione').value;
	var data = document.getElementById('data').value;
	var note = document.getElementById('note').value;
	var xhttp = new XMLHttpRequest();
	xhttp.onreadystatechange = function() {
		if (this.readyState == 4 && this.status == 200) {
			if (isNaN(parseInt(this.responseText))) {
				document.getElementById('erroreinfo').innerHTML = this.responseText;
			} else {
				window.location.href = 'partite.php?id=' + id + '&edit=true';
			}
		}
	};
	xhttp.open("POST", "php/ajax.php", true);
	xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
	xhttp.send("ajax=salvainfopartita&id=" + id + "&occasione=" + occasione + "&data=" + data + "&note=" + note);
}

function primogioc(colonna, nome) {
	modal('Inserisci giocatore', 'Quale giocatore inserire ' + (nome == false ? 'nella colonna ' + colonna : 'al posto di <strong>' + nome[nomealias] + '</strong>') + '?<input class="form-control" type="text" id="primogioc" placeholder="Cerca..." onkeyup="cercagiocatori(this.value, ' + colonna + ');" autofocus><div id="listag"></div>');
	cercagiocatori('', colonna);
	document.getElementById('primogioc').focus();
}

function cercagiocatori(testo, colonna) {
	var xhttp = new XMLHttpRequest();
	xhttp.onreadystatechange = function() {
		if (this.readyState == 4 && this.status == 200) {
			document.getElementById('listag').innerHTML = this.responseText;
			document.getElementById('primogioc').focus();
		}
	};
	xhttp.open("POST", "php/ajax.php", true);
	xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
	xhttp.send("ajax=cercagiocatori&testo=" + testo + "&colonna=" + colonna);
}

function salvagioc(idg, colonna) {
	var inizio = 1;
	if (colonna == false) {
		inizio = document.getElementById('cturno').value;
		colonna = document.getElementById('ccolonna').value;
	}
	var xhttp = new XMLHttpRequest();
	xhttp.onreadystatechange = function() {
		if (this.readyState == 4 && this.status == 200) {
			location.reload();
		}
	};
	xhttp.open("POST", "php/ajax.php", true);
	xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
	xhttp.send("ajax=salvagioc&id=" + id + "&idg=" + idg + "&colonna=" + colonna + "&inizio=" + inizio);
}

function nuovogioc(nome, colonna) {
	var xhttp = new XMLHttpRequest();
	xhttp.onreadystatechange = function() {
		if (this.readyState == 4 && this.status == 200) {
			salvagioc(this.responseText, colonna);
		}
	};
	xhttp.open("POST", "php/ajax.php", true);
	xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
	xhttp.send("ajax=nuovogiocatore&nome=" + nome + "&alias=");
}

function turno(numero) {
	var xhttp = new XMLHttpRequest();
	xhttp.onreadystatechange = function() {
		if (this.readyState == 4 && this.status == 200) {
			modal((numero == null ? 'Nuovo turno' : 'Modifica turno ' + numero), this.responseText, '<button class="btn btn-success" onclick="salvaturno(' + (numero == null ? '\'nuovo\'' : numero) + ');"><i class="bi bi-check-circle-fill"></i> Salva</button>');
			//setTimeout(function(){document.getElementById('codice').focus()}, 200);
		}
	};
	xhttp.open("POST", "php/ajax.php", true);
	xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
	xhttp.send("ajax=modalturno&id=" + id + "&numero=" + (numero == null ? 'null' : numero));
}

function vecia(check) {
	if (check) {
		//$('#imgfulminevecia').attr('src', "").attr('src', "img/gif/fulmine_vecia2.gif?t=" + new Date().getTime());
		$('#fulminevecia').css('opacity', '1').show();
		
		setTimeout(function() {
			$('#fulminevecia').animate({
				opacity: 0
			}, 400);
		}, 500);
		setTimeout(function() {
			$('#fulminevecia').hide();
		}, 900);
	}
}

function checkcodice(stringa) {
	var codice = '';
	var old = document.getElementById('codice');
	var oldvalue = (old.value.length < 4 ? '____' : old.value);
	for (var i = 0; i < 4; i++) {
		if (i == 2 && (stringa.charAt(2) == '1' || stringa.charAt(2) == '0')) {
			stringa = stringa.substring(0, 3) + '0';
			document.getElementById('concappotto').checked = false;
			document.getElementById('senzacappotto').checked = true;
			document.getElementById('concappotto').disabled = false;
			document.getElementById('senzacappotto').disabled = false;
		}
		if (i == 2 && stringa.charAt(2) == '-') {
			document.getElementById('concappotto').checked = false;
			document.getElementById('senzacappotto').checked = false;
			document.getElementById('concappotto').disabled = true;
			document.getElementById('senzacappotto').disabled = true;
		}
		codice = codice + (stringa.charAt(i) == 'n' ? oldvalue.charAt(i) : stringa.charAt(i));
	}
	old.value = codice;
}

function salvaturno(numero) {
	var codice = document.getElementById('codice').value;
	if (codice.includes('_') || codice.length < 4) {
		document.getElementById('erroreturno').innerHTML = 'Definire tutti i parametri richiesti prima di salvare';
	} else {
		var xhttp = new XMLHttpRequest();
		xhttp.onreadystatechange = function() {
			if (this.readyState == 4 && this.status == 200) {
				if (isNaN(parseInt(this.responseText))) {
					document.getElementById('erroreturno').innerHTML = this.responseText;
				} else {
					location.reload();
				}
			}
		};
		xhttp.open("POST", "php/ajax.php", true);
		xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
		xhttp.send("ajax=salvaturno&id=" + id + "&numero=" + numero + "&codice=" + codice + "&vecia=" + ($('#vecia').is(':checked')?1:0));
	}
}

function spostaturno(numero) {
	var posizione = document.getElementById('posizione').value;
	var xhttp = new XMLHttpRequest();
	xhttp.onreadystatechange = function() {
		if (this.readyState == 4 && this.status == 200) {
			location.reload();
		}
	};
	xhttp.open("POST", "php/ajax.php", true);
	xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
	xhttp.send("ajax=spostaturno&id=" + id + "&numero=" + numero + "&posizione=" + posizione);
}

function modalelimina(numero) {
	modal('Elimina turno ' + numero, 'Sei sicuro di voler eliminare il turno ' + numero + '?', '<button class="btn btn-success" onclick="eliminaturno(' + numero + ');"><i class="bi bi-trash-fill"></i> Elimina</button>');
}

function eliminaturno(numero) {
	var xhttp = new XMLHttpRequest();
	xhttp.onreadystatechange = function() {
		if (this.readyState == 4 && this.status == 200) {
			location.reload();
		}
	};
	xhttp.open("POST", "php/ajax.php", true);
	xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
	xhttp.send("ajax=eliminaturno&id=" + id + "&numero=" + numero);
}

function cambio() {
	var xhttp = new XMLHttpRequest();
	xhttp.onreadystatechange = function() {
		if (this.readyState == 4 && this.status == 200) {
			modal('Cambio giocatore', this.responseText);
			cercagiocatori('', false);
			//document.getElementById('primogioc').focus();
		}
	};
	xhttp.open("POST", "php/ajax.php", true);
	xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
	xhttp.send("ajax=modalcambio&id=" + id);
}

function aggiornaoutcolonna() {
	document.getElementById("outcolonna").innerHTML = document.getElementById("ccolonna").value;
}

function modalannullacambio(inizio, colonna, nome) {
	modal('Rimuovi cambio', 'Vuoi rimuovere l\'entrata in gioco di ' + nome[nomealias] + ' al turno ' + inizio + '?<span class="text-danger" id="erroreannulla"></span>', '<button class="btn btn-success" onclick="annullacambio(' + inizio + ', ' + colonna + ');"><i class="bi bi-check-circle-fill"></i> Rimuovi</button>');
}

function annullacambio(inizio, colonna) {
	var xhttp = new XMLHttpRequest();
	xhttp.onreadystatechange = function() {
		if (this.readyState == 4 && this.status == 200) {
			if (isNaN(parseInt(this.responseText))) {
				document.getElementById('erroreannulla').innerHTML = this.responseText;
			} else {
				location.reload();
			}
		}
	};
	xhttp.open("POST", "php/ajax.php", true);
	xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
	xhttp.send("ajax=annullacambio&id=" + id + "&inizio=" + inizio + "&colonna=" + colonna);
}

var foto;
						
function modalfoto() {
	var xhttp = new XMLHttpRequest();
	xhttp.onreadystatechange = function() {
		if (this.readyState == 4 && this.status == 200) {
			let lista = '';
			if (this.responseText != '0' && this.responseText != 'null') {
				foto = this.responseText.split(/\r?\n/);
				for (let i = 0; i < foto.length - 1; i++) {
					lista += '<div class="row"><div class="col-auto"><img src="foto/' + id + '/' + foto[i] + '" style="height: 60px; margin-bottom: 10px;"></div><div class="col">' + foto[i] + '<br><span id="elimfoto' + i + '"><button class="btn btn-sm btn-outline-info" onclick="rinfoto(' + i + ');"><i class="bi bi-pencil"></i> Rinomina</button>&nbsp;<button class="btn btn-sm btn-outline-danger" onclick="elimfoto(' + i + ');"><i class="bi bi-trash"></i> Elimina</button></span></div></div>';
				}
			} else {
				foto = null;
			}
			modal('Foto allegate', '<h6>Aggiungi foto</h6><form method="post" enctype="multipart/form-data" id="formupload"><input type="file" id="fileinput" class="form-control" style="cursor: pointer;" name="files[]" multiple="multiple"></form><div style="width: 100%; text-align: right;"><button class="btn btn-info" style="right: 0px;" onclick="caricafoto();"><i class="bi bi-upload"></i> Carica</button></div><span class="text-danger" id="erroreupload"></span>' + (this.responseText != '0' && this.responseText != 'null' ? '<hr><h6>Foto caricate</h6>' + lista : ''), false);
		}
	};
	xhttp.open("POST", "php/ajax.php", true);
	xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
	xhttp.send("ajax=listafile&id=" + id);
}

function elimfoto(index) {
	document.getElementById('elimfoto' + index).innerHTML = '<strong>Sei sicuro?</strong>&nbsp;<button class="btn btn-sm btn-outline-danger" onclick="annfoto(' + index + ');"><i class="bi bi-x-diamond-fill"></i> Annulla</button>&nbsp;<button class="btn btn-sm btn-success" onclick="eliminafoto(' + index + ');"><i class="bi bi-trash"></i> Elimina</button>';
}

function rinfoto(index) {
	document.getElementById('elimfoto' + index).innerHTML = '<input type="text" id="nuovonome' + index + '" class="form-control form-control-sm" value="' + foto[index] + '" onkeyup="if (event.keyCode == 13) rinominafoto(' + index + ');">&nbsp;<button class="btn btn-sm btn-outline-danger" onclick="annfoto(' + index + ');"><i class="bi bi-x-diamond-fill"></i> Annulla</button>&nbsp;<button class="btn btn-sm btn-success" onclick="rinominafoto(' + index + ');"><i class="bi bi-pencil"></i> Rinomina</button>';
}

function annfoto(index) {
	document.getElementById('elimfoto' + index).innerHTML = '<button class="btn btn-sm btn-outline-info" onclick="rinfoto(' + index + ');"><i class="bi bi-pencil"></i> Rinomina</button>&nbsp;<button class="btn btn-sm btn-outline-danger" onclick="elimfoto(' + index + ');"><i class="bi bi-trash"></i> Elimina</button>';
}

function eliminafoto(index) {
	var xhttp = new XMLHttpRequest();
	xhttp.onreadystatechange = function() {
		if (this.readyState == 4 && this.status == 200) {
			if (isNaN(parseInt(this.responseText))) {
				document.getElementById('erroreupload').innerHTML = this.responseText;
			} else {
				location.reload();
			}
		}
	};
	xhttp.open("POST", "php/ajax.php", true);
	xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
	xhttp.send("ajax=eliminafoto&id=" + id + "&indice=" + index);
}

function rinominafoto(index) {
	var nome = document.getElementById('nuovonome' + index).value;
	var xhttp = new XMLHttpRequest();
	xhttp.onreadystatechange = function() {
		if (this.readyState == 4 && this.status == 200) {
			if (isNaN(parseInt(this.responseText))) {
				document.getElementById('erroreupload').innerHTML = this.responseText;
			} else {
				location.reload();
			}
		}
	};
	xhttp.open("POST", "php/ajax.php", true);
	xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
	xhttp.send("ajax=rinominafoto&id=" + id + "&indice=" + index + "&nome=" + nome);
}

function caricafoto() {
	const form = document.getElementById("formupload");
	const formData = new FormData(form);
	formData.append("ajax", "caricafoto");
	formData.append("id", id);

	const request = new XMLHttpRequest();
	request.open("POST", "php/ajax.php", true);
	request.onreadystatechange = function() {
		if (this.readyState == 4 && this.status == 200) {
			if (isNaN(parseInt(this.responseText))) {
				document.getElementById('erroreupload').innerHTML = this.responseText;
			} else {
				location.reload();
			}
		}
	};
	request.send(formData);
}

function multiturno() {
	modal('Aggiunta di turni multipli', 'Inserire i codici separati da uno spazio:<textarea class="form-control" id="codici" rows="6"></textarea><span class="text-danger" id="erroreturni"></span>', '<button class="btn btn-success" onclick="salvaturni();"><i class="bi bi-patch-plus-fill"></i> Salva tutti</button>');
}

function salvaturni() {
	var codici = document.getElementById("codici").value;
	var xhttp = new XMLHttpRequest();
	xhttp.onreadystatechange = function() {
		if (this.readyState == 4 && this.status == 200) {
			if (isNaN(parseInt(this.responseText))) {
				document.getElementById('erroreturni').innerHTML = this.responseText;
			} else {
				location.reload();
			}
		}
	};
	xhttp.open("POST", "php/ajax.php", true);
	xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
	xhttp.send("ajax=salvaturni&id=" + id + "&codici=" + codici);
}

function modaleliminapartita() {
	modal('Elimina la partita', 'Sei sicuro di voler eliminare definitivamente questa partita e tutti i dati ad essa collegati? L\'azione non è reversibile.', '<button class="btn btn-success" onclick="eliminapartita();"><i class="bi bi-trash"></i> Elimina</button>');
}

function eliminapartita() {
	var xhttp = new XMLHttpRequest();
	xhttp.onreadystatechange = function() {
		if (this.readyState == 4 && this.status == 200) {
			window.location.href = 'partite.php';
		}
	};
	xhttp.open("POST", "php/ajax_admin.php", true);
	xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
	xhttp.send("ajax=eliminapartita&id=" + id);
}
