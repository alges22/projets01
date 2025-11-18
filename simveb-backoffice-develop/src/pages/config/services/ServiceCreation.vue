<template>
	<div>
		<CreateFormWrapper :col="12" @submit="submit">
			<template #form-head-inner>
				<div class="block m-auto">
					<nav class="-mb-px flex space-x-8 m-auto" aria-label="Tabs">
						<button
							v-for="(step, id, index) in stepper.steps.value"
							:key="index"
							:class="{
								'text-primary-600 border-primary-500': stepper.isCurrent(id),
								'text-dark hover:text-primary-700 hover:border-primary-500': stepper.isAfter(id),
								'border-transparent text-gray-500 ': stepper.isBefore(id),
								'whitespace-nowrap py-4 px-1 border-b-2 font-medium text-lg': true,
							}"
							:disabled="stepper.isCurrent(id) || stepper.isBefore(id)"
							@click="stepper.goTo(id)"
						>
							{{ step.title }}
						</button>
					</nav>
				</div>
			</template>
			<template #form-body>
				<CustomizationStep v-if="stepper.isCurrent('personnalisation')" @next="stepper.goTo('procedure')" />

				<ProceduralInfoStep
					v-else-if="stepper.isCurrent('procedure')"
					@next="stepper.goTo('relation')"
					@prev="stepper.goTo('personnalisation')"
				/>

				<RelationalInfoStep
					v-else-if="stepper.isCurrent('relation')"
					@next="stepper.goTo('steps')"
					@prev="stepper.goTo('procedure')"
				/>

				<ServiceSteps
					v-else-if="stepper.isCurrent('steps')"
					:update="update"
					@next="stepper.goTo('others')"
					@prev="stepper.goTo('relation')"
				/>

				<OtherStep v-else-if="stepper.isCurrent('others')" @next="submit" @prev="stepper.goTo('steps')" />
			</template>
		</CreateFormWrapper>
	</div>
</template>

<script setup lang="ts">
	import { storeToRefs } from "pinia";
	import { useCrudStore } from "/@src/stores/modules/crud";
	import { useNotyf } from "/@src/composable/useNotyf";
	import CustomizationStep from "/@src/pages/config/services/steps/CustomizationStep.vue";
	import ProceduralInfoStep from "/@src/pages/config/services/steps/ProceduralInfoStep.vue";
	import RelationalInfoStep from "/@src/pages/config/services/steps/RelationalInfoStep.vue";
	import ServiceSteps from "/@src/pages/config/services/steps/ServiceSteps.vue";
	import OtherStep from "/@src/pages/config/services/steps/OtherStep.vue";

	const props = defineProps({
		serviceId: {
			type: String,
			default: null,
		},
	});
	const router = useRouter();
	const notyf = useNotyf();
	const crudStore = useCrudStore();
	const { url, row: service } = storeToRefs(crudStore);

	const update = ref(false);
	const stepper = useStepper({
		personnalisation: {
			title: "Personnalisation",
		},
		procedure: {
			title: "Informations procédurales",
		},
		relation: {
			title: "Informations relationnelles",
		},
		steps: {
			title: "Les étapes",
		},
		others: {
			title: "Autres",
		},
	});

	onBeforeMount(() => {
		url.value = "/services";
		if (props.serviceId) {
			update.value = true;
		}
	});

	const submit = () => {
		if (update.value) {
			crudStore.updateWithFile(props.serviceId, service.value).then(() => {
				notyf.success("Le service a bien été modifié");
				router.push({ name: "services" });
			});
		} else {
			crudStore.createWithFile(service.value).then(() => {
				notyf.success("Le service a bien été crée");
				router.push({ name: "services" });
			});
		}
	};

	onMounted(async () => {
		await crudStore.loadCreateData();
		if (props.serviceId) {
			await crudStore.fetchRow(props.serviceId).then((res) => {
				service.value = {
					...res,
					target_organization_id: res.organization?.id,
					type_id: res.type?.id,
					type: null,
					organization: null,
					documents: res.documents.map((d) => d.id),
					children: res.children.map((c) => c.id),
				};
			});
		}
	});
</script>

<style lang="scss">
	.form-fieldset {
		width: 60%;
		max-width: 100% !important;
	}
</style>
