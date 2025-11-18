<template>
	<div class="page-content-inner">
		<div class="columns">
			<div v-if="service" class="font-medium text-lg">
				{{ service?.name }} <i class="fa fa-angle-right me-4"></i>
			</div>

			<span v-if="step" class="text-lg font-medium">
				{{ step?.label }} (Ensemble des actions à effectuer sur cette étape)
			</span>
		</div>

		<DataTable
			:headers="headers"
			:items="rows"
			:loading="loading"
			:meta="meta"
			create-title="Ajouter une nouvelle action"
			:create-link="{ name: 'action_creation' }"
			search
			@update-datatable="(newOptions) => (options = { ...options, ...newOptions })"
		>
			<template #name="{ item }">
				{{ item.permission_service.permission.label }}
			</template>
			<template #actions="{ item }">
				<div class="block">
					<VIconButton
						:to="{
							name: 'action_creation',
							params: { actionId: item.id },
						}"
						class="mr-2 has-text-primary"
						icon="edit"
					/>
					<!-- <VIconButton
						v-if="true"
						v-tooltip.right="'Voir'"
						color="primary"
						light
						icon="eye"
						:to="{
							name: 'action_show',
							params: { actionId: item.id },
						}"
					/> -->
					<VIconButton
						:loading="formLoading"
						class="ml-2 has-text-danger"
						icon="trash"
						@click="handleDelete(item)"
					/>
				</div>
			</template>
		</DataTable>
	</div>
</template>

<script setup>
	import { useCrudStore } from "/@src/stores/modules/crud";
	import { storeToRefs } from "pinia";
	import { onBeforeMount } from "vue";
	import { useActionStore } from "/@src/stores/modules/action";
	import client from "/@src/composable/axiosClient";
	import { useNotyf } from "/@src/composable/useNotyf";
	import { useDeleteConfirmation } from "/@src/composable/useDeleteConfirmation";

	const actionStore = useActionStore();
	const { service, step, nextPosition } = storeToRefs(actionStore);
	const crudStore = useCrudStore();
	const notyf = useNotyf();
	const { rows, url, loading, meta } = storeToRefs(crudStore);

	const headers = [
		{ title: "Nom", key: "name" },
		{ title: "Position", key: "position" },
		{ title: "Durée (en heure)", key: "duration" },
		{ title: "Mode de déclenchement", key: "process_type" },
	];
	// const options = ref({});
	const options = ref({});
	const props = defineProps(["serviceId", "stepId"]);

	const getItems = async (metadata) => {
		if (options.service_step_id) {
			await crudStore.fetchRows(metadata).finally(() => {
				loading.value = true;
			});
		} else {
			await crudStore.fetchRows(metadata);
		}
	};

	const { handleDelete } = useDeleteConfirmation((item) => {
		crudStore.deleteRow(item.id).then(() => {
			notyf.success("L'action a bien été supprimé");
			getItems(options.value);
		});
	}, "Êtes vous sur de vouloir supprimer l'action' ?");

	watch(
		options,
		(newOptions) => {
			getItems(newOptions).then(() => (nextPosition.value = rows.value.length + 1));
		},
		{ deep: true, immediate: true }
	);

	onBeforeMount(() => {
		url.value = "/actions";
		service.value = {};
		step.value = {};
		client.get("/services/" + props.serviceId).then((res) => {
			service.value = res.data;
			step.value = service.value.steps.find((step) => step.id == props.stepId);
			options.value = { service_step_id: step.value?.pivot.id };
		});
	});
</script>

<style lang="scss" scoped></style>
