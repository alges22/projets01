<template>
	<router-view name="mobile" />
	<!--	<DarkmodeSwitcher />-->
	<NavBar class="nav-vivid" />

	<div class="flex mt-[4.7rem] md:mt-0 overflow-hidden theme-vivid">
		<router-view name="sidebar" />

		<div class="content theme-vivid">
			<router-view />
		</div>
	</div>
</template>

<script setup>
	import NavBar from "@/layouts/NavBar.vue";
</script>

<style scoped></style>
