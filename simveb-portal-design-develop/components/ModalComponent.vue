<script setup>
    const props = defineProps({
        open: Boolean
    });

    import {FontAwesomeIcon} from "@fortawesome/vue-fontawesome";
</script>

<template>
    <div v-show="open" class="modal fixed w-full h-full top-0 left-0 flex items-center justify-center">
        <div class="modal-overlay absolute w-full h-full bg-black opacity-90"></div>

        <div class="modal-container bg-white w-11/12 md:w-2/5 mx-auto rounded-xl shadow-lg z-50 overflow-y-auto">
            <div class="modal-close absolute top-0 right-0 cursor-pointer flex flex-col items-center mt-4 mr-4 text-white text-sm z-50">
                <span class="text-xl" @click="$emit('close')"> <font-awesome-icon icon="fa fa-times"></font-awesome-icon> Fermer</span>
            </div>

            <div class="modal-content" style="max-height: 100vh; /* Adjust as needed */ overflow-y: auto;">
                <slot/>
            </div>
        </div>
    </div>
</template>

<style scoped>
    .modal {
        transition: opacity 0.25s ease;
    }

    body.modal-active {
        overflow-x: hidden;
        overflow-y: visible !important;
    }
</style>