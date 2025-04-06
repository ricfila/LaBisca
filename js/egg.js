
function totoro() {
	egg_incorso = true;
	const t = document.createElement("img");
	t.src = "media/egg/totoro.png";
	t.style.position = "fixed", t.style.left = "-179px", t.style.width = "179px", t.style.bottom = "5px", t.style.zIndex = "9999", t.style.transition = "left 9000ms linear", t.style.pointerEvents = "none";
	document.body.appendChild(t);
	window.setTimeout(function () { t.style.left = "120vw"; }, 500);
	window.setTimeout(function () { t.style.left = "-179px"; t.style.transform = "scaleX(-1)"; }, 9500);
	window.setTimeout(function () { t.parentNode.removeChild(t); egg_incorso = false; }, 19e3);
}

/*
function setCountdownTime(t, e) {
	const i = new Date;
	return i.setHours(t), i.setMinutes(e), i.setSeconds(0), i < new Date && i.setDate(i.getDate() + 1), i
}
const countDownDate = setCountdownTime(23, 59)
timer = setInterval(function () {
	const t = (new Date).getTime()
	e = countDownDate - t, i = Math.floor(e / 864e5), n = Math.floor(e % 864e5 / 36e5), o = Math.floor(e % 36e5 / 6e4), s = Math.floor(e % 6e4 / 1e3), a = String(i).padStart(2, "0"), h = String(n).padStart(2, "0"), r = String(o).padStart(2, "0"), d = String(s).padStart(2, "0");
	document.getElementById("countdown").innerHTML = a + ":" + h + ":" + r + ":" + d, e < 0 && (clearInterval(timer), document.getElementById("countdown").innerHTML = "EXPIRED")
}, 1e3);
*/

let eggclick = 0;
var currentAudio = new Audio;
let bee;
let randomIndex;
let egg_incorso = false;

$(document).ready(function(){
	randomIndex = Math.floor(3 * Math.random()) + 1;

	$(".toggle-easter-egg").each(function (i, e) {
		e.addEventListener("click", function (e) {
			if (++eggclick > 4) {
				if (!egg_incorso)
					attivaegg();
				eggclick = 0;
			}
		});
	});
});

function attivaegg() {
	//randomIndex = 1;
	switch (randomIndex) {
		case 0: // Ape Maia
			currentAudio.src = "media/egg/maia" + (Math.floor(6 * Math.random())) + ".mp3";
			bee = new BeeAnimator;
			bee.start();
			currentAudio.paused && currentAudio.play();
			break;
		case 1: // Gatto
			bee = new BeeAnimator({ width: 150, height: 150, imageSrc: "media/egg/oia-uia.gif", audioSrc: false });
			bee.start();
			if (currentAudio.paused) {
				currentAudio.src = "media/egg/oia_oia.mp3";
				currentAudio.loop = true;
				currentAudio.play();
			}
			break;
		case 2: // Totoro
			currentAudio.src = "media/egg/totoro" + (Math.floor(2 * Math.random())) + ".mp3";
			totoro();
			currentAudio.paused && currentAudio.play();
			break;
		case 3: // Stitch
			egg_incorso = true;
			let t = document.createElement("img");
			t.style.position = "fixed";
			t.style.left = "0";
			t.style.top = "0";
			t.style.width = "100vw";
			t.style.height = "100vh";
			t.style.objectFit = "cover";
			t.style.zIndex = "9999";
			t.style.pointerEvents = "none";
			document.body.appendChild(t);
			t.src = "media/egg/stitch.gif?random=" + Math.random();
			currentAudio.src = "media/egg/vetro.mp3";
			setTimeout(function () { currentAudio.play() }, 800);
			setTimeout(function () { t.parentElement.removeChild(t); egg_incorso = false; }, 2000);
			break;
		default:
			break;
	}
}
	
