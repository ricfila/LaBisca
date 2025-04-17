
let cars = []; // Array per tracciare le macchinette attive
let kz = 0;
				
function zigozago() {
	let dir = kz == 0 ? ++kz : kz++;
	if (kz == 1) kz = -1;

	let width = window.innerWidth;
	let height = window.innerHeight;
	let roads = [];
	for (let i = 20; i < height - 50; i += 150) {
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
	t.style.left = (dir == 1 ? -110 : width + 10) + "px";
	t.style.bottom = road + "px";
	t.style.transition = "left " + time + "ms linear";
	t.style.pointerEvents = "auto";
	t.style.cursor = "pointer";
	document.body.appendChild(t);

	// Aggiungi la macchinetta all'array con la sua posizione
	let car = { element: t, road: road, direction: dir };
	cars.push(car);

	window.setTimeout(() => {
		t.style.left = (dir == 1 ? "120vw" : "-110px");
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
	const explosion = document.createElement("video");
	explosion.src = "media/egg/boom.webm";
	explosion.autoplay = true;
	explosion.muted = true;
	explosion.playsInline = true;
	explosion.style.width = "150px";
	explosion.style.position = "fixed";
	explosion.style.left = x + "px";
	explosion.style.bottom = y + "px";
	explosion.style.zIndex = "10000";
	explosion.style.pointerEvents = "none";
	explosion.style.transition = "left 1000ms ease-out";
	explosion.style.objectFit = "contain";

	document.body.appendChild(explosion);

	window.setTimeout(() => {
		explosion.style.left = (x + dir*window.innerWidth/10) + "px";
	}, 0);

	setTimeout(() => {
		explosion.remove();
	}, 1000); // Tempo dell'animazione dell'esplosione

	let a = document.createElement("audio");
	a.src = "media/egg/incidente.mp3";
	a.play();
	setTimeout(() => { a.remove(); }, 2000);

	let b = document.getElementById('Clacson');
	b.pause();
	b.currentTime = 0;
}

function removeCar(car) {
	if (car.element.parentNode) {
		car.element.remove();
	}
	cars = cars.filter(c => c !== car);
}
