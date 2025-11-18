<template>
    <div class="grid grid-cols-4 gap-2 md:gap-16">
        <input
            v-for="(n, index) in length" :key="index"
            type="text"
            minlength="1"
            class="form-control"
            :name="'otpCodeInput' + index"
            :id="'otpCodeInput' + index"
            maxlength="1"
            autocomplete="off"
            autocapitalize="off"
            spellcheck="false"
            :autofocus="index === 0"
            :value="code[index]"
            @input="handleCodeInput(index, $event)"
            @keydown="handleBackspace(index, $event)"
            @paste="handlePaste(index, $event)"
        />
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
    };

    const emit = defineEmits(["update:modelValue"]);
</script>

<style scoped></style>