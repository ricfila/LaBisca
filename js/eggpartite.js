let k_vecia = 0;
const audioEgg = new Audio;
const suoni_vecia = ["media/suoni/Un_demone.mp3", "media/suoni/Un_mostro.mp3", "media/suoni/Mio_Dio.mp3", "media/suoni/Urlo1.mp3"];

const audioContext = new AudioContext();
let animationStepsDone = new Set();
let animationStartTime = null;
const animationTimeline = [
	{ time: 0.000, fn: t => { t.style.scale = 1; } },
	{ time: 1.000, fn: t => { t.style.transition = "all 0.2s ease-out"; } },
	{ time: 4.500, fn: t => { t.style.scale = 1.4; t.style.transition = "all 0s"; } },
	{ time: 4.600, fn: t => { t.style.scale = 1; t.style.transition = "all 0.2s ease-out"; } },
	{ time: 4.700, fn: t => { t.style.scale = 1.6; t.style.transition = "all 0s"; } },
	{ time: 4.800, fn: t => { t.style.scale = 1; t.style.transition = "all 0.2s ease-out"; } },
	{ time: 4.900, fn: t => { t.style.scale = 2; t.style.transition = "all 0s"; } },
	{ time: 5.000, fn: t => { t.style.scale = 1; t.style.transition = "all 0.1s ease-in-out"; } },
	{ time: 5.300, fn: t => { t.style.filter = "hue-rotate(90deg)"; t.style.rotate = "2deg"; t.style.scale = 1.2; } },
	{ time: 5.500, fn: t => { t.style.filter = "hue-rotate(180deg)"; t.style.rotate = "-2deg"; t.style.objectPosition = "67% 50%"; } },
	{ time: 5.700, fn: t => { t.style.filter = "hue-rotate(270deg)"; t.style.rotate = "4deg"; t.style.objectPosition = "65% 50%"; } },
	{ time: 5.900, fn: t => { t.style.filter = "hue-rotate(360deg)"; t.style.rotate = "-6deg"; t.style.objectPosition = "60% 50%"; } },
	{ time: 6.000, fn: t => { t.style.transition = "all 0s"; } },
	{ time: 6.200, fn: t => { t.style.filter = "hue-rotate(360deg)"; } },
	{ time: 6.400, fn: t => { t.style.filter = "brightness(4)"; } },
	{ time: 6.600, fn: t => { t.style.filter = "invert(1)"; } },
	{ time: 6.800, fn: t => { t.style.filter = "none"; t.style.transition = "all 0.1s ease-in-out"; t._interval1 = setInterval(() => { t.style.filter = "brightness(8)"; }, 200); } },
	{ time: 6.880, fn: t => { t._interval2 = setInterval(() => { t.style.filter = "brightness(1)"; }, 200); } },
	{ time: 7.100, fn: t => { t.style.scale = 1.4; } },
	{ time: 7.300, fn: t => { t.style.scale = 1.2; } },
	{ time: 7.500, fn: t => { t.style.scale = 1.6; } },
	{ time: 7.650, fn: t => { t.style.scale = 1.2; } },
	{ time: 7.800, fn: t => { clearInterval(t._interval1); clearInterval(t._interval2); t.style.transition = "all 0.6s ease-in-out"; t.style.objectPosition = "0% 50%"; t.style.scale = 3; } },
	{ time: 8.100, fn: t => { t.style.opacity = 0; t.style.filter = "blur(5px)"; } },
	{ time: 8.800, fn: t => { t.parentElement && t.parentElement.removeChild(t); animationStartTime = null; } }
];

async function startCatAnimation() {
	if (animationStartTime != null && audioContext.currentTime - animationStartTime < 9.0) {
		return; // Non parte se l’animazione è già in corso
	}
	const response = await fetch("media/egg/rainbowcat.mp3");
	const arrayBuffer = await response.arrayBuffer();
	const audioBuffer = await audioContext.decodeAudioData(arrayBuffer);

	const source = audioContext.createBufferSource();
	source.buffer = audioBuffer;
	source.connect(audioContext.destination);

	// Crea e mostra l'immagine
	const t = document.createElement("img");
	t.src = "media/egg/rainbowcat.gif?random=" + Math.random();
	t.style.position = "fixed";
	t.style.left = "0";
	t.style.top = "0";
	t.style.width = "100vw";
	t.style.height = "100vh";
	t.style.objectFit = "cover";
	t.style.objectPosition = "70% 50%";
	t.style.zIndex = "9999";
	t.style.pointerEvents = "auto";
	t.style.transition = "all 0.5s ease-in-out";
	t.style.scale = "2";
	document.body.appendChild(t);
	void t.offsetHeight; // Forza reflow per vedere la prima animazione

	animationStepsDone.clear();
	animationStartTime = audioContext.currentTime;
	source.start(); // Parte l’audio sincronizzato
	requestAnimationFrame(() => runAnimationLoop(t));
}

