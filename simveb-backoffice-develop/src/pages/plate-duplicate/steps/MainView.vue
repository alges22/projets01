<script lang="ts" setup>
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

	const duplicateStore = useDemandStore();
	const { demand, url } = storeToRefs(duplicateStore);
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
			await duplicateStore.fetchDemand(props.demandId);
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
				duplicateStore
					.validateDemand(demand.value.active_treatment.id, approved, value.value)
					.then((data) => {
						notyf.success(approved ? "Demande validé avec succès !" : "Demande rejeté avec succès !");
						duplicateStore.fetchDemand(demand.value.id).then(() => {
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
				duplicateStore
					.verifyDemand(demand.value.active_treatment.id, approved, value.value)
					.then((data) => {
						notyf.success(approved ? "Demande vérifié avec succès !" : "Demande suspendu avec succès !");
						duplicateStore.fetchDemand(demand.value.id).then(() => {
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
		url.value = "plate-duplicates";
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
								class="has-text-primary"
								color="white"
								tabindex="0"
								@click="copy(reference)"
								@keydown.space.prevent="copy(reference)"
							>
								<Transition mode="out-in" name="fade-fast">
									<span v-if="copied && text === reference" class="is-copied">
										<i aria-hidden="true" class="fa-light fa-check"></i>
									</span>
									<span v-else>
										<i aria-hidden="true" class="fa-light fa-copy"></i>
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
								v-if="!treatment.pre_validated_at && can('pre-validate-plate-duplicate')"
								color="success"
								raised
								size="medium"
								@click="validateDemand(true)"
							>
								Pré-valider
							</VButton>
							&nbsp;
							<VButton
								v-if="!treatment.validated_at && can('validate-plate-duplicate')"
								color="success"
								raised
								size="medium"
								@click="validateDemand(true)"
							>
								Valider
							</VButton>
							&nbsp;
							<VButton
								v-if="
									(!treatment.validated_at || !treatment.pre_validated_at) &&
									(can('pre-validate-plate-duplicate') || can('validate-plate-duplicate'))
								"
								color="danger"
								raised
								size="medium"
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

				<VBlock :subtitle="vehicleOwner.lastname + ' ' + vehicleOwner.firstname" center title="Nom Complet">
					<template #icon>
						<VIconBox bordered color="primary" rounded size="small">
							<i class="iconify" data-icon="user"></i>
						</VIconBox>
					</template>
				</VBlock>

				<VBlock :subtitle="vehicleOwner.email" center title="Email">
					<template #icon>
						<VIconBox bordered color="primary" rounded size="small">
							<i class="iconify" data-icon="mail"></i>
						</VIconBox>
					</template>
				</VBlock>

				<VBlock :subtitle="vehicleOwner.telephone" center title="Téléphone">
					<template #icon>
						<VIconBox bordered color="primary" rounded size="small">
							<i class="iconify" data-icon="phone"></i>
						</VIconBox>
					</template>
				</VBlock>
				<VBlock :subtitle="vehicleOwner.ifu" center title="IFU">
					<template #icon>
						<VIconBox bordered color="primary" rounded size="small">
							<i class="iconify" data-icon="align-justify"></i>
						</VIconBox>
					</template>
				</VBlock>
				<VBlock :subtitle="vehicleOwner?.npi" center title="NPI">
					<template #icon>
						<VIconBox bordered color="primary" rounded size="small">
							<i class="iconify" data-icon="info"></i>
						</VIconBox>
					</template>
				</VBlock>
				<VBlock :subtitle="legalStatus.name" center title="Statut Légal">
					<template #icon>
						<VIconBox bordered color="primary" rounded size="small">
							<i class="iconify" data-icon="arcticons:company-portal"></i>
						</VIconBox>
					</template>
				</VBlock>
				<div class="mt-5 pt-2">
					<VBlock :subtitle="vehicleOwner.birthdate" center title="Date de naissance">
						<template #icon>
							<VIconBox bordered color="primary" rounded size="small">
								<i class="iconify" data-icon="bx:party"></i>
							</VIconBox>
						</template>
					</VBlock>
					<VBlock :subtitle="vehicleOwner.birth_place" center title="Lieu de naissance">
						<template #icon>
							<VIconBox bordered color="primary" rounded size="small">
								<i class="iconify" data-icon="ic:outline-place"></i>
							</VIconBox>
						</template>
					</VBlock>
					<VBlock :subtitle="vehicleOwner?.gender == 'F' ? 'Féminin' : 'Masculin'" center title="Genre">
						<template #icon>
							<VIconBox bordered color="primary" rounded size="small">
								<i class="iconify" data-icon="ph:gender-intersex"></i>
							</VIconBox>
						</template>
					</VBlock>
					<VBlock :subtitle="ownerState?.name" center title="Commune">
						<template #icon>
							<VIconBox bordered color="primary" rounded size="small">
								<i class="iconify" data-icon="ic:baseline-place"></i>
							</VIconBox>
						</template>
					</VBlock>
					<VBlock :subtitle="vehicleOwner?.address" center title="Adresse">
						<template #icon>
							<VIconBox bordered color="primary" rounded size="small">
								<i class="iconify" data-icon="uiw:map"></i>
							</VIconBox>
						</template>
					</VBlock>
					<VBlock :subtitle="vehicleOwner?.district" center title="Quartier">
						<template #icon>
							<VIconBox bordered color="primary" rounded size="small">
								<i class="iconify" data-icon="solar:map-broken"></i>
							</VIconBox>
						</template>
					</VBlock>
					<VBlock :subtitle="vehicleOwner?.house" center title="Maison">
						<template #icon>
							<VIconBox bordered color="primary" rounded size="small">
								<i class="iconify" data-icon="pepicons-pencil:house"></i>
							</VIconBox>
						</template>
					</VBlock>
					<VBlock :subtitle="ownerNationality?.name" center title="Nationalité">
						<template #icon>
							<VIconBox bordered color="primary" rounded size="small">
								<i class="iconify" data-icon="gis:search-country"></i>
							</VIconBox>
						</template>
					</VBlock>
				</div>
				<div class="mt-5 pt-2">
					<VBlock :subtitle="ownerType?.label" center title="Type de demandeur">
						<template #icon>
							<VIconBox bordered color="success" rounded size="small">
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
