<script setup lang="ts">
    definePageMeta({
        layout: "auth"
    })

    const authStore = useAuthStore()

    onBeforeMount(() => {
        authStore.logUserOut()
    })
</script>

<template>

</template>

<style scoped lang="scss">

</style>