function runAnimationLoop(t) {
	if (animationStartTime == null) return;			  
	const elapsed = audioContext.currentTime - animationStartTime;
  
	for (const { time, fn } of animationTimeline) {
		if (elapsed >= time && !animationStepsDone.has(time)) {
			animationStepsDone.add(time);
			fn(t);
		}
	}

	if (elapsed < 9.0) {
		requestAnimationFrame(() => runAnimationLoop(t));
	}
}

function tableEgg(el, tipo) {
	let viewportOffset = el.getBoundingClientRect();
	switch (tipo) {
		case 'chiamatavinta':
		case 'chiamatapersa':
		case 'socio':
			audioEgg.src = "media/egg/" + tipo + ".mp3";
			audioEgg.paused && audioEgg.play();
			break;
		case 'cappotto':
			audioEgg.src = "media/egg/odetojoy.mp3";
			audioEgg.paused && audioEgg.play();

			let coriandoli1 = document.createElement("img");
			coriandoli1.src = "media/egg/coriandoli1.gif";
			coriandoli1.style.width = "600px";
			coriandoli1.style.position = "fixed";
			coriandoli1.style.zIndex = "1053";
			coriandoli1.style.left = (viewportOffset.left + (el.clientWidth/2) - 300) + "px";
			coriandoli1.style.top = (viewportOffset.top - 280) + "px";
			document.body.appendChild(coriandoli1);
			window.setTimeout(() => { coriandoli1.parentElement.removeChild(coriandoli1); }, 10000);

			let coriandoli2 = document.createElement("img");
			coriandoli2.src = "media/egg/coriandoli2.gif";
			coriandoli2.style.position = "fixed";
			coriandoli2.style.zIndex = "1052";
			coriandoli2.style.left = "0";
			coriandoli2.style.top = "0";
			coriandoli2.style.width = "100vw";
			coriandoli2.style.height = "100vh";
			coriandoli2.style.objectFit = "cover";
			coriandoli2.style.transition = "all 1s linear";
			coriandoli2.style.filter = "brightness(10)";
			document.body.appendChild(coriandoli2);
			window.setTimeout(() => { coriandoli2.style.filter = "brightness(1)"; coriandoli2.style.transition = "all 4s linear"; }, 1000);
			window.setTimeout(() => { coriandoli2.style.filter = "hue-rotate(180deg)"; }, 2000);
			window.setTimeout(() => { coriandoli2.style.filter = "hue-rotate(360deg)"; }, 6000);
			window.setTimeout(() => { coriandoli2.style.transition = "all 1s linear"; coriandoli2.style.opacity = 0; }, 10000);
			window.setTimeout(() => { coriandoli2.parentElement.removeChild(coriandoli2); }, 12000);
			
			let coriandoli3 = document.createElement("img");
			coriandoli3.src = "media/egg/coriandoli3.gif";
			coriandoli3.style.position = "fixed";
			coriandoli3.style.zIndex = "1051";
			coriandoli3.style.left = "0";
			coriandoli3.style.bottom = "0";
			coriandoli3.style.width = "100vw";
			coriandoli3.style.height = "100vh";
			coriandoli3.style.objectFit = "cover";
			coriandoli3.style.transition = "all 1s linear";
			document.body.appendChild(coriandoli3);
			window.setTimeout(() => { coriandoli3.style.opacity = 0; }, 10000);
			window.setTimeout(() => { coriandoli3.parentElement.removeChild(coriandoli3); }, 12000);

			break;
		case 'vecia':
			let scale = 0.2 + (Math.random() * 0.2);
			audioEgg.src = "media/suoni/Allarme.mp3";
			
			let bee = new BeeAnimator({ width: 310*scale, height: 370*scale, imageSrc: "media/egg/vecia.png", audioSrc: suoni_vecia[k_vecia++] }, viewportOffset.left, viewportOffset.top-50);
			if (k_vecia >= suoni_vecia.length) k_vecia = 0;

			bee.start();
			audioEgg.paused && audioEgg.play();
			break;
		case '+4':
			startCatAnimation();
			break;
		case '-4':
			audioEgg.src = "media/egg/cimitero.mp3";
			audioEgg.paused && audioEgg.play();

			let cimitero = document.createElement("img");
			cimitero.src = "media/egg/cimitero.gif";
			cimitero.style.position = "fixed";
			cimitero.style.left = "0";
			cimitero.style.top = "0";
			cimitero.style.width = "100vw";
			cimitero.style.height = "100vh";
			cimitero.style.objectFit = "cover";
			cimitero.style.zIndex = "9999";
			cimitero.style.transition = "all 1.5s linear";
			cimitero.style.objectPosition = "40% 50%";
			document.body.appendChild(cimitero);
			window.setTimeout(() => { cimitero.style.filter = "blur(20px)"; }, 8500);
			window.setTimeout(() => { cimitero.style.opacity = 0; }, 9000);
			window.setTimeout(() => { cimitero.parentElement.removeChild(cimitero); }, 11000);
		default: break;
	}
}
