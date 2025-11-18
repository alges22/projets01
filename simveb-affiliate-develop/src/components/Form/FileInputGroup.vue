<template>
	<label v-if="label" class="form-label" :class="required ? 'required' : ''" :for="name">
		{{ label }}
	</label>
	<label
		class="flex rounded-lg border px-4 py-2 items-center cursor-pointer focus-within:outline-none focus-within:ring-2 focus-within:ring-indigo-600 focus-within:ring-offset-2 hover:text-indigo-500"
		:class="file ? 'bg-[#E5F3FF]' : 'bg-[#F4F6F9]'"
		:for="name"
	>
		<i class="text-[#6D7580] me-8 fa-solid text-2xl fa-cloud-arrow-up"></i>
		<span class="flex text-sm leading-5 text-dark">
			<span class="relative rounded-md font-semibold">
				<span> {{ shownLabel }}</span>
			</span>
		</span>
	</label>
	<input
		:id="name"
		class="sr-only"
		type="file"
		:name="name"
		:required="required"
		:class="{ 'is-invalid': errors.length > 0 }"
		:disabled="disabled"
		:accept="accept"
		:multiple="multiple"
		@change="handleChange($event)"
	/>
	<slot></slot>
	<div v-if="errors.length > 0" class="invalid-feedback">
		<template v-for="error in errors">
			{{ error }}
		</template>
	</div>
</template>

<script setup>
	import { computed, ref } from "vue";

	const props = defineProps({
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
		accept: {
			type: String,
			required: false,
		},
		help: {
			type: String,
			required: false,
			default: "Sélectionner un fichier",
		},
		multiple: {
			type: Boolean,
			required: false,
			default: false,
		},
	});
	const emit = defineEmits(["update:modelValue"]);

	const file = ref(null);

	const handleChange = (event) => {
		const files = event.target.files;
		if (files.length > 0) {
			if (props.multiple) {
				emit("update:modelValue", files);
				file.value = files;
			} else {
				emit("update:modelValue", files[0]);
				file.value = files[0];
			}
		}
	};

	const shownLabel = computed(() => {
		return file.value
			? props.multiple
				? `${file.value.length} fichiers sélectionnés`
				: file.value.name
			: props.help;
	});
</script>

<style scoped></style>
