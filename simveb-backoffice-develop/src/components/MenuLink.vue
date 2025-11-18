<template>
	<RouterLink v-if="sKey === 'icon'" v-tooltip.primary.top="title" :to="to">
		<i aria-hidden="true" class="fa-light sidebar-svg fa-lg" :class="{ ['fa-' + icon]: true, active: active }"></i>
	</RouterLink>

	<RouterLink v-if="sKey === 'text'" :to="to"> {{ label }} </RouterLink>
</template>

<script setup>
	defineProps({
		active: {
			type: Boolean,
			default: false,
			required: false,
		},
		to: {
			type: Object,
			default: () => ({ name: "" }),
			required: true,
		},
		icon: {
			type: String,
			default: "",
			required: false,
		},
		label: {
			type: String,
			default: "",
			required: true,
		},
		title: {
			type: String,
			default: "",
			required: false,
		},
		sKey: {
			type: String,
			required: true,
		},
	});
</script>

<style scoped></style>
