<template>
	<div class="mx-auto border-t border-gray-300" :class="width"></div>
</template>
<script setup>
	defineProps({
		width: {
			type: String,
			default: "w-1/2",
		},
	});
</script>
