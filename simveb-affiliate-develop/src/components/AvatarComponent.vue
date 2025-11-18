<template>
	<span
		class="inline-flex items-center justify-center rounded-full bg-gray-500"
		:class="[
			{
				'h-6 w-6': size === 'xs',
				'h-8 w-8': size === 'sm',
				'h-10 w-10': size === 'md',
				'h-12 w-12': size === 'lg',
				'h-14 w-14': size === 'xl',
			},
			'bg-' + backgroundColor,
		]"
	>
		<span
			class="font-bold leading-relaxed"
			:class="[
				{
					'text-xs': textSize === 'xs',
					'text-sm': textSize === 'sm',
					'text-base': textSize === 'md',
					'text-lg': textSize === 'lg',
					'text-xl': textSize === 'xl',
				},
				'text-' + textColor,
			]"
		>
			{{ firstname ? firstname[0] : "" }}{{ lastname ? lastname[0] : "" }}
		</span>
	</span>
</template>

<script setup>
	defineProps({
		firstname: {
			type: String,
			required: false,
		},
		lastname: {
			type: String,
			required: true,
		},
		backgroundColor: {
			type: String,
			required: false,
			default: "primary",
		},
		textColor: {
			type: String,
			required: false,
			default: "primary",
		},
		badge: {
			type: String,
			required: false,
			default: "success",
		},
		size: {
			type: String,
			required: false,
			default: "md",
			validator: (value) => ["xs", "sm", "md", "lg", "xl"].includes(value),
		},
		textSize: {
			type: String,
			required: false,
			default: "md",
			validator: (value) => ["xs", "sm", "md", "lg", "xl"].includes(value),
		},
	});
</script>

<style scoped></style>
