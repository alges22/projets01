<template>
	<div class="columns tab-details-inner is-multiline">
		<div class="column is-8">
			<VCard class="side-card">
				<div class="card-head">
					<h4>Contrôle</h4>
				</div>
				<div v-if="treatment" class="card-inner is-size-6-5">
					<template v-if="treatment.validated_by_anatt_at || treatment.rejected_by_anatt_at">
						<div class="columns">
							<div class="column is-on-quarter">
								<span>Date de contrôle</span>
							</div>
							<div class="column">
								<span>{{ treatment.validated_by_anatt_at || treatment.rejected_by_anatt_at }}</span>
							</div>
						</div>
						<div class="columns">
							<div class="column is-on-quarter">
								<span>Résultat du contrôle : </span>
							</div>
							<div class="column">
								<span>{{ demand.status_label }}</span>
							</div>
						</div>
					</template>

					<template v-else>
						<div class="columns">
							<div class="column is-on-quarter">
								<span>La demande n'a pas encore été contrôlée par l'ANAT</span>
							</div>
						</div>
						<div
							v-if="demand.status === 'given_to_applicant'"
							class="columns is-flex is-justify-content-flex-end mt-4"
						>
							&nbsp;
							<VButton
								v-if="can('control-anatt-im-demand')"
								:loading="formLoading"
								color="success"
								raised
								size="medium"
								@click="validateControl(true)"
							>
								Valider
							</VButton>
							&nbsp;
							<VButton
								v-if="can('control-anatt-im-demand')"
								:loading="formLoading"
								color="danger"
								raised
								size="medium"
								@click="validateControl(false)"
							>
								Rejeter
							</VButton>
						</div>
					</template>
				</div>
			</VCard>
		</div>
	</div>
</template>

<script lang="ts" setup>
	import { useDemandStore } from "/src/stores/modules/demand";
	import { storeToRefs } from "pinia";
	import { Notyf } from "notyf";
	import { userHasPermissions } from "/@src/utils/permission";
	import Swal from "sweetalert2/dist/sweetalert2.js";

	export interface ImmatriculationPrintCardViewProps {
		demandId: string;
	}

	const props = withDefaults(defineProps<ImmatriculationPrintCardViewProps>(), {
		demandId: undefined,
	});
	const notyf = new Notyf();

	const demandStore = useDemandStore();
	const { demand, url, formLoading } = storeToRefs(demandStore);
	const { can } = userHasPermissions();
	const treatment = ref(null);

	const validateControl = (approved: boolean) => {
		Swal.fire({
			title: "Confirmation",
			text: approved
				? "Êtes vous sûr(e) de vouloir valider l'immatriculation ?"
				: "Êtes vous sûr(e) de vouloir rejeter l'immatriculation ?",
			icon: "warning",
			confirmButtonText: "Oui, Valider",
			showCancelButton: true,
			cancelButtonText: "Annuler",
			input: "textarea",
			inputPlaceholder: "Observations",
		}).then((value) => {
			if (value.isConfirmed) {
				demandStore.validateControl(demand.value.id, approved, value.value).then(() => {
					notyf.success("Contrôle effectué avec succès");
					demandStore.fetchDemand(demand.value.id).then(() => {
						treatment.value = demand.value.active_treatment;
					});
				});
			}
		});
	};

	onMounted(() => {
		if (!demand.value.id) {
			demandStore.getDemand(props.demandId).then(() => {
				treatment.value = demand.value.active_treatment;
			});
		} else {
			treatment.value = demand.value.active_treatment;
		}
	});

	onBeforeMount(() => {
		url.value = "/immatriculation-demands";
	});
</script>
