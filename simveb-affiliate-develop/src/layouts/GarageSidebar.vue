<template>
	<SideBar>
		<li>
			<MenuLink
				:active="$route.name === 'central-garage-dashboard'"
				icon="gauge-max"
				label="Tableau de bord"
				:to="{ name: 'central-garage-dashboard' }"
			/>
		</li>
		<li>
			<MenuLink
				v-if="can('browse-gov-vehicle') || can('store-reform-declaration')"
				:active="$route.meta.menuRef === 'gov-services'"
				icon="list-ul"
				label="Services"
				:to="{ name: 'gov-services' }"
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
