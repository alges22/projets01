<template>
	<div class="top-bar -mx-4 px-4 md:mx-0 md:px-0 md:align-baseline bg-primary-dark text-white">
		<a href="" class="intro-x flex items-center p-2 w-64 ms-4">
			<img alt="ASIN Logo" src="@/assets/images/logo_white.png" />
		</a>

		<div class="flex items-center ml-auto">
			<CartBox />

			<NotificationBox />

			<InvitationBox />

			<AppsBox />

			<UserMenuDropdown />
		</div>
	</div>
</template>
<script setup>
	// import { useTitle } from "@vueuse/core";
	import CartBox from "@/components/CartBox.vue";
	import NotificationBox from "@/components/NotificationBox.vue";
	import InvitationBox from "@/layouts/InvitationBox.vue";
	import AppsBox from "@/components/AppsBox.vue";
	import UserMenuDropdown from "@/components/UserMenuDropdown.vue";

	/*const title = useTitle(null, {
		observe: true,
	});*/
</script>
