<template>
	<div class="intro-y flex flex-col sm:flex-row items-center mt-4">
		<h2 class="text-lg font-semibold m-auto">Impression de plaques</h2>
		<div v-if="can('print-plate')" class="w-full sm:w-auto flex mt-4 sm:mt-0">
			<router-link class="btn btn-primary shadow-md mr-2" :to="{ name: 'print-orders-create' }">
				<i class="fa-light fa-print w-4 h-4 mr-2"></i>
				Nouveau
			</router-link>
		</div>
	</div>

	<div class="dashboard-card mt-4 !rounded-none">
		<DataTable
			:headers="headers"
			:items="orders"
			:loading="loading"
			:meta="meta"
			:create-button="false"
			empty-text="Aucune demande trouvé"
			header-class="uppercase text-start"
			search
			@update-datatable="(newOptions) => (options = newOptions)"
		>
			<template #reference="{ item }">
				<KeyField :value="item.reference" :to="{ name: 'print-orders-view', params: { id: item.id } }" />
			</template>
			<template #immatriculation="{ item }">
				<KeyField :value="item.immatriculation.number_label" />
			</template>
			<template #status="{ item }">
				<StatusComponent :status="item.status" :status-text="item.status_label" />
			</template>
			<template #images="{ item }">
				<div class="flex flex-wrap gap-2">
					<template v-for="(image, index) in item.images" :key="index">
						<img
							height="50"
							width="50"
							:alt="'Image Impression N°' + index + 1"
							:src="image.url"
							data-action="zoom"
						/>
					</template>
				</div>
			</template>
			<template #actions="{ item }">
				<router-link
					v-if="item.status === 'active' && can('confirm-print-order-affectation')"
					:to="{ name: 'print-orders-confirm', params: { id: item.id } }"
					class="flex items-center mr-3 text-primary"
				>
					<i class="fa-light fa-paintbrush-pencil w-4 h-4 mr-2"></i>
					Imprimer
				</router-link>
				<router-link
					v-if="can('show-print-order')"
					:to="{ name: 'print-orders-view', params: { id: item.id } }"
					class="flex items-center mr-3 text-primary"
				>
					<i class="fa-light fa-up-right-from-square w-4 h-4 mr-2"></i>
					Voir
				</router-link>
			</template>
		</DataTable>
	</div>
</template>

<script setup>
	import { ref, watch } from "vue";
	import DataTable from "@/components/DataTable.vue";
	import StatusComponent from "@/components/StatusComponent.vue";
	import { usePrintOrderStore } from "@/stores/print-order.js";
	import { storeToRefs } from "pinia";
	import KeyField from "@/components/KeyField.vue";
	import { userHasPermissions } from "@/helpers/permissions.js";

	const orderStore = usePrintOrderStore();
	const { orders, loading, meta } = storeToRefs(orderStore);
	const options = ref({});
	const { can } = userHasPermissions();

	const headers = [
		{ key: "reference", title: "Référence", sortable: false },
		{ key: "affected_at", title: "Date d'affectation", sortable: true },
		{ key: "status", title: "Statut", sortable: false },
		{ key: "images", title: "Images", sortable: true },
	];

	const fetchOrders = async (metadata) => {
		await orderStore.fetchOrders(metadata);
	};

	watch(
		options,
		(newOptions) => {
			loading.value = true;
			fetchOrders(newOptions);
		},
		{ deep: true, immediate: true }
	);
</script>

<style scoped lang="scss"></style>
