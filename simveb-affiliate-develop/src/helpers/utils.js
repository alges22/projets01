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

export function scrollToTop(el) {
	el = document.querySelector(el);
	window.scrollTo({
		top: el.getBoundingClientRect().top + window.scrollY - 30,
		left: 0,
		behavior: "smooth",
	});
}

export function isoToEmoji(code) {
	return code
		.split("")
		.map((letter) => (letter.charCodeAt(0) % 32) + 0x1f1e5)
		.map((emojiCode) => String.fromCodePoint(emojiCode))
		.join("");
}

export function localeToCountry(locale) {
	if (locale === "fr") return "FR";
	if (locale === "en") return "US";
}

export function localeLongName(locale) {
	if (locale === "fr") return "Français";
	if (locale === "en") return "Anglais";
}

export const formatPrice = (price) => {
	return new Intl.NumberFormat("fr-FR", {
		style: "currency",
		currency: "XOF",
	}).format(price);
};

export const copyToClipboard = async (text) => {
	return await navigator.clipboard.writeText(text);
};

export function formatURLSearchParams(params) {
	let url = "";
	for (const [key, value] of Object.entries(params)) {
		if (value && value.constructor === Object) {
			for (const [k, v] of Object.entries(value)) {
				url += `${key}[${k}]=${v}&`;
			}
		} else if (value) {
			url += `${key}=${value}&`;
		}
	}
	return url.slice(0, -1);
}

export function playSound(url) {
	const audio = new Audio(url);
	audio.play();
}

export function capitalizeFirstLetter(string) {
	return string.charAt(0).toUpperCase() + string.slice(1);
}
