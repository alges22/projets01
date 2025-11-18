<template>
	<label v-if="label" class="form-label" :for="name" :class="{ required: required }">{{ label }}</label>
	<textarea
		:id="name"
		class="form-control focus:border-indigo-500 focus:outline-none focus:ring-indigo-500 sm:text-sm"
		:name="name"
		:placeholder="placeholder"
		:required="required"
		:class="{ 'is-invalid': errors.length > 0, [addClass]: addClass }"
		:value="modelValue"
		:disabled="disabled"
		:minlength="minlength"
		:maxlength="maxlength"
		:aria-label="placeholder"
		:rows="rows"
		@input="$emit('update:modelValue', $event.target.value)"
	></textarea>
	<slot></slot>
	<div v-if="errors.length > 0" class="invalid-feedback">
		<template v-for="error in errors"> {{ error }} &nbsp; </template>
	</div>
</template>

<script setup>
	defineProps({
		modelValue: {},
		type: {
			type: String,
			required: false,
			default: "text",
		},
		label: {
			type: String,
			required: false,
		},
		name: {
			type: String,
			required: true,
		},
		placeholder: {
			type: String,
			required: false,
			default: "",
		},
		errors: {
			type: Array,
			required: false,
			default: () => [],
		},

		required: {
			type: Boolean,
			required: false,
			default: false,
		},
		disabled: {
			type: Boolean,
			required: false,
			default: false,
		},
		autoFocus: {
			type: Boolean,
			required: false,
			default: false,
		},
		class: {
			type: String,
			required: false,
		},
		minlength: {
			type: Number,
			required: false,
		},
		maxlength: {
			type: Number,
			required: false,
		},
		addClass: {
			type: String,
			required: false,
		},
		rows: {
			type: Number,
			required: false,
			default: 3,
		},
	});
	defineEmits(["update:modelValue"]);
</script>

<style scoped></style>
