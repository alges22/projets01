<script setup lang="ts">
import {useBannerStore} from "~/stores/banner";

const bannerStore = useBannerStore()

const { title, subTitle } = storeToRefs(bannerStore)
</script>

<template>
    <div class="banner">
        <div class="container flex flex-col py-4 sm:py-20 md:px-32 px-4 md:w-1/2 sm:w-full">
            <h4 class="text-white text-3xl font-bold uppercase">{{ title }}</h4>

            <h4 class="text-white text-lg mt-4"> {{ subTitle }} </h4>
        </div>
    </div>
</template>

<style scoped>
.banner{
/*background-image: linear-gradient(to right, #023B8E, rgba(0,0,0,0.5));*/
    background-image: linear-gradient(to right, #023B8E 0%, #023B8E 40%, rgba(0,0,0,0.5) 100%);
    width: 100%;
    background-size: cover; /* or contain, depending on your preference */
    background-repeat: no-repeat;
    background-position: center;
    position: relative;
}

/* Add a pseudo-element for the background image */
.banner::before {
    content: "";
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-image: url("/banner_image.jpg");
    background-size: cover; /* or contain, depending on your preference */
    background-repeat: no-repeat;
    background-position: center;
    z-index: -1; /* Ensure the image is behind the gradient */
}
</style>