<template>
	<button
		:class="{
			btn: true,
			'cursor-not-allowed': disabled,
			'opacity-50': disabled,
			'pointer-events-none': loading || disabled,
		}"
		:disabled="disabled || loading"
		type="button"
		@click="$emit('click')"
	>
		<template v-if="loading">
			<svg width="25" viewBox="-2 -2 42 42" xmlns="http://www.w3.org/2000/svg" stroke="#ffffff" class="w-6 h-6">
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
			<div class="text-center ms-2">{{ loadingText }}</div>
		</template>
		<template v-else>
			<slot />
		</template>
	</button>
</template>

<script setup>
	defineProps({
		loading: {
			type: Boolean,
			default: false,
		},
		disabled: {
			type: Boolean,
			default: false,
		},
		loadingText: {
			type: String,
			required: false,
			default: "Chargement",
		},
	});

	defineEmits(["click"]);
</script>

<style scoped></style>
