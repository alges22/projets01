<template>
	<div
		class="grid gap-4"
		:class="[
			panelOpen && 'grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4',
			!panelOpen && 'grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-4',
		]"
	>
		<slot></slot>
	</div>
</template>

<script setup lang="ts">
	import { useSidebar } from "/@src/stores/sidebar";
	import { storeToRefs } from "pinia";

	const { panelOpen } = storeToRefs(useSidebar());
</script>

<style scoped lang="scss"></style>
