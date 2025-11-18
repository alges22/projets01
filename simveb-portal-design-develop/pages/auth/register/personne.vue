<script setup lang="ts">
    definePageMeta({
        layout: "auth"
    })

    useHead({
        title: 'SIMVEB - Inscription',
    })

    import HowToRegister2 from "~/components/register_steps/HowToRegister2.vue";
    import HowToRegister1 from "~/components/register_steps/HowToRegister1.vue";
    import Register from "~/components/register_steps/Register.vue";
    import ProcessVerification from "~/components/register_steps/ProcessVerification.vue";
    import InformationsConfirm from "~/components/register_steps/InformationsConfirm.vue";

    import { useRegisterStore } from '~/stores/register'

    const store = useRegisterStore()
    const { activeStep } = storeToRefs(store)

    // const steps = [HowToRegister1, HowToRegister2, Register, ProcessVerification, InformationsConfirm]
    const steps = [Register, ProcessVerification, InformationsConfirm]
</script>


<template>
    <div class="flex flex-col">
        <component :is="steps[activeStep]" />

        <div class="w-1/2 mx-auto">
            <div class="flex justify-center mt-8">
                <!-- Progress Indicator -->
                <div class="flex gap-2">
                    <!-- Generate circles for each step -->
                    <template v-for="(step, index) in steps" :key="index">
                        <div :class="['w-5 h-5 rounded-full', { 'bg-blue-900': index <= activeStep, 'bg-gray-300': index > activeStep }]"></div>
                        <!-- Add a divider if this is not the last step -->
                        <div v-if="index < steps.length - 1" class="flex-grow h-0.5 bg-gray-300"></div>
                    </template>
                </div>
            </div>
        </div>
    </div>
</template>


<style lang="sass">

</style>