<script setup lang="ts">
	import {FontAwesomeIcon} from "@fortawesome/vue-fontawesome";
	import {useApi} from "~/helpers/useApi";
	const route = useRoute()

	const api = useApi()

	const code = route.params.code

	function downloadBlob(blob, name = 'file.txt') {
		// Convert your blob into a Blob URL (a special url that points to an object in the browser's memory)
		const blobUrl = URL.createObjectURL(blob);

		// Create a link element
		const link = document.createElement("a");

		// Set link's href to point to the Blob URL
		link.href = blobUrl;
		link.download = name;

		// Append link to the body
		document.body.appendChild(link);

		// Dispatch click event on the link
		// This is necessary as link.click() does not work on the latest firefox
		link.dispatchEvent(
			new MouseEvent('click', {
				bubbles: true,
				cancelable: true,
				view: window
			})
		);

		// Remove link from body
		document.body.removeChild(link);
	}

	const downloadInvoice = () => {
		api({
			method: 'POST',
			url: `/client/invoices/${code}/generate`,
			responseType: "blob",
		}).then((response) => {
			const href = URL.createObjectURL(response.data);
			const link = document.createElement("a");
			link.href = href;
			link.setAttribute(
				"download",
				`${code}.pdf`
			);
			document.body.appendChild(link);
			link.click();

			document.body.removeChild(link);
			URL.revokeObjectURL(href);
		})
	}
</script>

<template>
	<div class="card p-4 md:p-16 mt-4 text-center">
		<h4 class="text-blue font-bold text-4xl">Paiement</h4>

		<font-awesome-icon :icon="['fas', 'circle-check']" shake size="2xl" style="color: #00A906; height: 150px" class="mt-8" />

		<h4 class="text-gray-600 text-2xl mt-8">Votre paiement à été traité avec succès</h4>

		<button class="btn btn-outline-blue" @click="downloadInvoice">Voir la facture </button>

		<div class="mt-8">
			<RouterLink to="/file-status" class="text-primary font-bold text-xl">Consultez l'état d'avancement de vos dossiers</RouterLink>
		</div>
	</div>
</template>

<style scoped lang="scss">

</style>