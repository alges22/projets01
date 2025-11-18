<!-- Step.vue -->
<template>
	<div class="mx-2 is-flex is-align-items-center">
		<button
			@click="setActiveStep(index)"
			data-v-b5b0dfcc=""
			type="button"
			class="button is-circle is-outlined is-primary button mr-2"
		>
			<span data-v-b5b0dfcc="" class="icon">
				<span>{{ index + 1 }}</span>
			</span>
		</button>

		<span
			:class="{ 'text-blue-600': activeStep >= index, 'text-gray-400': activeStep < index }"
			class="text-lg px-2"
			>{{ label }}</span
		>

		<!-- <font-awesome-icon :icon="['far', 'chevron-right']" /> -->
	</div>
</template>

<script lang="ts" setup>
	// import {FontAwesomeIcon} from "@fortawesome/vue-fontawesome";
	import { defineProps } from "vue";
	import { useServiceStore } from "/@src/stores/modules/service";

	const serviceStore = useServiceStore();

	const props = defineProps({
		label: String,
		index: Number,
		activeStep: Number,
	});

	const setActiveStep = (newStep) => {
		serviceStore.setActiveStep(newStep);
	};
</script>

<style scoped>
	button {
		width: 40px;
		height: 40px;
	}

	button.passed {
		@apply rounded-full border-blue-500 border text-blue-500 flex items-center justify-center;
	}

	button.active {
		@apply rounded-full border-blue-500 border text-white bg-blue-500 flex items-center justify-center;
	}

	button.coming {
		@apply rounded-full text-gray-400 border-gray-400 border flex items-center justify-center;
	}
</style>
