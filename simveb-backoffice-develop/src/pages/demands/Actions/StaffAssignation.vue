<template>
	<VModal
		is="form"
		:open="open"
		actions="center"
		title="Assignation à un staff"
		size="large"
		@close="close"
		@submit.prevent="submit"
	>
		<template #content>
			<div class="m-2">
				<input id="staff_auto" v-model="row.affectationAutomatique" type="checkbox" class="mr-2" />
				<label class="has-text-centered has-text-weight-semibold" for="staff_auto">
					Assignation automatique ?
				</label>
			</div>
			<div v-if="!row.affectationAutomatique">
				<VField>
					<VLabel required>Entrer le NPI du staff à assigner</VLabel>
					<VControl fullwidth :errors="errors.npi || []">
						<VInput
							v-model="row.staff_id"
							name="staff_id"
							placeholder="NPI"
							required
							type="text"
							pattern="\d{10}"
						/>
					</VControl>
				</VField>
			</div>
		</template>
		<template #action>
			<VButton type="submit" color="primary" raised :loading="formLoading"> Confirmer </VButton>
		</template>
	</VModal>
</template>
<script setup lang="ts">
	import { useDemandStore } from "/src/stores/modules/demand";
	import { storeToRefs } from "pinia";
	import { useNotyf } from "/src/composable/useNotyf";

	const props = defineProps<{
		open: boolean;
	}>();

	const emit = defineEmits(["close"]);
	const notyf = useNotyf();
	const demandStore = useDemandStore();
	const { demand: demandInfo, formLoading, errors } = storeToRefs(demandStore);

	const row = ref({
		affectationAutomatique: true,
	});

	const submit = async () => {
		await demandStore
			.assignDemand("staff", {
				demand_id: demandInfo.value.id,
				npi: row.value.affectationAutomatique ? null : row.value.staff_id,
			})
			.then(async () => {
				notyf.success("La demande a bien été assignée au staff");
				await demandStore.fetchDemand(demandInfo.value.id);
				emit("close");
			});
	};

	const close = () => {
		row.value = { affectationAutomatique: true };
		emit("close");
	};

	watch(
		() => props.open,
		(value) => {
			if (value && demandInfo.value.active_treatment.responsible_id) {
				row.value = {
					affectationAutomatique: false,
					// npi: demandInfo.value.active_treatment.identity.npi,
				};
				notyf.warning(
					`La demande est déjà assignée à ${demandInfo.value.active_treatment.responsible.identity.fullName}`,
				);
			}
		},
	);
</script>
