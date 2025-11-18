<template>
	<SideBar>
		<li>
			<MenuLink
				:active="$route.meta.menuRef === 'interpol-dashboard'"
				icon="gauge-max"
				label="Tableau de bord"
				:to="{ name: 'interpol-dashboard' }"
			/>
		</li>

		<li v-if="can('browse-blacklist-vehicle')">
			<MenuLink
				:active="$route.meta.menuRef === 'blacklist'"
				icon="ban"
				label="Liste noire"
				:to="{ name: 'blacklist-vehicles' }"
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
