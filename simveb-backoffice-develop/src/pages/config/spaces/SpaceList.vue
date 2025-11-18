<template>
	<div class="page-content-inner">
		<DataTable
			:headers="headers"
			:items="rows"
			:loading="loading"
			:meta="meta"
			:create-button="false"
			empty-text="Aucun espace existant"
			search
			@update-datatable="(newOptions) => (options = { ...options, ...newOptions })"
		>
			<template #type_label="{ item }">
				<router-link
					v-if="item.has_suspension"
					v-tooltip.right="'Une demande de suspension est en attente pour cet espace'"
					:to="{
						name: 'space_view',
						params: { spaceId: item.id },
					}"
					class="mr-2"
				>
					<i class="fa fa-exclamation-triangle text-yellow-500 fa-xl fa-shake" />
				</router-link>
				<router-link
					v-else-if="item?.status === 'suspended' && !item?.has_lifting"
					v-tooltip.right="'Cet espace est suspendu'"
					:to="{
						name: 'space_view',
						params: { spaceId: item.id },
					}"
					class="mr-2"
				>
					<i class="fa fa-exclamation-circle text-danger-500 fa-xl fa-shake" />
				</router-link>
				<router-link
					v-else-if="item?.has_lifting"
					v-tooltip.right="'Une demande de levée de suspension est en attente pour cet espace'"
					:to="{
						name: 'space_view',
						params: { spaceId: item.id },
					}"
					class="mr-2"
				>
					<i class="fa fa-exclamation-triangle text-yellow-500 fa-xl fa-shake" />
				</router-link>
				{{ item.type_label }}
			</template>
			<template #institution="{ item }">
				{{ item.institution?.name }}
			</template>
			<template #status="{ item }">
				<VTag :label="item.status_label" curved :color="statusColor(item?.status)" />
			</template>
			<template #actions="{ item }">
				<div class="block">
					<VIconButton
						v-if="can('show-space')"
						class="has-text-primary"
						icon="eye"
						:to="{
							name: 'space_view',
							params: { spaceId: item.id },
						}"
					/>
				</div>
			</template>
		</DataTable>
	</div>
</template>

<script setup lang="ts">
	import { userHasPermissions } from "/@src/utils/permission";
	import { useCrudStore } from "/@src/stores/modules/crud";
	import { storeToRefs } from "pinia";
	import { onBeforeMount, onUnmounted, ref, watch } from "vue";
	import statusColor from "/@src/utils/status-color";

	const { can } = userHasPermissions();

	const crudStore = useCrudStore();
	const { rows, meta, loading, url } = storeToRefs(crudStore);
	const options = ref({});

	onBeforeMount(() => {
		url.value = "/spaces";
	});

	const getItems = async (metadata) => {
		url.value = "/spaces";
		await crudStore.fetchRows(metadata);
	};

	const headers = [
		{ title: "Libellé", key: "type_label" },
		{ title: "Institution", key: "institution" },
		{ title: "Status", key: "status" },
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

<style scoped lang="scss"></style>
