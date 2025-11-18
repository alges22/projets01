<template>
	<div class="flex flex-row items-center justify-between">
		<div v-for="(n, index) in length" :key="index" class="w-16 h-16">
			<input
				:id="'otpCodeInput' + index"
				type="text"
				minlength="1"
				class="otp-code-input"
				:name="'otpCodeInput' + index"
				maxlength="1"
				autocomplete="off"
				autocapitalize="off"
				spellcheck="false"
				:value="code[index]"
				@input="handleCodeInput(index, $event)"
				@keydown="handleBackspace(index, $event)"
				@paste="handlePaste(index, $event)"
			/>
		</div>
	</div>
</template>

<script setup>
	import { ref } from "vue";

	const props = defineProps({
		length: {
			type: Number,
			required: true,
			default: 6,
		},
		modelValue: {},
	});

	const code = ref(Array(props.length).fill(null));

	const handleCodeInput = (index, event) => {
		const inputValue = event.target.value;
		if (inputValue.length === 1) {
			code.value[index] = inputValue;
			emit("update:modelValue", code.value.join(""));
			if (index < props.length - 1) {
				document.getElementById(`otpCodeInput${index + 1}`).focus();
			} else if (index === props.length - 1) {
				emit("submit");
			}
		}
	};

	const handleBackspace = (index, event) => {
		if (event.key === "Backspace" && index > 0) {
			code.value[index] = null;
			emit("update:modelValue", code.value.join(""));
			document.getElementById(`otpCodeInput${index - 1}`).focus();
		}
	};

	const handlePaste = (index, event) => {
		event.preventDefault();
		const pastedText = (event.clipboardData || window.clipboardData).getData("text");
		for (let i = 0; i < pastedText.length && i + index < props.length; i++) {
			if (!isNaN(pastedText[i])) {
				code.value[i + index] = pastedText[i];
				emit("update:modelValue", code.value.join(""));
				document.getElementById(`otpCodeInput${i + index}`).focus();
			}
		}
		emit("submit");
	};

	const emit = defineEmits(["update:modelValue", "submit"]);
</script>

<style scoped></style>
