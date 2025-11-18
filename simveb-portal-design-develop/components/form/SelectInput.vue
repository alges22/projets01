<template>
    <label v-if="label" :class="labelClass">{{ label }}</label>
    <Field
        class="custom-select"
        :name="name"
        :value="modelValue"
        as="select"
        @update:modelValue="$emit('update:modelValue', $event)"
    >
        <option value="">{{ placeholder }}</option>
        <option
            v-for="option in options"
            :key="option[id]"
            :value="option[id]"
        >
            {{ option[labelField] }}
        </option>
    </Field>
    <ErrorMessage :name="name" class="form-invalid" />
</template>

<script setup>
    import { Field, ErrorMessage } from 'vee-validate';
    import { toRefs } from 'vue';

    // Props
    const props = defineProps({
        label: {
            type: String,
            required: false,
        },
        name: {
            type: String,
            required: true,
        },
        options: {
            type: Array,
            required: true,
        },
        placeholder: {
            type: String,
            default: 'Select an option',
        },
        modelValue: {
            type: [String, Number],
            default: '',
        },
        labelClass: {
            type: String,
            default: 'block text-lg font-bold text-black',
        },
        id: {
            type: String,
            default: 'id',  // Default key for option's unique identifier
        },
        labelField: {
            type: String,
            default: 'value',  // Default key for option's display text
        },
    });

    // Emit update event
    const emit = defineEmits(['update:modelValue']);

    // Extract props with toRefs for reactivity
    const { name } = toRefs(props);
</script>

<style scoped>
    .custom-select {
        /* Hide default arrow */
        -webkit-appearance: none;
        -moz-appearance: none;
        appearance: none;

        /* Custom styling */
        width: 100%;
        padding: 0.75rem;
        font-size: 1rem;
        border: 1px solid #d1d5db; /* gray-300 */
        border-radius: 0.375rem; /* rounded-md */
        background-color: white;
        color: #374151; /* gray-700 */

        /* Custom dropdown arrow */
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='%23374151'%3E%3Cpath d='M7 10l5 5 5-5H7z'/%3E%3C/svg%3E");
        background-position: right 0.75rem center;
        background-repeat: no-repeat;
        background-size: 1.25rem;
        cursor: pointer;
    }

    .custom-select:focus {
        border-color: #3b82f6; /* blue-500 */
        outline: none;
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.2); /* blue shadow */
    }

    .custom-select.is-invalid {
        border-color: #ef4444; /* red-500 */
        background-color: #fee2e2; /* red-100 */
    }

    .form-invalid {
        color: #ef4444;
        font-size: 0.875rem;
        margin-top: 0.25rem;
    }

    /* Hide the default arrow in IE */
    select::-ms-expand {
        display: none;
    }
</style>
