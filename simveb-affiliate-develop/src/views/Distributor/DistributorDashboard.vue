<template>
	<div class="intro-y flex flex-col sm:flex-row items-center mt-4">
		<div class="w-full sm:w-auto flex mt-4 sm:mt-0 ml-auto"></div>
	</div>

	<div class="grid grid-cols-12 gap-6">
		<div class="col-span-12">
			<div class="grid grid-cols-12 gap-6">
				<DashboardStats />
			</div>
		</div>
	</div>
</template>

<script setup>
	import DashboardStats from "@/views/Stats/DashboardStats.vue";
</script>

<style scoped></style>
