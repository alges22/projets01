<template>
	<!--	header-->
	<slot name="header">
		<div class="intro-y col-span-12 flex flex-wrap sm:flex-nowrap items-center">
			<IconButton
				v-if="createButton || createLink"
				icon="plus"
				color="btn-primary"
				class="shadow-md mr-2"
				@click="createButton ? $emit('create') : $router.push(createLink)"
			>
				<template v-if="createTitle">{{ createTitle }}</template>
				<template v-else> <i class="fa-light fa-plus"></i></template>
			</IconButton>

			<slot name="header-actions"></slot>

			<div class="w-auto mt-3 sm:mt-0 ml-auto">
				<form v-if="search" class="w-56 relative text-slate-500">
					<input
						id="search"
						type="search"
						class="form-control w-56 box pr-10"
						:placeholder="searchPlaceholder"
						:aria-label="searchPlaceholder"
						@input="onSearch($event.target.value)"
					/>
					<i class="fa-light fa-search w-4 h-4 absolute my-auto inset-y-0 mr-3 right-0" />
				</form>
			</div>
			<slot name="search"></slot>
		</div>
	</slot>

	<!--	Body-->
	<div class="intro-y col-span-12 overflow-auto lg:overflow-visible">
		<table class="table table-report -mt-2">
			<thead class="bg-gray-100">
				<tr>
					<th
						v-for="header in headers"
						:key="header.key"
						class="whitespace-nowrap"
						:class="[
							header.sortable ? 'sorting' : 'sorting_disabled',
							tableOptions.sort.hasOwnProperty(header.key)
								? 'sorting_' + tableOptions.sort[header.key]
								: '',
							header.center ? 'text-center' : '',
						]"
						:colspan="header.colspan"
						:rowspan="header.rowspan"
						:aria-label="
							header.title +
							': activate to sort column ' +
							(tableOptions.sortBy === header.key
								? tableOptions.sortDirection === 'asc'
									? 'ascending'
									: 'descending'
								: 'ascending')
						"
						:aria-sort="
							tableOptions.sortBy === header.key
								? tableOptions.sortDirection === 'asc'
									? 'ascending'
									: 'descending'
								: 'none'
						"
						@click="
							header.sortable
								? sort(
										header.key,
										tableOptions.sort.hasOwnProperty(header.key)
											? tableOptions.sort[header.key] === 'asc'
												? 'desc'
												: 'asc'
											: 'asc'
								  )
								: null
						"
					>
						{{ header.title.toUpperCase() }}
					</th>
					<th v-if="hasActions" aria-label="Actions" class="sorting_disabled text-center whitespace-nowrap">
						ACTIONS
					</th>
				</tr>
			</thead>
			<tbody>
				<tr v-if="loading" class="odd">
					<td colspan="4" class="dataTables_empty">
						<DataTableSkeleton />
					</td>
				</tr>
				<template v-else>
					<tr
						v-for="(item, index) in items"
						:key="index"
						:class="{
							odd: index % 2,
							even: !(index % 2),
							[item.class]: item.class,
							['intro-x h-16']: true,
						}"
					>
						<td v-for="header in headers" :key="header.key" :class="[header.sorting ? 'sorting_1' : '']">
							<slot :name="header.key" :item="item">
								<template v-if="header.idField">
									<KeyField :value="item[header.key]" />
								</template>
								<template v-else>
									{{ item[header.key] }}
								</template>
							</slot>
						</td>
						<td v-if="hasActions" class="table-report__action w-56">
							<div class="flex justify-center items-center">
								<slot name="actions" :item="item"></slot>
							</div>
						</td>
					</tr>

					<tr v-if="Object.keys(error).length > 0 && items.length === 0" class="odd w-full">
						<td class="dataTables_empty text-center" colspan="100%">Une erreur est survenue</td>
					</tr>

					<tr v-else-if="items && items.length === 0" class="odd">
						<td class="dataTables_empty text-center" colspan="100%">
							{{ emptyText ?? "Aucun enregistrements correspondants trouvés !" }}
						</td>
					</tr>
				</template>
				<slot name="additionalRows"></slot>
			</tbody>
		</table>
	</div>

	<!--	Pagination-->

	<div v-if="meta" class="intro-y col-span-12 flex flex-wrap sm:flex-row sm:flex-nowrap items-center">
		<nav class="w-full sm:w-auto sm:mr-auto">
			<pagination-component
				v-if="Object.keys(meta).length > 0"
				:current-page="meta.current_page"
				:total-pages="getNumberOfPages"
				@paginate="(page) => (tableOptions.page = page)"
			/>
		</nav>
		<div>
			<label for="dataTable-meta_per_page" class="me-2">Affichage de :</label>
			<select id="dataTable-meta_per_page" class="w-20 form-select box mt-3 sm:mt-0">
				<option :value="items.length" selected>{{ items.length }}</option>
				<option value="4">4</option>
				<option value="6">6</option>
				<option value="8">8</option>
				<option value="12">12</option>
			</select>
			<span class="mx-2">sur</span>
			<span>{{ meta.total }} éléments</span>
		</div>
	</div>
