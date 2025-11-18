<script setup lang="ts">
	import Swal from "sweetalert2/dist/sweetalert2.js";
	import { Notyf } from "notyf";
	import { storeToRefs } from "pinia";
	import { useDemandStore } from "/src/stores/modules/demand";
	import { userHasPermissions } from "/@src/utils/permission";

	export interface ImmatriculationDemandMainViewProps {
		demandId: string;
	}

	const props = withDefaults(defineProps<ImmatriculationDemandMainViewProps>(), {
		demandId: undefined,
	});
	const notyf = new Notyf();

	const demandStore = useDemandStore();
	const { demand, url } = storeToRefs(demandStore);
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
			await demandStore.fetchDemand(props.demandId);
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
					.validateDemand(demand.value.active_treatment.id, approved, value.value)
					.then((data) => {
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

	onBeforeMount(() => {
		url.value = "gray-card-duplicates";
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
										<i class="iconify" data-icon="check" aria-hidden="true"></i>
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

					<div v-if="treatment" class="columns is-flex is-justify-content-flex-end mt-4">
						<template v-if="!treatment.pre_validated_at || !treatment.validated_at">
							<VButton
								v-if="!treatment.pre_validated_at && can('pre-validate-card-duplicate')"
								color="success"
								size="medium"
								raised
								@click="validateDemand(true)"
							>
								Pré-valider
							</VButton>
							&nbsp;
							<VButton
								v-if="!treatment.validated_at && can('validate-card-duplicate')"
								color="success"
								size="medium"
								raised
								@click="validateDemand(true)"
							>
								Valider
							</VButton>
							&nbsp;
							<VButton
								v-if="
									(!treatment.validated_at || !treatment.pre_validated_at) &&
									(can('pre-validate-card-duplicate') || can('validate-card-duplicate'))
								"
								color="danger"
								size="medium"
								raised
								@click="validateDemand(false)"
							>
								Rejeter
							</VButton>
						</template>
					</div>
				</div>
			</VCard>
		</div>

		<div class="column is-4">
			<div class="side-card">
				<h4>Demandeur</h4>

				<VBlock center title="Nom Complet" :subtitle="vehicleOwner.lastname + ' ' + vehicleOwner.firstname">
					<template #icon>
						<VIconBox size="small" color="primary" rounded bordered>
							<i class="iconify" data-icon="user"></i>
						</VIconBox>
					</template>
				</VBlock>

				<VBlock center title="Email" :subtitle="vehicleOwner.email">
					<template #icon>
						<VIconBox size="small" color="primary" rounded bordered>
							<i class="iconify" data-icon="mail"></i>
						</VIconBox>
					</template>
				</VBlock>

				<VBlock center title="Téléphone" :subtitle="vehicleOwner.telephone">
					<template #icon>
						<VIconBox size="small" color="primary" rounded bordered>
							<i class="iconify" data-icon="phone"></i>
						</VIconBox>
					</template>
				</VBlock>
				<VBlock center title="IFU" :subtitle="vehicleOwner.ifu">
					<template #icon>
						<VIconBox size="small" color="primary" rounded bordered>
							<i class="iconify" data-icon="align-justify"></i>
						</VIconBox>
					</template>
				</VBlock>
				<VBlock center title="NPI" :subtitle="vehicleOwner?.npi">
					<template #icon>
						<VIconBox size="small" color="primary" rounded bordered>
							<i class="iconify" data-icon="info"></i>
						</VIconBox>
					</template>
				</VBlock>
				<VBlock center title="Statut Légal" :subtitle="legalStatus.name">
					<template #icon>
						<VIconBox size="small" color="primary" rounded bordered>
							<i class="iconify" data-icon="arcticons:company-portal"></i>
						</VIconBox>
					</template>
				</VBlock>
				<div class="mt-5 pt-2">
					<VBlock center title="Date de naissance" :subtitle="vehicleOwner.birthdate">
						<template #icon>
							<VIconBox size="small" color="primary" rounded bordered>
								<i class="iconify" data-icon="bx:party"></i>
							</VIconBox>
						</template>
					</VBlock>
					<VBlock center title="Lieu de naissance" :subtitle="vehicleOwner.birth_place">
						<template #icon>
							<VIconBox size="small" color="primary" rounded bordered>
								<i class="iconify" data-icon="ic:outline-place"></i>
							</VIconBox>
						</template>
					</VBlock>
					<VBlock center title="Genre" :subtitle="vehicleOwner?.gender == 'F' ? 'Féminin' : 'Masculin'">
						<template #icon>
							<VIconBox size="small" color="primary" rounded bordered>
								<i class="iconify" data-icon="ph:gender-intersex"></i>
							</VIconBox>
						</template>
					</VBlock>
					<VBlock center title="Commune" :subtitle="ownerState?.name">
						<template #icon>
							<VIconBox size="small" color="primary" rounded bordered>
								<i class="iconify" data-icon="ic:baseline-place"></i>
							</VIconBox>
						</template>
					</VBlock>
					<VBlock center title="Adresse" :subtitle="vehicleOwner?.address">
						<template #icon>
							<VIconBox size="small" color="primary" rounded bordered>
								<i class="iconify" data-icon="uiw:map"></i>
							</VIconBox>
						</template>
					</VBlock>
					<VBlock center title="Quartier" :subtitle="vehicleOwner?.district">
						<template #icon>
							<VIconBox size="small" color="primary" rounded bordered>
								<i class="iconify" data-icon="solar:map-broken"></i>
							</VIconBox>
						</template>
					</VBlock>
					<VBlock center title="Maison" :subtitle="vehicleOwner?.house">
						<template #icon>
							<VIconBox size="small" color="primary" rounded bordered>
								<i class="iconify" data-icon="pepicons-pencil:house"></i>
							</VIconBox>
						</template>
					</VBlock>
					<VBlock center title="Nationalité" :subtitle="ownerNationality?.name">
						<template #icon>
							<VIconBox size="small" color="primary" rounded bordered>
								<i class="iconify" data-icon="gis:search-country"></i>
							</VIconBox>
						</template>
					</VBlock>
				</div>
				<div class="mt-5 pt-2">
					<VBlock center title="Type de demandeur" :subtitle="ownerType?.label">
						<template #icon>
							<VIconBox size="small" color="success" rounded bordered>
								<i class="iconify" data-icon="fluent-mdl2:work"></i>
							</VIconBox>
						</template>
					</VBlock>
				</div>
			</div>
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
