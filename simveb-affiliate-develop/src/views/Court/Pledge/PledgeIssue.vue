<script setup>
	import { useStepper } from "@vueuse/core";
	import Issue from "@/views/Court/Pledge/Steps/Issue.vue";
	import Recap from "@/views/Court/Pledge/Steps/Recap.vue";
	import PledgeFile from "@/views/Court/Pledge/Steps/PledgeFile.vue";
    import {onMounted} from "vue";
    import {usePledgeStore} from "@/stores/pledge.js";

	const stepper = useStepper({
		issue: {
			title: "Émettre un gage",
			isValid: true,
		},
		recap: {
			title: "Récapitulatif",
		},
		pledge_file: {
			title: "Dossier de gage",
		},
	});

    const pledgeStore = usePledgeStore()

    onMounted(() => {
        pledgeStore.loadCreate()
    })
</script>

<template>
	<Issue v-if="stepper.isCurrent('issue')" @prev="$router.back()" @next="stepper.goToNext()" />

	<Recap v-if="stepper.isCurrent('recap')" @prev="stepper.goToPrevious" @next="stepper.goToNext()" />

	<PledgeFile v-if="stepper.isCurrent('pledge_file')" @prev="stepper.goToPrevious" @next="stepper.goToNext()" />
</template>

<style scoped lang="scss"></style>
