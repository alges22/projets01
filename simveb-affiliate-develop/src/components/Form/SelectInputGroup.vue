<template>
	<Listbox
		:class="{ 'is-invalid': errors.length > 0 }"
		as="div"
		@update:model-value="(value) => $emit('update:modelValue', value)"
	>
		<ListboxLabel v-if="label" class="form-label block" :class="{ required: required }">{{ label }}</ListboxLabel>
		<div class="relative mt-2">
			<ListboxButton
				class="relative w-full cursor-default rounded-md border border-gray-300 bg-white py-2 pl-3 pr-10 text-left shadow-sm focus:border-primary-500 focus:outline-none focus:ring-1 focus:ring-primary-500 sm:text-sm"
			>
				<span class="block truncate">{{ selected ? selected[optionText] : placeholder || "Choisissez" }}</span>
				<span class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-2">
					<i class="fa-light fa-chevron-down h-5 w-5 text-gray-400" aria-hidden="true" />
				</span>
			</ListboxButton>

			<transition
				leave-active-class="transition ease-in duration-100"
				leave-from-class="opacity-100"
				leave-to-class="opacity-0"
			>
				<ListboxOptions
					class="absolute z-10 mt-1 max-h-60 w-full overflow-auto rounded-md bg-white py-1 text-base shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none sm:text-sm"
				>
					<ListboxOption
						v-for="(option, index) in options"
						:key="index"
						v-slot="{ active, selected }"
						as="template"
						:value="option[optionValue]"
					>
						<li
							:class="[
								active ? 'text-white bg-primary-600' : 'text-gray-900',
								'relative cursor-pointer select-none py-2 pl-3 pr-9 flex items-center',
							]"
						>
							<slot name="custom" :option="option" :selected="selected" :active="active">
								<span :class="[selected ? 'font-semibold' : 'font-normal', 'block truncate']">
									{{ option[optionText] }}
								</span>
							</slot>

							<span
								v-if="selected"
								:class="[
									active ? 'text-white' : 'text-primary-600',
									'absolute inset-y-0 right-0 flex items-center pr-4',
								]"
							>
								<i class="fa-solid fa-check h-5 w-5" aria-hidden="true" />
							</span>
						</li>
					</ListboxOption>
				</ListboxOptions>
			</transition>
		</div>
		<div v-if="errors.length > 0" class="invalid-feedback">
			<template v-for="error in errors"> {{ error }} &nbsp;</template>
		</div>
	</Listbox>
</template>

<script setup>
	import { Listbox, ListboxButton, ListboxLabel, ListboxOption, ListboxOptions } from "@headlessui/vue";
	import { ref, watch } from "vue";

	const selected = ref(null);

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
		placeholder: {
			type: String,
			required: false,
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
			default: () => [],
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
		optionTitle: {
			type: String,
			required: false,
		},
	});
	defineEmits(["update:modelValue"]);

	watch(
		() => props.modelValue,
		(value) => {
			selected.value = props.options.find((option) => option[props.optionValue] === value);
		},
		{ immediate: true }
	);
</script>

<style scoped></style>
