<template>
	<div class="intro-y flex flex-col sm:flex-staff items-center mt-4">
		<div class="w-full sm:w-auto flex mt-4 sm:mt-0 ml-auto">
			<button v-if="can('store-motorcycle')" class="btn btn-primary shadow-md mr-2" @click="modalIsOpen = true">
				Ajouter un véhicule
			</button>
		</div>
	</div>

	<div class="grid grid-cols-12 gap-6 !rounded-none mt-4">
		<DataTable
			:headers="headers"
			:items="vehicles"
			:loading="loading"
			:meta="meta"
			:create-button="false"
			empty-text="Aucun véhicule trouvé"
			header-class="uppercase text-start"
			search
		>
			<template #search>
				<div class="w-full sm:w-auto flex mt-4 sm:mt-0 ml-auto">
					<button class="border-0 mr-2">
						<i class="w-4 h-4 fa-light fa-ellipsis text-2xl"></i>
					</button>
				</div>
			</template>
			<template #npi="{ item }">
				<KeyField :value="item.author.identity.npi" />
			</template>
			<template #vin="{ item }">
				<KeyField :value="item.vin" />
			</template>
			<template #customs_reference="{ item }">
				<KeyField :value="item.customs_reference" />
			</template>
			<template #actions="{ item }">
				<router-link
					v-if="can('show-motorcycle')"
					:to="{ name: 'vehicles-2-3-roue-detail', params: { id: item.id } }"
					class="flex items-center mr-3 text-primary"
				>
					<i class="fa-light fa-up-right-from-square w-4 h-4 mr-4" /> Détails
				</router-link>
			</template>
		</DataTable>
	</div>

	<SuccessModalComponent :open="successModalIsOpen" @close="successModalIsOpen = false" />
	<MotorcycleRegistrationForm v-if="can('store-motorcycle')" :open="modalIsOpen" @close="modalIsOpen = false" />
</template>

<script setup>
	import DataTable from "@/components/DataTable.vue";
	import KeyField from "@/components/KeyField.vue";
	import { ref, watch } from "vue";
	import SuccessModalComponent from "@/components/SuccessModalComponent.vue";
	import MotorcycleRegistrationForm from "./MotorcycleRegistrationForm.vue";
	import { useMotorcycleStore } from "@/stores/motorcycle.js";
	import { storeToRefs } from "pinia";
	import { userHasPermissions } from "@/helpers/permissions.js";

	const options = ref({});
	const successModalIsOpen = ref(false);
	const modalIsOpen = ref(false);

	const motorcycleStore = useMotorcycleStore();
	const { vehicles, meta, loading } = storeToRefs(useMotorcycleStore());
	const { can } = userHasPermissions();

	const headers = [
		{ key: "vin", title: "VIN", sortable: false },
		{ key: "npi", title: "NPI", sortable: false },
		{ key: "customs_reference", title: "Quittance", sortable: false },
	];

	const fetchVehicles = async (metadata) => {
		await motorcycleStore.fetchVehicles(metadata);
	};

	watch(
		options,
		(newOptions) => {
			fetchVehicles(newOptions);
		},
		{ deep: true, immediate: true }
	);
</script>
