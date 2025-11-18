import SimpleBar from "simplebar";

const mobileMenu = () => {
	// Scrollbar
	if (document.querySelector(".mobile-menu .scrollable")) {
		new SimpleBar(document.querySelector(".mobile-menu .scrollable"));
	}

	// Mobile Menu
	document.querySelectorAll(".mobile-menu-toggler").forEach((el) => {
		el.addEventListener("click", function () {
			if (document.querySelector(".mobile-menu").classList.contains("mobile-menu--active")) {
				document.querySelector(".mobile-menu").classList.remove("mobile-menu--active");
			} else {
				document.querySelector(".mobile-menu").classList.add("mobile-menu--active");
			}
		});
	});

	document.querySelectorAll(".mobile-menu .menu").forEach((el) => {
		el.addEventListener("click", function () {
			if (el.parentElement.querySelector("ul")) {
				if (el.parentElement.querySelector("ul").style.display === "block") {
					el.querySelector(".menu__sub-icon").classList.remove("transform", "rotate-180");
					el.parentElement.querySelector("ul").style.display = "none";
					el.parentElement.querySelector("ul").classList.remove("menu__sub-open");
				} else {
					el.querySelector(".menu__sub-icon").classList.add("transform", "rotate-180");
					el.parentElement.querySelector("ul").style.display = "block";
					el.parentElement.querySelector("ul").classList.add("menu__sub-open");
				}
			}
		});
	});
};

export default mobileMenu;
