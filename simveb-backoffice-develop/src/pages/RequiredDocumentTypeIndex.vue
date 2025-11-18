<template>
	<div class="page-content-inner">
		<DataTable
			:headers="headers"
			:items="rows"
			:loading="loading"
			:meta="meta"
			create-title="Ajout d'un type de document requis"
			:create-button="can('store-required-document-type')"
			empty-text="Liste vide"
			search
			@create="modalIsOpen = true"
			@update-datatable="(newOptions) => (options = { ...options, ...newOptions })"
		>
			<template #document_type="{ item }">{{ item.document_type.description }}</template>
			<template #relation_type="{ item }">
				{{ relationArray[item.relation_type] }}
			</template>
			<template #required="{ item }">{{ item.required ? "Oui" : "Non" }}</template>
			<template #actions="{ item }">
				<div class="block">
					<VIconButton
						v-if="can('update-required-document-type')"
						class="has-text-primary"
						icon="edit"
						@click="handleUpdate(item)"
					/>
					<VIconButton
						v-if="can('update-required-document-type')"
						class="ml-2 has-text-danger"
						icon="trash"
						@click="handleDelete(item)"
					/>
				</div>
			</template>
		</DataTable>

		<VModal
			:open="modalIsOpen"
			actions="right"
			:title="update ? 'Modification' : 'Nouvel élément'"
			@close="modalIsOpen = false"
		>
			<template #content>
				<div class="container">
					<VField label="Relation" horizontal>
						<VControl fullwidth :errors="errors.relation_type || []">
							<CustomVSelect v-model="row.relation_type" name="relation_type">
								<VOption
									v-for="(relationType, index) in relationTypes"
									:key="index"
									:value="relationType.classname"
								>
									{{ relationType.basename }}
								</VOption>
							</CustomVSelect>
						</VControl>
					</VField>
					<VField label="Type de document" horizontal>
						<VControl fullwidth :errors="errors.document_type_id || []">
							<CustomVSelect v-model="row.document_type_id" name="document_type_id">
								<VOption
									v-for="(documentType, index) in formData.document_types"
									:key="index"
									:value="documentType.id"
								>
									{{ documentType.description }}
								</VOption>
							</CustomVSelect>
						</VControl>
					</VField>
					<VField grouped>
						<VControl :errors="errors.required || []">
							<VSwitchSegment
								v-model="row.required"
								label-true="Requis"
								label-false="Non requis"
								color="primary"
								name="required"
							/>
						</VControl>
					</VField>
				</div>
			</template>
			<template #action>
				<VButton :loading="formLoading" :disabled="formLoading" color="primary" raised @click="handleSubmit">
					Enregistrer
				</VButton>
			</template>
		</VModal>
	</div>
</template>

<script setup lang="ts">
	import { storeToRefs } from "pinia";
	import { onBeforeMount, onUnmounted, ref, watch } from "vue";
	import { useCrudStore } from "/@src/stores/modules/crud";
	import { userHasPermissions } from "/@src/utils/permission";
	import { useDeleteConfirmation } from "/@src/composable/useDeleteConfirmation";
	import { Notyf } from "notyf";

	const { can } = userHasPermissions();
	const crudStore = useCrudStore();
	const { rows, meta, loading, url, row, formLoading, errors, formData } = storeToRefs(crudStore);
	const notyf = new Notyf();
	const options = ref({});
	const relationTypes = ref([]);
	const relationArray = ref([]);
	const modalIsOpen = ref(false);
	const update = ref(false);

	onBeforeMount(() => {
		url.value = "/required-document-types";
	});

	const headers = [
		{ title: "Relation", key: "relation_type", sortable: true },
		{ title: "Type de document", key: "document_type", sortable: true },
		{ title: "Requis", key: "required" },
	];

	const getItems = async (metadata) => {
		url.value = "/required-document-types";
		await crudStore.fetchRows(metadata);
	};

	const { handleDelete } = useDeleteConfirmation(async (item) => {
		await crudStore.deleteRow(item.id).then(async () => {
			notyf.success("Le service a bien été supprimé");
			await getItems(options.value);
		});
	});

	const handleUpdate = (item) => {
		row.value = item;
		update.value = true;
		modalIsOpen.value = true;
	};

	const handleSubmit = async () => {
		if (update.value) {
			await crudStore.updateRow(row.value.id, row.value).then(() => {
				modalIsOpen.value = false;
				notyf.success("Modification effectuée avec succès!");
			});
		} else {
			await crudStore.createRow(row.value).then(() => {
				modalIsOpen.value = false;
				notyf.success("Enregistrement effectué avec succès!");
			});
		}
		update.value = false;
		await getItems(options.value);
		row.value = {};
	};

	watch(
		options,
		(newOptions) => {
			getItems(newOptions);
		},
		{ deep: true, immediate: true }
	);

	onMounted(() => {
		crudStore.loadCreateData().then((res) => {
			relationTypes.value = res.relation_types;

			relationTypes.value.forEach((element) => {
				relationArray.value[element.classname] = element.basename;
			});
		});
	});

	onUnmounted(() => {
		crudStore.reset();
	});
</script>
