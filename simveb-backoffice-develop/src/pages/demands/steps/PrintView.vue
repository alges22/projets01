<script setup lang="ts">
	import { useDemandStore } from "/src/stores/modules/demand";
	import { storeToRefs } from "pinia";
	import { Notyf } from "notyf";
	import CountryFlag from "vue-country-flag-next";
	import { userHasPermissions } from "/@src/utils/permission";

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

	const emitOrPrint = () => {
		Swal.fire({
			title: "Confirmation",
			text: "Êtes vous sur ?",
			icon: "warning",
			confirmButtonText: "Oui, Valider",
			showCancelButton: true,
			cancelButtonText: "Annuler",
			input: "text",
			inputPlaceholder: "Observations",
		}).then((value) => {
			if (value.isConfirmed) {
				demandStore
					.emitOrPrintOrder({
						treatment_id: treatment.value.id,
						print_observations: value.value,
					})
					.then((data) => {
						notyf.success("Ordre d'impression émis avec succès");
						demandStore.getDemand(demand.value.id).then(() => {
							treatment.value = demand.value.active_treatment;
						});
					})
					.catch(() => {});
			}
		});
	};

	onMounted(() => {
		if (!demand.value.id) {
			demandStore.getDemand(props.demandId).then((res) => {
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

<template>
	<div class="columns tab-details-inner is-multiline">
		<div class="column is-8">
			<VCard class="side-card">
				<div class="card-head">
					<h4>Impression</h4>
				</div>
				<div v-if="treatment" class="card-inner is-size-6-5">
					<template v-if="treatment.printed_at">
						<div class="columns">
							<div class="column is-on-quarter">
								<span>Service d'impression</span>
							</div>
							<div class="column">
								<span>Service général</span>
							</div>
						</div>
						<div class="columns">
							<div class="column is-on-quarter">
								<span>Imprimé par</span>
							</div>
							<div class="column">
								<span>{{ treatment.printed_by?.identity.fullName }}</span>
							</div>
						</div>
						<div class="columns">
							<div class="column is-on-quarter">
								<span>Date d'impression</span>
							</div>
							<div class="column">
								<span>{{ treatment.printed_at }}</span>
							</div>
						</div>
					</template>
					<template v-else-if="treatment.print_order_emitted_at">
						<div class="columns">
							<div class="column is-on-quarter">
								<span>Ordre d'impression émis par : </span>
							</div>
							<div class="column">
								<span>{{ treatment.print_order_emitted_by?.identity?.fullName }}</span>
							</div>
						</div>
						<div class="columns">
							<div class="column is-on-quarter">
								<span>Ordre d'impression émis le : </span>
							</div>
							<div class="column">
								<span>{{ treatment.print_order_emitted_at }}</span>
							</div>
						</div>
						<div class="columns is-flex is-justify-content-flex-end mt-4">
							&nbsp;
							<VButton
								v-if="can('emit-print-order-im-demand')"
								color="success"
								:loading="formLoading"
								size="medium"
								raised
								@click="emitOrPrint"
							>
								Valider
							</VButton>
						</div>
					</template>
					<template v-else-if="treatment.interpol_validated_at">
						<div class="columns">
							<div class="column is-on-quarter">
								<span>En attente d'émission de l'ordre d'impression</span>
							</div>
						</div>
						<div class="columns is-flex is-justify-content-flex-end mt-4">
							&nbsp;
							<VButton
								v-if="can('print-im-demand')"
								color="success"
								:loading="formLoading"
								size="medium"
								raised
								@click="emitOrPrint"
							>
								Ordre d'impression
							</VButton>
						</div>
					</template>
					<template v-else>
						<div class="columns">
							<div class="column is-on-quarter">
								<span>La demande n'a pas encore été validé par interpole</span>
							</div>
						</div>
					</template>
				</div>
			</VCard>
		</div>
	</div>
</template>
