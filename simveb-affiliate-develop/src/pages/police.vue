<template>
	<SideBar>
		<li>
			<MenuLink
				:active="$route.path.startsWith('/police/dashboard')"
				icon="gauge-max"
				label="Tableau de bord"
				to="/police/dashboard"
			/>
		</li>
		<li>
			<MenuLink
				:active="$route.path.startsWith('/police/verification')"
				icon="car"
				label="Vérification"
				to="/police/verification"
			/>
		</li>
		<li>
			<MenuLink
				:active="$route.path.startsWith('/police/alert')"
				icon="hexagon-exclamation"
				label="Alertes"
				to="/police/alert"
			/>
		</li>
		<li>
			<MenuLink :active="$route.path === '/parc/staff'" icon="ban" label="Liste noire" to="/parc/staff" />
		</li>
	</SideBar>

	<router-view />
</template>

<script setup>
	import SideBar from "@/layouts/SideBar.vue";
	import MenuLink from "@/components/MenuLink.vue";
</script>

<style scoped></style>