</template>

<script setup>
	import { computed, ref, watch } from "vue";
	import { debounce } from "@/helpers/utils";
	import IconButton from "@/components/IconButton.vue";
	import PaginationComponent from "@/components/DataTable/PaginationComponent.vue";
	import DataTableSkeleton from "@/components/Skeleton/DataTableSkeleton.vue";
	import KeyField from "@/components/KeyField.vue";

	const props = defineProps({
		headers: {
			type: Array,
			required: true,
		},
		emptyText: {
			type: String,
			default: null,
			required: false,
		},
		createLink: {
			type: [String, Object],
			required: false,
			default: null,
		},
		createButton: {
			type: Boolean,
			required: false,
		},
		createTitle: {
			type: String,
			required: false,
			default: null,
		},
		meta: {
			type: Object,
			required: false,
			default: null,
		},
		items: {
			type: Array,
			required: true,
		},
		search: {
			type: Boolean,
			required: false,
			default: false,
		},
		searchPlaceholder: {
			type: String,
			required: false,
			default: "Rechercher...",
		},
		loading: {
			type: Boolean,
			required: false,
			default: false,
		},
		hasActions: {
			type: Boolean,
			required: false,
			default: true,
		},
		filters: {
			type: Array,
			required: false,
			default: () => [],
		},
		error: {
			type: Object,
			required: false,
			default: () => ({}),
		},
		striped: {
			type: Boolean,
			required: false,
			default: false,
		},
		hasHeader: {
			type: Boolean,
			required: false,
			default: true,
		},
		headerClass: {
			type: String,
			required: false,
			default: "",
		},
	});

	const getNumberOfPages = computed(() => {
		return Math.ceil(props.meta.total / tableOptions.value.per_page);
	});

	const emit = defineEmits(["update-datatable", "create"]);
	const tableOptions = ref({
		sort: {},
		per_page: 10,
		page: 1,
		search: "",
	});

	const sort = (column, direction) => {
		let sort = {};
		sort[column] = direction;
		tableOptions.value.sort = sort;
	};

	// const updateFilter = (filter) => {
	// 	tableOptions.value = { ...tableOptions.value, ...filter };
	// };

	/*const deStructure = (obj) => {
		const result = {};
		Object.keys(obj).forEach((key) => {
			if (obj[key] === null || obj[key] === undefined || obj[key] === "") return;
			if (typeof obj[key] === "object") {
				Object.assign(result, deStructure(obj[key]));
			} else {
				result[key] = obj[key];
			}
		});
		return result;
	};*/

	const onSearch = debounce((value) => {
		tableOptions.value.search = value;
	}, 500);

	watch(
		tableOptions,
		(newOptions) => {
			emit("update-datatable", newOptions);
		},
		{ deep: true }
	);
</script>

<style></style>
