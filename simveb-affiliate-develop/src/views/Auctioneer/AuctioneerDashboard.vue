<template>
	<div class="intro-y flex flex-col sm:flex-row items-center mt-4">
		<div v-if="can('store-auction-sale-declaration')" class="w-full sm:w-auto flex mt-4 sm:mt-0 ml-auto">
			<router-link class="btn btn-primary shadow-md mr-2" :to="{ name: 'auction-declarations-create' }">
				<i class="fa-light fa-circle-plus w-4 h-4 mr-2"></i>
				Vente aux enchères
			</router-link>
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
</template>

<script setup>
	import { userHasPermissions } from "@/helpers/permissions.js";
	import DashboardStats from "@/views/Stats/DashboardStats.vue";

	const { can } = userHasPermissions();
</script>

<style scoped></style>
