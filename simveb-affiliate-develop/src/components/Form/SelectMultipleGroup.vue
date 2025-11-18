<template>
	<label v-if="label" class="font-bold mb-2" :class="required ? 'required' : ''" :for="name">{{ label }}</label>
	<select
		:id="name"
		:ref="(el) => bindSelect(el)"
		class="w-full mt-2 border-none py-2 pl-3 pr-10 text-sm leading-5 text-gray-900 focus:ring-0 focus:outline-none"
		:disabled="disabled"
		:name="name"
		multiple
		:required="required"
		:class="classes"
	>
		<option disabled value="">{{ placeholder }}</option>
	</select>
	<slot></slot>
	<div v-if="errors.length > 0" class="invalid-feedback">
		<template v-for="error in errors">
			{{ error }}
		</template>
	</div>
</template>

<script setup>
	import { ref, watch } from "vue";
	import TomSelect from "tom-select";
	import "tom-select/dist/css/tom-select.bootstrap5.css";

	const props = defineProps({
		modelValue: {},
		label: {
			type: String,
			required: false,
		},
		classes: {
			type: String,
			required: false,
			default: "",
		},
		items: {
			type: Array,
			default: () => [],
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
		options: {
			type: Array,
			required: true,
		},
		optionValue: {
			type: String,
			required: false,
			default: "id",
		},
		optionText: {
			type: String,
			required: false,
			default: "name",
		},
		searchFields: {
			type: Array,
			required: false,
			default: () => ["label", "name"],
		},
		placeholder: {
			type: String,
			required: false,
			default: "Sélectionnez une option",
		},
	});
	const emit = defineEmits(["update:add", "update:remove", "update:modelValue"]);
	const initialized = ref(false);
	let tomSelect = null;

	const bindSelect = (el) => {
		watch(
			() => props.options,
			(newOptions) => {
				if (initialized.value || Object.keys(newOptions).length <= 0) return;
				if (el) {
					tomSelect = new TomSelect(el, {
						onInitialize: function () {
							initialized.value = true;
						},
						render: {
							no_results: function (data, escape) {
								return '<div class="no-results">Aucun résultat pour "' + escape(data.input) + '"</div>';
							},
						},
						plugins: props.multiple && {
							remove_button: {
								title: "Supprimer",
							},
						},
						valueField: props.optionValue,
						labelField: props.optionText,
						searchField: props.searchFields,
						options: newOptions,
						items: props.modelValue ? props.modelValue : props.items.map((item) => item[props.optionValue]),
						onItemAdd: function (value) {
							emit("update:add", value);
						},
						onItemRemove: function (value) {
							emit("update:remove", value);
						},
					});
				}
			},
			{ immediate: true }
		);
	};

	watch(
		() => props.items,
		(items) => {
			if (initialized.value && items) {
				items.map((item) => {
					tomSelect.addItem(item[props.optionValue]);
				});
			}
		}
	);
</script>

<style scoped></style>
