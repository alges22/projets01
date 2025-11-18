<script setup lang="ts">
	import { userHasPermissions } from "/@src/utils/permission";

	const route = useRoute();

	const { can } = userHasPermissions();
	const options = ref({});

	const headers = [
		{ title: "Référence", key: "reference", sortable: true },
		{ title: "Nom du demandeur", key: "full_name", sortable: true },
		{ title: "IFU", key: "ifu" },
	];

	watch(route, async (newRoute) => {
		nbWheels.value = newRoute.params.wheels;
	});

	const loading = false;

	const rows = [];

	const meta = {
		current_page: 1,
		total: 3,
		per_page: 10,
		from: 1,
		to: 3,
		links: null,
	};
</script>
<template>
	<div class="page-content-inner">
		<DataTable
			:headers="headers"
			:items="rows"
			:loading="loading"
			:meta="meta"
			empty-text="Aucune demande trouvée"
			search
			@update-datatable="(newOptions) => (options = { ...options, ...newOptions })"
		>
			<template #plate_color_id="{ item }">
				<VTag color="" :label="item.plate_color_id" curved />
			</template>
			<template #actions="{ item }">
				<div class="block">
					<VIconButton
						v-if="can('show-gray-card-demand')"
						class="has-text-primary"
						icon="eye"
						:to="{
							name: 'immatriculation_demands_show',
							params: { id: item.id, wheel: nbWheels },
						}"
					/>
				</div>
			</template>
		</DataTable>
	</div>
</template>
