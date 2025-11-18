<script setup lang="ts">
	import { storeToRefs } from "pinia";
	import { onUnmounted, ref } from "vue";
	import { useCrudStore } from "/@src/stores/modules/crud";
	import dayjs from "dayjs";
	import statusColor from "/@src/utils/status-color";

	const printStore = useCrudStore();
	const { rows: oppositions, meta, loading, url } = storeToRefs(printStore);
	const options = ref({});

	const headers = [
		{ key: "reference", title: "Ref", sortable: false },
		{ key: "owner_name", title: "Nom du propriétaire", sortable: false },
		{ key: "created_at", title: "Date et heure", sortable: false },
		{ key: "statut", title: "Statut", sortable: false },
	];

	const getOppositions = async (metadata) => {
		url.value = "oppositions";
		await printStore.fetchRows(metadata);
	};

	watch(
		options,
		(newOptions) => {
			getOppositions(newOptions);
		},
		{ deep: true, immediate: true }
	);

	onUnmounted(() => {
		oppositions.value = [];
		loading.value = false;
	});
</script>

<template>
	<div class="page-content-inner">
		<DataTable
			:headers="headers"
			:items="oppositions"
			:loading="loading"
			:meta="meta"
			empty-text="Aucun produit d'épargne trouvé"
			search
		>
			<template #owner_name="{ item }">
				{{ item.owner.identity.fullName }}
			</template>
			<template #created_at="{ item }">
				{{ dayjs(item.created_at).format("DD/MM/YYYY HH:mm") }}
			</template>
			<template #statut="{ item }">
				<VTag :label="item.status_label" curved :color="statusColor(item.status)" />
			</template>
			<template #actions="{ item }">
				<div class="block">
					<VIconButton
						v-tooltip.right="'Voir'"
						class="has-text-primary"
						icon="eye"
						:to="{
							name: 'oppositions_show',
							params: { id: item.id },
						}"
					/>
				</div>
			</template>
		</DataTable>
	</div>
</template>

<style scoped lang="scss"></style>
