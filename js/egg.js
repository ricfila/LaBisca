
function totoro() {
	const t = document.createElement("img");
	t.src = "media/egg/totoro.png";
	t.style.position = "fixed", t.style.left = "-179px", t.style.width = "179px", t.style.bottom = "5px", t.style.zIndex = "9999", t.style.transition = "left 9000ms linear", t.style.pointerEvents = "none";
	document.body.appendChild(t);
	window.setTimeout(function () { t.style.left = "120vw" }, 500);
	window.setTimeout(function () { t.style.left = "-179px", t.style.transform = "scaleX(-1)" }, 9500);
	window.setTimeout(function () { t.parentNode.removeChild(t) }, 19e3);
}

let cars = []; // Array per tracciare le macchinette attive

function zigozago(dir) {
	let width = window.innerWidth;
	let height = window.innerHeight;
	let roads = [];
	for (let i = 50; i < height - 200; i += 150) {
		roads.push(i);
	}
	let road = roads[Math.floor(Math.random() * roads.length)];

	const t = document.createElement("img");
	t.src = "media/egg/zigozago.png";
	t.style.width = "100px";
	t.style.position = "fixed";
	t.style.zIndex = "9999";

	let time = Math.floor((Math.random() * 2000) + 3000);

	t.style.transform = "scaleX(" + dir + ")";
	t.style.left = (dir == 1 ? -110 : width + 110) + "px";
	t.style.bottom = road + "px";
	t.style.transition = "left " + time + "ms linear";
	t.style.pointerEvents = "none";
	t.style.cursor = "pointer";
	document.body.appendChild(t);

	// Aggiungi la macchinetta all'array con la sua posizione
	let car = { element: t, road: road, direction: dir };
	cars.push(car);

	window.setTimeout(() => {
		t.style.left = (dir == 1 ? "120vw" : "-20vw");
	}, 0);

	window.setTimeout(() => {
		removeCar(car);
	}, time + 500);
}
document.addEventListener("click", function (event) {
    cars.forEach(car => {
        let rect = car.element.getBoundingClientRect();
        if (
            event.clientX >= rect.left && event.clientX <= rect.right &&
            event.clientY >= rect.top && event.clientY <= rect.bottom
        ) {
            showExplosion(rect.left, parseInt(car.element.style.bottom), car.direction);
            removeCar(car);
        }
    });
});

// Controllo collisioni
setInterval(() => {
	for (let i = 0; i < cars.length; i++) {
		for (let j = i + 1; j < cars.length; j++) {
			let car1 = cars[i];
			let car2 = cars[j];

			// Stessa altezza e direzioni opposte
			if (car1.road === car2.road && car1.direction !== car2.direction) {
				let rect1 = car1.element.getBoundingClientRect();
				let rect2 = car2.element.getBoundingClientRect();

				// Controllo se si sovrappongono
				if (Math.abs(rect1.left - rect2.left) < 50) {
					showExplosion((rect1.left + rect2.left) / 2, car1.road);
					removeCar(car1);
					removeCar(car2);
				}
			}
		}
	}
}, 50);

function showExplosion(x, y, dir=0) {
	let explosion = document.createElement("img");
	explosion.src = "media/egg/boom.gif?rand=" + Math.random();
	explosion.style.width = "150px";
	explosion.style.position = "fixed";
	explosion.style.left = x + "px";
	explosion.style.bottom = y + "px";
	explosion.style.zIndex = "10000";

	explosion.style.transition = "left 1000ms ease-out";
	window.setTimeout(() => {
		explosion.style.left = (x + dir*window.innerWidth/10) + "px";
	}, 0);

	document.body.appendChild(explosion);

	setTimeout(() => {
		explosion.remove();
	}, 1000); // Tempo dell'animazione dell'esplosione

	let a = document.createElement("audio");
	a.src = "media/suoni/Tuono.mp3";
	a.play();
	setTimeout(() => { a.remove(); }, 15000);
}

