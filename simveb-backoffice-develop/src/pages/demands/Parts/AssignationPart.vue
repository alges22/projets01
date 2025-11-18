<template>
	<div class="columns tab-details-inner">
		<div class="column is-half">
			<VCard class="side-card">
				<div class="card-head mt-4">
					<h3>Interpole</h3>
				</div>
				<div class="my-4">
					<span v-if="demandInfo.active_treatment.affected_to_interpol_at">
						Cette demande est déja affectée à interpole
					</span>
					<span v-else> Cette demande est en attente d'affection à interpole </span>
				</div>

				<hr />

				<div class="card-head">
					<h3>Centre de gestion</h3>
				</div>
				<div class="my-4">
					<template v-if="demandInfo.active_treatment.management_center_id">
						<span>
							Cette demande est déja affecté à :
							<span class="font-semibold underline">
								{{ demandInfo.active_treatment.management_center.name }}
							</span>
						</span>
						<div
							v-if="can('assign-to-center')"
							class="columns is-flex is-justify-content-flex-end mt-4 mb-4"
						>
							<VButton color="primary" outlined @click="managementCenterAssignationModal = true">
								Assigner à nouveau
							</VButton>
						</div>
					</template>
					<span v-else> Cette demande est en attente d'assignation à un centre de gestion </span>
				</div>

				<hr />

				<div class="card-head mt-4">
					<h3>Service</h3>
				</div>
				<div class="my-4">
					<template v-if="demandInfo.active_treatment.organization_id">
						<span>
							Cette demande est déja assigné à :
							<span class="font-semibold underline">
								{{ demandInfo.active_treatment.organization.name }}
							</span>
						</span>
						<div
							v-if="can('assign-to-service')"
							class="columns is-flex is-justify-content-flex-end mt-4 mb-4"
						>
							<VButton color="primary" outlined @click="serviceAssignationModal = true">
								Assigner à nouveau
							</VButton>
						</div>
					</template>
					<span v-else> Cette demande est en attente d'assignation à un service </span>
				</div>

				<hr />

				<div class="card-head mt-4">
					<h3>Staff</h3>
				</div>
				<div class="my-4">
					<template v-if="demandInfo.active_treatment.responsible_id">
						<span>
							Cette demande est déja assigné à :
							<span class="font-semibold underline">
								{{ demandInfo.active_treatment.responsible.identity.fullName }}
							</span>
						</span>
						<div
							v-if="can('assign-to-staff')"
							class="columns is-flex is-justify-content-flex-end mt-4 mb-4"
						>
							<VButton color="primary" outlined @click="staffAssignationModal = true">
								Assigner à nouveau
							</VButton>
						</div>
					</template>
					<span v-else> Cette demande est en attente d'assignation à un staff </span>
				</div>
			</VCard>
		</div>
	</div>
</template>

<script setup lang="ts">
	import { userHasPermissions } from "/@src/utils/permission";
	import { useDemandStore } from "/@src/stores/modules/demand";
	import { storeToRefs } from "pinia";

	defineProps<{
		demandInfo: any;
	}>();

	const { can } = userHasPermissions();

	const demandStore = useDemandStore();
	const { managementCenterAssignationModal, serviceAssignationModal, staffAssignationModal } =
		storeToRefs(demandStore);
</script>
