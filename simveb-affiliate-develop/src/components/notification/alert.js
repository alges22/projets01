import Toastify from "toastify-js";
import dom from "@left4code/tw-starter/dist/js/dom";

const toastifyClass = "_" + Math.random().toString(36).substr(2, 9);
export default class Alert {
	constructor() {
		this.toast = null;
	}

	/**
	 * @description Affiche une alerte de succès dans le coin supérieur droit de l'application
	 * @param {string} message Message de l'alerte
	 * @param {boolean} autohide Masquer automatique l'alerte ?
	 * @param {number} timeout Temps d'affichage de l'alerte si masquage automatique
	 */
	static success(message, autohide = true, timeout = 5000) {
		let alert = new Alert();
		alert.notify("success", message, autohide, timeout, "beat");
	}

	static error(message, autohide = false, timeout = 7000) {
		let alert = new Alert();
		alert.notify("danger", message || "Une erreur s'est produite", autohide, timeout, "fade");
	}

	static warn(message, autohide = false, timeout = 7000) {
		let alert = new Alert();
		alert.notify("warning", message, autohide, timeout, "shake");
	}

	static info(message, autohide = false, timeout = 7000) {
		let alert = new Alert();
		alert.notify("info", message, autohide, timeout, "bounce");
	}

	notify(type, message, autohide = false, timeout = 5000, animation) {
		const toast = document.createElement("div");
		toast.classList.add("toastify-content", "flex", "items-center", "!border-l-4", toastifyClass);
		let icon = "";
		switch (type) {
			case "success":
				icon = "fa-check-circle text-green-500";
				toast.classList.add("!border-green-500");
				break;
			case "danger":
				icon = "fa-exclamation-circle text-red-500";
				toast.classList.add("!border-red-500");
				break;
			case "warning":
				icon = "fa-exclamation-triangle text-yellow-500";
				toast.classList.add("!border-yellow-500");
				break;
			default:
				icon = "fa-info-circle text-blue-500";

				break;
		}
		toast.innerHTML = `
			<i class="fa-light font-bold ${icon} fa-2x mr-4 ${!autohide ? "fa-" + animation : ""}"></i>
			<div class="ml-4 mr-4">
                  <div class="text-slate-500 text-lg">
                    ${message}
                  </div>
            </div>
		`;
		toast.toastify = Toastify({
			duration: autohide ? timeout : -1,
			newWindow: true,
			close: true,
			gravity: "top",
			position: "right",
			stopOnFocus: true,
			node: toast,
		}).showToast();

		dom(toast).on("click", "[data-dismiss='notification']", function () {
			toast.toastify.hideToast();
		});
	}
}
