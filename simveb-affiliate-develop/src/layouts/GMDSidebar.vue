<template>
	<SideBar>
		<li>
			<MenuLink
				:active="$route.path === '/gmd/dashboard'"
				icon="gauge-max"
				label="Tableau de bord"
				:to="{ name: 'gmd-dashboard' }"
			/>
		</li>
		<li v-if="can('browse-gmd-vehicle')">
			<MenuLink
				:active="$route.meta.menuRef === 'gmd-services'"
				icon="list-ul"
				label="Services"
				:to="{ name: 'gmd-services' }"
			/>
		</li>
	</SideBar>
</template>

<script setup>
	import SideBar from "@/layouts/SideBar.vue";
	import MenuLink from "@/components/MenuLink.vue";
	import { userHasPermissions } from "@/helpers/permissions.js";

	const { can } = userHasPermissions();
</script>

<style scoped></style>
