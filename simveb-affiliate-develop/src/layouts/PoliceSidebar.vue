<template>
	<SideBar>
		<li>
			<MenuLink
				:active="$route.path.startsWith('/police-dashboard')"
				icon="gauge-max"
				label="Tableau de bord"
				:to="{ name: 'police-dashboard' }"
			/>
		</li>
		<li v-if="can('browse-vehicle-passage')">
			<MenuLink
				:active="$route.meta.menuRef === 'check-vehicle'"
				icon="car"
				label="Vérification"
				:to="{ name: 'immatriculation-verifs' }"
			/>
		</li>
		<li v-if="can('browse-vehicle-alert')">
			<MenuLink
				:active="$route.meta.menuRef === 'alert'"
				icon="hexagon-exclamation"
				label="Alertes"
				:to="{ name: 'alert-dashboard' }"
			/>
		</li>

		<li v-if="can('browse-blacklist-vehicle')">
			<MenuLink
				:active="$route.name === 'blacklist'"
				icon="ban"
				label="Liste noire"
				:to="{ name: 'blacklist' }"
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
