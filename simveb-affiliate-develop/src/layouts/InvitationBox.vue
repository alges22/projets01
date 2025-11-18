<template>
	<button
		v-tooltip
		class="mr-auto sm:mr-6 notification cursor-pointer"
		title="Mes invitations"
		@click="window.open(PORTAL_URL, '_blank')"
		@keydown.enter="window.open(PORTAL_URL, '_blank')"
	>
		<i class="text-2xl fa-light fa-mailbox nav-icon" :class="user.pending_invitations_count ? 'fa-shake' : ''"></i>
		<span
			v-if="user.pending_invitations_count"
			class="w-4 h-4 flex items-center justify-center absolute top-0 right-0 text-xs text-white rounded-full bg-danger font-medium -mt-1 -mr-1"
		>
			{{ user.pending_invitations_count }}
		</span>
	</button>
</template>
<script setup>
	import { useAuthStore } from "@/stores/auth.js";
	import { storeToRefs } from "pinia";

	const authStore = useAuthStore();
	const { user } = storeToRefs(authStore);
	const PORTAL_URL = import.meta.env.VITE_PORTAL_URL;
</script>
