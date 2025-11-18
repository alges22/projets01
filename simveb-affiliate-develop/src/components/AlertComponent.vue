<template>
	<div class="alert show flex items-center mb-2" :class="`alert-${color}`" role="alert">
		<i class="h-5 w-5" :class="`text-${color}-400 fa fa-${icon}`" aria-hidden="true" />
		<slot />
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
