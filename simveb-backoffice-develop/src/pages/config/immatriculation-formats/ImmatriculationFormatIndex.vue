<template>
	<div class="page-content-inner">
		<DataTable
			:headers="headers"
			:items="formats || []"
			:loading="loading"
			:meta="meta"
			create-title="Ajout d'un format"
			:create-link="can('store-immatriculation-format') ? { name: 'immatriculation_formats_create' } : null"
			empty-text="Liste vide"
			search
			@update-datatable="(newOptions) => (options = { ...options, ...newOptions })"
		>
			<template #created_at="{ item }">
				{{ dayjs(item.created_at).format("DD-MM-YYYY") }}
			</template>

			<template #category="{ item }">
				{{ item.vehicle_category?.name }}
			</template>

			<template #components="{ item: { components } }">
				<SquarePlateView
					:immatriculation-number="
						[...components]
							.sort((a, b) => a.pivot.position - b.pivot.position)
							.map((component) => component.pivot.value)
					"
				/>
			</template>
			<template #actions="{ item }">
				<div class="block">
					<VIconButton
						v-if="can('update-immatriculation-format')"
						class="has-text-primary"
						icon="edit"
						:to="{
							name: 'immatriculation_formats_create',
							params: { id: item.id },
						}"
					/>
					<VIconButton
						v-if="can('delete-immatriculation-format')"
						class="ml-2 has-text-danger"
						icon="trash"
						@click="openDeleteModal(item)"
					/>
				</div>
			</template>
			<template #format="{ item }">
				{{ item.formatLabel }}
			</template>
		</DataTable>

		<VModal
			v-if="can('delete-immatriculation-format')"
			:open="deleteModalOpen"
			size="small"
			actions="center"
			title="Confirmation"
			@close="deleteModalOpen = false"
		>
			<template #content>
				<div class="has-text-centered">
					<p class="mb-4">Voulez-vous vraiment supprimer ce format ?</p>
				</div>
			</template>
			<template #action>
				<VButton color="primary" raised @click="handleDelete">Confirm</VButton>
			</template>
		</VModal>
	</div>
</template>

<script setup lang="ts">
	import { useImmatriculationFormatStore } from "/@src/stores/modules/immatriculation-format";
	import { userHasPermissions } from "/@src/utils/permission";
	import { storeToRefs } from "pinia";
	import { ref, watch } from "vue";
	import dayjs from "dayjs";
	import { Notyf } from "notyf";
	import SquarePlateView from "/src/components/ImmatriculationPlateView.vue";

	const { can } = userHasPermissions();
	const notyf = new Notyf();
	const store = useImmatriculationFormatStore();
	const { formats, meta, loading, format } = storeToRefs(store);
	const options = ref({});
	const deleteModalOpen = ref(false);

	const getFormats = async (metadata) => {
		await store.fetchFormats(metadata);
	};

	const openDeleteModal = (item) => {
		format.value = item;
		deleteModalOpen.value = true;
	};

	const handleDelete = async () => {
		await store.deleteFormat(format.value.id).then(() => {
			deleteModalOpen.value = false;
			format.value = {};
			notyf.success("Format d'immatriculation supprimé avec succès");
			getFormats(null);
		});
	};

	const headers = [
		{ title: "Catégorie", key: "category" },
		{ title: "Composants", key: "components" },
		{ title: "Format", key: "format" },
		{ title: "Date de création", key: "created_at", sortable: true },
	];

	watch(
		options,
		(newOptions) => {
			getFormats(newOptions);
		},
		{ deep: true, immediate: true }
	);
</script>

<style scoped lang="scss"></style>
