<template>
	<SideBar>
		<li>
			<MenuLink
				:active="$route.path === '/judge/dashboard'"
				icon="gauge-max"
				label="Tableau de bord"
				:to="{ name: 'court-dashboard' }"
			/>
		</li>
		<li v-if="hasOnePermissions(['browse-pledge'])">
			<MenuLink
				:active="$route.meta.menuRef === 'court-pledge'"
				icon="hand-pointer"
				label="Gages"
				:to="{ name: 'court-pledges' }"
			/>
		</li>
		<li v-if="hasOnePermissions(['browse-pledge-lift'])">
			<MenuLink
				:active="$route.meta.menuRef === 'pledge-lifts'"
				icon="hand-pointer"
				label="Levées de gage"
				:to="{ name: 'pledge-lifts' }"
			/>
		</li>
		<li v-if="hasOnePermissions(['browse-opposition'])">
			<MenuLink
				:active="$route.meta.menuRef === 'opposition'"
				icon="arrows-cross"
				label="Oppositions"
				:to="{ name: 'oppositions' }"
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
