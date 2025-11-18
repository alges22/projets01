<template>
	<VModal
		is="form"
		:open="open"
		actions="center"
		title="Assignation à un centre de gestion"
		size="large"
		@close="close"
		@submit.prevent="submit"
	>
		<template #content>
			<div class="m-2">
				<input id="center_auto" v-model="row.affectationAutomatique" type="checkbox" class="mr-2" />
				<label class="has-text-centered has-text-weight-semibold" for="center_auto">
					Assignation automatique ?
				</label>
			</div>
			<div v-if="!row.affectationAutomatique">
				<VField>
					<VLabel required>Choisissez le centre</VLabel>
					<VControl fullwidth :errors="errors.center_id">
						<v-select
							v-model="row.center_id"
							:options="formData.centers"
							:reduce="(item) => item.id"
							label="name"
							placeholder="Sélectionnez le centre"
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
			.assignDemand("center", {
				demand_id: demandInfo.value.id,
				...row.value,
			})
			.then(async () => {
				notyf.success("La demande a bien été assignée au centre de gestion");
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
			if (value && demandInfo.value.active_treatment.management_center_id) {
				row.value = {
					affectationAutomatique: false,
					center_id: demandInfo.value.active_treatment.management_center_id,
				};
				notyf.warning(
					`La demande est déjà assignée à ${demandInfo.value.active_treatment.management_center.name}`
				);
			}
		}
	);
</script>
