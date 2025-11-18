<template>
	<VModal
		is="form"
		:open="open"
		actions="center"
		title="Assignation à un service"
		size="large"
		@close="close"
		@submit.prevent="submit"
	>
		<template #content>
			<div class="m-2">
				<input id="organization_auto" v-model="row.affectationAutomatique" type="checkbox" class="mr-2" />
				<label class="has-text-centered has-text-weight-semibold" for="organization_auto">
					Assignation automatique ?
				</label>
			</div>
			<div v-if="!row.affectationAutomatique">
				<VField>
					<VLabel required>Choisissez le service</VLabel>
					<VControl fullwidth :errors="errors.organization_id">
						<v-select
							v-model="row.organization_id"
							:options="formData.organizations"
							:reduce="(item) => item.id"
							label="name"
							placeholder="Sélectionnez le service"
						></v-select>
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
	const { demand: demandInfo, formData, formLoading, errors } = storeToRefs(demandStore);

	const row = ref({
		affectationAutomatique: true,
	});

	const submit = async () => {
		await demandStore
			.assignDemand("service", {
				demand_id: demandInfo.value.id,
				...row.value,
			})
			.then(async () => {
				notyf.success("La demande a bien été assignée au service");
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
			if (value && demandInfo.value.active_treatment.organization_id) {
				row.value = {
					affectationAutomatique: false,
					organization_id: demandInfo.value.active_treatment.organization_id,
				};
				notyf.warning(`La demande est déjà assignée à ${demandInfo.value.active_treatment.organization.name}`);
			}
		}
	);
</script>
