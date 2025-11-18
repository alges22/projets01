import tippy from "tippy.js";

export const tooltip = {
	mounted(el, options) {
		if (el.getAttribute("title")) {
			tippy(el, {
				content: el.getAttribute("title"),
				placement: el.getAttribute("data-placement") || "top",
				allowHTML: true,
				trigger: el.getAttribute("data-trigger") || "mouseenter",
				hideOnClick: true,
			});
			el.removeAttribute("title");
		}
	},
	update(el) {
		const instance = tippy(el);
		instance && instance.setContent(el.getAttribute("title"));
	},
	unmounted(el) {
		const instance = tippy(el);
		instance && instance.destroy();
	},
};
