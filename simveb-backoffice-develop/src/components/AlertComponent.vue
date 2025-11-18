<template>
	<div class="p-4" :class="`border-${color}-400 bg-${color}-100`">
		<div class="flex">
			<div v-if="icon" class="flex-shrink-0">
				<i class="h-5 w-5" :class="`text-${color}-400 fa fa-${icon}`" aria-hidden="true" />
			</div>
			<div class="ml-3">
				<slot />
			</div>
		</div>
	</div>
</template>
<script setup>
	defineProps({
		icon: {
			type: String,
			required: false,
			default: undefined,
		},
		color: {
			type: String,
			required: false,
			validValues: ["primary", "secondary", "success", "danger", "warning", "info", "light", "dark"],
		},
	});
</script>
