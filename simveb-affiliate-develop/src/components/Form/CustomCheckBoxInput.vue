<template>
	<input
		:id="id"
		class="peer absolute"
		:class="inputClass + ' ' + (errors.length > 0 ? 'is-invalid' : '')"
		:type="type"
		:name="name"
		:value="modelValue"
		:disabled="disabled"
		@input="$emit('update:modelValue', $event.target.value)"
	/>
	<label
		:class="labelClass + ' ' + (errors.length > 0 ? 'border border-danger' : '')"
		:for="id"
		class="cursor-pointer"
	>
		<slot></slot>
	</label>
	<div v-if="errors.length > 0" class="invalid-feedback">
		<template v-for="error in errors">
			{{ error }}
		</template>
	</div>
</template>

<script setup>
	defineProps({
		modelValue: {},
		label: {
			type: String,
			required: false,
		},
		name: {
			type: String,
			required: true,
		},
		id: {
			type: String,
			required: false,
			default: "",
		},
		errors: {
			type: Array,
			required: false,
			default: () => [],
		},
		disabled: {
			type: Boolean,
			required: false,
			default: false,
		},
		inputClass: {
			type: String,
			required: false,
			default: "form-check-input",
		},
		labelClass: {
			type: String,
			required: false,
			default: "form-label-check",
		},
		type: {
			type: String,
			required: false,
			default: "checkbox",
		},
	});
	defineEmits(["update:modelValue"]);
</script>

<style scoped></style>
