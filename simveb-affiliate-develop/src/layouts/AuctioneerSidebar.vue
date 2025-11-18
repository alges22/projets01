<template>
	<SideBar>
		<li>
			<MenuLink
				:active="$route.meta.menuRef === 'auctioneer-dashboard'"
				icon="gauge-max"
				label="Tableau de bord"
				:to="{ name: 'auctioneer-dashboard' }"
			/>
		</li>
		<li>
			<MenuLink
				:active="$route.meta.menuRef === 'auction-declaration'"
				icon="car"
				label="Ventes aux enchères"
				:to="{ name: 'auction-declarations' }"
			/>
		</li>
	</SideBar>
</template>

<script setup>
	import SideBar from "@/layouts/SideBar.vue";
	import MenuLink from "@/components/MenuLink.vue";
</script>

<style scoped></style>
