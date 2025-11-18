<template>
	<div class="page-content-inner">
		<DataTable
			:headers="headers"
			:items="rows"
			:loading="loading"
			:meta="meta"
			create-title="Nouvel enregistrement"
			:create-link="
				can('store-affiliate-registration-request') ? { name: 'affiliate_registration_request_create' } : null
			"
			empty-text="Liste vide"
			search
			@update-datatable="(newOptions) => (options = { ...options, ...newOptions })"
		>
			<template #profile_id="{ item }">
				{{ item.profile_type?.name }}
			</template>
			<template #status="{ item }">
				<VTag :label="t('status.' + item.status)" curved :color="statusColor(item.status)" />
			</template>
			<template #name="{ item }">
				<span class="truncate">{{ item.social_reason }}</span>
			</template>
			<template #created_at="{ item }">
				{{ dayjs(item.created_at).format("DD-MM-YYYY : H:mm") }}
			</template>
			<template #actions="{ item }">
				<VIconButton
					v-if="can('show-affiliate-registration-request')"
					class="has-text-primary"
					icon="eye"
					:to="{
						name: 'affiliate_registration_request_show',
						params: { id: item.id },
					}"
				/>
				<VIconButton
					v-if="can('delete-affiliate-registration-request')"
					class="ml-2 has-text-danger"
					icon="trash"
				/>
			</template>
		</DataTable>
	</div>
</template>

<script setup lang="ts">
	import { useI18n } from "vue-i18n";
	import { storeToRefs } from "pinia";
	import { onBeforeMount, onUnmounted, ref, watch } from "vue";
	import { useCrudStore } from "/@src/stores/modules/crud";
	import { userHasPermissions } from "/@src/utils/permission";
	import { returnPreviousPage } from "/@src/utils/helpers";
	import dayjs from "dayjs";
	import statusColor from "/@src/utils/status-color";

	const { can } = userHasPermissions();

	const crudStore = useCrudStore();
	const { rows, meta, loading, url } = storeToRefs(crudStore);
	const options = ref({});

	const urlPath = "/affiliate-registration-requests";

	onBeforeMount(() => {
		url.value = urlPath;
	});

	const getItems = async (metadata) => {
		url.value = urlPath;
		await crudStore.fetchRows(metadata);
	};

	const headers = [
		{ title: "Raison sociale / Nom", key: "name", sortable: true },
		{ title: "Type de profile", key: "profile_id" },
		{ title: "IFU", key: "ifu" },
		{ title: "Statut de l'enregistrement", key: "status" },
		{ title: "Créé le", key: "created_at", sortable: true },
	];

	watch(
		options,
		(newOptions) => {
			getItems(newOptions);
		},
		{ deep: true, immediate: true }
	);

	onUnmounted(() => {
		crudStore.reset();
	});
</script>

<style lang="scss" scoped>
	.truncate {
		display: block;
		width: 150px;
		white-space: nowrap;
		overflow: hidden;
		text-overflow: ellipsis;
	}
</style>