class BeeAnimator {
	constructor(t = {}, x = null, y = null) {
		this.options = {
			width: -100, height: 74, imageSrc: "media/egg/maia.png", maxSpeed: 3, acceleration: .001, friction: .95, randomFactor: .8, xstart: -100,
			useFullScreen: true, container: null, audioSrc: "media/egg/maia0.mp3", ...t
		};
		if (x === null) this.x = window.innerWidth / 2; else this.x = x;
		if (y === null) this.y = window.innerHeight / 2; else this.y = y;
		this.angle = 0, this.vx = 0, this.vy = 0, this.targetX = this.x, this.targetY = this.y;
		this.initialize();
	}
	initialize() {
		if (this.options.useFullScreen)
			this.options.container = document.body, /*document.body.style.margin = "0", document.body.style.padding = "0", document.body.style.width = "100vw",*/ document.body.style.height = "100vh", document.body.style.position = "relative";
		else if (!this.options.container) {
			const t = document.createElement("div");
			t.style.position = "relative", t.style.width = "100vw", t.style.height = "100vh", document.body.appendChild(t), this.options.container = t
		}
		this.element = document.createElement("div");
		this.element.style.position = "fixed";
		this.element.style.width = `${this.options.width}px`, this.element.style.height = `${this.options.height}px`;
		this.element.style.backgroundImage = `url('${this.options.imageSrc}')`, this.element.style.backgroundSize = "cover";
		this.element.style.transition = "all 0.2s ease";
		this.element.style.left = `${this.x}px`, this.element.style.top = `${this.y}px`;
		this.element.style.zIndex = "1060";
		this.element.style.pointerEvents = "none";
		this.element.style.scale = "0.2";
		this.element.style.transform = "scaleX(0)";
		if (this.options.audioSrc) {
			this.element.style.cursor = "pointer";
			this.audioElement = document.createElement("audio");
			this.audioElement.src = this.options.audioSrc;
			this.element.addEventListener("click", () => {
				this.audioElement.currentTime = 0;
				this.audioElement.play().catch(t => console.error("Errore riproduzione audio:", t));
				this.element.style.scale = "0.8";
				this.element.style.filter = "brightness(150%)";
				window.setTimeout(() => { this.element.style.scale = "1"; this.element.style.filter = "";}, 200);
			});
		}
		this.options.container.appendChild(this.element), window.addEventListener("resize", this.handleResize.bind(this))
	}
	setAudio(t) {
		return !this.audioElement && t && (this.audioElement = document.createElement("audio"), this.element.addEventListener("click", () => { this.audioElement.currentTime = 0, this.audioElement.play().catch(t => console.error("Errore riproduzione audio:", t)) })), this.audioElement && t && (this.audioElement.src = t, this.options.audioSrc = t), this
	}
	start() {
		this.newTarget();
		this.animationFrame = requestAnimationFrame(this.animate.bind(this));
		window.setTimeout(() => { this.element.style.scale = "1"; }, 0);
		if (this.options.audioSrc)
			window.setTimeout(() => { this.element.style.pointerEvents = "auto"; }, 2000);
		return this;
	}
	stop() {
		return this.animationFrame && cancelAnimationFrame(this.animationFrame), this
	}
	remove() {
		return this.stop(), this.element && this.element.parentNode && this.element.parentNode.removeChild(this.element), this
	}
	newTarget() {
		this.targetX = Math.random() * (window.innerWidth - this.options.width);
		this.targetY = Math.random() * (window.innerHeight - this.options.height - 100) + 50;
		this.element.style.transform = "scaleX(" + (this.targetX > this.x ? 1 : -1) + ")";
		return this;
	}
	animate() {
		const t = this.targetX - this.x, e = this.targetY - this.y, i = Math.sqrt(t * t + e * e);
		i < 15 && this.newTarget();
		this.vx += t * this.options.acceleration, this.vy += e * this.options.acceleration;
		const n = Math.sqrt(this.vx * this.vx + this.vy * this.vy);
		n > this.options.maxSpeed && (this.vx = this.vx / n * this.options.maxSpeed, this.vy = this.vy / n * this.options.maxSpeed);
		this.vx += (Math.random() - .5) * this.options.randomFactor, this.vy += (Math.random() - .5) * this.options.randomFactor;
		this.x += this.vx, this.y += this.vy;
		this.vx *= this.options.friction, this.vy *= this.options.friction;
		this.x < this.options.xstart && (this.x = this.options.xstart, this.vx *= -.5), this.x > window.innerWidth - this.options.width && (this.x = window.innerWidth - this.options.width, this.vx *= -.5);
		this.y < 0 && (this.y = 0, this.vy *= -.5), this.y > window.innerHeight - this.options.height && (this.y = window.innerHeight - this.options.height, this.vy *= -.5);
		this.angle += .1;
		const o = 2 * Math.sin(this.angle);
		this.element.style.left = `${this.x}px`, this.element.style.top = `${this.y + o}px`;
		this.animationFrame = requestAnimationFrame(this.animate.bind(this))
	}
	handleResize() {
		this.x > window.innerWidth - this.options.width && (this.x = window.innerWidth - this.options.width), this.y > window.innerHeight - this.options.height && (this.y = window.innerHeight - this.options.height)
	}
}
