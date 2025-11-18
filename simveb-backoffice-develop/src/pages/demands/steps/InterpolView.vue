<script setup lang="ts">
	import { useDemandStore } from "/src/stores/modules/demand";
	import { storeToRefs } from "pinia";
	import { Notyf } from "notyf";
	import { userHasPermissions } from "/@src/utils/permission";

	export interface ImmatriculationDemandInterpolViewProps {
		demandId: string;
	}

	const props = withDefaults(defineProps<ImmatriculationDemandInterpolViewProps>(), {
		demandId: undefined,
	});

	const demandStore = useDemandStore();
	const { demand, formLoading, url } = storeToRefs(demandStore);
	const { can } = userHasPermissions();

	const treatment = ref(null);
	const createData = ref({});
	const serviceId = ref({});
	const staffId = ref({});
	const interpolAffectationModalState = ref(false);
	const staffAffectationModalState = ref(false);
	const notyf = new Notyf();

	onMounted(() => {
		if (!demand.value.id) {
			demandStore.getDemand(props.demandId).then((res) => {
				treatment.value = demand.value.active_treatment;
			});
		} else {
			treatment.value = demand.value.active_treatment;
		}
		demandStore.loadTreatmentCreateData().then((data) => {
			createData.value = data;
		});
	});

	const assignToService = () => {
		demandStore
			.assignDemandToService({ service_id: serviceId.value, treatment_id: treatment.value.id }, true)
			.then((data) => {
				notyf.success(`Demande assigné à interpol avec succès`);
				demandStore.fetchDemand(demand.value.id).then(() => {
					treatment.value = demand.value.active_treatment;
				});
				interpolAffectationModalState.value = false;
			})
			.catch(() => {});
	};

	const assignToStaff = () => {
		demandStore
			.assignDemandToAgent({ staff_id: staffId.value, treatment_id: treatment.value.id }, true)
			.then((data) => {
				notyf.success(`Demande assigné à l'agent d'interpole avec succès`);
				demandStore.fetchDemand(demand.value.id).then(() => {
					treatment.value = demand.value.active_treatment;
				});
				staffAffectationModalState.value = false;
			})
			.catch(() => {})
			.finally(() => {});
	};

	const validateDemand = (approved: boolean) => {
		Swal.fire({
			title: "Confirmation",
			text: approved
				? "Êtes vous sûr(e) de vouloir valider cette demande?"
				: "Êtes vous sûr(e) de vouloir rejeter cette demande?",
			icon: "warning",
			confirmButtonText: "Oui, Valider",
			showCancelButton: true,
			cancelButtonText: "Annuler",
			input: approved ? undefined : "text",
			inputPlaceholder: "Raison du rejet",
			inputValidator: (value?: String) => {
				if (!value) {
					return "Vous devez fournir une raison pour le rejet";
				}
			},
		}).then((value) => {
			if (value.isConfirmed) {
				demandStore
					.validateDemandInterpol(demand.value.active_treatment_id, approved, value.value)
					.then((data) => {
						notyf.success(approved ? "Demande validé avec succès !" : "Demande rejeté avec succès !");
						demandStore.fetchDemand(demand.value.id).then(() => {
							treatment.value = demand.value.active_treatment;
						});
					})
					.catch(() => {});
			}
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
					<h4>Interpol</h4>
				</div>
				<div v-if="treatment && treatment.affected_to_interpol_at" class="card-inner is-size-6-5">
					<div class="columns">
						<div class="column is-on-quarter">
							<span>Date de passage à cette étape</span>
						</div>
						<div class="column">
							<span>{{ treatment.affected_to_interpol_at }}</span>
						</div>
					</div>
					<div class="columns">
						<div class="column is-on-quarter">
							<span>Date de contrôle</span>
						</div>
						<div class="column">
							<span>{{ treatment.interpol_validated_at ?? treatment.interpol_pre_validated_at }}</span>
						</div>
					</div>
					<div class="columns">
						<div class="column is-on-quarter">
							<span>Contrôler par</span>
						</div>
						<div class="column">
							<span>{{
								treatment.interpol_validated_at
									? treatment.interpol_validated_by
									: treatment.interpol_validated_by
							}}</span>
						</div>
					</div>

					<div
						v-if="!treatment.interpol_validated_at"
						class="columns is-flex is-justify-content-flex-end mt-4"
					>
						<VButton
							v-if="!treatment.interpol_pre_validated_at && can('interpol-pre-validate-im-demand')"
							color="success"
							:loading="formLoading"
							size="medium"
							raised
							@click="validateDemand(true)"
						>
							Pré-valider
						</VButton>
						&nbsp;
						<VButton
							v-else-if="can('interpol-validate-im-demand')"
							:loading="formLoading"
							color="success"
							size="medium"
							raised
							@click="validateDemand(true)"
						>
							Valider
						</VButton>
						&nbsp;
						<VButton
							v-if="can('reject-interpol-im-demand')"
							:loading="formLoading"
							color="danger"
							size="medium"
							raised
							@click="validateDemand(false)"
						>
							Rejeter
						</VButton>
					</div>
				</div>
				<div v-else>
					Cette demande est en attente d'affection à interpol
					<div class="columns is-flex is-justify-content-flex-end mt-4">
						<VButton
							v-if="can('affect-to-interpol-im-demand')"
							color="primary"
							raised
							@click="interpolAffectationModalState = true"
						>
							Affecter à un interpol
						</VButton>
					</div>
				</div>
			</VCard>
		</div>

		<div class="column">
			<VCard class="side-card">
				<div class="card-head">
					<h4>Assigner à l'agent d'interpole</h4>
				</div>
				<div v-if="treatment && treatment.assigned_to_interpol_staff_at" class="card-inner is-size-6-5">
					<div class="columns">
						<div class="column is-on-quarter">
							<span>Nom de l'agent en charge</span>
						</div>
						<div class="column">
							<span>{{ treatment.interpol_staff?.identity.fullName }}</span>
						</div>
					</div>
					<div class="columns">
						<div class="column is-on-quarter">
							<span>Date d'assignation</span>
						</div>
						<div class="column">
							<span>{{ treatment.assigned_to_interpol_staff_at }}</span>
						</div>
					</div>
					<div class="columns">
						<div class="column is-on-quarter">
							<span>Assigner par</span>
						</div>
						<div class="column">
							<span>{{ treatment?.assigned_to_interpol_staff_by?.identity?.fullName }}</span>
						</div>
					</div>
				</div>
				<div v-else>
					Cette demande est en attente d'assignation à un agent d'interpole
					<div class="columns is-flex is-justify-content-flex-end mt-4">
						<VButton
							v-if="
								treatment?.affected_to_interpol_at &&
								!treatment?.assigned_to_interpol_staff_at &&
								can('assign-to-interpol-im-demand')
							"
							color="primary"
							raised
							@click="staffAffectationModalState = true"
						>
							Assigner à un agent d'interpole
						</VButton>
					</div>
				</div>
			</VCard>
		</div>

		<VModal
			v-if="can('affect-to-interpol-im-demand')"
			:open="interpolAffectationModalState"
			actions="right"
			title="Affectation à un service"
			@close="interpolAffectationModalState = false"
		>
			<template #content>
				<div class="container">
					<VField horizontal>
						<VControl fullwidth>
							<VLabel>Service</VLabel>
							<v-select
								v-model="serviceId"
								:options="createData.services"
								label="name"
								:reduce="(item) => item.id"
							>
							</v-select>
						</VControl>
					</VField>
				</div>
			</template>
			<template #action>
				<VButton :loading="formLoading" :disabled="formLoading" color="primary" raised @click="assignToService">
					Confirmer
				</VButton>
			</template>
		</VModal>

		<VModal
			v-if="can('assign-to-interpol-im-demand')"
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
								:options="createData.staff"
								label="fullName"
								:reduce="(item) => item.id"
							>
							</v-select>
						</VControl>
					</VField>
				</div>
			</template>
			<template #action>
				<VButton :disabled="formLoading" :loading="formLoading" color="primary" raised @click="assignToStaff">
					Confirmer
				</VButton>
			</template>
		</VModal>
	</div>
</template>
