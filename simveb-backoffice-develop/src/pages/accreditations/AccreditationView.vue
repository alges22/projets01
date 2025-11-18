<template>
	<div class="page-content-inner">
		<div class="bg-white rounded-lg mb-4 p-4 flex items-center justify-between accreditation-x-4">
			<VIconButton color="primary" outlined circle icon="fa-light fa-arrow-left" @click="$router.back" />
			<div class="text-primary-dark text-2xl font-bold">
				Accreditation de
				<span v-if="accreditation" class="uppercase">
					{{ accreditation.receiver.identity.fullName }}
				</span>
			</div>
			<div>
				<template v-if="!accreditation?.validated_at && !accreditation?.rejected_at">
					<VButton
						v-if="can('validate-accreditation')"
						class="me-2"
						color="success"
						size="medium"
						raised
						@click="handleValidation(true)"
					>
						<i class="fa fa-check" aria-hidden="true"></i>
						Valider
					</VButton>
					<VButton
						v-if="can('reject-accreditation')"
						class="me-2"
						color="danger"
						size="medium"
						raised
						@click="handleValidation(false)"
					>
						Rejeter
					</VButton>
				</template>
			</div>
		</div>

		<VLoader size="large" :active="loading">
			<div class="grid grid-cols-2 gap-4 tab-details-inner">
				<VCard class="side-card">
					<div class="card-inner is-one-third">
						<div class="columns">
							<div class="column">Accrédité :</div>
							<div class="column">
								<a href="#" class="underline font-bold">
									{{ accreditation.receiver.identity.fullName }}
								</a>
							</div>
						</div>
						<div class="columns">
							<div class="column is-half">
								<span class="has-text-weight-semibold">Profile actuel</span>
							</div>
							<div class="column">{{ accreditation.receiver.type.name }}</div>
						</div>
						<div class="columns">
							<div class="column is-half">
								<span class="has-text-weight-semibold">Auteur de la demande</span>
							</div>
							<div class="column">
								<a href="#" class="underline font-bold">{{ accreditation.author.identity.fullName }}</a>
							</div>
						</div>

						<div class="">
							<span class="font-semibold">Permissions demandées</span>
							<div class="p-2">
								<ul>
									<li
										v-for="(permission, index) in accreditation.permissions"
										:key="index"
										class="m-2"
									>
										- {{ permission.label }}
									</li>
									<li v-if="!accreditation.permissions.length" class="m-2">
										Aucune permission demandée
									</li>
								</ul>
							</div>
						</div>
						<hr class="m-2" />
						<div class="">
							<span class="font-semibold">Rôles demandées</span>
							<div class="p-2">
								<ul>
									<li v-for="(role, index) in accreditation.roles" :key="index" class="m-2">
										- {{ role.label }}
									</li>
									<li v-if="!accreditation.roles.length" class="m-2">Aucune permission demandée</li>
								</ul>
							</div>
						</div>
						<hr class="m-2" />
						<div class="columns">
							<div class="column is-half">
								<span class="has-text-weight-semibold">Status</span>
							</div>
							<div class="column">
								<VTag
									:label="accreditation.status_label"
									:color="statusColor(accreditation.status)"
								></VTag>
							</div>
						</div>
					</div>
				</VCard>
			</div>
		</VLoader>
	</div>
</template>

<script setup lang="ts">
	import { storeToRefs } from "pinia";
	import statusColor from "/@src/utils/status-color";
	import { useCrudStore } from "/@src/stores/modules/crud";
	import Swal from "sweetalert2";
	import { userHasPermissions } from "/@src/utils/permission";

	const crudStore = useCrudStore();
	const { row: accreditation, loading, url } = storeToRefs(crudStore);
	const { can } = userHasPermissions();

	const props = defineProps({
		accreditationId: {
			type: String,
			required: true,
		},
	});

	const handleValidation = async (accept: Boolean) => {
		await Swal.fire({
			title: accept
				? "Êtes-vous sûr de vouloir accepter la demande ?"
				: "Êtes-vous sûr de vouloir rejeter la demande ?",
			text: accept
				? "La demande sera validée et l'utilisateur aura les rôles et permissions demandées"
				: "La demande sera rejetée et l'utilisateur ne pourra pas accéder aux ressources demandées",
			icon: accept ? "question" : "warning",
			showCancelButton: true,
			confirmButtonText: "Oui, Je confirme",
			cancelButtonText: "Annuler",
			input: accept ? null : "textarea",
			inputPlaceholder: "Raison du rejet",
			inputValidator: (value) => {
				if (!value) {
					return "Vous devez fournir une raison pour le rejet";
				}
			},
		}).then(async (result) => {
			if (result.isConfirmed) {
				await crudStore
					.makeRequest(
						"POST",
						{ rejected_reason: result.value, accreditation_id: props.accreditationId },
						accept ? `accreditations/validate` : `accreditations/reject`,
					)
					.then(() => {
						Swal.fire("Enregistré", "La modification a bien été prise en compte", "success");
						fetchAccreditation();
					});
			}
		});
	};

	// Hook

	onUnmounted(() => {
		accreditation.value = {};
	});

	onBeforeMount(() => {
		loading.value = true;
		accreditation.value = null;
	});

	const fetchAccreditation = async () => {
		url.value = "/accreditations";
		await crudStore.fetchRow(props.accreditationId).then(() => {
			loading.value = false;
		});
	};

	onMounted(async () => {
		await fetchAccreditation();
	});
</script>

<style lang="scss"></style>
