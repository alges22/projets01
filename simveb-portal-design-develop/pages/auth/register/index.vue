<script setup lang="ts">
    definePageMeta({
        layout: "auth"
    })

    useHead({
        title: 'SIMVEB - Inscription',
    })
</script>


<template>
    <div class="flex flex-col">
        <span class="text-4xl text-blue-900 font-bold mt-8 text-center">Inscription</span>

        <div class="rounded-xl md:w-2/3 lg:w-2/3 p-10 bg-white mx-auto mt-8 shadow text-center">
            <h4 class="text-gray-500 font-bold text-xl">Inscrivez - vous en tant que ?</h4>
            <div class="mt-8 grid grid-cols-1 md:grid-cols-2 gap-2 md:gap-8">
                <RouterLink to="/auth/register/personne" class="px-8 py-4 rounded-md text-primary font-bold border-2 hover:border-blue-900">
                    Personne physique
                </RouterLink>

                <RouterLink to="/auth/register/entreprise" class="px-8 py-4 rounded-md text-primary font-bold border-2 hover:border-blue-900">
                    Personne morale
                </RouterLink>
            </div>
        </div>
    </div>
</template>


<style scoped>

</style>