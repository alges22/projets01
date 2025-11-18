<template>
	<VModal
		is="div"
		:open="open"
		title="Détails de la demande de suspension"
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
							<span>{{ suspension.author.identity.fullName }}</span>
						</div>
					</div>
					<div class="columns">
						<div class="column">
							<span>Référence</span>
						</div>
						<div class="column">
							<KeyField :value="suspension.reference" />
						</div>
					</div>
					<div class="columns">
						<div class="column">
							<span>Statut</span>
						</div>
						<div class="column is-flex is-justify-content-space-between">
							<VTag :label="suspension.status_label" :color="statusColor(suspension?.status)" />
						</div>
					</div>
					<div class="columns">
						<div class="column">
							<span>Date :</span>
						</div>
						<div class="column">
							<span>{{ suspension.created_at }}</span>
						</div>
					</div>
					<template v-if="suspension.validated_at">
						<div class="columns">
							<div class="column">
								<span>Validé par :</span>
							</div>
							<div class="column">
								<span>{{ suspension.validator.identity.fullName }}</span>
							</div>
						</div>
						<div class="columns">
							<div class="column">
								<span>Validé le :</span>
							</div>
							<div class="column">
								<span>{{ suspension.validated_at }}</span>
							</div>
						</div>
					</template>
					<template v-if="suspension.rejected_at">
						<div class="columns">
							<div class="column">
								<span>Rejeté par :</span>
							</div>
							<div class="column">
								<span>{{ suspension.rejector.identity.fullName }}</span>
							</div>
						</div>
						<div class="columns">
							<div class="column">
								<span>Rejeté le :</span>
							</div>
							<div class="column">
								<span>{{ suspension.rejected_at }}</span>
							</div>
						</div>
						<div class="columns">
							<div class="column">
								<span>Raison de rejet :</span>
							</div>
							<div class="column">
								<span>{{ suspension.reject_reason || "Non implémenter" }}</span>
							</div>
						</div>
					</template>
				</div>
			</VCard>
		</template>
		<template #action>
			<template
				v-if="
					!suspension?.validated_at &&
					!suspension?.rejected_at &&
					suspension?.status === 'pending' &&
					can('validate-space-suspension-request')
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
		suspensionId: {
			type: [String, null],
			required: true,
		},
	});

	const emit = defineEmits(["submit", "close"]);
	const { can } = userHasPermissions();
	const suspension = ref(null);
	const loading = ref(false);
	const formLoading = ref(false);

	const fetchSuspension = async () => {
		loading.value = true;
		await client({
			method: "GET",
			url: `space-suspension-requests/${props.suspensionId}`,
		})
			.then((response) => {
				suspension.value = response.data;
			})
			.finally(() => {
				loading.value = false;
			});
	};

	const handleValidate = async (validate: Boolean) => {
		await Swal.fire({
			title: `Êtes-vous sûr de vouloir ${validate ? "valider" : "rejeter"} cette demande de suspension ?`,
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
					url: `space-suspension-requests/${props.suspensionId}/validate-or-reject`,
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
			if (val && !suspension.value) {
				fetchSuspension();
			} else {
				suspension.value = null;
			}
		}
	);

	onUnmounted(() => {
		suspension.value = null;
	});
</script>

<style scoped lang="scss"></style>
