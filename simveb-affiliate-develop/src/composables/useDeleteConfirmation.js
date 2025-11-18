import { reactive, ref } from "vue";
import Swal from "sweetalert2";

/**
 * useDeleteConfirmation is a composable function that provides a delete confirmation dialog.
 * It uses SweetAlert2 (Swal) to display a confirmation dialog when deleting an item.
 *
 * @function
 * @param {Function} onSubmit - The function to be called when the delete is confirmed.
 * @param {String} [message] - Optional. The message to be displayed in the confirmation dialog. Defaults to "Êtes vous sûr(e) de vouloir supprimer cet élément ?".
 * @param {Function} [onCancel] - Optional. The function to be called when the delete is cancelled.
 * @returns {Object} An object containing the 'submitting' ref and the 'handleDelete' function.
 */
export const useDeleteConfirmation = (onSubmit, message, onCancel, confirmButton) => {
	const submitting = ref(false);
	/**
	 * The function to be called when the delete action is initiated.
	 * It displays a confirmation dialog and calls the appropriate function based on the user's response.
	 *
	 * @async
	 * @param {any} item - The item to be deleted.
	 */
	const init = async (item) => {
		await Swal.fire({
			title: "Attention",
			text: message ?? "Êtes vous sûr(e) de vouloir supprimer cet élément ?",
			icon: "warning",
			confirmButtonText: confirmButton ?? "Oui, Supprimer",
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
		handleDelete: init,
		submitting,
	});
};
