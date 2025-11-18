import { Popover } from "bootstrap";

export const popover = {
	mounted(el) {
		new Popover(el, {
			placement: "top",
			trigger: "focus",
		});
	},
	unmounted(el) {
		const popover = Popover.getInstance(el);
		if (popover) {
			popover.dispose();
		}
	},
};
