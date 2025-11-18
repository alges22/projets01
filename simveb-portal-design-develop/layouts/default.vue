<script setup lang="ts">
    import Navbar from "~/components/Navbar.vue";
    import ToolBar from "~/components/ToolBar.vue";

    useHead({
        title: 'SIMVEB - Portail',
        meta: [
            { name: 'description', content: 'My amazing site.' }
        ],
        bodyAttrs: {
            class: 'bg-gray-100'
        },
    })
</script>
<template>
    <ToolBar />

    <img alt="Bannière" class="w-full" src="/banniere.png">

    <Navbar />

    <main class="container mx-auto mb-16">
        <NuxtPage />
    </main>

    <Footer/>
</template>

<style>

    @import url('https://fonts.googleapis.com/css?family=Montserrat:100,100i,200,200i,300,300i,400,400i,500,500i,600,600i,700,700i,800,800i,900,900i');

    body {
        font-family: 'Montserrat', sans-serif;
        margin: 0;
        padding: 0;
    }
</style>