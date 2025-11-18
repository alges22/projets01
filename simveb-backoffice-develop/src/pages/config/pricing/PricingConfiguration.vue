<script setup lang="ts">
	import { storeToRefs } from "pinia";
	import { useCrudStore } from "/@src/stores/modules/crud";
	import { useNotyf } from "/@src/composable/useNotyf";
	import Steps from "/@src/components/steps/Steps.vue";
	import Step from "/@src/components/steps/Step.vue";

	const props = defineProps({
		serviceId: {
			type: String,
			default: null,
		},
	});
	const router = useRouter();
	const notyf = useNotyf();
	const crudStore = useCrudStore();
	const update = ref(false);
	const { url, formLoading, errors, row: service, loading } = storeToRefs(crudStore);

	onBeforeMount(() => {
		url.value = "/services";
		update.value = !!props.serviceId;
	});

	onMounted(() => {
		crudStore.loadCreateData();
	});

	const submit = () => {
		crudStore
			.sendPriceVariation(props.serviceId, { ...data })
			.then((res) => {
				notyf.success("Les prix ont bien été configuré");
				router.push({ name: "services" });
			})
			.finally(() => (disableButton.value = false));
	};
</script>

<template>
	<div>
		<Steps>
			<Step
				v-for="(step, index) in pricingSteps"
				:label="step.label"
				:index="index"
				:key="index"
				:active-step="activeStep"
			/>
		</Steps>

		<CreateFormWrapper :col="12" @submit="submit">
			<template #form-head-inner>
				<div class="left">Variation de prix du service: {{ serviceName }}</div>
				<div>
					<button
						v-if="activeStep > 0"
						data-v-b5b0dfcc=""
						type="button"
						class="button is-circle is-outlined is-primary button mr-2"
						@click="setActiveStep(activeStep - 1)"
					>
						<span data-v-b5b0dfcc="" class="icon">
							<i aria-hidden="true" class="fa-light fa-arrow-left rem-100"></i>
						</span>
					</button>

					<button
						data-v-b5b0dfcc=""
						type="button"
						class="button is-circle is-outlined is-primary button mr-2"
					>
						<span data-v-b5b0dfcc="" class="icon">
							<span>{{ activeStep + 1 }}</span>
						</span>
					</button>

					<button
						v-if="activeStep < lastStep"
						data-v-b5b0dfcc=""
						type="button"
						class="button is-circle is-outlined is-primary button mr-2"
						@click="setActiveStep(activeStep + 1)"
					>
						<span data-v-b5b0dfcc="" class="icon">
							<i aria-hidden="true" class="fa-light fa-arrow-right rem-100"></i>
						</span>
					</button>
				</div>
				<div class="right">
					<div class="buttons">
						<VButton
							v-if="activeStep == lastStep"
							color="primary"
							raised
							tabindex="0"
							type="submit"
							:disabled="disableButton"
						>
							Enregistrer
						</VButton>
					</div>
				</div>
			</template>
			<template #form-body>
				<component :is="pricingSteps[activeStep].content" />
			</template>
		</CreateFormWrapper>
	</div>
</template>

<style lang="scss">
	.form-fieldset {
		width: 60%;
		max-width: 100% !important;
	}
</style>
