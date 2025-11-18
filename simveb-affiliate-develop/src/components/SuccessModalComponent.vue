<template>
	<Modal :show="open" backdrop="static" @hidden="$emit('close')">
		<ModalBody class="p-0">
			<div class="p-5 text-center font-bold">
				<svg
					class="lucide lucide-check-circle w-16 h-16 text-success mx-auto mt-3"
					fill="none"
					height="24"
					stroke="currentColor"
					stroke-linecap="round"
					stroke-linejoin="round"
					stroke-width="2"
					viewBox="0 0 24 24"
					width="24"
					xmlns="http://www.w3.org/2000/svg"
				>
					<path d="M22 11.08V12a10 10 0 11-5.93-9.14"></path>
					<polyline points="22 4 12 14.01 9 11.01"></polyline>
				</svg>
				<div class="text-2xl mt-5">{{ message }}</div>
			</div>
			<div class="px-5 pb-8 text-center mt-4">
				<button class="btn btn-primary w-full mx-2" type="button" @click="$emit('close')">
					{{ buttonText }}
				</button>
			</div>
		</ModalBody>
	</Modal>
</template>
<script setup>
	import { Modal, ModalBody } from "@/global-components/modal/index.js";

	defineProps({
		open: Boolean,
		buttonText: {
			type: String,
			default: "Ok, j’ai compris !",
		},
		message: {
			type: String,
			default: "Invitation envoyée avec succès !",
		},
	});

	defineEmits(["close"]);
</script>
