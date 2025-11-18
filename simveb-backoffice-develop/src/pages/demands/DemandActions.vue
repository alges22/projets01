<template>
	<ManagementCenterAssignation
		v-if="can('assign-to-center')"
		:open="managementCenterAssignationModal"
		@close="
			() => {
				managementCenterAssignationModal = false;
				close();
			}
		"
	/>

	<ServiceAssignation
		v-if="can('assign-to-service')"
		:open="serviceAssignationModal"
		@close="
			() => {
				serviceAssignationModal = false;
				close();
			}
		"
	/>

	<StaffAssignation
		v-if="can('assign-to-staff')"
		:open="staffAssignationModal"
		@close="
			() => {
				staffAssignationModal = false;
				close();
			}
		"
	/>

	<VerifyReject
		v-if="hasOnePermissions(['verify-imstd', 'verify-tfmn', 'verify-rim', 'verify-vtf'])"
		:open="verificationModal"
		@close="
			() => {
				verificationModal = false;
				close();
			}
		"
	/>

	<ValidateReject
		v-if="can('pre-validate-imstd') || can('validate-imstd')"
		:open="validationModal"
		@close="
			() => {
				validationModal = false;
				close();
			}
		"
	/>

	<PrintOrderValidation
		v-if="can('validate-print')"
		:open="printOrderValidationModal"
		@close="
			() => {
				printOrderValidationModal = false;
				close();
			}
		"
	/>
</template>

<script lang="ts" setup>
	import { useDemandStore } from "/@src/stores/modules/demand";
	import { storeToRefs } from "pinia";
	import ManagementCenterAssignation from "/src/pages/demands/Actions/ManagementCenterAssignation.vue";
	import { userHasPermissions } from "/@src/utils/permission";
	import ServiceAssignation from "/src/pages/demands/Actions/ServiceAssignation.vue";
	import StaffAssignation from "/@src/pages/demands/Actions/StaffAssignation.vue";
	import VerifyReject from "/@src/pages/demands/Actions/VerifyReject.vue";
	import affectToInterpol from "/@src/pages/demands/Actions/InterpolAssignation";
	import ValidateReject from "/@src/pages/demands/Actions/ValidateReject.vue";
	import emitPrintOrder from "/@src/pages/demands/Actions/PrintOrderEmission";
	import PrintOrderValidation from "/@src/pages/demands/Actions/PrintOrderValidation.vue";
	import closeDemand from "/@src/pages/demands/Actions/CloseDemand";

	const { can, hasOnePermissions } = userHasPermissions();
	const demandStore = useDemandStore();
	const {
		operate,
		nextAction,
		managementCenterAssignationModal,
		serviceAssignationModal,
		staffAssignationModal,
		verificationModal,
		validationModal,
		printOrderValidationModal,
	} = storeToRefs(demandStore);

	const launchNextAction = () => {
		switch (nextAction.value.post_status) {
			case "assigned_to_center":
				managementCenterAssignationModal.value = true;
				break;
			case "assigned_to_service":
				serviceAssignationModal.value = true;
				break;
			case "assigned_to_staff":
				staffAssignationModal.value = true;
				break;
			case "verified":
				verificationModal.value = true;
				break;
			case "affected_to_interpol":
				affectToInterpol().then(() => {
					close();
				});
				break;
			case "pre_validated":
			case "validated":
				validationModal.value = true;
				break;
			case "print_order_emitted":
				emitPrintOrder().then(() => {
					close();
				});
				break;
			case "print_order_validated":
				printOrderValidationModal.value = true;
				break;
			case "closed":
				closeDemand().then(() => {
					close();
				});
				break;
			default:
				close();
		}
	};

	const close = () => {
		operate.value = false;
	};

	watch(operate, (value) => {
		if (value) {
			launchNextAction();
		}
	});

	onMounted(() => {
		demandStore.loadTreatmentCreateData();
	});
</script>

<style lang="scss" scoped></style>
