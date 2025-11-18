<template>
	<SideBar>
		<li>
			<MenuLink
				:active="$route.path === '/affiliate/dashboard'"
				icon="gauge-max"
				label="Tableau de bord"
				:to="{ name: 'affiliate-dashboard' }"
			/>
		</li>
		<li>
			<MenuLink
				:active="$route.meta.menuRef === 'demands'"
				icon="arrow-down-arrow-up"
				label="Demandes"
				:to="{ name: 'affiliate-demands' }"
			/>
		</li>
	</SideBar>
</template>

<script setup>
	import SideBar from "@/layouts/SideBar.vue";
	import MenuLink from "@/components/MenuLink.vue";
</script>

<style scoped></style>
