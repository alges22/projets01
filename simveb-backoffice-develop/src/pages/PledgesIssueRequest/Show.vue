<script setup>
	import client from "/src/composable/axiosClient";
	import dayjs from "dayjs";
	import statusColor from "/src/utils/status-color";
	import { userHasPermissions } from "/src/utils/permission";
	import Swal from "sweetalert2";
	import { useNotyf } from "/src/composable/useNotyf";
	import statuses from "/@src/data/statuses.js";

	const { can } = userHasPermissions();

	const notyf = useNotyf();

	const route = useRoute();
	const id = route.params.id;

	const pledge = ref(null);
	const loading = ref(true);

	onMounted(() => {
		loadPledge();
	});

	const loadPledge = () => {
		loading.value = true;

		client({
			method: "GET",
			url: `/pledge/${id}`,
		})
			.then((response) => response.data)
			.then((response) => {
				pledge.value = response;
				loading.value = false;
			});
	};

	const validate = () => {
		Swal.fire({
			title: "Validation en cours..",
			text: "Patientez pendant le traitement de votre requête",
			allowOutsideClick: false,
			didOpen: () => {
				Swal.showLoading();
			},
		});

		client({
			method: "PUT",
			url: `/pledge/validate/${id}`,
		})
			.then((response) => {
				notyf.success("Demande d'émission de dage validée avec succès");

				loadPledge();
			})
			.finally(() => {
				Swal.close();
			});
	};

	const reject = () => {
		Swal.fire({
			title: "Rejeter la demande",
			input: "text",
			inputLabel: "Raison du rejet",
			inputPlaceholder: "Entrez la raison ici...",
			showCancelButton: true,
			confirmButtonText: "Soumettre",
			cancelButtonText: "Anuuler",
			inputValidator: (value) => {
				if (!value) {
					return "Veuillez saisir la raison du rejet";
				}
			},
		}).then((result) => {
			if (result.isConfirmed) {
				Swal.fire({
					title: "Rejet en cours..",
					text: "Patientez pendant le traitement de votre requête",
					allowOutsideClick: false,
					didOpen: () => {
						Swal.showLoading();
					},
				});

				client({
					method: "PUT",
					url: `/pledge/reject/${id}`,
					data: {
						rejected_reasons: result.value,
					},
				})
					.then((response) => {
						notyf.success("Demande d'émission de gage rejetée avec succès");

						loadPledge();
					})
					.finally(() => {
						Swal.close();
					});
			} else if (result.dismiss === Swal.DismissReason.cancel) {
				Swal.fire({
					title: "Anuulée",
					text: "Vous avez annulé l'opération de rejet",
					icon: "error",
				});
			}
		});
	};
</script>

<template>
	<div class="page-content-inner">
		<VLoader :active="loading" size="large">
			<div class="grid gap-4">
				<VCard class="side-card">
					<div class="card-inner is-size-6-5">
						<div class="columns">
							<div class="column is-one-quarter">
								<span class="has-text-weight-semibold"> Référence </span>
							</div>
							<div class="column">
								<span>
									{{ pledge.reference }}
								</span>
							</div>
						</div>
						<div class="columns">
							<div class="column is-one-quarter">
								<span class="has-text-weight-semibold"> Statut </span>
							</div>
							<div class="column is-flex is-justify-content-space-between">
								<div>
									<VTag :label="pledge.status_label" curved :color="statusColor(pledge.status)" />
								</div>
							</div>
						</div>
						<div class="columns">
							<div class="column is-one-quarter">
								<span class="has-text-weight-semibold"> Date de soumission </span>
							</div>
							<div class="column is-flex is-justify-content-space-between">
								<div>
									<span>{{ dayjs(pledge?.created_at).format("DD/MM/YYYY H:m") }}</span>
								</div>
							</div>
						</div>

						<div class="columns">
							<div class="column is-one-quarter">
								<span class="has-text-weight-semibold"> Banque </span>
							</div>
							<div class="column">
								<span class="">{{
									pledge?.financial_institution?.name ?? pledge?.institution_emitted.name
								}}</span>
							</div>
						</div>

						<div class="columns" v-if="pledge?.financial_institution">
							<div class="column is-one-quarter">
								<span class="has-text-weight-semibold"> Concessionnaire </span>
							</div>
							<div class="column">
								<span class="">{{ pledge?.institution_emitted.name }}</span>
							</div>
						</div>
					</div>

					<div class="mt-8 flex justify-end">
						<VButton
							v-if="
								can('reject-pledge-by-anatt') &&
								pledge.active_treatment.status === statuses.JUSTICE_VALIDATED
							"
							color="danger"
							raised
							size="medium"
							@click="reject"
						>
							Rejeter
						</VButton>

						<VButton
							class="mx-2"
							v-if="
								can('validate-pledge-by-anatt') &&
								pledge.active_treatment.status === statuses.JUSTICE_VALIDATED
							"
							color="success"
							raised
							size="medium"
							@click="validate"
						>
							Valider
						</VButton>
					</div>
				</VCard>

				<VCard class="side-card">
					<div class="card-inner is-size-6-5">
						<div class="columns">
							<div class="column is-one-quarter">
								<span class="has-text-weight-semibold"> Nom </span>
							</div>
							<div class="column">
								<span> {{ pledge?.vehicle_owner.identity.firstname }}</span>
							</div>
						</div>
						<div class="columns">
							<div class="column is-one-quarter">
								<span class="has-text-weight-semibold"> Prénoms </span>
							</div>
							<div class="column">
								<span> {{ pledge?.vehicle_owner.identity.lastname }}</span>
							</div>
						</div>
						<div class="columns">
							<div class="column is-one-quarter">
								<span class="has-text-weight-semibold"> NPI </span>
							</div>
							<div class="column">
								<span> {{ pledge?.vehicle_owner.identity.npi }}</span>
							</div>
						</div>
						<div class="columns">
							<div class="column is-one-quarter">
								<span class="has-text-weight-semibold"> Téléphone </span>
							</div>
							<div class="column">
								<span> {{ pledge?.vehicle_owner.identity.telephone }}</span>
							</div>
						</div>
					</div>
				</VCard>
			</div>
		</VLoader>
	</div>
</template>

<style scoped lang="scss"></style>
