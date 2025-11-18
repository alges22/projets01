<template>
	<div class="flex flex-row mt-4 overflow-x-auto mb-3">
		<slot :activeStep="activeStep"></slot>
	</div>
</template>

<script setup lang="ts">
	const props = defineProps({
		activeStep: Number,
	});
</script>

<style scoped></style>
