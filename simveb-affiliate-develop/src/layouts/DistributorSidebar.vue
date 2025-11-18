<template>
	<SideBar>
		<li>
			<MenuLink
				:active="$route.path === '/distributor-dashboard'"
				icon="gauge-max"
				label="Tableau de bord"
				:to="{ name: 'distributor-dashboard' }"
			/>
			<!--			TODO : Check-->
			<!--			<MenuLink-->
			<!--				:active="$route.meta.menuRef === 'motorcycles'"-->
			<!--				icon="motorcycle"-->
			<!--				label="Gestion des véhicles à 2 ou 3 roues"-->
			<!--				:to="{ name: 'vehicles-2-3-roue-list' }"-->
			<!--			/>-->
		</li>

		<li v-if="hasOnePermissions(['browse-pledge'])">
			<MenuLink
				:active="$route.meta.menuRef === 'court-pledge'"
				icon="hand-pointer"
				label="Gages"
				:to="{ name: 'court-pledges' }"
			/>
		</li>
	</SideBar>
</template>

<script setup>
	import SideBar from "@/layouts/SideBar.vue";
	import MenuLink from "@/components/MenuLink.vue";
	import { userHasPermissions } from "@/helpers/permissions.js";

	const { hasOnePermissions } = userHasPermissions();
</script>

<style scoped></style>
