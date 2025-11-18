import { useDemandStore } from "/@src/stores/modules/demand";
import { storeToRefs } from "pinia";
import Swal from "sweetalert2";
import { Notyf } from "notyf";

const notyf = new Notyf();

const affectToInterpol = async () => {
	const demandStore = useDemandStore();
	const { demand } = storeToRefs(demandStore);
	return new Promise<void>((resolve) => {
		Swal.fire({
			title: "Affection à Interpol",
			text: "Cette action fera passer la demande au niveau d'Interpol pour contrôle et validation, voulez-vous continuer?",
			icon: "warning",
			confirmButtonText: "Oui, Poursuivre",
			showCancelButton: true,
			cancelButtonText: "Annuler",
		}).then(async (value) => {
			if (value.isConfirmed) {
				await demandStore
					.assignDemand("interpol", {
						demand_id: demand.value.id,
					})
					.then(async (res) => {
						notyf.success(res.message);
						await demandStore.getDemand(demand.value.id);
					})
					.finally(() => {
						resolve();
					});
			}
		});
	});
};

export default affectToInterpol;
