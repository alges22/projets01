export default function statusColor(status: string) {
	if (
		[
			"pending",
			"processing",
			"suspended",
			"waiting_for_payment",
			"submitted",
			"assigned_to_center",
			"assigned_to_service",
			"assigned_to_staff",
			"affected_to_interpol",
		].includes(status)
	) {
		return "warning";
	} else if (
		[
			"completed",
			"success",
			"validated",
			"print_order_emitted",
			"pre_validated",
			"verified",
			"approved",
			"validated_by_interpol",
			"validated_by_anatt",
			"plate_printed",
			"confirmed",
			"opposition_emitted",
			"opposition_lifted_emitted",
			"judge_validated",
			"clerk_validated",
			"institution_validated",
			"justice_validated",
			"anatt_validated",
		].includes(status)
	) {
		return "success";
	} else if (
		[
			"failed",
			"error",
			"rejected",
			"rejected_by_interpol",
			"rejected_by_anatt",
			"judge_rejected",
			"clerk_rejected",
			"institution_rejected",
			"justice_rejected",
			"anatt_rejected",
		].includes(status)
	) {
		return "danger";
	} else if (["canceled", "closed", "confirmed", ""].includes(status)) {
		return "secondary";
	} else {
		return "primary";
	}
}
