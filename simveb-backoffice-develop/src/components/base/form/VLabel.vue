<script setup lang="ts">
	import { useVFieldContext } from "/@src/composable/useVFieldContext";

	export interface VLabelProps {
		raw?: boolean;
		required?: boolean;
		id?: string;
	}

	const props = defineProps<VLabelProps>();

	const vFieldContext = reactive(
		useVFieldContext({
			create: false,
			help: "VLabel",
		})
	);

	const classes = computed(() => {
		if (props.raw) return [];

		return ["label", props.required && "is-required"];
	});

	const onEnter = () => {
		if (vFieldContext.id) {
			document.getElementById(vFieldContext.id)?.click();
		}
	};
</script>

<template>
	<label :class="classes" :for="id ?? vFieldContext.id" role="button" tabindex="0" @keydown.enter.prevent="onEnter">
		<slot v-bind="vFieldContext" />
	</label>
</template>
