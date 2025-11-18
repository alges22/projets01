import { useDemandStore } from "/@src/stores/modules/demand";
import { storeToRefs } from "pinia";
import Swal from "sweetalert2";

const emitPrintOrder = async () => {
	const demandStore = useDemandStore();
	const { demand } = storeToRefs(demandStore);
	return new Promise<void>((resolve) => {
		Swal.fire({
			title: "Souhaitez-vous émettre l'ordre d'impression ?",
			text: "En émettant l'ordre d'impression, le demandeur pourra initier l'impression au niveau d'un affilié",
			icon: "warning",
			confirmButtonText: "Oui, Émettre",
			showCancelButton: true,
			cancelButtonText: "Annuler",
			input: "textarea",
			inputPlaceholder: "Observations",
		})
			.then(async (value) => {
				if (value.isConfirmed) {
					await demandStore
						.emitPrintOrder({
							demand_id: demand.value.id,
							print_observations: value.value,
						})
						.then(async (res) => {
							Swal.fire({
								title: "Ordre d'impression émis",
								text: `L'ordre d'impression a été émis. Référence: ${res.reference}`,
								icon: "success",
								confirmButtonText: "Copier la référence",
							})
								.then((result) => {
									if (result.isConfirmed) {
										navigator.clipboard.writeText(res.data.reference);
									}
								})
								.finally(() => {
									resolve();
								});
							await demandStore.getDemand(demand.value.id);
						});
				}
			})
			.finally(() => {
				resolve();
			});
	});
};

export default emitPrintOrder;
