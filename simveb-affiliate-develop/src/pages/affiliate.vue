<template>
	<SideBar>
		<li>
			<MenuLink
				:active="$route.path.startsWith('/affiliate/dashboard')"
				icon="gauge-max"
				label="Tableau de bord"
				to="/affiliate/dashboard"
			/>
		</li>
		<li>
			<MenuLink
				:active="$route.path === '/affiliate/demand'"
				icon="arrow-down-arrow-up"
				label="Demandes"
				to="/affiliate/demand"
			/>
		</li>
		<li>
			<MenuLink
				:active="$route.path === '/affiliate/certificats' || $route.path === '/affiliate/gages'"
				icon="file-certificate"
				label="Certificats"
				to="/affiliate/certificats"
			/>
		</li>
	</SideBar>

	<router-view />
</template>

<script setup>
	import SideBar from "@/layouts/SideBar.vue";
	import MenuLink from "@/components/MenuLink.vue";
</script>

<style scoped></style>
