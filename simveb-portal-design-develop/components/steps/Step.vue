<!-- Step.vue -->
<template>
    <div class="flex flex-row items-center mx-2 mt-2">
        <button :class="{ 'passed': activeStep > index, 'active': activeStep === index, 'coming': activeStep < index }">
            <span class="text-md">
                <span v-if="activeStep <= index">{{ index + 1 }}</span>
                <font-awesome-icon v-else :icon="['fas', 'check']"/>
            </span>
        </button>

        <span :class="{'text-blue-600': activeStep >= index, 'text-gray-400': activeStep < index }" class="text-lg px-2">{{ label }}</span>

        <font-awesome-icon :icon="['far', 'chevron-right']" />
    </div>
</template>

<script lang="ts" setup>
    import {FontAwesomeIcon} from "@fortawesome/vue-fontawesome";
    import { defineProps } from 'vue';

    const props = defineProps({
        label: String,
        index: Number,
        activeStep: Number
    });

</script>

<style scoped>
    button {
        width: 40px;
        height: 40px;
    }

    button.passed {
        @apply rounded-full border-blue-500 border text-blue-500 flex items-center justify-center;
    }

    button.active {
        @apply rounded-full border-blue-500 border text-white bg-blue-500 flex items-center justify-center;
    }

    button.coming {
        @apply rounded-full text-gray-400 border-gray-400 border flex items-center justify-center;
    }
</style>
