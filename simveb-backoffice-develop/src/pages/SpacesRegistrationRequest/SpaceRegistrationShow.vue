<script setup>
	import client from "/@src/composable/axiosClient";
	import dayjs from "dayjs";
	import statusColor from "/@src/utils/status-color";
	import { userHasPermissions } from "/@src/utils/permission";
	import Swal from "sweetalert2";
	import { useNotyf } from "/@src/composable/useNotyf";
	import BackButton from "/@src/pages/activity-logs/BackButton.vue";

	const { can } = userHasPermissions();

	const notyf = useNotyf();

	const route = useRoute();
	const id = route.params.id;

	const registrationRequest = ref(null);
	const loading = ref(true);

	onMounted(() => {
		loadRegistrationRequest();
	});

	const loadRegistrationRequest = () => {
		loading.value = true;

		client({
			method: "GET",
			url: `/space-registration-requests/${id}`,
		})
			.then((response) => response.data)
			.then((response) => {
				registrationRequest.value = response;
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
			method: "POST",
			url: `/space-registration-requests/validate/${id}`,
		})
			.then(() => {
				notyf.success("Demande validée avec succès");

				loadRegistrationRequest();
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
			cancelButtonText: "Annuler",
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
					method: "POST",
					url: `/space-registration-requests/reject/${id}`,
					data: {
						reason: result.value,
					},
				})
					.then(() => {
						notyf.success("Demande rejetée avec succès");

						loadRegistrationRequest();
					})
					.finally(() => {
						Swal.close();
					});
			} else if (result.dismiss === Swal.DismissReason.cancel) {
				Swal.fire({
					title: "Annulée",
					text: "Vous avez annulée l'opération de rejet",
					icon: "error",
				});
			}
		});
	};
</script>

<template>
	<div class="page-content-inner">
		<div class="mb-4">
			<BackButton />
		</div>
		<VLoader :active="loading" size="large">
			<div class="grid gap-4">
				<VCard class="side-card">
					<div class="card-head">
						<h3 class="font-bold text-light">Détails de la demande d'enregistrement</h3>
					</div>
					<div class="card-inner is-size-6-5">
						<div class="columns">
							<div class="column is-one-quarter">
								<span class="has-text-weight-semibold"> Type de profil </span>
							</div>
							<div class="column">
								<span class="">
									{{ registrationRequest.profile_type.name }}
								</span>
							</div>
						</div>
						<div class="columns">
							<div class="column is-one-quarter">
								<span class="has-text-weight-semibold"> Institution </span>
							</div>
							<div class="column is-flex is-justify-content-space-between">
								<div>
									<span>{{ registrationRequest.institution.name }}</span>
								</div>
							</div>
						</div>

						<div class="columns">
							<div class="column is-one-quarter">
								<span class="has-text-weight-semibold"> Date de création </span>
							</div>
							<div class="column">
								<span class="">{{
									dayjs(registrationRequest.created_at).format("DD/MM/YYYY H:m")
								}}</span>
							</div>
						</div>

						<div class="columns">
							<div class="column is-one-quarter">
								<span class="has-text-weight-semibold"> Statut </span>
							</div>
							<div class="column">
								<VTag
									:label="registrationRequest.status_label"
									curved
									:color="statusColor(registrationRequest.status)"
								/>
							</div>
						</div>

						<div v-if="registrationRequest.validator" class="columns">
							<div class="column is-one-quarter">
								<span class="has-text-weight-semibold"> Validé par </span>
							</div>
							<div class="column">
								{{ registrationRequest.validator.identity.fullName }}
							</div>
						</div>

						<div v-if="registrationRequest.rejector" class="columns">
							<div class="column is-one-quarter">
								<span class="has-text-weight-semibold"> Rejeté par </span>
							</div>
							<div class="column">
								{{ registrationRequest.rejector.identity.fullName }}
							</div>
						</div>
					</div>

					<div
						v-if="!registrationRequest.validated_at && !registrationRequest.rejected_at"
						class="mt-8 flex justify-end"
					>
						<VButton
							v-if="can('reject-space-registration-request')"
							color="danger"
							raised
							size="medium"
							@click="reject"
						>
							Rejeter
						</VButton>

						&nbsp;

						<VButton
							v-if="can('validate-space-registration-request')"
							color="success"
							raised
							size="medium"
							@click="validate"
						>
							Valider
						</VButton>
					</div>
				</VCard>
			</div>
		</VLoader>
	</div>
</template>

<style scoped lang="scss"></style>
