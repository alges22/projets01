<template>
	<div>
		<div v-if="hasHeader" class="datatable-toolbar card-head mt-4">
			<VButton
				v-if="$route.name !== 'dashboard'"
				color="dark"
				icon="fa-light fa-arrow-left"
				class="is-outlined me-4 text-sm"
				size="medium"
				@click="$router.go(-1)"
			>
				Retour
			</VButton>
			<VField v-if="meta">
				<VControl class="select has-icons-left" icon="fa-light fa-sort">
					<CustomVSelect v-model="tableOptions.per_page">
						<VOption value="10">10</VOption>
						<VOption value="25">25</VOption>
						<VOption value="50">50</VOption>
						<VOption value="100">100</VOption>
					</CustomVSelect>
				</VControl>
			</VField>
			<VField v-if="search" class="pl-4">
				<VControl icon="search">
					<input
						aria-label="Rechercher"
						class="input custom-text-filter"
						placeholder="Rechercher..."
						type="search"
						@input="onSearch($event.target.value)"
					/>
				</VControl>
			</VField>

			<slot name="filters">
				<div v-if="filters.length > 0" class="mt-5">
					<h5 class="">Filtres</h5>
				</div>
			</slot>

			<VButtons>
				<VButton
					v-if="createButton || createLink"
					color="primary"
					icon="plus"
					@click="createButton ? $emit('create') : $router.push(createLink)"
				>
					{{ createTitle ?? "Ajouter" }}
				</VButton>
				<slot name="buttons"></slot>
			</VButtons>
		</div>
		<VCard elevated>
			<div class="datatable-wrapper card-inner">
				<div class="table-container">
					<table class="table datatable-table is-fullwidth" :class="{ 'is-striped': striped }">
						<thead>
							<tr>
								<th
									v-for="header in headers"
									:key="header.key"
									:class="[
										header.sortable ? 'sorting' : 'sorting_disabled',
										tableOptions.sortBy === header.key
											? tableOptions.sortDirection === 'asc'
												? 'sorting_asc'
												: 'sorting_desc'
											: '',
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
										sort(
											header.key,
											tableOptions.sortBy === header.key && tableOptions.sortDirection === 'asc'
												? 'desc'
												: 'asc',
										)
									"
								>
									{{ header.title }}
								</th>
								<th
									v-if="hasActions"
									aria-label="Actions"
									class="sorting_disabled text-center"
									style="text-align: center"
								>
									Actions
								</th>
							</tr>
						</thead>
						<tbody>
							<tr v-if="loading" class="">
								<td colspan="100%" class="align-center" style="height: 250px">
									<VLoader size="large" :active="loading" grey></VLoader>
								</td>
							</tr>
							<template v-else>
								<tr v-for="(item, index) in items" :key="index" :class="index % 2 ? 'odd' : 'even'">
									<td
										v-for="header in headers"
										:key="header.key"
										:class="[header.sorting ? 'sorting_1' : '']"
									>
										<slot :name="header.key" :item="item">
											<template v-if="header.idField">
												<KeyField :value="item[header.key]" />
											</template>
											<template v-else>
												{{ item[header.key] }}
											</template>
										</slot>
									</td>
									<td v-if="hasActions">
										<slot name="actions" :item="item"> </slot>
										<!-- <FlexTableDropdown /> -->
									</td>
								</tr>
								<tr v-if="Object.keys(error).length > 0 && items.length === 0">
									Une erreur est survenue lors du chargement des données
								</tr>
								<tr v-else-if="items && items.length === 0" class="text-center">
									<td class="border" :colspan="headers.length + 1" style="text-align: center">
										{{ emptyText ?? "Aucun item retrouvée !" }}
									</td>
								</tr>
							</template>
							<slot name="additionalRows"></slot>
							<!-- <VAvatarStack :avatars="user.contacts" size="small" :limit="3" /> -->
						</tbody>
					</table>
				</div>
			</div>

			<template v-if="meta">
				<VFlexPagination
					v-if="hasPagination"
					:current-page="meta.current_page"
					:item-per-page="Number.parseInt(tableOptions.per_page)"
					:total-items="meta.total"
					:total-pages="getNumberOfPages"
					:max-links-displayed="5"
					no-router
					class="mt-4 p-4"
					@paginate="(page) => (tableOptions.page = page)"
				/>
				<div class="p-4">
					<p class="dark-text">
						Affichage de {{ meta?.from }} à {{ meta?.to }} sur {{ meta?.total }} éléments
					</p>
				</div>
			</template>
		</VCard>
	</div>
</template>

<script setup lang="ts">
	import { debounce } from "/@src/utils/helpers";
	import KeyField from "/@src/components/KeyField.vue";

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
			type: Object,
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
	});

	const emit = defineEmits(["update-datatable", "create"]);
	const tableOptions = ref({
		sortBy: "",
		sortDirection: "asc",
		per_page: 10,
		page: 1,
		search: "",
	});

	const sort = (column: string, direction: string) => {
		tableOptions.value.sortBy = column;
		tableOptions.value.sortDirection = direction;
	};

	const getNumberOfPages = computed(() => {
		return Math.ceil(props.meta.total / props.meta.per_page);
	});

	const hasPagination = computed(() => {
		return props.meta && props.meta.total > tableOptions.value.per_page;
	});

	const onSearch = debounce((value: string) => {
		tableOptions.value.search = value;
	}, 500);

	watch(
		tableOptions,
		(newOptions) => {
			emit("update-datatable", newOptions);
		},
		{ deep: true },
	);
