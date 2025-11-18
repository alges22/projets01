<template>
	<div class="intro-y flex flex-col sm:flex-row items-center mt-4">
		<h2 class="text-lg font-semibold m-auto">Commande de plaques</h2>

		<div class="w-full sm:w-auto flex mt-4 sm:mt-0">
			<button
				v-if="can('store-plate-order')"
				class="btn btn-outline-primary shadow-md me-4"
				@click="plateOrderModal = true"
			>
				<i class="fa-light fa-plus w-4 h-4 mr-2"></i>
				Nouveau
			</button>
			<router-link v-if="can('browse-plate')" class="btn btn-primary shadow-md mr-2" :to="{ name: 'plate-list' }">
				<i class="fa-light fa-rectangle-history-circle-plus w-4 h-4 mr-2"></i>
				Inventaire
			</router-link>
		</div>
	</div>

	<div class="dashboard-card mt-4 !rounded-none">
		<DataTable
			:headers="headers"
			:items="plateOrders"
			:loading="loading"
			:meta="meta"
			:create-button="false"
			empty-text="Aucune demande trouvé"
			header-class="uppercase text-start"
			search
			@update-datatable="(newOptions) => (options = newOptions)"
		>
			<template #reference="{ item }">
				<KeyField :value="item.reference" :to="{ name: 'plate-orders-show', params: { id: item.id } }" />
			</template>
			<template #amount="{ item }">
				<span class="font-medium whitespace-nowrap"> {{ item.amount.toLocaleString() }} FCFA </span>
			</template>
			<template #status="{ item }">
				<StatusComponent :status="item.status" :status-text="item.status_label" />
			</template>
			<template #created_at="{ item }">
				<span class="font-medium whitespace-nowrap">
					{{ dayjs(item.created_at).format("DD/MM/YYYY H:m") }}
				</span>
			</template>
			<template #actions="{ item }">
				<router-link
					v-if="can('show-plate-order')"
					:to="{ name: 'plate-orders-show', params: { id: item.id } }"
					class="flex items-center mr-3 text-primary"
				>
					<i class="fa-light fa-up-right-from-square w-4 h-4 mr-4"></i>
					Détails de la commande
				</router-link>
			</template>
		</DataTable>
	</div>

	<PlateOrderForm
		v-if="can('store-plate-order')"
		:open="plateOrderModal"
		@close="plateOrderModal = false"
		@submit="fetchDeclarations({})"
	/>
</template>

<script setup>
	import PlateOrderForm from "@/views/Approved/PlateOrder/PlateOrderForm.vue";
	import { ref, watch } from "vue";
	import client from "@/assets/js/axios/client.js";
	import { formatURLSearchParams } from "@/helpers/utils.js";
	import dayjs from "dayjs";
	import DataTable from "@/components/DataTable.vue";
	import StatusComponent from "@/components/StatusComponent.vue";
	import KeyField from "@/components/KeyField.vue";
	import { userHasPermissions } from "@/helpers/permissions.js";

	const plateOrderModal = ref(false);

	const options = ref({});

	const headers = [
		{ key: "reference", title: "Référence", sortable: false },
		{ key: "amount", title: "Prix", sortable: false },
		{ key: "status", title: "Statut", sortable: false },
		{ key: "created_at", title: "Date et Heure", sortable: true },
	];

	const plateOrders = ref([]);
	const meta = ref({});
	const loading = ref(true);
	const { can } = userHasPermissions();

	const fetchDeclarations = async (metadata) => {
		plateOrderModal.value = false;

		client({
			method: "GET",
			url: "/plate-orders?" + formatURLSearchParams(metadata).toString(),
		})
			.then((response) => response.data)
			.then((response) => {
				plateOrders.value = response.data;

				meta.value = {
					current_page: response.current_page,
					total: response.total,
					per_page: response.per_page,
					from: response.from,
					to: response.to,
					links: response.links,
				};

				loading.value = false;
			});
	};

	watch(
		options,
		(newOptions) => {
			loading.value = true;
			fetchDeclarations(newOptions);
		},
		{ deep: true, immediate: true }
	);
</script>

<style scoped lang="scss"></style>
