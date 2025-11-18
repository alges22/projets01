<template>
	<div v-if="theme === 'default'" class="report-box zoom-in cursor-default">
		<div class="box p-4" :class="backgroundColor">
			<div class="flex mb-2">
				<div class="rounded-full p-2" :class="'bg-' + iconColor">
					<i :class="'fa-' + icon" class="fa-solid report-box__icon text-xl text-white text-center"></i>
				</div>
			</div>
			<div v-if="title" class="text-base text-slate-500 mt-1 font-semibold">{{ title }}</div>
			<div class="text-base font-bold leading-6 mt-2 mb-2">
				<slot></slot>
			</div>
			<slot name="footer"></slot>
		</div>
	</div>

	<div
		v-else-if="theme === 'vivid'"
		class="p-4 grid grid-cols-4 rounded-xl shadow-md cursor-default"
		:class="backgroundColor"
	>
		<div class="col-span-3">
			<div class="font-bold text-2xl" :class="'text-' + iconColor">
				<slot></slot>
			</div>
			<div class="py-4 font-bold text-base" :class="'text-' + iconColor">{{ title }}</div>
		</div>
		<div class="px-4">
			<div class="p-4 rounded-xl flex justify-center bg-blue-100">
				<i class="fa-solid report-box__icon text-2xl" :class="'fa-' + icon"></i>
			</div>
		</div>
	</div>
</template>

<script setup>
	import { useAuthStore } from "@/stores/auth.js";
	import { storeToRefs } from "pinia";

	defineProps({
		title: {
			type: String,
			required: false,
			default: null,
		},
		iconColor: {
			type: String,
			default: "primary",
		},
		icon: {
			type: String,
			required: true,
		},
		backgroundColor: {
			type: String,
			default: "white",
		},
	});

	const { theme } = storeToRefs(useAuthStore());
</script>

<style scoped></style>
