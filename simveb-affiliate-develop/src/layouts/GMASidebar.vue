<template>
	<SideBar>
		<li>
			<MenuLink
				:active="$route.path === '/gma/dashboard'"
				icon="gauge-max"
				label="Tableau de bord"
				:to="{ name: 'gma-dashboard' }"
			/>
		</li>
		<li v-if="can('browse-gma-vehicle')">
			<MenuLink
				:active="$route.meta.menuRef === 'gma-services'"
				icon="list-ul"
				label="Services"
				:to="{ name: 'gma-services' }"
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
