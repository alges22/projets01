<template>
	<div class="intro-y flex items-center mt-4">
		<h2 class="text-lg font-semibold mr-auto">Sélectionner un type de service</h2>
	</div>

	<div class="grid grid-cols-12 gap-6 mt-5">
		<div v-if="can('store-gov-vehicle')" class="col-span-12 md:col-span-6 bg-white rounded-md shadow-md">
			<CallToActionCard
				button-text="Commencer"
				title="Enregistrer un véhicule"
				subtitle="Petite description"
				@click="openImportModal(false)"
			>
				<div class="w-24 h-24 lg:w-16 lg:h-16 lg:mr-4 bg-lime-400 rounded-full p-2 flex-center text-white">
					<i class="fa-light fa-car text-4xl"></i>
				</div>
			</CallToActionCard>
		</div>
		<div v-if="can('store-gov-vehicle')" class="col-span-12 md:col-span-6 bg-white rounded-md shadow-md">
			<CallToActionCard
				button-text="Commencer"
				title="Enregistrer plusieurs véhicules"
				subtitle="Petite description"
				@click="openImportModal(true)"
			>
				<div class="w-24 h-24 lg:w-16 lg:h-16 lg:mr-4 bg-lime-400 rounded-full p-2 flex-center text-white">
					<i class="fa-light fa-cars text-4xl"></i>
				</div>
			</CallToActionCard>
		</div>
		<div class="col-span-12 md:col-span-6 bg-white rounded-md shadow-md">
			<CallToActionCard
				button-text="Commencer"
				title="Changement du type d’immatriculation"
				subtitle="Petite description"
				@click=""
			>
				<div class="w-24 h-24 lg:w-16 lg:h-16 lg:mr-4 bg-lime-400 rounded-full p-2 flex-center text-white">
					<i class="fa-light fa-rotate text-4xl"></i>
				</div>
			</CallToActionCard>
		</div>
		<div class="col-span-12 md:col-span-6 bg-white rounded-md shadow-md">
			<CallToActionCard
				button-text="Commencer"
				title="Retrait de l’immatriculation spéciale"
				subtitle="Petite description"
				@click=""
			>
				<div class="w-24 h-24 lg:w-16 lg:h-16 lg:mr-4 bg-lime-400 rounded-full p-2 flex-center text-white">
					<i class="fa-light fa-car text-4xl"></i>
				</div>
			</CallToActionCard>
		</div>
		<div v-if="can('store-reform-declaration')" class="col-span-12 md:col-span-6 bg-white rounded-md shadow-md">
			<CallToActionCard
				button-text="Commencer"
				title="Déclaration de réforme"
				subtitle="Petite description"
				@click="$router.push({ name: 'reform-declaration-create' })"
			>
				<div class="w-24 h-24 lg:w-16 lg:h-16 lg:mr-4 bg-lime-400 rounded-full p-2 flex-center text-white">
					<i class="fa-light fa-reload text-4xl"></i>
				</div>
			</CallToActionCard>
		</div>
	</div>

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

	<VehicleImportForm
		v-if="can('store-gov-vehicle')"
		:open="importFormModal"
		:multiple="importFormMultiple"
		@close="importFormModal = false"
		@submit="
			() => {
				importFormModal = false;
				fetchData(options.value);
			}
		"
	/>
</template>

<script setup>
	import CallToActionCard from "@/components/CallToActionCard.vue";
	import VehicleImportForm from "@/views/CentralGarage/VehicleImportForm.vue";
	import { computed, ref, watch } from "vue";
	import { userHasPermissions } from "@/helpers/permissions.js";
	import KeyField from "@/components/KeyField.vue";
	import DataTable from "@/components/DataTable.vue";
	import StatusComponent from "@/components/StatusComponent.vue";
	import { useCrudStore } from "@/stores/crud.js";
	import { storeToRefs } from "pinia";

	const { can } = userHasPermissions();
	const crudStore = useCrudStore();
	const { url, rows, loading, meta } = storeToRefs(crudStore);
	const options = ref({});
	const active = ref("vehicles");

	const importFormModal = ref(false);
	const importFormMultiple = ref(false);

	const openImportModal = (isMultiple) => {
		importFormMultiple.value = isMultiple;
		importFormModal.value = true;
	};

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
