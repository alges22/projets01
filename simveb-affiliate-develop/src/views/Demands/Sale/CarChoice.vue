<template>
	<div class="intro-y flex items-center justify-center mt-4">
		<h2 class="text-lg font-semibold">Veuillez sélectionner le véhicule que vous souhaitez transformer</h2>
	</div>

	<LoaderSpinner v-if="loading" />

	<form v-else @submit.prevent="goNext">
		<template v-if="vehicles.length === 0">
			<div class="intro-y flex items-center justify-center mt-8">
				<h2 class="text-lg font-semibold">Vous n'avez pas de véhicule enregistré</h2>
			</div>
		</template>

		<template v-else>
			<label
				v-for="(vehicle, index) in vehicles"
				:key="index"
				class="flex items-center justify-center dashboard-card mb-4 mt-4 cursor-pointer"
				:for="`car-${index}`"
			>
				<span class="w-1/4 p-3.5">
					<img
						class="w-24 h-24 object-cover rounded-xl"
						src="@/assets/images/parc/car_red_2.png"
						alt="vehicle"
					/>
				</span>
				<span class="w-1/4 p-3.5">
					<span class="text-lg">{{ vehicle.vin }}</span>
				</span>
				<span class="w-1/4 p-3.5 text-center">
					<span class="text-lg p-4 bg-[#F3F5F8] rounded-xl font-semibold">{{ vehicle.immatriculation }}</span>
				</span>
				<span class="form-check ml-auto mr-4">
					<input
						:id="`car-${index}`"
						v-model="demand.vin"
						:value="vehicle.vin"
						class="form-check-input"
						name="car-choice"
						:required="index === 0"
						type="radio"
					/>
				</span>
			</label>
		</template>

		<div class="intro-y col-span-12">
			<div class="text-right mt-4">
				<button class="btn btn-outline-primary w-36 mr-4 border-2" type="button" @click="$emit('prev')">
					Annuler
				</button>
				<button class="btn btn-primary w-36" type="submit" :disabled="!demand.vin">Suivant</button>
			</div>
		</div>
	</form>
</template>

<script setup>
	import { useDemandStore } from "@/stores/demand.js";
	import { storeToRefs } from "pinia";
	import { onMounted, ref } from "vue";
	import LoaderSpinner from "@/components/LoaderSpinner.vue";
	import Alert from "@/components/notification/alert.js";

	const emit = defineEmits(["next", "prev"]);

	const demandStore = useDemandStore();
	const { demand, loading } = storeToRefs(demandStore);
	const vehicles = ref([]);

	const goNext = () => {
		if (!demand.value.vin) {
			Alert.error("Vous devez choisir un véhicule");
		}
		emit("next");
	};

	onMounted(async () => {
		await demandStore.fetchVehicles(demand.value.npi).then((response) => {
			vehicles.value = response;
		});
	});
</script>

<style scoped></style>
