<script setup lang="ts">
	import { storeToRefs } from "pinia";
	import { onBeforeMount, onUnmounted, ref, watch } from "vue";
	import { useCrudStore } from "/@src/stores/modules/crud";
	import { userHasPermissions } from "/@src/utils/permission";
	import { returnPreviousPage } from "/@src/utils/helpers";
	import dayjs from "dayjs";

	const { can } = userHasPermissions();

	const crudStore = useCrudStore();
	const { rows, meta, loading, url } = storeToRefs(crudStore);
	const options = ref({});

	const urlPath = "/affiliates";

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
		{ title: "Email", key: "email" },
		{ title: "Téléphone", key: "telephone", sortable: true },
		{ title: "IFU", key: "ifu" },
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
<template>
	<div class="page-content-inner">
		<DataTable
			:headers="headers"
			:items="rows"
			:loading="loading"
			:meta="meta"
			:create-button="false"
			empty-text="Liste vide"
			search
			@update-datatable="(newOptions) => (options = { ...options, ...newOptions })"
		>
			<template #profile_id="{ item }">
				{{ item.profile_type?.name }}
			</template>
			<template #name="{ item }">
				<span class="truncate">{{ item.social_reason }}</span>
			</template>
			<template #created_at="{ item }">
				{{ dayjs(item.created_at).format("DD-MM-YYYY : H:mm") }}
			</template>
			<template #actions="{ item }">
				<div class="block">
					<VIconButton
						v-if="can('show-affiliate')"
						class="has-text-primary"
						icon="eye"
						:to="{
							name: 'affiliate_show',
							params: { id: item.id },
						}"
					/>
				</div>
			</template>
		</DataTable>
	</div>
</template>

<style lang="scss" scoped>
	.truncate {
		display: block;
		width: 150px;
		white-space: nowrap;
		overflow: hidden;
		text-overflow: ellipsis;
	}
</style>
