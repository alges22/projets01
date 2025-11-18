<template>
	<div
		:id="id"
		ref="modalElement"
		:class="['modal', animation, modalFrom ? 'modal-' + modalFrom : '']"
		tabindex="-1"
		aria-hidden="true"
	>
		<slot>Default Content</slot>
	</div>
</template>

<script setup>
	import { onMounted, ref, watch } from "vue";
	import { Modal } from "bootstrap";

	const props = defineProps({
		id: {
			type: String,
			required: true,
		},
		open: {
			type: Boolean,
			default: false,
		},
		modalFrom: {
			type: String,
			default: "",
			required: false,
		},
		animation: {
			type: String,
			default: "fade",
			required: false,
		},
		keyboard: {
			type: Boolean,
			default: true,
			required: false,
		},
		backdrop: {
			default: true,
			required: false,
		},
	});

	const modalElement = ref(null);
	const emit = defineEmits(["close"]);
	let modal = null;

	onMounted(() => {
		if (modalElement.value) {
			let element = modalElement.value;
			modal = new Modal(element, {
				keyboard: props.keyboard,
				backdrop: props.backdrop,
			});
			if (element.querySelector("input")) {
				element.addEventListener("shown.bs.modal", () => {
					element.querySelector("input, select, textarea").focus();
				});
			}
			element.addEventListener("hide.bs.modal", () => {
				emit("close");
			});
		}
	});
	watch(
		() => props.open,
		(val) => {
			if (val) {
				modal.show();
			} else {
				modal.hide();
			}
		}
	);
</script>

<style scoped></style>
