export const useDeleteConfirmation = (onSubmit: Function, message?: String, onCancel?: Function) => {
	const submitting = ref(false);
	const init = async (item: any) => {
		await Swal.fire({
			title: "Attention",
			text: message ?? "Êtes vous sûr(e) de vouloir supprimer cet élément ?",
			icon: "warning",
			confirmButtonText: "Oui, Supprimer",
			showCancelButton: true,
			cancelButtonText: "Annuler",
		}).then((value) => {
			submitting.value = true;
			if (value.isConfirmed) {
				onSubmit(item);
			} else if (onCancel) {
				onCancel(item);
			}
			submitting.value = false;
		});
	};

	return reactive({
		submitting,
		handleDelete: init,
	});
};
