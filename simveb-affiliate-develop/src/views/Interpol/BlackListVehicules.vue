<template>
	<div class="intro-y mt-4 flex items-center col-span-12">
		<h2 class="mr-auto text-lg font-bold">Liste noire des véhicules</h2>
		<div class="w-full sm:w-auto flex mt-4 sm:mt-0 ml-auto">
			<button
				v-if="can('store-blacklist-vehicle')"
				class="btn btn-outline-danger shadow-md mr-4"
				@click="blacklistModalIsOpen = true"
			>
				Ajouter dans liste noire
			</button>
			<router-link
				v-if="can('browse-blacklist-vehicle')"
				:to="{ name: 'pending-blacklist-vehicles' }"
				class="text-white btn btn-warning shadow-md mr-4"
			>
				En attente de validation
			</router-link>
		</div>
	</div>

	<div class="grid grid-cols-12 gap-6 mt-5 dashboard-card">
		<DataTable
			:headers="headers"
			:items="vehicles"
			:loading="loading"
			:meta="meta"
			empty-text="Aucun véhicule trouvé"
			header-class="uppercase text-start"
			search
			@update-datatable="(newOptions) => (options = newOptions)"
		>
			<template #vin="{ item }">
				<KeyField :value="item.vin" :to="{ name: 'vehicle-view', params: { vin: item.vin } }" />
			</template>
			<template #author="{ item }">
				{{ item.author.identity.fullName }}
			</template>
			<template #validator="{ item }">
				{{ item.validator?.identity.fullName }}
			</template>
			<template #actions="{ item }">
				<router-link
					v-if="can('show-vehicle')"
					v-tooltip
					:to="{ name: 'vehicle-view', params: { vin: item.vin } }"
					class="flex items-center mr-3 btn btn-outline-primary shadow-md"
					title="Voir le véhicule"
				>
					<i class="fa-light fa-eye w-4 h-4" />
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
	import { onMounted, ref } from "vue";
	import { storeToRefs } from "pinia";
	import { useCrudStore } from "@/stores/crud.js";
	import KeyField from "@/components/KeyField.vue";
	import BlackListAddForm from "@/views/Interpol/BlackListAddForm.vue";
	import { userHasPermissions } from "@/helpers/permissions.js";

	const { can } = userHasPermissions();
	const crudStore = useCrudStore();
	const { rows: vehicles, url, loading, meta } = storeToRefs(crudStore);
	const options = ref({
		status: "validated",
	});
	const blacklistModalIsOpen = ref(false);

	const headers = [
		{ key: "vin", title: "Véhicule", sortable: false },
		{ key: "author", title: "Ajouté par" },
		{ key: "validator", title: "Validé par" },
		{ key: "created_at", title: "Date et heure", sortable: false },
	];

	const fetchBlacklistedVehicles = async (metadata) => {
		url.value = "/blacklist-vehicles";
		await crudStore.fetchRows(metadata);
	};

	onMounted(async () => {
		await fetchBlacklistedVehicles(options.value);
	});
</script>

<style scoped></style>
