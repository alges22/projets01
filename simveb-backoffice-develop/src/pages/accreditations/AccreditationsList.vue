<template>
	<div>
		<DataTable
			:headers="headers"
			:items="accreditations"
			create-title="Faire une accréditation"
			:create-link="can('store-accreditation') ? { name: 'accreditations_create' } : null"
			:loading="loading"
			:meta="meta"
			empty-text="Aucune commande trouvée"
			search
			@update-datatable="(newOptions) => (options = { ...options, ...newOptions })"
		>
			<template #receiver="{ item }">
				<a class="ms-2 decoration-dotted underline text-primary">
					{{ item.receiver.identity.fullName }}
				</a>
				<br />
				<span class="ms-2 text-xs text-slate">{{ item.receiver.identity.npi }}</span>
			</template>
			<template #author="{ item }">
				<div class="flex items-center">
					<a class="ms-2 decoration-dotted underline text-primary">
						{{ item.author?.identity?.fullName }}
					</a>
					<span class="ms-2 text-xs text-slate">{{ item.author?.identity?.npi }}</span>
				</div>
			</template>
			<template #created_at="{ item }">
				{{ item.created_at }}
			</template>
			<template #actions="{ item }">
				<div class="block">
					<VIconButton
						v-if="can('show-accreditation')"
						v-tooltip.right="'Voir'"
						color="primary"
						light
						icon="eye"
						:to="{
							name: 'accreditations_show',
							params: { accreditationId: item.id },
						}"
					/>
				</div>
			</template>
			<template #status="{ item }">
				<VTag :label="item.status_label" :color="statusColor(item?.status)"></VTag>
			</template>

			<template #filters> </template>
		</DataTable>
	</div>
</template>

<script setup>
	import { storeToRefs } from "pinia";
	import { useCrudStore } from "/@src/stores/modules/crud";
	import statusColor from "/@src/utils/status-color";
	import { userHasPermissions } from "/@src/utils/permission.ts";

	const crudStore = useCrudStore();
	const { rows: accreditations, url, loading, meta } = storeToRefs(crudStore);
	const { can } = userHasPermissions();

	const headers = [
		{ title: "Receveur", key: "receiver", sortable: true },
		{ title: "Auteur", key: "author", sortable: true },
		{ title: "Status", key: "status", sortable: true },
	];

	const options = ref({});

	const getItems = (metadata) => {
		url.value = "accreditations";
		crudStore.fetchRows(metadata);
	};

	watch(
		options,
		(newOptions) => {
			getItems(newOptions);
		},
		{ deep: true, immediate: true }
	);
</script>

<style lang="scss" scoped></style>
