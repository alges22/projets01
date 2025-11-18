<template>
	<LoaderSpinner v-if="loading || !vehicleInfo" type="block" class="mt-4" />

	<template v-else>
		<template v-if="vehicleInfo.title_deposits.length > 0">
			<div class="intro-y flex items-center mt-4">
				<h2 class="text-lg text-primary mr-auto">Ce véhicule a été soumis à un dépôt de titre</h2>
			</div>
		</template>
		<template v-else>
			<div class="intro-y flex items-center mt-4">
				<h2 class="text-lg text-primary mr-auto">Ce véhicule n'a pas été soumis à un dépôt de titre</h2>
			</div>
		</template>
		<div class="grid grid-cols-12 gap-6 mt-8 bg-white p-2 xl:p-4 rounded-md">
			<template v-for="(deposit, index) in vehicleInfo.title_deposits" :key="index">
				<div class="col-span-12 py-3 px-8 bg-[#E8F1FD] rounded-sm">
					<div class="intro-y flex items-center">
						<h2 class="text-primary text-2xl">Détails du dépôt de titre</h2>
					</div>
				</div>
				<div class="col-span-12 grid grid-cols-12 gap-6">
					<div class="col-span-12 lg:col-span-6 mx-2 lg:mx-9 intro-y">
						<div class="flex items-center pb-2 mt-2 border-b">
							<p class="w-1/2 text-lg leading-5">Date</p>
							<p class="font-bold text-dark w-1/2 text-lg leading-5">{{ deposit.date }}</p>
						</div>
						<div class="flex items-center pb-2 mt-6 border-b">
							<p class="w-1/2 text-lg leading-5">Motif du dépôt de titre</p>
							<p class="font-bold text-dark w-1/2 text-lg leading-5">{{ deposit.reason }}</p>
						</div>
					</div>
				</div>
			</template>
			<VehicleInfoCard :vehicle-info="vehicleInfo" :loading="loading" />
		</div>

		<div class="intro-y col-span-12">
			<div class="flex align-center justify-end mt-4">
				<button class="btn btn-outline-primary w-36 mr-4 border-2" type="button" @click="$emit('prev')">
					Annuler
				</button>
				<button
					v-if="vehicleInfo.title_deposits.length > 0"
					class="btn btn-primary w-36"
					type="button"
					@click="$emit('next')"
				>
					Suivant
				</button>
			</div>
		</div>
	</template>

	<Modal :show="modalIsOpen" backdrop="static" is-form @hidden="modalIsOpen = false" @submit="submit">
		<ModalBody>
			<div class="flex flex-col justify-between mx-4">
				<div class="mt-4">
					<TextInputGroup
						v-model="demand.vin"
						label="Entrer le VIN du véhicule"
						name="vin"
						:disabled="loading"
						required
						:errors="errors.vin || []"
					/>
				</div>
			</div>
		</ModalBody>
		<ModalFooter class="">
			<div class="flex align-center justify-end mb-2">
				<BasicButton class="btn-primary w-full" type="submit" :loading="loading"> Suivant </BasicButton>
			</div>
		</ModalFooter>
	</Modal>
</template>

<script setup>
	import { useDemandStore } from "@/stores/demand.js";
	import { storeToRefs } from "pinia";
	import VehicleInfoCard from "@/components/VehicleInfoCard.vue";
	import { Modal, ModalBody, ModalFooter } from "@/global-components/modal/index.js";
	import BasicButton from "@/components/BasicButton.vue";
	import TextInputGroup from "@/components/Form/TextInputGroup.vue";
	import LoaderSpinner from "@/components/LoaderSpinner.vue";
	import { onMounted, ref } from "vue";

	defineEmits(["next", "prev"]);

	const demandStore = useDemandStore();
	const { vehicle_info: vehicleInfo, loading, demand, errors } = storeToRefs(demandStore);

	const modalIsOpen = ref(false);

	const submit = () => {
		demandStore.fetchVehicleInfo(demand.value.vin).then((res) => {
			if (res.title_deposits.length > 0) {
				demand.value.deposit_id = res.title_deposits[0].id;
			}
			modalIsOpen.value = false;
		});
	};

	onMounted(() => {
		if (!vehicleInfo.value) {
			modalIsOpen.value = true;
		}
	});
</script>

<style scoped></style>
