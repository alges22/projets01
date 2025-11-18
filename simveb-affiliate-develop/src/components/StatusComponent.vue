<template>
	<div :class="`text-${statusColor}-600`" class="flex items-center font-bold">
		<slot>
			<span
				:class="`bg-${statusColor}-600`"
				class="inline-flex px-3 py-1 text-xs font-semibold text-white rounded-lg"
			>
				<template v-if="statusColor === 'green'">
					<i class="fa-light fa-check-circle w-4 h-4 mr-2" />
				</template>
				<template v-else-if="statusColor === 'red'">
					<i class="fa-light fa-xmark-circle w-4 h-4 mr-2" />
				</template>
				<template v-else-if="statusColor === 'amber'">
					<i class="fa-light fa-clock w-4 h-4 mr-2" />
				</template>
				<template v-else-if="statusColor === 'secondary'">
					<i class="fa-light fa-circle w-4 h-4 mr-2" />
				</template>
				<template v-else-if="statusColor === 'cyan'">
					<i class="fa-light fa-info-circle w-4 h-4 mr-2" />
				</template>
				<template v-else-if="statusColor === 'primary'">
					<i class="fa-light fa-circle w-4 h-4 mr-2" />
				</template>

				{{ statusText || capitalize(status) }}
			</span>
		</slot>
	</div>
</template>

<script setup>
	import { capitalize, onMounted, ref } from "vue";

	const statusColor = ref("");

	const props = defineProps({
		status: {
			type: String,
			required: true,
		},
		statusText: {
			type: String,
			required: false,
		},
		title: {
			type: String,
			required: false,
		},
	});

	onMounted(() => {
		if (props.status === "pending" || props.status === "waiting") {
			statusColor.value = "amber";
		} else if (
			[
				"created",
				"validated",
				"success",
				"active",
				"done",
				"paid",
				"open",
				"opening",
				"online",
				"submitted",
				"plate_printed",
				"issued",
				"recorded",
				"opposition_emitted",
				"judge_validated",
				"clerk_validated",
				"institution_validated",
				"justice_validated",
				"anatt_validated",
				"closed",
			].includes(props.status)
		) {
			statusColor.value = "green";
		} else if (
			[
				"rejected",
				"failed",
				"unpaid",
				"judge_rejected",
				"clerk_rejected",
				"institution_rejected",
				"justice_rejected",
				"anatt_rejected",
			].includes(props.status)
		) {
			statusColor.value = "red";
		} else if (
			[
				"non_payable",
				"suspended",
				"canceled",
				"closed",
				"refunded",
				"suspension",
				"unsuspension",
				"closure",
				"offline",
			].includes(props.status)
		) {
			statusColor.value = "secondary";
		} else if (["electronic", "raised", "no_reformed"].includes(props.status)) {
			statusColor.value = "cyan";
		} else if (
			[
				"released",
				"initiated",
				"in_review",
				"initiated",
				"cash",
				"in_cart",
				"link",
				"direct",
				"assignation_to_staff",
				"unassignation_from_staff",
				"assignation_to_agency",
				"unassignation_from_agency",
				"staff_transfer",
				"ordinary",
				"assigned_to_board",
				"assigned_to_agent",
				"emitted",
				"print_order_emitted",
				"opposition_lifted_emitted",
			].includes(props.status)
		) {
			statusColor.value = "primary";
		}
	});
</script>

<style scoped></style>
