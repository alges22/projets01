<template>
	<label v-if="label" class="form-label" :for="name" :class="{ required: required }">{{ label }}</label>
	<input
		:id="name"
		:type="type"
		:pattern="pattern"
		:autocomplete="autoComplete ? autoComplete : name"
		class="form-control focus:border-indigo-500 focus:outline-none focus:ring-indigo-500 sm:text-sm"
		:name="name"
		:placeholder="placeholder"
		:required="required"
		:class="{ 'is-invalid': errors.length > 0, [addClass]: addClass }"
		:value="modelValue"
		:disabled="disabled"
		:autofocus="autoFocus"
		:step="step"
		:min="min"
		:max="max"
		:minlength="minlength"
		:maxlength="maxlength"
		:aria-label="placeholder"
		@input="$emit('update:modelValue', $event.target.value)"
	/>
	<slot></slot>
	<div v-if="errors.length > 0" class="invalid-feedback mt-2">
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
		pattern: {
			type: String,
			required: false,
		},
		required: {
			type: Boolean,
			required: false,
			default: false,
		},
		autoComplete: {
			type: String,
			required: false,
			default: "",
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
		step: {
			type: String,
			required: false,
		},
		min: {
			type: String,
			required: false,
		},
		max: {
			type: String,
			required: false,
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
	});
	defineEmits(["update:modelValue"]);
</script>

<style scoped></style>
