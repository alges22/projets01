<template>
	<input
		:id="name"
		:pattern="pattern"
		:autocomplete="autoComplete ? autoComplete : name"
		class="form-control"
		:name="name"
		:placeholder="placeholder"
		aria-describedby="password"
		type="password"
		:required="required"
		:class="{ 'is-invalid': errors.length > 0, [addClass]: addClass }"
		:value="modelValue"
		:disabled="disabled"
		@input="$emit('update:modelValue', $event.target.value)"
	/>
	<slot></slot>
	<div v-if="errors.length > 0" class="invalid-feedback">
		<template v-for="error in errors">
			{{ error }}
		</template>
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
		addClass: {
			type: String,
			required: false,
			default: "",
		},
	});
	defineEmits(["update:modelValue"]);
</script>

<style scoped></style>
