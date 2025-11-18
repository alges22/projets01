<script lang="ts" setup>
    import {useMutationStore} from '~/stores/mutation'
    import {FontAwesomeIcon} from "@fortawesome/vue-fontawesome";
    import Basket from "~/components/Basket.vue";

    const store = useMutationStore()
</script>

<template>
    <div class="card py-16 px-32 mt-4 text-center">
        <Basket />
    </div>
</template>

<style scoped>

</style>