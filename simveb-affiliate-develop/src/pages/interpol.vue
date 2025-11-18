<template>
	<SideBar>
		<li>
			<MenuLink :active="$route.path === '/interpol'" icon="gauge-max" label="Tableau de bord" to="/interpol" />
		</li>
		<li>
			<MenuLink
				:active="$route.path.startsWith('/interpol/application')"
				icon="suitcase"
				label="Dossiers"
				to="interpol/application"
			/>
		</li>
	</SideBar>

	<router-view />
</template>

<script setup>
	import SideBar from "@/layouts/SideBar.vue";
	import MenuLink from "@/components/MenuLink.vue";
</script>

<style scoped></style>
