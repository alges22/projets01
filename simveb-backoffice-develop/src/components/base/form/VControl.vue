<script setup lang="ts">
	import VLabel from "/@src/components/base/form/VLabel.vue";
	import { useVFieldContext } from "/@src/composable/useVFieldContext";

	const props = defineProps({
		id: {
			type: String,
			default: undefined,
		},
		icon: {
			type: String,
			default: undefined,
		},
		isValid: {
			type: Boolean,
			default: undefined,
		},
		hasError: {
			type: Boolean,
			default: undefined,
		},
		loading: {
			type: Boolean,
			default: undefined,
		},
		expanded: {
			type: Boolean,
			default: undefined,
		},
		fullwidth: {
			type: Boolean,
			default: undefined,
		},
		textaddon: {
			type: Boolean,
			default: undefined,
		},
		nogrow: {
			type: Boolean,
			default: undefined,
		},
		subcontrol: {
			type: Boolean,
			default: undefined,
		},
		raw: {
			type: Boolean,
			default: undefined,
		},
		errors: {
			type: Array,
			required: false,
			default: () => [],
		},
	});

	// const isIconify = computed(() => {
	// 	return props.icon && props.icon.indexOf(":") !== -1;
	// });

	const vFieldContext = reactive(
		useVFieldContext({
			id: props.id,
			inherit: !props.subcontrol,
		}),
	);

	const isValid = computed(() => props.isValid);
	const hasError = computed(() =>
		vFieldContext?.field ? Boolean(vFieldContext?.field?.errorMessage?.value) : props.hasError,
	);

	const controlClasees = computed(() => {
		if (props.raw) return [];

		return [
			"control",
			props.icon && "has-icon",
			props.loading && "is-loading",
			props.expanded && "is-expanded",
			props.fullwidth && "is-fullwidth",
			props.nogrow && "is-nogrow",
			props.textaddon && "is-textarea-addon",
			isValid.value && "has-validation has-success",
			(hasError.value || props.errors.length > 0) && "has-validation has-error",
			props.subcontrol && "subcontrol",
		];
	});
</script>

<template>
	<div :class="controlClasees">
		<slot v-bind="vFieldContext"></slot>

		<template v-if="props.icon">
			<VLabel class="form-icon">
				<i aria-hidden="true" :class="`fa-light fa-${props.icon}`"></i>
			</VLabel>
		</template>

		<template v-if="errors && errors.length > 0">
			<p v-for="(error, index) in errors" :key="index" class="help has-text-danger">
				{{ error }}
			</p>
		</template>

		<VLabel v-if="isValid" class="validation-icon is-success">
			<i aria-hidden="true" class="fa-light fa-check"></i>
		</VLabel>
		<a
			v-else-if="errors && errors.length > 0"
			class="validation-icon is-error"
			role="button"
			tabindex="0"
			@click.prevent="() => vFieldContext.field?.resetField?.()"
			@keyup.enter.prevent="() => vFieldContext.field?.resetField?.()"
		>
			<i aria-hidden="true" class="fa-light fa-x"></i>
		</a>

		<slot v-bind="vFieldContext" name="extra"></slot>
	</div>
</template>

<style lang="scss" scoped>
	.is-nogrow {
		flex-grow: 0 !important;
	}

	.is-fullwidth {
		width: 100%;
	}
</style>
