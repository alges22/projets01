<script setup lang="ts">
	import { initDarkmode } from "/@src/stores/darkmode";
	import { useHead } from "@vueuse/head";
	import { useDate } from "vue3-dayjs-plugin/useDate";
	import fr from "dayjs/locale/fr";

	const dayjs = useDate();
	dayjs.locale(fr);

	// This is the global app setup function

	useHead(() => ({
		htmlAttrs: {
			lang: "fr",
		},
	}));

	/**
	 * Initialize the darkmode watcher
	 *
	 * @see /@src/stores/darkmode
	 */
	initDarkmode();
</script>

<template>
	<div>
		<RouterView v-slot="{ Component }">
			<Transition name="fade-slow" mode="out-in">
				<component :is="Component" />
			</Transition>
		</RouterView>
		<ClientOnly>
			<VReloadPrompt app-name="Simveb" />
		</ClientOnly>
	</div>
</template>
