<template>
	<SideBar>
		<li>
			<MenuLink
				:active="$route.meta.menuRef === 'approved-dashboard'"
				icon="gauge-max"
				label="Tableau de bord"
				:to="{ name: 'approved-dashboard' }"
			/>
		</li>
		<li v-if="hasOnePermissions(['browse-plate-order', 'show-plate-order', 'store-plate-order'])">
			<MenuLink
				:active="$route.meta.menuRef === 'plate-orders'"
				icon="list-timeline"
				label="Commandes de plaques"
				:to="{ name: 'plate-orders' }"
			/>
		</li>
		<li v-if="userHasPermissions(['browse-print-order', 'show-print-order'])">
			<MenuLink
				:active="$route.meta.menuRef === 'print-orders'"
				icon="paint-roller"
				label="Impressions de plaques"
				:to="{ name: 'print-orders' }"
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
