<template>
	<div class="intro-y flex items-center mt-4">
		<h2 class="text-lg font-semibold mr-auto">Sélectionner un type de service</h2>
	</div>

	<div class="grid grid-cols-12 gap-6 mt-5">
		<div v-if="can('store-gma-vehicle')" class="col-span-12 md:col-span-6 bg-white rounded-md shadow-md">
			<CallToActionCard
				button-text="Commencer"
				title="Enregistrer un véhicule"
				subtitle="Petite description"
				@click="openImportModal(false)"
			>
				<div class="w-24 h-24 lg:w-16 lg:h-16 lg:mr-4 bg-marron rounded-full p-2 flex-center text-white">
					<i class="fa-light fa-car text-4xl"></i>
				</div>
			</CallToActionCard>
		</div>
		<div v-if="can('import-gma-vehicle')" class="col-span-12 md:col-span-6 bg-white rounded-md shadow-md">
			<CallToActionCard
				button-text="Commencer"
				title="Enregistrer plusieurs véhicules"
				subtitle="Petite description"
				@click="openImportModal(true)"
			>
				<div class="w-24 h-24 lg:w-16 lg:h-16 lg:mr-4 bg-marron rounded-full p-2 flex-center text-white">
					<i class="fa-light fa-cars text-4xl"></i>
				</div>
			</CallToActionCard>
		</div>
	</div>

	<div class="dashboard-card !rounded-none mt-12">
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
			<template #author="{ item }">{{ item.author?.name }}</template>
			<template #status="{ item }">
				<StatusComponent :status="item.status" :status-text="item.status_label" />
			</template>
			<template #vin="{ item }">
				<KeyField :value="item.vin" />
			</template>
			<template #actions="{ item }">
				<router-link
					v-if="can('show-gma-vehicle')"
					:to="{ name: 'gma-vehicle', params: { id: item.id } }"
					class="flex items-center mr-3 text-primary"
				>
					<i class="fa-light fa-up-right-from-square w-4 h-4 mr-4" /> Détails
				</router-link>
			</template>
		</DataTable>
	</div>

	<VehicleRegistrationForm
		v-if="can('import-gma-vehicle') || can('store-gma-vehicle')"
		url="gma-vehicles"
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
	import { computed, ref, watch } from "vue";
	import StatusComponent from "@/components/StatusComponent.vue";
	import DataTable from "@/components/DataTable.vue";
	import { useCrudStore } from "@/stores/crud.js";
	import { storeToRefs } from "pinia";
	import VehicleRegistrationForm from "@/views/GMA/VehicleRegistrationForm.vue";
	import { userHasPermissions } from "@/helpers/permissions.js";
	import KeyField from "@/components/KeyField.vue";

	const { can } = userHasPermissions();
	const crudStore = useCrudStore();
	const { url, rows, loading, meta } = storeToRefs(crudStore);
	const options = ref({});
	const active = ref("vehicles");

	const importFormModal = ref(false);
	const importFormMultiple = ref(false);

	const headers = computed(() => {
		switch (active.value) {
			default:
				return [
					{ key: "institution", title: "Institution", sortable: true },
					{ key: "vin", title: "VIN", sortable: false },
					{ key: "Auteur", title: "Auteur", sortable: false },
					{ key: "created_at", title: "Date de création", sortable: true },
					{ key: "status", title: "Statut", sortable: false },
				];
		}
	});

	const emptyText = computed(() => {
		switch (active.value) {
			default:
				return "Aucun véhicule trouvé";
		}
	});

	const openImportModal = (isMultiple) => {
		importFormMultiple.value = isMultiple;
		importFormModal.value = true;
	};
	const fetchData = async (metadata) => {
		switch (active.value) {
			case "vehicles":
				url.value = "/gma-vehicles";
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
