<template>
	<div class="intro-y flex flex-col sm:flex-row items-center mt-4">
		<div class="w-full sm:w-auto flex mt-4 sm:mt-0 ml-auto">
			<button
				v-if="can('store-blacklist-vehicle')"
				class="btn btn-outline-danger shadow-md mr-4"
				@click="blacklistModalIsOpen = true"
			>
				Ajouter dans liste noire
			</button>
		</div>
	</div>

	<div class="grid grid-cols-12 gap-6">
		<div class="col-span-12">
			<div class="grid grid-cols-12 gap-6">
				<DashboardStats />
			</div>
		</div>
	</div>

	<div class="grid grid-cols-12 gap-6 mt-4">
		<div class="intro-y pt-4 flex items-center col-span-12">
			<h2 class="mr-auto text-lg font-bold">Les demandes</h2>
		</div>
		<DataTable
			:headers="headers"
			:items="demands"
			:loading="loading"
			:meta="meta"
			:create-button="false"
			empty-text="Aucune demande trouvé"
			header-class="uppercase text-start"
			search
			@update-datatable="(newOptions) => (options = newOptions)"
		>
			<template #key="{ item }">
				<span v-if="item.is_blacklisted" v-tooltip class="" title="Le véhicule est en liste noire.">
					<i class="fa-light fa-exclamation-triangle text-danger fa-2xl fa-beat" />
				</span>
			</template>
			<template #reference="{ item }">
				<span class="font-medium whitespace-nowrap">
					{{ item.reference }}
				</span>
				<div class="text-slate-500 text-xs whitespace-nowrap mt-0.5">
					{{ item.service.name }}
				</div>
			</template>
			<template #statut="{ item }">
				<StatusComponent :status="item.status" :status-text="item.status_label">
					<i v-if="item.status === 'in_cart'" class="fa-light fa-arrow-down-from-arc w-4 h-4 mr-2" />
				</StatusComponent>
			</template>
			<template #actions="{ item }">
				<router-link
					v-if="can('show-im-demand')"
					:to="{ name: 'interpol-demand', params: { id: item.id } }"
					class="flex items-center mr-3 text-primary"
				>
					<i class="fa-light fa-up-right-from-square w-4 h-4 mr-4" /> Voir la demande
				</router-link>
			</template>
		</DataTable>
	</div>

	<BlackListAddForm
		v-if="can('store-blacklist-vehicle')"
		:open="blacklistModalIsOpen"
		@close="blacklistModalIsOpen = false"
	/>
</template>

<script setup>
	import DataTable from "@/components/DataTable.vue";
	import StatusComponent from "@/components/StatusComponent.vue";
	import { onBeforeUnmount, ref, watch } from "vue";
	import { storeToRefs } from "pinia";
	import { useCrudStore } from "@/stores/crud.js";
	import BlackListAddForm from "@/views/Interpol/BlackListAddForm.vue";
	import { userHasPermissions } from "@/helpers/permissions.js";
	import DashboardStats from "@/views/Stats/DashboardStats.vue";

	const crudStore = useCrudStore();
	const { rows: demands, meta, loading, url } = storeToRefs(crudStore);
	const options = ref({});
	const blacklistModalIsOpen = ref(false);
	const { can } = userHasPermissions();

	const headers = [
		{ key: "key", title: "#", sortable: false },
		{ key: "reference", title: "Référence", sortable: false },
		{ key: "created_at", title: "Date et Heure", sortable: true },
		{ key: "statut", title: "Statut", sortable: false },
	];

	const fetchDemands = async (metadata) => {
		url.value = "/interpol-demands";
		await crudStore.fetchRows(metadata);
	};

	watch(
		options,
		(newOptions) => {
			fetchDemands(newOptions);
		},
		{ deep: true, immediate: true }
	);

	onBeforeUnmount(() => {
		crudStore.reset();
	});
</script>

<style scoped></style>
