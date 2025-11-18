// import tailwind from "tailwindcss";
import "./show-modal";
// import "./show-slide-over";
// import "./show-dropdown";
// import "./search";
// import "./copy-code";
// import "./show-code";
import mobileMenu from "@/assets/js/mobile-menu.js";
// import "./side-menu-tooltip";
// import darkModeSwitcher from "@/assets/js/dark-mode-switcher.js";
import sideMenu from "@/assets/js/side-menu.js";
import "zoom-vanilla.js";

export const init = () => {
	mobileMenu();
	// darkModeSwitcher();
	sideMenu();
	// tailwind.svgLoader();
	// showSideMenuTooltip();
	// showModal();
	// showSlideOver();
	// showDropdown();
	// search();
	// copyCode();
	// showCode();
};
