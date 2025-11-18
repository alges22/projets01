<script lang="ts" setup>
    import {FontAwesomeIcon} from "@fortawesome/vue-fontawesome";
    import { useRegisterStore } from '~/stores/register'

    const store = useRegisterStore()
</script>

<template>
    <span class="text-2xl text-blue-900 font-bold mt-8 text-center">Comment créer son compte ? (1/2) </span>

    <div class="rounded-xl w-1/2 p-10 bg-white mx-auto mt-8 shadow">
        <h4 class="text-gray-500 font-bold text-2xl">Vous devez disposer d'un Numéro d'Identification Personnel (NIP).</h4>

        <ul class="list-disc text-gray-400 mt-8 text-xl">
            <li class="flex items-center space-x-2 my-4">
                <font-awesome-icon class="bg-blue-900 text-white rounded-xl p-1 text-sm" icon="fa fa-check"></font-awesome-icon>
                <span> Assurez-vous d'avoir votre téléphone à portée de main </span>
            </li>
            <li class="flex items-center space-x-2 my-4">
                <font-awesome-icon class="bg-blue-900 text-white rounded-xl p-1 text-sm" icon="fa fa-check"></font-awesome-icon>
                <span>Cliquez sur créer un compte</span>
            </li>
            <li class="flex items-center space-x-2 my-4">
                <font-awesome-icon class="bg-blue-900 text-white rounded-xl p-1 text-sm"
                                   icon="fa fa-check"></font-awesome-icon>
                <span>Remplissez les informations, adrresse e-mail et votre NPI</span>
            </li>
        </ul>
    </div>

    <div class="w-1/2 mx-auto">
        <button class="btn btn-primary w-full" @click="store.nextStep()">Suivant</button>
    </div>
</template>

<style scoped>

</style>