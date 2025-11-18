<template>
	<div>
		<div class="columns">
			<div class="column is-one-quarter">
				<div class="select is-normal">
					<select v-model="filter_causer" class="">
						<option value="All causers">Rechercher par causers</option>
						<option v-for="causer in rows.causers" :value="causer.id">
							{{ causer.identity.fullName }} - {{ causer.type.name }}
						</option>
					</select>
				</div>
			</div>
			<div class="column is-one-quarter">
				<div class="select is-normal">
					<select v-model="filter_action" class="">
						<option value="Rechercher par action">Rechercher par action</option>
						<option v-for="action in rows.actions" :value="action.name">{{ action.label }}</option>
					</select>
				</div>
			</div>
		</div>
		<DataTable
			:headers="headers"
			:items="logs"
			:loading="loading"
			:meta="meta"
			empty-text="Aucune donnée existante"
			search
			@update-datatable="(newOptions) => (options = { ...options, ...newOptions })"
		>
			<template #name="{ item }">
				{{ item.log_name }}
			</template>
			<template #action="{ item }">
				{{ item.log_action }}
			</template>
			<template #event="{ item }">
				{{ item.event }}
			</template>
			<template #description="{ item }">
				{{ item.description }}
			</template>
			<template #created_at="{ item }">
				{{ item.created_at }}
			</template>
			<template #actions="{ item }">
				<div class="block text-center">
					<!-- v-if="can('show-im-demand')" -->
					<VIconButton
						v-tooltip.right="'Voir'"
						color="primary"
						light
						icon="eye"
						:to="{
							name: 'logs_show',
							params: { logId: item.id },
						}"
					/>
				</div>
			</template>

			<template #filters> </template>
		</DataTable>
	</div>
</template>

<script setup>
	import { storeToRefs } from "pinia";
	import { useCrudStore } from "/@src/stores/modules/crud";

	const crudStore = useCrudStore();
	const { rows, meta, loading, url } = storeToRefs(crudStore);
	const logs = ref([]);
	const getItems = async (metadata) => {
		url.value = "/activity-logs";
		await crudStore.fetchRows(metadata);
		logs.value = rows.value.logs.data;
		meta.value = {
			current_page: rows.value.logs.current_page,
			first_page_url: rows.value.logs.first_page_url,
			from: rows.value.logs.from,
			last_page: rows.value.logs.last_page,
			last_page_url: rows.value.logs.last_page_url,
			links: rows.value.logs.links,
			next_page_url: rows.value.logs.next_page_url,
			path: rows.value.logs.path,
			per_page: rows.value.logs.per_page,
			prev_page_url: rows.value.logs.prev_page_url,
			to: rows.value.logs.to,
			total: rows.value.logs.total,
		};
	};
	const options = ref({});
	const filter_action = ref("Rechercher par action");
	const filter_causer = ref("All causers");
	watch(filter_action, async () => {
		if (filter_action.value != "Rechercher par action") {
			await crudStore.fetchRows({ log_action: filter_action.value });
			logs.value = rows.value.logs.data;
			meta.value = {
				current_page: rows.value.logs.current_page,
				first_page_url: rows.value.logs.first_page_url,
				from: rows.value.logs.from,
				last_page: rows.value.logs.last_page,
				last_page_url: rows.value.logs.last_page_url,
				links: rows.value.logs.links,
				next_page_url: rows.value.logs.next_page_url,
				path: rows.value.logs.path,
				per_page: rows.value.logs.per_page,
				prev_page_url: rows.value.logs.prev_page_url,
				to: rows.value.logs.to,
				total: rows.value.logs.total,
			};
		} else {
			await crudStore.fetchRows({});
			logs.value = rows.value.logs.data;
			meta.value = {
				current_page: rows.value.logs.current_page,
				first_page_url: rows.value.logs.first_page_url,
				from: rows.value.logs.from,
				last_page: rows.value.logs.last_page,
				last_page_url: rows.value.logs.last_page_url,
				links: rows.value.logs.links,
				next_page_url: rows.value.logs.next_page_url,
				path: rows.value.logs.path,
				per_page: rows.value.logs.per_page,
				prev_page_url: rows.value.logs.prev_page_url,
				to: rows.value.logs.to,
				total: rows.value.logs.total,
			};
		}
	});

	watch(filter_causer, async () => {
		if (filter_causer.value != "All causers") {
			await crudStore.fetchRows({ causer_id: filter_causer.value });
			logs.value = rows.value.logs.data;
			meta.value = {
				current_page: rows.value.logs.current_page,
				first_page_url: rows.value.logs.first_page_url,
				from: rows.value.logs.from,
				last_page: rows.value.logs.last_page,
				last_page_url: rows.value.logs.last_page_url,
				links: rows.value.logs.links,
				next_page_url: rows.value.logs.next_page_url,
				path: rows.value.logs.path,
				per_page: rows.value.logs.per_page,
				prev_page_url: rows.value.logs.prev_page_url,
				to: rows.value.logs.to,
				total: rows.value.logs.total,
			};
		} else {
			await crudStore.fetchRows({});
			logs.value = rows.value.logs.data;

			meta.value = {
				current_page: rows.value.logs.current_page,
				first_page_url: rows.value.logs.first_page_url,
				from: rows.value.logs.from,
				last_page: rows.value.logs.last_page,
				last_page_url: rows.value.logs.last_page_url,
				links: rows.value.logs.links,
				next_page_url: rows.value.logs.next_page_url,
				path: rows.value.logs.path,
				per_page: rows.value.logs.per_page,
				prev_page_url: rows.value.logs.prev_page_url,
				to: rows.value.logs.to,
				total: rows.value.logs.total,
			};
		}
	});
	watch(
		options,
		(newOptions) => {
			getItems(newOptions);
		},
		{ deep: true, immediate: true }
	);
	const headers = [
		{ title: "Nom", key: "name", sortable: true },
		{ title: "Action", key: "action", sortable: true },
		{ title: "Événement", key: "event" },
		{ title: "Description", key: "description" },
		{ title: "Date", key: "created_at", sortable: true },
	];
	onUnmounted(() => {
		meta.value = null;
	});
</script>

<style lang="scss" scoped></style>
