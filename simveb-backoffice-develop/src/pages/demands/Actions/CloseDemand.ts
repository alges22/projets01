import { useDemandStore } from "/@src/stores/modules/demand";
import { storeToRefs } from "pinia";
import Swal from "sweetalert2";

const closeDemand = async () => {
	const demandStore = useDemandStore();
	const { demand } = storeToRefs(demandStore);
	return new Promise<void>((resolve) => {
		Swal.fire({
			title: "Confirmer la clôture",
			text: "Êtes-vous sûr de vouloir clôturer cette demande ? Cela mettra fin à tout traitement en cours.",
			icon: "warning",
			confirmButtonText: "Oui, Clôturer",
			showCancelButton: true,
			cancelButtonText: "Annuler",
			input: null,
		})
			.then(async (value) => {
				if (value.isConfirmed) {
					await demandStore
						.closeDemand({
							demand_id: demand.value.id,
						})
						.then(async (res) => {
							Swal.fire({
								title: "Demande clôturée",
								text: "La demande a été clôturée avec succès.",
								icon: "success",
								confirmButtonText: "OK",
							})
								.then(async () => {
									await demandStore.getDemand(demand.value.id);
								})
								.finally(() => {
									resolve();
								});
						});
				}
			})
			.finally(() => {
				resolve();
			});
	});
};

export default closeDemand;
