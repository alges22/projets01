<template>
	<div class="form-check">
		<input
			:id="name"
			type="checkbox"
			class="form-check-input"
			:name="name"
			:class="{ 'is-invalid': errors.length > 0 }"
			:value="modelValue"
			@input="$emit('update:modelValue', $event.target.checked)"
			:disabled="disabled"
		/>
		<label v-if="label" class="form-label-check" :for="name">{{ label }}</label>
	</div>
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
	});
	defineEmits(["update:modelValue"]);
</script>

<style scoped></style>
