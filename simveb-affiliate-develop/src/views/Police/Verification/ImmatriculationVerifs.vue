<template>
	<template v-if="can('show-vehicle-passage')">
		<div class="intro-y flex items-center mt-4 lg:mt-16">
			<h2 class="text-lg font-semibold mr-auto">Vérifier un véhicule</h2>
		</div>

		<form
			class="flex justify-between items-end dashboard-card !rounded-md mt-4"
			@submit.prevent="$router.push({ name: 'check-vehicle' })"
		>
			<div class="w-full">
				<TextInputGroup
					v-model="immatriculationNumber"
					name="immatriculation_number"
					class="w-full"
					label="Entrer le numéro d'immatriculation"
					add-class="w-full"
					required
				/>
			</div>
			<div class="ms-2 w-1/5">
				<BasicButton class="btn-primary w-full h-auto" type="submit" :disabled="!immatriculationNumber">
					Vérifier
				</BasicButton>
			</div>
		</form>
	</template>

	<template v-if="can('store-vehicle-passage')">
		<div class="intro-y flex items-center mt-4">
			<h2 class="text-lg font-semibold mr-auto">Enregistrer un véhicule</h2>
		</div>

		<div class="grid grid-cols-12 gap-6 mt-5">
			<div class="intro-y col-span-12 md:col-span-6 bg-white rounded-md shadow-md">
				<CallToActionCard
					subtitle="Regroupe les voitures de provenance local"
					title="Voiture local"
					button-text="Enregistrer"
					image-alt=""
					@click="instantiatePassage('local')"
				>
					<div class="w-24 h-24 lg:w-16 lg:h-16 lg:mr-4 bg-[#27ae6033] rounded-full p-2">
						<img alt="" class="" src="../../../assets/images/parc/car_green.png" />
					</div>
				</CallToActionCard>
			</div>

			<div class="intro-y col-span-12 md:col-span-6 bg-white rounded-md shadow-md">
				<CallToActionCard
					subtitle="Regroupe les voitures de provenance étrangères"
					title="Voiture étrangères"
					button-text="Enregistrer"
					image-alt=""
					@click="instantiatePassage('external')"
				>
					<div class="w-24 h-24 lg:w-16 lg:h-16 lg:mr-4 bg-[#27ae6033] rounded-full p-2">
						<img alt="" class="" src="../../../assets/images/parc/car_red.png" />
					</div>
				</CallToActionCard>
			</div>
		</div>
	</template>

	<div class="pt-8 pb-5 my-0 border-t mx-5 border-slate-200/60 dark:border-darkmode-400">
		<h2 class="text-lg leading-7 font-bold mb-8">Passages récents</h2>
		<DataTable
			:has-header="false"
			:headers="headers"
			:items="passages"
			empty-text="Aucun passage trouvé"
			header-class="uppercase text-start"
			search
		>
			<template #driver="{ item }">
				{{ `${item.driver_lastname} ${item.driver_firstname}` }}
			</template>
			<template #owner_name="{ item }">
				{{ `${item.vehicle_owner_lastname} ${item.vehicle_owner_firstname}` }}
			</template>
			<template #actions="{ item }">
				<router-link
					v-if="can('show-vehicle-passage')"
					:to="{ name: 'vehicle-detail', params: { id: item?.id } }"
				>
					Voir détails
				</router-link>
			</template>
		</DataTable>
	</div>
</template>

<script setup>
	import { onMounted } from "vue";
	import { useRouter } from "vue-router";
	import { useVehiclePassageStore } from "@/stores/passage.js";
	import { storeToRefs } from "pinia";
	import DataTable from "@/components/DataTable.vue";
	import TextInputGroup from "@/components/Form/TextInputGroup.vue";
	import BasicButton from "@/components/BasicButton.vue";
	import CallToActionCard from "@/components/CallToActionCard.vue";
	import { userHasPermissions } from "@/helpers/permissions.js";

	const { can } = userHasPermissions();
	const vehiclePassageStore = useVehiclePassageStore();
	const { passages, immatriculationNumber, passage } = storeToRefs(vehiclePassageStore);
	const router = useRouter();

	const headers = [
		{ key: "immatriculation_number", title: "Immatriculation", sortable: false },
		{ key: "vehicle_provenance_label", title: "Véhicule", sortable: false },
		{ key: "owner_name", title: "Propriétaire", sortable: false },
		{ key: "driver", title: "Conducteur", sortable: false },
		{ key: "created_date", title: "Date et heure", sortable: false },
		{ key: "passage_type_label", title: "Passage", sortable: false },
	];

	const instantiatePassage = (type) => {
		passage.value.vehicle_provenance = type;
		router.push({ name: "register-vehicle" });
	};

	onMounted(() => {
		vehiclePassageStore.fetchPassages({});
	});
</script>