function removeCar(car) {
	if (car.element.parentNode) {
		car.element.remove();
	}
	cars = cars.filter(c => c !== car);
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

var randomIndex = Math.floor(12 * Math.random()) + 1;
var currentAudio = new Audio;
document.addEventListener("DOMContentLoaded", function () {
	let bee;
	let k = 0;
	let fisa = [0,1,2,3,4,5,6,7,8,9].sort(() => Math.random() - 0.5);
	const btn = document.createElement("img");
	btn.src = "media/egg/vecia_btn.png";
	//randomIndex = 6;
	btn.id = "randomButton", document.body.appendChild(btn), btn.addEventListener("click", () => {
		switch (randomIndex) {
			case 1: // Ape Maia
				currentAudio.src = "media/egg/maia" + (Math.floor(6 * Math.random())) + ".mp3";
				bee = new BeeAnimator;
				bee.start();
				break;
			case 2: // Gatto
				currentAudio.src = "media/egg/oia_oia.mp3";
				currentAudio.loop = true;
				bee = new BeeAnimator({ width: 200, height: 200, imageSrc: "media/egg/oia-uia.gif", audioSrc: "media/egg/oia_oia.mp3" });
				bee.start();
				break;
			case 3: // Totoro
				btn.parentNode && btn.parentNode.removeChild(btn);
				currentAudio.src = "media/egg/totoro" + (Math.floor(2 * Math.random())) + ".mp3";
				totoro();
				break;
			case 4: // Fisarmonica
				btn.src = "media/egg/fisarmonica.gif";
				btn.style.width = "200px";
				currentAudio.pause();
				currentAudio.src = "media/egg/fisa" + fisa[k++] + ".mp3";
				if (k >= fisa.length) k = 0;
				currentAudio.loop = true;
				currentAudio.play();
				break;
			case 5: // Zigo Zago
				currentAudio.src = "media/suoni/Clacson.mp3";
				zigozago(k == 0 ? ++k : k++);
				if (k == 1) k = -1;
				break;
			case 6: // Stitch
				btn.parentNode && btn.parentNode.removeChild(btn);
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
				setTimeout(function () { t.parentElement.removeChild(t); }, 2000);
				break;
			default: // Vecia
				const scale = 0.2 + (Math.random() * 0.3);
				const vecia = ["media/suoni/Un_demone.mp3", "media/suoni/Un_mostro.mp3", "media/suoni/Mio_Dio.mp3"];
				currentAudio.src = "media/suoni/Allarme.mp3";//, currentAudio.loop = true;
				bee = new BeeAnimator({ width: 310*scale, height: 370*scale, imageSrc: "media/egg/vecia.png", audioSrc: vecia[k++] });
				if (k >= vecia.length) k = 0;
				bee.start();
				break;
		}
		currentAudio.paused && currentAudio.play()
	})
});
	
class BeeAnimator {
	constructor(t = {}) {
		this.options = {
			width: 64, height: 74, imageSrc: "media/egg/maia.png", maxSpeed: 3, acceleration: .001, friction: .95, randomFactor: .8,
			useFullScreen: true, container: null, audioSrc: "media/egg/maia0.mp3", ...t
		};
		this.x = window.innerWidth / 2, this.y = window.innerHeight / 2, this.angle = 0, this.vx = 0, this.vy = 0, this.targetX = this.x, this.targetY = this.y;
		this.initialize();
	}
	initialize() {
		if (this.options.useFullScreen)
			this.options.container = document.body, /*document.body.style.margin = "0", document.body.style.padding = "0", document.body.style.width = "100vw",*/ document.body.style.height = "100vh", document.body.style.position = "relative";
		else if (!this.options.container) {
			const t = document.createElement("div");
			t.style.position = "relative", t.style.width = "100vw", t.style.height = "100vh", document.body.appendChild(t), this.options.container = t
		}
		this.element = document.createElement("div"), this.element.style.position = "fixed", this.element.style.width = `${this.options.width}px`, this.element.style.height = `${this.options.height}px`, this.element.style.backgroundImage = `url('${this.options.imageSrc}')`, this.element.style.backgroundSize = "cover", this.element.style.transition = "transform 0.2s ease", this.element.style.left = `${this.x}px`, this.element.style.top = `${this.y}px`, this.element.style.zIndex = "1000", this.element.style.pointerEvents = "auto", this.element.style.cursor = "pointer", this.options.audioSrc && (this.audioElement = document.createElement("audio"), this.audioElement.src = this.options.audioSrc, this.element.addEventListener("click", () => { this.audioElement.currentTime = 0, this.audioElement.play().catch(t => console.error("Errore riproduzione audio:", t)) })), this.options.container.appendChild(this.element), window.addEventListener("resize", this.handleResize.bind(this))
	}
	setAudio(t) {
		return !this.audioElement && t && (this.audioElement = document.createElement("audio"), this.element.addEventListener("click", () => { this.audioElement.currentTime = 0, this.audioElement.play().catch(t => console.error("Errore riproduzione audio:", t)) })), this.audioElement && t && (this.audioElement.src = t, this.options.audioSrc = t), this
	}
	start() {
		return this.newTarget(), this.animationFrame = requestAnimationFrame(this.animate.bind(this)), this
	}
	stop() {
		return this.animationFrame && cancelAnimationFrame(this.animationFrame), this
	}
	remove() {
		return this.stop(), this.element && this.element.parentNode && this.element.parentNode.removeChild(this.element), this
	}
	newTarget() {
		return this.targetX = Math.random() * (window.innerWidth - this.options.width - 100) + 50, this.targetY = Math.random() * (window.innerHeight - this.options.height - 100) + 50, this.targetX > this.x ? this.element.style.transform = "scaleX(1)" : this.element.style.transform = "scaleX(-1)", this
	}
	animate() {
		const t = this.targetX - this.x, e = this.targetY - this.y, i = Math.sqrt(t * t + e * e);
		i < 15 && this.newTarget(), this.vx += t * this.options.acceleration, this.vy += e * this.options.acceleration;
		const n = Math.sqrt(this.vx * this.vx + this.vy * this.vy);
		n > this.options.maxSpeed && (this.vx = this.vx / n * this.options.maxSpeed, this.vy = this.vy / n * this.options.maxSpeed), this.vx += (Math.random() - .5) * this.options.randomFactor, this.vy += (Math.random() - .5) * this.options.randomFactor, this.x += this.vx, this.y += this.vy, this.vx *= this.options.friction, this.vy *= this.options.friction, this.x < 0 && (this.x = 0, this.vx *= -.5), this.x > window.innerWidth - this.options.width && (this.x = window.innerWidth - this.options.width, this.vx *= -.5), this.y < 0 && (this.y = 0, this.vy *= -.5), this.y > window.innerHeight - this.options.height && (this.y = window.innerHeight - this.options.height, this.vy *= -.5), this.angle += .1;
		const o = 2 * Math.sin(this.angle);
		this.element.style.left = `${this.x}px`, this.element.style.top = `${this.y + o}px`, this.animationFrame = requestAnimationFrame(this.animate.bind(this))
	}
	handleResize() {
		this.x > window.innerWidth - this.options.width && (this.x = window.innerWidth - this.options.width), this.y > window.innerHeight - this.options.height && (this.y = window.innerHeight - this.options.height)
	}
}