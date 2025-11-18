<template>
	<template v-if="theme === 'vivid'">
		<VividLayout />
	</template>

	<template v-else>
		<SerenityLayout />
	</template>
</template>

<script setup>
	import { onMounted } from "vue";
	import { init } from "@/assets/js/app.js";
	import { useAuthStore } from "@/stores/auth.js";
	import { storeToRefs } from "pinia";
	import VividLayout from "@/layouts/themes/VividLayout.vue";
	import SerenityLayout from "@/layouts/themes/SerenityLayout.vue";

	const authStore = useAuthStore();
	const { theme } = storeToRefs(authStore);

	onMounted(() => {
		init();
	});
</script>
