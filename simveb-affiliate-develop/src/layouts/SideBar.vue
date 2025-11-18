<template>
	<nav class="side-nav bg-white">
		<template v-if="theme === 'default'">
			<a href="" class="intro-x flex items-center pl-5 pt-4 mt-3 w-1/2 lg:w-full">
				<img alt="ASIN Logo" src="@/assets/images/logo.png" />
			</a>
		</template>

		<div class="side-nav__devider my-6 bg-white"></div>
		<ul class="flex flex-col gap-3">
			<slot></slot>

			<li v-if="can('browse-space-staff')">
				<MenuLink
					:active="$route.path === '/staff'"
					icon="users"
					label="Gestion du personnel"
					:to="{ name: 'staff' }"
				/>
			</li>
		</ul>
	</nav>
</template>

<script setup>
	import MenuLink from "@/components/MenuLink.vue";
	import { useAuthStore } from "@/stores/auth.js";
	import { storeToRefs } from "pinia";
	import { userHasPermissions } from "@/helpers/permissions.js";

	const authStore = useAuthStore();
	const { theme } = storeToRefs(authStore);
	const { can } = userHasPermissions();
</script>

<style scoped></style>
