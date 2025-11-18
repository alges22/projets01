<template>
	<span class="group/item relative">
		<router-link v-if="to" :to="to" class="underline decoration-dotted whitespace-nowrap me-2">
			{{ value }}
		</router-link>
		<span v-else class="me-2 whitespace-nowrap">
			{{ value }}
		</span>
		<button
			class="group-hover/item:inline-flex hidden ms-2 end-2 text-gray-500 dark:text-gray-400 bg-gray-100 dark:hover:bg-gray-800 rounded-lg p-1 items-center justify-center"
			type="button"
			@click="copyToClipboard"
		>
			<span v-show="!copied">
				<i class="w-2 h-2 fa fa-copy"></i>
			</span>
			<span v-show="copied" class="items-center">
				<i class="w-2 h-2 fa fa-check text-blue-700 dark:text-blue-500"></i>
			</span>
		</button>
	</span>
</template>

<script setup>
	import { ref } from "vue";
	import { Notyf } from "notyf";

	const notyf = new Notyf();

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
					notyf.error("Erreur lors de la copie du texte: " + err);
				});
		}
	};
</script>

<style scoped></style>
