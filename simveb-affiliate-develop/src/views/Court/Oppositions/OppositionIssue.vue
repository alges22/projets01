<script setup>
    import {onMounted} from "vue"
    import {useStepper} from "@vueuse/core";
    import Issue from "@/views/Court/Oppositions/Steps/Issue.vue";
    import Vehicles from "@/views/Court/Oppositions/Steps/Vehicles.vue";
    import Reason from "@/views/Court/Oppositions/Steps/Reason.vue";
    import {useOppositionStore} from "@/stores/opposition.js";
    import Attachments from "@/views/Court/Oppositions/Steps/Attachments.vue";

    const stepper = useStepper({
        issue: {
            title: "Recherche de véhicule",
            isValid: true,
        },
        vehicles: {
            title: "Sélectionnez le véhicule concerné",
        },
        reason: {
            title: "Sélectionnez le motif de l'opposition",
        },
        opposition_files: {
            title: "Pièces jointes",
        }
    });

    const oppositionStore = useOppositionStore()

    onMounted(() => {
        oppositionStore.loadCreate()
    })
</script>

<template>
    <Issue
        v-if="stepper.isCurrent('issue')"
        @next="stepper.goToNext()"
        @prev="$router.back()"
    />

    <Vehicles
        v-if="stepper.isCurrent('vehicles')"
        @next="stepper.goToNext()"
        @prev="stepper.goToPrevious"
    />

    <Reason
        v-if="stepper.isCurrent('reason')"
        @next="stepper.goToNext()"
        @prev="stepper.goToPrevious"
    />

    <Attachments
        v-if="stepper.isCurrent('opposition_files')"
        @next="stepper.goToNext()"
        @prev="stepper.goToPrevious"
    />
</template>

<style lang="scss" scoped>

</style>