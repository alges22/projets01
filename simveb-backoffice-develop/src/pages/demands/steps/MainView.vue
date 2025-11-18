<script setup lang="ts">
	import { useDemandStore } from "/src/stores/modules/demand";
	import Swal from "sweetalert2/dist/sweetalert2.js";
	import { Notyf } from "notyf";
	import { storeToRefs } from "pinia";
	import { userHasPermissions } from "/@src/utils/permission";

	export interface ImmatriculationDemandMainViewProps {
		demandId: string;
	}

	const props = withDefaults(defineProps<ImmatriculationDemandMainViewProps>(), {
		demandId: undefined,
	});
	const notyf = new Notyf();

	const demandStore = useDemandStore();
	const { demand, url, formLoading } = storeToRefs(demandStore);
	const { can } = userHasPermissions();

	const reference = ref("");
	const treatment = ref(null);
	const { text, copy, copied } = useClipboard();
	const vehicleOwner = ref({});
	const vehicle = ref({});
	const vehicleType = ref({});
	const vehicleBrand = ref({});
	const vehicleEnergyType = ref({});
	const vehicleOriginCountry = ref({});
	const vehicleCategoryType = ref({});
	const plateColor = ref({});
	const legalStatus = ref({});
	const ownerState = ref({});
	const ownerType = ref({});
	const ownerNationality = ref({});
	const submitting = ref(false);

	onMounted(async () => {
		if (!demand.value.id) {
			await demandStore.getDemand(props.demandId);
		}
		treatment.value = demand.value.active_treatment;
		reference.value = demand.value.reference;
		vehicleOwner.value = demand.value.vehicle_owner;
		vehicle.value = demand.value.vehicle;
		vehicleType.value = demand.value.vehicle?.vehicle_type;
		vehicleBrand.value = demand.value.vehicle?.brand;
		vehicleEnergyType.value = demand.value.vehicle?.energy_type;
		vehicleOriginCountry.value = demand.value.vehicle?.origin_country;
		vehicleCategoryType.value = demand.value.vehicle?.category;
		plateColor.value = demand.value?.plate_color;
		legalStatus.value = demand.value.vehicle_owner?.legal_status;
		ownerState.value = demand.value.vehicle_owner?.state;
		ownerType.value = demand.value.vehicle_owner?.owner_type;
		ownerNationality.value = demand.value.vehicle_owner?.nationality;
	});

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
			inputValidator: (value) => {
				if (!value) {
					return "Vous devez fournir une raison pour le rejet";
				}
			},
		}).then((value) => {
			submitting.value = true;
			if (value.isConfirmed) {
				demandStore
					.validateDemand(demand.value.active_treatment_id, approved, value.value)
					.then(() => {
						notyf.success(approved ? "Demande validé avec succès !" : "Demande rejeté avec succès !");
						demandStore.fetchDemand(demand.value.id).then(() => {
							treatment.value = demand.value.active_treatment;
						});
					})
					.catch(() => {})
					.finally(() => {
						submitting.value = false;
					});
			}
		});
	};

	const verifyDemand = (approved: boolean) => {
		Swal.fire({
			title: "Confirmation",
			text: approved
				? "Êtes vous sûr(e) de vouloir vérifier cette demande?"
				: "Êtes vous sûr(e) de vouloir rejeter cette demande?",
			icon: "warning",
			confirmButtonText: "Oui, Vérifier",
			showCancelButton: true,
			cancelButtonText: "Annuler",
			input: approved ? undefined : "text",
			inputPlaceholder: "Raison du rejet",
			inputValidator: (value) => {
				if (!value) {
					return "Vous devez fournir une raison pour le rejet";
				}
			},
		}).then((value) => {
			submitting.value = true;
			if (value.isConfirmed) {
				demandStore
					.verifyDemand(demand.value.active_treatment_id, approved, value.value)
					.then((data) => {
						notyf.success(approved ? "Demande vérifié avec succès !" : "Demande suspendu avec succès !");
						demandStore.fetchDemand(demand.value.id).then(() => {
							treatment.value = demand.value.active_treatment;
						});
					})
					.catch(() => {})
					.finally(() => {
						submitting.value = false;
					});
			}
		});
	};

	onBeforeMount(() => {
		url.value = "/immatriculation-demands";
	});
</script>

<template>
	<div v-if="demand" class="columns tab-details-inner">
		<div class="column is-8">
			<VCard class="side-card">
				<div class="card-head">
					<h4>Information sur la demande</h4>
				</div>
				<div class="card-inner is-size-6-5">
					<div class="columns">
						<div class="column is-one-quarter">
							<span class="has-text-weight-semibold">Référence</span>
						</div>
						<div class="column is-flex is-justify-content-space-between">
							<div>
								<span>{{ reference }}</span>
							</div>
							<VButton
								v-tooltip.primary.rounded="'Copier'"
								tabindex="0"
								class="has-text-primary"
								color="white"
								@keydown.space.prevent="copy(reference)"
								@click="copy(reference)"
							>
								<Transition name="fade-fast" mode="out-in">
									<span v-if="copied && text === reference" class="is-copied">
										<i class="iconify" data-icon="check" aria-hidden="true" color="primary"></i>
									</span>
									<span v-else>
										<i class="iconify" data-icon="copy" aria-hidden="true"></i>
									</span>
								</Transition>
							</VButton>
						</div>
					</div>
					<div class="columns">
						<div class="column is-one-quarter">
							<span class="has-text-weight-semibold">Date de création</span>
						</div>
						<div class="column">
							<span class="">
								{{ demand.created_at }}
							</span>
						</div>
					</div>
					<div class="columns">
						<div class="column is-one-quarter">
							<span class="has-text-weight-semibold">Type de véhicule</span>
						</div>
						<div class="column">
							<span>{{ vehicleType?.label }}</span>
						</div>
					</div>
					<div class="columns">
						<div class="column is-one-quarter">
							<span class="has-text-weight-semibold">Couleur de plaque</span>
						</div>
						<div class="flex-row">
							<div :style="{ backgroundColor: plateColor?.color_code }" class="color-box"></div>
						</div>
					</div>

					<div v-if="treatment" class="columns is-flex is-justify-content-flex-end mt-4">
						<template v-if="!treatment.verified_at">
							<VButton
								v-if="can('verify-im-demand')"
								:loading="formLoading"
								color="primary"
								size="medium"
								raised
								@click="verifyDemand(true)"
							>
								<i class="iconify ms-2" data-icon="check"></i>
								Vérifier
							</VButton>
							&nbsp;
							<VButton
								v-if="can('verify-im-demand')"
								color="warning"
								:loading="formLoading"
								size="medium"
								raised
								@click="verifyDemand(false)"
							>
								Suspendre
							</VButton>
							&nbsp;
						</template>
					</div>
				</div>
			</VCard>
		</div>
	</div>
</template>

<style lang="scss">
	.color-box {
		width: 35px;
		height: 35px;
		border-radius: 15%;
		margin-left: 10px;
	}
</style>
