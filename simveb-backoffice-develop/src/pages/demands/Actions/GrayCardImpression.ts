import { useDemandStore } from "/@src/stores/modules/demand";
import { storeToRefs } from "pinia";
import Swal from "sweetalert2";
import { Notyf } from "notyf";

const notyf = new Notyf();
const printGrayCard = async () => {
	const demandStore = useDemandStore();
	const { demand } = storeToRefs(demandStore);
	return new Promise<void>((resolve) => {
		Swal.fire({
			cancelButtonText: "Annuler",
			confirmButtonText: "Oui, Imprimer",
			icon: "warning",
			input: null,
			showCancelButton: true,
			text: "Vous êtes sur le point de lancer l'impression de la carte grise.",
			title: "Impression de la carte grise",
		})
			.then(async (value) => {
				if (value.isConfirmed) {
					await demandStore
						.printGrayCard({
							demand_id: demand.value.id,
						})
						.then((res) => {
							notyf.success("L'impression de la carte grise est en cours.");
							demandStore.getDemand(demand.value.id);
							setTimeout(() => {
								notyf.error("Aucune imprimante compatible disponible.");
							}, 2000);
						})
						.finally(() => {
							resolve();
						});
				}
			})
			.finally(() => {
				resolve();
			});
	});
};

export default printGrayCard;