</script>

<style lang="scss" scoped>
	.is-navbar {
		.datatable-toolbar {
			padding-top: 30px;
		}
	}

	.r-card {
		padding: 0;
	}

	.datatable-toolbar {
		display: flex;
		align-items: center;
		margin-bottom: 20px;

		&.is-reversed {
			flex-direction: row-reverse;
		}

		.field {
			margin-bottom: 0;

			.control {
				.button {
					color: var(--light-text);

					&:hover,
					&:focus {
						background: var(--primary);
						border-color: var(--primary);
						color: var(--primary--color-invert);
					}
				}
			}
		}

		.buttons {
			margin-left: auto;
			margin-bottom: 0;

			.v-button {
				margin-bottom: 0;
			}
		}
	}

	.is-dark {
		.datatable-toolbar {
			.field {
				.control {
					.button {
						background: var(--dark-sidebar) !important;
						color: var(--light-text);

						&:hover,
						&:focus {
							background: var(--primary) !important;
							border-color: var(--primary) !important;
							color: var(--smoke-white) !important;
						}
					}
				}
			}
		}
	}

	.card-head {
		background-color: var(--white);
		border-radius: 10px;
		border: 1px solid var(--fade-grey-dark-3);
	}

	.datatable-wrapper {
		width: 100%;

		.datatable-container {
			background: var(--white);
			border: none !important;
			overflow-x: auto;

			.table,
			table {
				width: 100%;
			}

			&::-webkit-scrollbar {
				height: 8px !important;
			}

			&::-webkit-scrollbar-thumb {
				border-radius: 10px !important;
				background: rgb(0 0 0 / 20%) !important;
			}
		}
	}

	.datatable-table {
		border: 1px solid var(--fade-grey);
		border-collapse: collapse;
		border-radius: 0.75rem;

		th {
			padding: 16px 20px;
			font-family: var(--font-alt);
			font-size: 0.8rem;
			color: var(--dark-text);
			text-transform: uppercase;
			//border: 1px solid var(--fade-grey);
			border-left: none;
			border-right: none;
			font-weight: 600;

			&:last-child {
				text-align: right;
			}
		}

		td {
			color: black;
			font-family: var(--font);
			vertical-align: middle;
			padding: 12px 20px;
			border-bottom: 1px solid var(--fade-grey);

			&:last-child {
				text-align: right;
			}

			&.datatables-empty {
				opacity: 0;
			}
		}

		.light-text {
			color: var(--light-text);
		}

		.flex-media {
			display: flex;
			align-items: center;

			.meta {
				margin-left: 10px;
				line-height: 1.3;

				span {
					display: block;
					font-size: 0.8rem;
					color: var(--light-text);
					font-family: var(--font);

					&:first-child {
						font-family: var(--font-alt);
						color: var(--dark-text);
					}
				}
			}
		}

		.row-action {
			display: flex;
			justify-content: flex-end;
		}

		.checkbox {
			padding: 0;
		}

		.product-photo {
			width: 80px;
			height: 80px;
			object-fit: contain;
		}

		.file-icon {
			width: 46px;
			height: 46px;
			object-fit: contain;
		}

		.drinks-icon {
			display: block;
			max-width: 48px;
			border-radius: var(--radius-rounded);
			border: 1px solid var(--fade-grey);
		}

		.negative-icon,
		.positive-icon {
			svg {
				height: 16px;
				width: 16px;
			}
		}

		.positive-icon {
			.iconify {
				color: var(--success);

				* {
					stroke-width: 4px;
				}
			}
		}

		.negative-icon {
			&.is-danger {
				.iconify {
					color: var(--danger) !important;
				}
			}

			.iconify {
				color: var(--light-text);

				* {
					stroke-width: 4px;
				}
			}
		}

		.price {
			color: var(--dark-text);
			font-weight: 500;

			&::before {
				content: "$";
			}

			&.price-free {
				color: var(--light-text);
			}
		}

		.status {
			display: flex;
			align-items: center;

			&.is-available {
				i {
					color: var(--success);
				}
			}

			&.is-busy {
				i {
					color: var(--danger);
				}
			}

			&.is-offline {
				i {
					color: var(--light-text);
				}
			}

			i {
				margin-right: 8px;
				font-size: 8px;
			}

			span {
				font-family: var(--font);
				font-size: 0.9rem;
				color: var(--light-text);
			}
		}
	}

	.is-dark {
		.datatable-wrapper {
			.datatable-container {
				border-color: var(--dark-sidebar-light-12);
				background: var(--dark-sidebar-light-6);
			}
		}

		.datatable-table {
			border-color: var(--dark-sidebar-light-12);

			th,
			td {
				border-color: var(--dark-sidebar-light-12);
				color: var(--dark-dark-text);
			}

			.drinks-icon {
				border-color: var(--dark-sidebar-light-12);
			}
		}
	}

	.has-loader.has-loader-active {
		overflow: visible;
	}
</style>
