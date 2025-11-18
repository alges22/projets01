<template>
	<VModal
		is="div"
		:open="open"
		title="Détails de la demande de levée de suspension"
		size="large"
		actions="right"
		@close="$emit('close')"
	>
		<template #content>
			<VPlaceload v-if="loading" height="300px" class="mx-4" />
			<VCard v-else class="side-card">
				<div class="card-inner is-one-third">
					<div class="columns">
						<div class="column">
							<span>Requêté par :</span>
						</div>
						<div class="column">
							<span>{{ lifting.author.identity.fullName }}</span>
						</div>
					</div>
					<div class="columns">
						<div class="column">
							<span>Référence</span>
						</div>
						<div class="column">
							<KeyField :value="lifting.reference" />
						</div>
					</div>
					<div class="columns">
						<div class="column">
							<span>Statut</span>
						</div>
						<div class="column is-flex is-justify-content-space-between">
							<VTag :label="lifting.status_label" :color="statusColor(lifting?.status)" />
						</div>
					</div>
					<div class="columns">
						<div class="column">
							<span>Date :</span>
						</div>
						<div class="column">
							<span>{{ lifting.created_at }}</span>
						</div>
					</div>
					<template v-if="lifting.validated_at">
						<div class="columns">
							<div class="column">
								<span>Validé par :</span>
							</div>
							<div class="column">
								<span>{{ lifting.validator.identity.fullName }}</span>
							</div>
						</div>
						<div class="columns">
							<div class="column">
								<span>Validé le :</span>
							</div>
							<div class="column">
								<span>{{ lifting.validated_at }}</span>
							</div>
						</div>
					</template>
					<template v-if="lifting.rejected_at">
						<div class="columns">
							<div class="column">
								<span>Rejeté par :</span>
							</div>
							<div class="column">
								<span>{{ lifting.rejector.identity.fullName }}</span>
							</div>
						</div>
						<div class="columns">
							<div class="column">
								<span>Rejeté le :</span>
							</div>
							<div class="column">
								<span>{{ lifting.rejected_at }}</span>
							</div>
						</div>
						<div class="columns">
							<div class="column">
								<span>Raison de rejet :</span>
							</div>
							<div class="column">
								<span>{{ lifting.reject_reason || "Non implémenter" }}</span>
							</div>
						</div>
					</template>
				</div>
			</VCard>
		</template>
		<template #action>
			<template
				v-if="
					!lifting?.validated_at &&
					!lifting?.rejected_at &&
					lifting?.status === 'pending' &&
					can('store-space-suspension-lifting-request')
				"
			>
				<VButton
					icon="fa-light fa-check"
					type="button"
					color="success"
					raised
					:loading="formLoading"
					@click="handleValidate(true)"
				>
					Valider
				</VButton>
				<VButton
					icon="fa-light fa-x"
					type="button"
					color="danger"
					raised
					:loading="formLoading"
					@click="handleValidate(false)"
				>
					Rejeter
				</VButton>
			</template>
		</template>
	</VModal>
</template>

<script setup lang="ts">
	import client from "/@src/composable/axiosClient";
	import statusColor from "/@src/utils/status-color";
	import Swal from "sweetalert2";
	import { userHasPermissions } from "/@src/utils/permission";

	const props = defineProps({
		open: {
			type: Boolean,
			required: true,
		},
		liftingId: {
			type: [String, null],
			required: true,
		},
	});

	const emit = defineEmits(["submit", "close"]);
	const { can } = userHasPermissions();
	const lifting = ref(null);
	const loading = ref(false);
	const formLoading = ref(false);

	const fetchLifting = async () => {
		loading.value = true;
		await client({
			method: "GET",
			url: `space-suspension-lifting-requests/${props.liftingId}`,
		})
			.then((response) => {
				lifting.value = response.data;
			})
			.finally(() => {
				loading.value = false;
			});
	};

	const handleValidate = async (validate: Boolean) => {
		await Swal.fire({
			title: `Êtes-vous sûr de vouloir ${
				validate ? "valider" : "rejeter"
			} cette demande de levée de suspension ?`,
			text: "Cette action est irréversible",
			icon: "warning",
			showCancelButton: true,
			confirmButtonText: "Oui, Je confirme",
			cancelButtonText: "Annuler",
			input: validate ? null : "textarea",
			inputPlaceholder: "Raison du rejet",
			inputValidator: (value) => {
				if (!value) {
					return "Vous devez fournir une raison pour le rejet";
				}
			},
		}).then(async (result) => {
			if (result.isConfirmed) {
				formLoading.value = true;
				await client({
					method: "PUT",
					url: `space-suspension-lifting-requests/${props.liftingId}/validate-or-reject`,
					data: validate ? { action: "validate" } : { action: "reject", reject_reason: result.value },
				})
					.then((response) => {
						emit("submit");
						Swal.fire("Succès", response.data.message, "success");
					})
					.finally(() => {
						formLoading.value = false;
					});
			}
		});
	};

	watch(
		() => props.open,
		(val) => {
			if (val && !lifting.value) {
				fetchLifting();
			} else {
				lifting.value = null;
			}
		}
	);

	onUnmounted(() => {
		lifting.value = null;
	});
</script>

<style scoped lang="scss"></style>
