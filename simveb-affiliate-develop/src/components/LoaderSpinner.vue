<template>
	<button v-if="type === 'button'" :class="['btn-' + color, addStyle]" class="btn" disabled type="button">
		<span aria-hidden="true" class="spinner-border me-1" :class="spinnerClass" role="status"></span>
		{{ text }}
	</button>
	<div v-else-if="type === 'block'" class="bg-white rounded-lg shadow-md p-4 animate-pulse" :class="addStyle">
		<div class="w-2/3 h-4 bg-gray-300 rounded mb-2"></div>
		<div class="w-full h-8 bg-gray-300 rounded mb-2"></div>
		<div class="flex mb-4">
			<div class="w-1/2 h-48 bg-gray-300 rounded me-2"></div>
			<div class="w-1/2 h-48 bg-gray-300 rounded"></div>
		</div>
	</div>
	<template v-else>
		<svg
			width="25"
			viewBox="-2 -2 42 42"
			xmlns="http://www.w3.org/2000/svg"
			:stroke="color"
			class="w-6 h-6"
			:class="addStyle"
		>
			<g fill="none" fill-rule="evenodd">
				<g transform="translate(1 1)" stroke-width="4">
					<circle stroke-opacity=".5" cx="18" cy="18" r="18" />
					<path d="M36 18c0-9.94-8.06-18-18-18">
						<animateTransform
							attributeName="transform"
							type="rotate"
							from="0 18 18"
							to="360 18 18"
							dur="1s"
							repeatCount="indefinite"
						/>
					</path>
				</g>
			</g>
		</svg>
		<div class="text-center text-xs mt-2">{{ text }}</div>
	</template>
</template>

<script setup>
	defineProps({
		type: {
			type: String,
			required: false,
			validValues: ["button", "block", "inline"],
		},
		color: {
			type: String,
			required: false,
			default: "primary",
		},
		text: {
			type: String,
			required: false,
			default: "Chargement",
		},
		spinnerClass: {
			type: String,
			required: false,
		},
		addStyle: {
			type: String,
			required: false,
		},
	});
</script>

<style scoped></style>
