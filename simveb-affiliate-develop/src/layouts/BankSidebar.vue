<template>
	<SideBar>
		<li v-if="hasOnePermissions(['browse-pledge'])">
			<MenuLink
				:active="$route.path === 'court-pledges'"
				:to="{ name: 'court-pledges' }"
				icon="gauge-max"
				label="Tableau de bord"
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
	</SideBar>
</template>

<script setup>
	import SideBar from "@/layouts/SideBar.vue";
	import MenuLink from "@/components/MenuLink.vue";
	import { userHasPermissions } from "@/helpers/permissions.js";

	const { hasOnePermissions } = userHasPermissions();
</script>

<style scoped></style>
