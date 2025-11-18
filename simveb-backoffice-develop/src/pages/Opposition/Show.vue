<script setup lang="ts">
	import client from "/@src/composable/axiosClient";
	import { userHasPermissions } from "/@src/utils/permission";
	import { useNotyf } from "/@src/composable/useNotyf";
	import statusColor from "/@src/utils/status-color";
	import dayjs from "dayjs";
	import Swal from "sweetalert2";
	const { can } = userHasPermissions();

	const notyf = useNotyf();

	const route = useRoute();
	const id = route.params.id;

	const opposition = ref(null);
	const loading = ref(true);

	onMounted(() => {
		loadOpposition();
	});

	const loadOpposition = () => {
		loading.value = true;

		client({
			method: "GET",
			url: `/oppositions/${id}`,
		})
			.then((response) => response.data)
			.then((response) => {
				opposition.value = response;
				loading.value = false;
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
					url: `/oppositions/reject/${id}`,
					data: {
						rejected_reason: result.value,
					},
				})
					.then((response) => {
						notyf.success("Demande d'opposition rejetée avec succès");

						loadOpposition();
					})
					.finally(() => {
						Swal.close();
					});
			} else if (result.dismiss === Swal.DismissReason.cancel) {
				Swal.fire({
					title: "Anuulé",
					text: "Vous avez annulé l'opération de rejet",
					icon: "error",
				});
			}
		});
	};

	const issue = () => {
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
			url: `/oppositions/issue/${id}`,
		})
			.then((response) => {
				notyf.success("Opposition émise avec succès");

				loadOpposition();
			})
			.finally(() => {
				Swal.close();
			});
	};

	const lift = () => {
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
			url: `/oppositions/lift/${id}`,
		})
			.then((response) => {
				notyf.success("Opposition levée avec succès");

				loadOpposition();
			})
			.finally(() => {
				Swal.close();
			});
	};
</script>

<template>
	<div class="page-content-inner">
		<VLoader :active="loading" size="large">
			<div class="grid grid-cols-4">
				<div class="col-span-3 grid gap-4">
					<VCard class="side-card">
						<div class="card-inner is-size-6-5">
							<div class="columns">
								<div class="column is-one-quarter">
									<span class="has-text-weight-semibold"> Référence </span>
								</div>
								<div class="column">
									<span>
										{{ opposition.reference }}
									</span>
								</div>
							</div>

							<div class="columns">
								<div class="column is-one-quarter">
									<span class="has-text-weight-semibold"> Propriétaire du véhicule </span>
								</div>
								<div class="column is-flex is-justify-content-space-between">
									<div>
										<span>{{ opposition.owner.identity.fullName }}</span>
									</div>
								</div>
							</div>

							<div class="columns">
								<div class="column is-one-quarter">
									<span class="has-text-weight-semibold"> Raison de l'opposition </span>
								</div>
								<div class="column is-flex is-justify-content-space-between">
									<div>
										<span>{{ opposition.title_reason.label }}</span>
									</div>
								</div>
							</div>

							<div class="columns">
								<div class="column is-one-quarter">
									<span class="has-text-weight-semibold"> Date de soumission </span>
								</div>
								<div class="column">
									<span class="">{{ dayjs(opposition.created_at).format("DD/MM/YYYY H:m") }}</span>
								</div>
							</div>

							<div class="columns">
								<div class="column is-one-quarter">
									<span class="has-text-weight-semibold"> Statut </span>
								</div>
								<div class="column">
									<VTag
										:label="opposition.status_label"
										curved
										:color="statusColor(opposition.status)"
									/>
								</div>
							</div>

							<div class="columns" v-if="opposition.active_treatment.rejected_at">
								<div class="column is-one-quarter">
									<span class="has-text-weight-semibold"> Rejete le </span>
								</div>
								<div class="column">
									{{ dayjs(opposition.active_treatment.rejected_at).format("DD/MM/YYYY") }}
								</div>
							</div>

							<div class="columns" v-if="opposition.active_treatment.issued_at">
								<div class="column is-one-quarter">
									<span class="has-text-weight-semibold"> Emis le </span>
								</div>
								<div class="column">
									{{ dayjs(opposition.active_treatment.issued_at).format("DD/MM/YYYY") }}
								</div>
							</div>

							<div class="columns" v-if="opposition.active_treatment.lifting_at">
								<div class="column is-one-quarter">
									<span class="has-text-weight-semibold"> Levé le </span>
								</div>
								<div class="column">
									{{ dayjs(opposition.active_treatment.lifting_at).format("DD/MM/YYYY") }}
								</div>
							</div>
						</div>

						<div class="mt-8 flex justify-end">
							<VButton
								v-if="
									can('reject-opposition') &&
									!opposition.active_treatment.rejected_at &&
									!opposition.active_treatment.issued_at &&
									!opposition.active_treatment.lifting_at
								"
								color="danger"
								class="mx-2"
								raised
								size="medium"
								@click="reject"
							>
								Rejeter
							</VButton>

							<VButton
								class="mx-2"
								v-if="
									can('issue-opposition') &&
									!opposition.active_treatment.rejected_at &&
									!opposition.active_treatment.issued_at
								"
								color="success"
								raised
								size="medium"
								@click="issue"
							>
								Emettre l'opposition
							</VButton>

							<VButton
								class="mx-2"
								v-if="
									can('lift-opposition') &&
									opposition.active_treatment.issued_at &&
									!opposition.active_treatment.lifting_at
								"
								color="success"
								raised
								size="medium"
								@click="lift"
							>
								Lever le gage
							</VButton>
						</div>
					</VCard>
				</div>
				<VCard class="side-card">
					<div class="card-inner pt-1 mt-2" style="font-size: 12px">
						<div v-for="vehicle in opposition.vehicles" :key="vehicle.id" class="mt-4">
							<div class="columns">
								<div
									class="column is-half has-text-weight-bold"
									style="background: #d4e4fb; color: #1470eb"
								>
									<span class="">{{ vehicle.vin }}</span>
								</div>

								<div
									class="column is-half has-text-weight-bold"
									style="background: #cee0fa; color: #1470eb"
								>
									<span class="has-text-weight-semibold">{{ vehicle.model }}</span>
								</div>
							</div>
						</div>
					</div>
				</VCard>
			</div>
		</VLoader>
	</div>
</template>

<style scoped lang="scss"></style>
