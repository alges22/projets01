<script lang="ts" setup>
    useHead({
        title: 'SIMVEB - Connexion',
        meta: [
            {name: 'description', content: 'My amazing site.'}
        ]
    })
</script>

<template>
    <div class="flex flex-row" style="min-height: 100vh">
        <div class="w-5/12 bg-blue-900 px-32 hidden xl:block">
            <div class="text-6xl text-white font-bold text-center mt-15vh">SIMVeB</div>
            <div class="text-white text-center mt-8 text-2xl font-light">Système intégré d'immatriculation et
                d'identification des véhicules en circulation au Bénin
            </div>
        </div>
        <div class="w-full xl:w-7/12 bg-gray-100">
            <div class="flex-col flex mt-5vh">
                <div class="flex justify-center">
                    <RouterLink to="/">
                        <img alt="ANaTT" src="/logo_anatt.png" style="width: 350px">
                    </RouterLink>
                </div>

                <NuxtPage />
            </div>
        </div>
    </div>
</template>

<style>
    @import url('https://fonts.googleapis.com/css?family=Montserrat:100,100i,200,200i,300,300i,400,400i,500,500i,600,600i,700,700i,800,800i,900,900i');

    body {
        font-family: 'Montserrat', sans-serif;
        margin: 0;
        padding: 0;
    }
</style>