<script setup>
	import client from "@/assets/js/axios/client.js";
	import { onMounted, ref } from "vue";
	import { useRoute } from "vue-router";
	import DataTable from "@/components/DataTable.vue";
	import dayjs from "dayjs";
	import statuses from "@/data/statuses.js";
	import Swal from "sweetalert2";
	import BasicButton from "@/components/BasicButton.vue";
	import Alert from "@/components/notification/alert.js";

	const headers = [
		{ key: "shape", title: "Forme", sortable: false },
		{ key: "color", title: "Couleur", sortable: true },
		{ key: "nb", title: "Nombre", sortable: false },
		{ key: "unity_amount", title: "Prix unitaire", sortable: false },
		{ key: "total_amount", title: "Prix total", sortable: false },
	];

	const route = useRoute();
	const id = route.params.id;

	const order = ref(null);
	const loading = ref(false);

	const formDataHeaders = {
		...client.defaults.headers,
		"Content-Type": "multipart/form-data",
	};

	const payOrder = () => {
		Swal.fire({
			title: "Confirmation",
			text: `Êtes vous sûr(e) de vouloir payer cette facture? ${order.value.amount} sera débité de votre portefeuille!`,
			icon: "warning",
			confirmButtonText: "Oui, Payer",
			showCancelButton: true,
			cancelButtonText: "Annuler",
		}).then((value) => {
			if (value.isConfirmed) {
				loading.value = true;

				client({
					method: "POST",
					url: "/plate-orders/pay/",
					data: {
						plate_order_id: id,
					},
				})
					.then((response) => response.data)
					.then((response) => {
						Alert.success("Paiement effectué avec succès");

						loadOrder();
					})
					.finally(() => {
						loading.value = false;
					});
			}
		});
	};

	const validateOrder = () => {
		Swal.fire({
			title: "Bordereau de livraison",
			html: '<input type="file" id="fileInput" class="form-control shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">',
			showCancelButton: true,
			confirmButtonText: "Soumettre",
			preConfirm: () => {
				const file = Swal.getPopup().querySelector("#fileInput").files[0];
				if (!file) {
					Swal.showValidationMessage("Veuillez sélectionner un fichier");
					return false;
				}
				return file;
			},
		}).then((result) => {
			if (result.isConfirmed) {
				Swal.showLoading();

				const file = result.value;

				client({
					method: "POST",
					url: `/plate-orders/validate`,
					data: {
						plate_order_id: id,
						file: file,
					},
					headers: formDataHeaders,
				}).then(() => {
					Alert.success("Validation effectuée effectué avec succès");
					Swal.clickCancel();

					loadOrder();
				});
			}
		});
	};

	onMounted(() => {
		loadOrder();
	});

	const loadOrder = () => {
		client({
			method: "GET",
			url: `/plate-orders/${id}`,
		})
			.then((response) => response.data)
			.then((response) => {
				order.value = response;
			});
	};
</script>

<template>
	<div v-if="order">
		<div class="bg-white mt-2 p-2 xl:p-4 rounded-md">
			<div class="bg-blue-100 p-4">
				<span class="font-bold text-lg text-blue-500 rounded-md">Informations de la commande</span>
			</div>
			<table class="table mt-5">
				<tr>
					<th>Référence</th>
					<th>{{ order.reference }}</th>
				</tr>
				<tr>
					<td>Date de soumission</td>
					<th>
						{{ dayjs(order.created_at).format("DD/MM/YYYY H:m") }}
					</th>
				</tr>
				<tr>
					<td>Statut</td>
					<th>
						{{ order.status_label }}
					</th>
				</tr>
			</table>
		</div>

		<div class="bg-white mt-2 p-2 xl:p-4 rounded-md">
			<div class="bg-blue-100 p-4 mt-8">
				<span class="font-bold text-lg text-blue-500 rounded-md">Plaques commandées</span>
			</div>

			<DataTable
				:has-actions="false"
				:create-button="false"
				:headers="headers"
				:items="order.order_details"
				:loading="loading"
				empty-text="Aucune donnée trouvé"
				header-class="uppercase text-start"
			>
				<template #unity_amount="{ item }">
					{{ item.unity_amount.toLocaleString() }}
				</template>
				<template #total_amount="{ item }">
					{{ item.total_amount.toLocaleString() }}
				</template>
			</DataTable>
		</div>

		<div class="mt-2 p-2 xl:p-4 flex">
			<BasicButton
				v-if="order.status === statuses.EN_ATTENTE_DE_PAIEMENT"
				:loading="loading"
				class="btn btn-success text-white ms-auto"
				@click="payOrder"
			>
				Payer la facture
			</BasicButton>
			<BasicButton
				v-if="order.status === statuses.PAYE"
				:loading="loading"
				class="btn btn-success text-white ms-auto"
				@click="validateOrder"
				>Valider la réception des plaques
			</BasicButton>
		</div>
	</div>
</template>

<style lang="scss" scoped></style>
