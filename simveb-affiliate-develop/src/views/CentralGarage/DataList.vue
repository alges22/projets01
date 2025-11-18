<template>
	<div class="mt-12 flex items-end">
		<button
			:disabled="active === 'vehicles'"
			class="w-1/6 inline-flex justify-center items-center border bg-slate-50 hover:cursor-pointer"
			:class="{
				'border-t-8 border-t-blue-600 h-20': active === 'vehicles',
				'border-t-4 border-t-slate-300 h-16': active !== 'vehicles',
			}"
			@click="active = 'vehicles'"
		>
			Enregistrement
		</button>

		<button
			v-if="can('browse-reform-declaration')"
			:disabled="active === 'reformed'"
			class="w-1/6 inline-flex justify-center items-center border bg-slate-50 hover:cursor-pointer"
			:class="{
				'border-t-8 border-t-green-600 h-20': active === 'reformed',
				'border-t-4 border-t-slate-300 h-16': active !== 'reformed',
			}"
			@click="active = 'reformed'"
		>
			Réformes
		</button>
	</div>
	<div class="dashboard-card !rounded-none">
		<DataTable
			:headers="headers"
			:items="rows"
			:loading="loading"
			:meta="meta"
			:create-button="false"
			:empty-text="emptyText"
			header-class="uppercase text-start"
			search
			@update-datatable="(newOptions) => (options = newOptions)"
		>
			<template #institution="{ item }">{{ item.institution?.name }}</template>
			<template #status="{ item }">
				<StatusComponent :status="item.status" :status-text="item.status_label" />
			</template>
			<template #reference="{ item }">
				<KeyField :value="item.reference" />
			</template>
			<template #declarant="{ item }">
				{{ item.declarant?.full_name }}
			</template>
			<template #actions="{ item }">
				<template v-if="active === 'reformed'">
					<router-link
						v-if="can('show-reform-declaration')"
						:to="{ name: 'reform-declaration', params: { reformId: item.id } }"
						class="flex items-center mr-3 text-primary"
					>
						<i class="fa-light fa-up-right-from-square w-4 h-4 mr-4" /> Détails
					</router-link>
				</template>
			</template>
		</DataTable>
	</div>
</template>

<script setup>
	import { useCrudStore } from "@/stores/crud.js";
	import { storeToRefs } from "pinia";
	import { computed, ref, watch } from "vue";
	import DataTable from "@/components/DataTable.vue";
	import StatusComponent from "@/components/StatusComponent.vue";
	import KeyField from "@/components/KeyField.vue";
	import { userHasPermissions } from "@/helpers/permissions.js";

	const { can } = userHasPermissions();
	const crudStore = useCrudStore();
	const { url, rows, loading, meta } = storeToRefs(crudStore);
	const options = ref({});
	const active = ref("vehicles");

	const headers = computed(() => {
		switch (active.value) {
			default:
				return [
					{ key: "owner_npi", title: "NPI propriétaire", sortable: false, idField: true },
					{ key: "vin", title: "VIN", sortable: false, idField: true },
					{ key: "institution", title: "Institution", sortable: true },
					{ key: "created_at", title: "Date de création", sortable: true },
					{ key: "status", title: "Statut", sortable: false },
				];
			case "reformed":
				return [
					{ key: "reference", title: "Référence", sortable: false },
					{ key: "declarant", title: "Auteur", sortable: false },
					{ key: "created_at", title: "Date de création", sortable: true },
				];
		}
	});

	const emptyText = computed(() => {
		switch (active.value) {
			default:
				return "Aucun véhicule trouvé";
			case "reformed":
				return "Aucune déclaration de réforme trouvée";
		}
	});

	const fetchData = async (metadata) => {
		switch (active.value) {
			case "vehicles":
				url.value = "/gov-vehicles";
				break;
			case "reformed":
				url.value = "/reform-declarations";
				break;
		}
		await crudStore.fetchRows(metadata);
	};

	watch(active, () => {
		fetchData(options.value);
	});

	watch(
		options,
		(newOptions) => {
			fetchData(newOptions);
		},
		{ deep: true, immediate: true }
	);
</script>

<style scoped></style>
