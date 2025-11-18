<template>
	<router-view name="mobile" />

	<div class="flex mt-[4.7rem] md:mt-0 overflow-hidden theme-default">
		<router-view name="sidebar" />

		<div class="content theme-default">
			<div class="top-bar -mx-4 px-4 md:mx-0 md:px-0 md:align-baseline nav-default">
				<nav aria-label="breadcrumb" class="-intro-x mr-auto hidden sm:flex">
					<ol class="breadcrumb">
						<li class="breadcrumb-item">{{ title.substring(0, title.indexOf(" | ")) }}</li>
					</ol>
				</nav>

				<CartBox />

				<NotificationBox />

				<InvitationBox />

				<AppsBox />

				<UserMenuDropdown />
			</div>

			<router-view />
		</div>
	</div>
</template>

<script setup>
	import NotificationBox from "@/components/NotificationBox.vue";
	import UserMenuDropdown from "@/components/UserMenuDropdown.vue";
	import AppsBox from "@/components/AppsBox.vue";
	import InvitationBox from "@/layouts/InvitationBox.vue";
	import CartBox from "@/components/CartBox.vue";
	import { useTitle } from "@vueuse/core";

	const title = useTitle(null, {
		observe: true,
	});
</script>

<style scoped></style>
