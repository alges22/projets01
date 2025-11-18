<template>
	<router-link
		v-if="to && !active"
		:to="to"
		class="flex flex-col items-center intro-y col-span-12 sm:col-span-4 2xl:col-span-3"
		:class="{
			'cursor-default': active || disabled,
			'cursor-pointer': !active,
			disabled: disabled,
		}"
	>
		<span
			class="h-14 w-16 flex items-center justify-center zoom-in rounded-lg border-[3px] relative"
			:class="addClass + ' ' + (active ? 'border-primary' : borderColor) + ' ' + (disabled ? 'opacity-50' : '')"
		>
			<slot></slot>
			<span v-if="active" class="group-has-[input:checked]:flex floating-icon -bottom-1.5 -right-1.5">
				<i class="fa-light fa-check"></i>
			</span>
			<span v-if="disabled" class="group-has-[input:checked]:flex floating-icon-danger -bottom-1.5 -right-1.5">
				<i class="fa-light fa-ban"></i>
			</span>
		</span>
		<div class="mt-2 text-center text-xs">{{ label }}</div>
	</router-link>

	<button
		v-else
		class="flex flex-col items-center intro-y col-span-12 sm:col-span-4 2xl:col-span-3"
		:class="{
			'cursor-default': active || disabled,
			'cursor-pointer': !active,
			disabled: disabled,
		}"
		@click="$emit('click')"
	>
		<span
			class="h-14 w-16 flex items-center justify-center zoom-in rounded-lg border-[3px] relative"
			:class="addClass + ' ' + (active ? 'border-primary' : borderColor) + ' ' + (disabled ? 'opacity-50' : '')"
		>
			<slot></slot>
			<span v-if="active" class="group-has-[input:checked]:flex floating-icon -bottom-1.5 -right-1.5">
				<i class="fa-light fa-check"></i>
			</span>
			<span v-if="disabled" class="group-has-[input:checked]:flex floating-icon-danger -bottom-1.5 -right-1.5">
				<i class="fa-light fa-ban"></i>
			</span>
		</span>
		<span class="mt-2 text-center text-xs" :class="active ? 'text-primary font-bold' : ''">{{ label }}</span>
	</button>
</template>
<script setup>
	defineProps({
		label: {
			type: String,
			required: true,
		},
		to: {
			type: [String, Object],
			required: false,
			default: "",
		},
		addClass: {
			type: String,
			required: false,
			default: "",
		},
		borderColor: {
			type: String,
			required: false,
			default: "",
		},
		active: {
			type: Boolean,
			required: false,
			default: false,
		},
		disabled: {
			type: Boolean,
			required: false,
			default: false,
		},
	});

	defineEmits(["click"]);
</script>
