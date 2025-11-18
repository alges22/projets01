<script setup lang="ts">
	import { useImmatriculationStore } from '~/stores/immatriculation'
	import Steps from "~/components/steps/Steps.vue";
	import Step from "~/components/steps/Step.vue";
	import Base from "~/components/immatriculation_steps/Base.vue";
	import Informations from "~/components/immatriculation_steps/Informations.vue";
	import Plate from "~/components/immatriculation_steps/Plate.vue";
	import Attachments from "~/components/immatriculation_steps/Attachments.vue";
	import Resume from "~/components/immatriculation_steps/Resume.vue";
	import ImmatriculationBasket from "~/components/immatriculation_steps/ImmatriculationBasket.vue";
	import {useBannerStore} from "~/stores/banner";
	import Label from "~/components/immatriculation_steps/Label.vue";
	import Number from "~/components/immatriculation_steps/Number.vue";

	definePageMeta({
		layout: "without-navbar"
	})

	useHead({
		title: 'SIMVEB - Demande d\'immatriculation de prestige label'
	})

	const bannerStore = useBannerStore()
	bannerStore.setTitle("Demande d'immatriculation de prestige numéro et label")
	bannerStore.setSubTitle("Remplissez facilement les informations nécessaires et bénéficiez d'un processus d'immatriculation rapide et efficace")

	const store = useImmatriculationStore()
	const { activeStep, loading } = storeToRefs(store)

	const route = useRoute();

	const id = route.params.id

	const steps = [
		{
			'label' : 'Base',
			'content' : Base
		},
		{
			'label' : 'Informations',
			'content': Informations
		},
		{
			'label' : 'Numéro',
			'content': Number
		},
		{
			'label' : 'Label',
			'content': Label
		},
		{
			'label' : 'Plaque',
			'content' : Plate
		},
		{
			'label' : 'Pièces jointes',
			'content' : Attachments
		},
		{
			'label' : 'Récapitualif',
			'content' : Resume
		},
		{
			'label' : 'Panier',
			'content' : ImmatriculationBasket
		},
	]

	onMounted(() => {
		if (!store.create){
			store.loadCreate(id)
		}
	})

	onUnmounted(() => {
		store.resetImmatriculation()
	})
</script>

<template>
	<div v-if="loading" class="flex justify-center mt-16">
		<Loader />
	</div>
	<template v-else>
		<Steps>
			<Step v-for="(step, index) in steps" :label="step.label" :index="index" :activeStep="activeStep" />
		</Steps>

		<component :is="steps[activeStep].content" />
	</template>
</template>

<style scoped>
	button{
		width: 10px;
		height: 10px;
	}
</style>