<script setup lang="ts">
	import { useDemandStore } from "/src/stores/modules/demand";
	import { Notyf } from "notyf";
	import { storeToRefs } from "pinia";
	import { userHasPermissions } from "/@src/utils/permission";

	export interface ImmatriculationDemandAffectationViewProps {
		demandId: string;
	}

	const props = withDefaults(defineProps<ImmatriculationDemandAffectationViewProps>(), {
		demandId: undefined,
	});

	const demandStore = useDemandStore();
	const { demand, formData: createData, url } = storeToRefs(demandStore);
	const { can } = userHasPermissions();

	const treatment = ref(null);
	const serviceId = ref({});
	const staffId = ref({});
	const staffAffectationModalState = ref(false);
	const assigning = ref(false);
	const serviceAffectationModalState = ref(false);
	const notyf = new Notyf();

	onMounted(() => {
		if (!demand.value.id) {
			demandStore.fetchDemand(props.demandId).then(() => {
				treatment.value = demand.value.active_treatment;
			});
		} else {
			treatment.value = demand.value.active_treatment;
		}
		if (!createData.value?.services || !createData.value?.staff) {
			demandStore.loadTreatmentCreateData();
		}
	});

	const assignToService = () => {
		assigning.value = true;
		demandStore
			.assignDemandToService({
				service_id: serviceId.value,
				treatment_id: treatment.value.id,
			})
			.then((data) => {
				notyf.success(`Demande assignée au service avec succès`);
				demandStore.fetchDemand(demand.value.id).then(() => {
					treatment.value = demand.value.active_treatment;
				});
				serviceAffectationModalState.value = false;
			})
			.catch(() => {})
			.finally(() => {
				assigning.value = false;
			});
	};

	const assignToStaff = () => {
		assigning.value = true;
		demandStore
			.assignDemandToAgent({
				staff_id: staffId.value,
				treatment_id: treatment.value.id,
			})
			.then((data) => {
				notyf.success(`Demande assigné au staff avec succès`);
				demandStore.fetchDemand(demand.value.id).then(() => {
					treatment.value = demand.value.active_treatment;
				});
				staffAffectationModalState.value = false;
			})
			.catch(() => {})
			.finally(() => {
				assigning.value = false;
			});
	};

	onBeforeMount(() => {
		url.value = "/immatriculation-demands";
	});
</script>

<template>
	<div class="columns tab-details-inner">
		<div class="column is-half">
			<VCard class="side-card">
				<div class="card-head">
					<h4>Affectation au service</h4>
				</div>
				<div v-if="treatment && treatment.assigned_to_service_at" class="card-inner is-size-6-5">
					<div class="columns">
						<div class="column is-on-quarter">
							<span>Nom du service</span>
						</div>
						<div class="column">
							<span>{{ treatment.service?.name }}</span>
						</div>
					</div>
					<div class="columns">
						<div class="column is-on-quarter">
							<span>Date d'affectation</span>
						</div>
						<div class="column">
							<span>{{ treatment.assigned_to_service_at }}</span>
						</div>
					</div>
					<div class="columns">
						<div class="column is-on-quarter">
							<span>Affecter par</span>
						</div>
						<div class="column">
							<span>{{ treatment?.assigned_to_service_by.identity.fullName }}</span>
						</div>
					</div>
				</div>
				<div v-else>
					Cette demande est en attente d'affection à un service
					<div class="columns is-flex is-justify-content-flex-end mt-4">
						<VButton
							v-if="can('affect-to-service-im-demand')"
							color="primary"
							raised
							@click="serviceAffectationModalState = true"
						>
							Affecter à un service
						</VButton>
					</div>
				</div>
			</VCard>
		</div>
		<div class="column">
			<VCard class="side-card">
				<div class="card-head">
					<h4>Assigner à l'agent</h4>
				</div>
				<div v-if="treatment && treatment.assigned_to_staff_at" class="card-inner is-size-6-5">
					<div class="columns">
						<div class="column is-on-quarter">
							<span>Nom de l'agent en charge</span>
						</div>
						<div class="column">
							<span>{{ treatment.responsible?.identity.fullName }}</span>
						</div>
					</div>
					<div class="columns">
						<div class="column is-on-quarter">
							<span>Date d'assignation</span>
						</div>
						<div class="column">
							<span>{{ treatment.assigned_to_staff_at }}</span>
						</div>
					</div>
					<div class="columns">
						<div class="column is-on-quarter">
							<span>Assigner par</span>
						</div>
						<div class="column">
							<span>{{ treatment?.assigned_to_staff_by?.identity.fullName }}</span>
						</div>
					</div>
				</div>
				<div v-else>
					Cette demande est en attente d'assignation à un agent
					<div class="columns is-flex is-justify-content-flex-end mt-4">
						<VButton
							v-if="
								treatment?.assigned_to_service_at &&
								!treatment?.assigned_to_staff_at &&
								can('assign-to-staff-im-demand')
							"
							color="primary"
							raised
							@click="staffAffectationModalState = true"
						>
							Assigner à un agent
						</VButton>
					</div>
				</div>
			</VCard>
		</div>

		<VModal
			v-if="can('affect-to-service-im-demand')"
			:open="serviceAffectationModalState"
			actions="right"
			title="Affectation à un service"
			@close="serviceAffectationModalState = false"
		>
			<template #content>
				<div class="container">
					<VField horizontal>
						<VControl fullwidth>
							<VLabel>Service</VLabel>
							<v-select
								v-model="serviceId"
								:options="createData?.services || []"
								label="name"
								:reduce="(item) => item.id"
							>
							</v-select>
						</VControl>
					</VField>
				</div>
			</template>
			<template #action>
				<VButton :loading="assigning" :disabled="assigning" color="primary" raised @click="assignToService">
					Confirmer
				</VButton>
			</template>
		</VModal>
		<VModal
			v-if="can('assign-to-staff-im-demand')"
			:open="staffAffectationModalState"
			actions="right"
			title="Affectation à un agent"
			@close="staffAffectationModalState = false"
		>
			<template #content>
				<div class="container">
					<VField horizontal>
						<VControl fullwidth>
							<VLabel>Agent</VLabel>
							<v-select
								v-model="staffId"
								:options="createData?.staff || []"
								label="fullName"
								:reduce="(item) => item.id"
							>
							</v-select>
						</VControl>
					</VField>
				</div>
			</template>
			<template #action>
				<VButton :disabled="assigning" :loading="assigning" color="primary" raised @click="assignToStaff">
					Confirmer
				</VButton>
			</template>
		</VModal>
	</div>
</template>
