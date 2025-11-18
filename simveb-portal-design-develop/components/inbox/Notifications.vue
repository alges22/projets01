<script setup lang="ts">
    import {FontAwesomeIcon} from "@fortawesome/vue-fontawesome";
</script>

<template>
    <div class="flex items-center px-4 py-4 border rounded-xl mb-4">
        <font-awesome-icon :icon="['far', 'magnifying-glass']" size="xl" style="color: #aaaeb5;" />
        <input type="text" placeholder="Rechercher" class="flex-grow px-4 outline-none" />
    </div>

    <div class="notification-item">
        <div>
            <div class="icon-container"></div>
        </div>
        <div class="w-3/5 flex flex-col ms-4">
            <span class="text-red-500">Alerte routière</span>

            <span
                class="text-gray-400 mt-2">Consultez votre compte pour les détails de l'infraction</span>
        </div>
        <div class="ms-auto items-center flex">
            <span class="text-gray-500">14:33</span>
        </div>
    </div>
</template>

<style scoped lang="scss">
    .notification-item {
        line-height: 1.2;
        display: flex;
        flex-direction: row;
        border-bottom: 0.5px solid #ece8e8;
        width: 100%;
        padding-bottom: 10px;
        padding-top: 10px;
    }

    .icon-container {
        width: 50px;
        height: 50px;
        background-color: #f3f0f0;
        border-radius: 50%;
    }
</style>