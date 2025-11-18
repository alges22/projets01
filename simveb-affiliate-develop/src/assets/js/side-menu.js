const sideMenu = () => {
	// Side Menu
	document.querySelectorAll(".side-menu").forEach((el) => {
		el.addEventListener("click", function () {
			if (el.parentElement.querySelector("ul")) {
				if (el.parentElement.querySelector("ul").style.display === "block") {
					el.querySelector(".side-menu__sub-icon").classList.remove("transform", "rotate-180");
					el.classList.remove("side-menu--open");
					el.parentElement.querySelector("ul").style.display = "none";
					el.parentElement.querySelector("ul").classList.remove("side-menu__sub-open");
				} else {
					el.querySelector(".side-menu__sub-icon").classList.add("transform", "rotate-180");
					el.classList.add("side-menu--open");
					el.parentElement.querySelector("ul").style.display = "block";
					el.parentElement.querySelector("ul").classList.add("side-menu__sub-open");
				}
			}
		});
	});

	document.querySelectorAll(".dropdown-toggle").forEach((el) => {
		if (el.parentElement.querySelector(".side-menu__sub-icon")) {
			el.addEventListener("click", function () {
				if (el.parentElement.querySelector(".side-menu__sub-icon").classList.contains("rotate-180")) {
					el.parentElement.querySelector(".side-menu__sub-icon").classList.remove("rotate-180");
				} else {
					el.parentElement.querySelector(".side-menu__sub-icon").classList.add("rotate-180");
				}
			});
		}
	});
};
export default sideMenu;
