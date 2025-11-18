<template>
    <div class="w-full flex flex-wrap">
        <slot :activeStep="activeStep"></slot>
    </div>
</template>

<script setup lang="ts">
    const props = defineProps({
        activeStep: Number
    });
</script>

<style scoped>

</style>