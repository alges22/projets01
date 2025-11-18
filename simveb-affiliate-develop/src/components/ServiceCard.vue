<template>
	<div class="relative group">
		<router-link
			:to="to"
			class="flex flex-col lg:flex-row items-center p-5 bg-blue-50 rounded-md border-primary border"
		>
			<div class="w-12 h-12 lg:mr-4 bg-[#27ae6033] rounded-full flex justify-center items-center">
				<i class="fa-solid fa-plus-circle text-5xl text-center text-[#194D98]" />
			</div>
			<div class="lg:ml-2 lg:mr-auto text-center lg:text-left mt-3 lg:mt-0">
				<h2 class="font-bold text-lg leading-5 text-[#194D98]">{{ title }}</h2>
			</div>
		</router-link>
		<span class="flex floating-text px-2 rounded-bl-md rounded-tr-md bg-primary text-white text-xs top-0 right-0">
			<span>Prix : {{ formatPrice(price) }}</span>
		</span>
	</div>
</template>
<script setup>
	import { formatPrice } from "@/helpers/utils.js";

	defineProps({
		title: {
			type: String,
			required: true,
		},
		price: {
			type: String,
			required: true,
		},
		to: {
			required: true,
		},
	});
</script>
