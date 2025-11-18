export function debounce(fn, wait) {
	let timer;
	return function (...args) {
		if (timer) {
			clearTimeout(timer); // clear any pre-existing timer
		}
		const context = this; // get the current context
		timer = setTimeout(() => {
			fn.apply(context, args); // call the function if time expires
		}, wait);
	};
}

export function throttle(fn, wait) {
	let throttled = false;
	return function (...args) {
		if (!throttled) {
			fn.apply(this, args);
			throttled = true;
			setTimeout(() => {
				throttled = false;
			}, wait);
		}
	};
}

export function returnPreviousPage(router) {
	const hasHistory = window.history.length > 2;

	return hasHistory ? router.go(-1) : router.push("/login");
}

export function getRandomInt(min, max) {
	min = Math.ceil(min);
	max = Math.floor(max);
	return Math.floor(Math.random() * (max - min) + min); // The maximum is exclusive and the minimum is inclusive
}

export function deStructure(obj) {
	const result: any = {};
	Object.keys(obj).forEach((key) => {
		if (obj[key] === null || obj[key] === undefined || obj[key] === "") return;
		if (typeof obj[key] === "object") {
			Object.assign(result, deStructure(obj[key]));
		} else {
			result[key] = obj[key];
		}
	});
	return result;
}

export function formatHours(hours: number): string {
	const days = Math.floor(hours / 24); // Nombre de jours complets
	const remainingHours = hours % 24; // Heures restantes après avoir compté les jours

	let result = "";

	if (days > 0) {
		result += `${days} jour${days > 1 ? "s" : ""}`;
	}

	if (remainingHours > 0) {
		if (days > 0) {
			result += " "; // Ajouter un espace si les jours sont présents
		}
		result += `${remainingHours}h`;
	}

	return result || "0h";
}

export function trim(str: string) {
	return str.replace(/^\s+|\s+$/g, "");
}

export function capitalize(str: string) {
	return str.charAt(0).toUpperCase() + str.slice(1);
}
