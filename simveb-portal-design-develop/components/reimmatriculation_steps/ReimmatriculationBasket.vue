<script lang="ts" setup>
    import {FontAwesomeIcon} from "@fortawesome/vue-fontawesome";

    import Basket from "~/components/Basket.vue";

    const store = useReimmatriculationStore()
</script>

<template>
    <div class="card py-16 px-32 mt-4 text-center">
        <Basket />
    </div>
</template>

<style scoped>

</style>