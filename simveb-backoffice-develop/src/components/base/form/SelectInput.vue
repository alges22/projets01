<script lang="ts" setup>
	import { useVFieldContext } from "/@src/composable/useVFieldContext";

	export interface VSelectProps {
		raw?: boolean;
		multiple?: boolean;
	}

	defineOptions({
		inheritAttrs: false,
	});

	const modelValue = defineModel<any>({
		default: "",
		local: true,
	});
	const props = defineProps<VSelectProps>();
	const attrs = useAttrs();

	const { field, id } = useVFieldContext({
		create: false,
		help: "VSelect",
	});

	const internal = computed({
		get() {
			if (field?.value) {
				return field.value.value;
			} else {
				return modelValue.value;
			}
		},
		set(value: any) {
			if (field?.value) {
				field.value.setValue(value);
			}
			modelValue.value = value;
		},
	});

	const classes = computed(() => {
		if (props.raw) return [];

		return ["select", props.multiple && "is-multiple"];
	});
</script>

<template>
	<div :class="classes">
		<select
			:id="id"
			v-model="internal"
			:multiple="props.multiple"
			:name="id"
			v-bind="attrs"
			@blur="field?.handleBlur"
			@change="field?.handleChange"
		>
			<slot v-bind="{ selected: internal, id }" />
		</select>
	</div>
</template>
