<script setup lang="ts">
	import { useCrudStore } from "/src/stores/modules/crud";
	import { storeToRefs } from "pinia";
	import { onUnmounted, ref } from "vue";
	import statusColor from "/src/utils/status-color";
	import dayjs from "dayjs";

	const printStore = useCrudStore();
	const { rows: pledges, meta, loading, url } = storeToRefs(printStore);
	const options = ref({});

	const headers = [
		{ key: "key", title: "#", sortable: false },
		{ key: "reference", title: "Ref", sortable: false },
		{ key: "institution", title: "Banque/Concessionnaire", sortable: false },
		{ key: "owner_name", title: "Nom du propriétaire", sortable: false },
		{ key: "vin", title: "Vin", sortable: false },
		{ key: "created_at", title: "Date et heure", sortable: false },
		{ key: "statut", title: "Statut", sortable: false },
	];

	const getPledges = async (metadata) => {
		url.value = "pledge";
		await printStore.fetchRows(metadata);
	};

	watch(
		options,
		(newOptions) => {
			getPledges(newOptions);
		},
		{ deep: true, immediate: true },
	);

	onUnmounted(() => {
		pledges.value = [];
		loading.value = false;
	});
</script>

<template>
	<div class="page-content-inner">
		<DataTable
			:headers="headers"
			:items="pledges"
			:loading="loading"
			:meta="meta"
			empty-text="Aucune demande d'émission de gage trouvée"
			:create-link="null"
			search
			@update-datatable="(newOptions) => (options = { ...options, ...newOptions })"
		>
			<template #vin="{ item }">
				{{ item.vehicle.vin }}
			</template>
			<template #owner_name="{ item }">
				{{ item.vehicle_owner.identity.fullName }}
			</template>
			<template #institution="{ item }">
				{{ item.institution_emitted.acronym }} - {{ item.institution_emitted.name }}
			</template>
			<template #statut="{ item }">
				<VTag :label="item.status_label" curved :color="statusColor(item.status)" />
			</template>
			<template #created_at="{ item }">
				{{ dayjs(item.created_at).format("DD/MM/YYYY") }}
			</template>
			<template #actions="{ item }">
				<div class="block">
					<VIconButton
						v-tooltip.right="'Voir'"
						class="has-text-primary"
						icon="eye"
						:to="{
							name: 'pledge_issue_request_show',
							params: { id: item.id },
						}"
					/>
				</div>
			</template>
		</DataTable>
	</div>
</template>

<style scoped lang="scss"></style>
