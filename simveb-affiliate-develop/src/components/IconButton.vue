<template>
	<button
		type="button"
		:class="['btn-' + color, { 'me-2': margin }]"
		:title="title"
		:disabled="disabled"
		@click="$emit('click')"
	>
		<slot>
			<i
				:class="{
					[font]: true,
					['fa-' + icon]: true,
				}"
			></i>
		</slot>
	</button>
</template>

<script setup>
	defineProps({
		icon: {
			type: String,
			required: true,
		},
		font: {
			type: String,
			default: "fa-light",
		},
		color: {
			type: String,
			default: "primary",
		},
		title: {
			type: String,
			default: "",
		},
		disabled: {
			type: Boolean,
			default: false,
			required: false,
		},
		margin: {
			type: Boolean,
			default: true,
		},
	});
	defineEmits(["click"]);
</script>

<style scoped></style>
