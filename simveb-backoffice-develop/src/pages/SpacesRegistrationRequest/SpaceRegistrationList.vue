<script setup lang="ts">
	import { useCrudStore } from "/@src/stores/modules/crud";
	import { storeToRefs } from "pinia";
	import { onUnmounted } from "vue";
	import statusColor from "/@src/utils/status-color";
	import dayjs from "dayjs";
	import { userHasPermissions } from "/@src/utils/permission";

	const crudStore = useCrudStore();
	const { rows: spaces, meta, loading, url } = storeToRefs(crudStore);
	const options = ref({});
	const { can } = userHasPermissions();

	const headers = [
		{ title: "Type d'espace", key: "profile_type", sortable: true },
		{ title: "Institution", key: "institution", sortable: true },
		{ title: "Status", key: "status_label", sortable: true },
		{ title: "Date de création", key: "created_at", sortable: true },
	];

	const getSpaces = async (metadata) => {
		url.value = "space-registration-requests";
		await crudStore.fetchRows(metadata);
	};

	watch(
		options,
		(newOptions) => {
			getSpaces(newOptions);
		},
		{ deep: true, immediate: true },
	);

	onUnmounted(() => {
		spaces.value = [];
		loading.value = false;
	});
</script>

<template>
	<div class="page-content-inner">
		<DataTable
			:headers="headers"
			:items="spaces"
			:loading="loading"
			:meta="meta"
			empty-text="Aucun enregistrement trouvé"
			create-title="Enregistrer un espace"
			:create-link="
				can('store-space-registration-request') ? { name: 'spaces_registration_request_create' } : null
			"
			search
			@update-datatable="(newOptions) => (options = { ...options, ...newOptions })"
		>
			<template #profile_type="{ item }">
				{{ item.profile_type.name }}
			</template>
			<template #institution="{ item }">
				{{ item.institution.name }}
			</template>
			<template #status_label="{ item }">
				<VTag :label="item.status_label" curved :color="statusColor(item.status)" />
			</template>
			<template #created_at="{ item }">
				{{ dayjs(item.created_at).format("DD/MM/YYYY") }}
			</template>
			<template #actions="{ item }">
				<div class="block">
					<VIconButton
						v-if="can('show-space-registration-request')"
						v-tooltip.right="'Voir'"
						class="has-text-primary"
						icon="eye"
						:to="{
							name: 'spaces_registration_request_show',
							params: { id: item.id },
						}"
					/>
				</div>
			</template>
		</DataTable>
	</div>
</template>

<style scoped lang="scss"></style>
