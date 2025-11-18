<template>
	<div class="flex items-center relative">
		<router-link v-if="to" :to="to" class="underline decoration-dotted whitespace-nowrap me-2">
			{{ value }}
		</router-link>
		<span v-else class="me-2 whitespace-nowrap">
			{{ value }}
		</span>
		<button
			class="end-2 text-gray-500 dark:text-gray-400 bg-gray-100 dark:hover:bg-gray-800 rounded-lg p-1 inline-flex items-center justify-center"
			type="button"
			@click="copyToClipboard"
		>
			<span v-show="!copied">
				<i class="w-3.5 h-3.5 fa fa-copy"></i>
			</span>
			<span v-show="copied" class="items-center">
				<i class="w-3.5 h-3.5 fa fa-check text-blue-700 dark:text-blue-500"></i>
			</span>
		</button>
	</div>
</template>

<script setup>
	import { ref } from "vue";
	import Alert from "@/components/notification/alert.js";

	const props = defineProps({
		value: {
			type: String,
			required: true,
		},
		copyValue: {
			type: String,
			required: false,
			default: undefined,
		},
		to: {
			type: [Object, String],
			required: false,
			default: undefined,
		},
	});

	const copied = ref(false);

	const copyToClipboard = () => {
		if (navigator.clipboard) {
			navigator.clipboard
				.writeText(props.copyValue || props.value)
				.then(() => {
					copied.value = true;
					setTimeout(() => {
						copied.value = false;
					}, 2000);
				})
				.catch((err) => {
					Alert.error("Erreur lors de la copie du texte: " + err, true);
				});
		}
	};
</script>

<style scoped></style>
