const darkModeSwitcher = () => {
	// Dark mode switcher
	document.querySelector(".dark-mode-switcher").addEventListener("click", function () {
		let switcher = this.querySelector(".dark-mode-switcher__toggle");
		if (switcher.classList.contains("dark-mode-switcher__toggle--active")) {
			switcher.classList.remove("dark-mode-switcher__toggle--active");
		} else {
			switcher.classList.add("dark-mode-switcher__toggle--active");
		}

		setTimeout(() => {
			let link = this.getAttribute("data-url");
			window.location.href = link;
		}, 500);
	});
};

export default darkModeSwitcher;
