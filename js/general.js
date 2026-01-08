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

function alias(checked) {
	setCookie("alias", checked, 365);
	nomealias = (checked ? 1 : 0);
	var nomi = document.getElementsByClassName('nome');
	var alias = document.getElementsByClassName('alias');
	for (var i = 0; i < nomi.length; i++) {
		nomi[i].style.display = (checked ? 'none' : 'inline');
	}
	for (var i = 0; i < alias.length; i++) {
		alias[i].style.display = (checked ? 'inline' : 'none');
	}
	if (typeof marquee === "function")
		marquee();
}

;(function () {
	const htmlElement = document.querySelector("html")
	if (htmlElement.getAttribute("data-bs-theme") === 'auto') {
		window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', updateTheme)
		updateTheme()
	}
})()

function updateTheme() {
	if (document.querySelector("html").getAttribute("data-bs-theme") === 'auto') {
		let dark = window.matchMedia("(prefers-color-scheme: dark)").matches;
		document.querySelector("html").setAttribute("data-bs-theme", (dark ? "dark" : "light"))
	}
	aggiornalogo();
}

function aggiornalogo() {
	let logo = document.querySelector("#imglogo");
	let dark = document.querySelector("html").getAttribute("data-bs-theme") == "dark";
	if (logo != null)
		logo.setAttribute("src", (dark ? 'media/img/Bisca_index_bianco.png' : 'media/img/Bisca_index.png'));
}

function initializecarousel() {
	let carousels = document.getElementsByClassName('carousel');
	for (let i = 0; i < carousels.length; i++) {
		let carousel = new bootstrap.Carousel(carousels[i]);
		carousels[i].addEventListener('slide.bs.carousel', function(e) {
			let nextH = $(e.relatedTarget).height();
			$('#' + carousels[i].id + '-inner').animate({
				height: nextH
			}, 600);
		});
	}
}

const audioMed = new Audio;
function suonomedaglia(n) {
	audioMed.src = "media/egg/med" + n + ".mp3";
	audioMed.paused && audioMed.play();
}