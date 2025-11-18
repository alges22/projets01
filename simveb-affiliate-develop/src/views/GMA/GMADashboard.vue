<template>
	<div v-if="can('store-gma-vehicle')" class="intro-y flex flex-col sm:flex-row items-center mt-4">
		<div class="w-full sm:w-auto flex mt-4 sm:mt-0 ml-auto">
			<button class="btn btn-primary mr-2" @click="importFormModal = true">Enregistrer un véhicule</button>
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
			<h2 class="mr-auto text-lg font-bold">Activité Récentes</h2>
		</div>
	</div>

	<VehicleRegistrationForm
		v-if="can('store-gma-vehicle')"
		url="gma-vehicles"
		:open="importFormModal"
		@close="importFormModal = false"
		@submit="importFormModal = false"
	/>
</template>

<script setup>
	import { ref } from "vue";
	import VehicleRegistrationForm from "@/views/GMA/VehicleRegistrationForm.vue";
	import { userHasPermissions } from "@/helpers/permissions.js";
	import DashboardStats from "@/views/Stats/DashboardStats.vue";

	const { can } = userHasPermissions();

	const importFormModal = ref(false);
</script>

<style scoped></style>
