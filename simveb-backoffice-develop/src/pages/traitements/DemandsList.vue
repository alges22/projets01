<template>
	<div class="page-content-inner">
		<DataTable
			:headers="headers"
			:items="rows"
			:loading="loading"
			:meta="meta"
			empty-text="Aucun traitement trouvé"
			search
			@update-datatable="(newOptions) => (options = { ...options, ...newOptions })"
		>
			<template #service="{ item }"> {{ item.service.name }} </template>
			<template #reference="{ item }">
				<KeyField :value="item.reference" :to="{ name: 'demands_show', params: { demandId: item.id } }" />
			</template>
			<template #vin="{ item }">
				<KeyField :value="item.vehicle?.vin" />
			</template>
			<template #author="{ item }">
				<div class="flex items-center">
					<a class="ms-2 decoration-dotted underline text-primary">
						{{ item.vehicle_owner?.identity?.fullName }}
					</a>
					<span class="ms-2 text-xs text-slate">{{ item.vehicle_owner?.identity?.npi }}</span>
				</div>
			</template>
			<template #status_label="{ item }">
				<VTag :label="item.status_label" curved :color="statusColor(item?.status)" />
			</template>
			<template #created_at="{ item }">
				{{ dayjs(item.created_at).format("DD / MM / YYYY") }}
			</template>
			<template #actions="{ item }">
				<div class="block">
					<VIconButton
						v-if="can('show-im-demand')"
						v-tooltip.right="'Voir'"
						color="primary"
						light
						icon="eye"
						:to="{
							name: 'demands_show',
							params: { demandId: item.id },
						}"
					/>
				</div>
			</template>

			<template #filters> </template>
		</DataTable>
	</div>
</template>

<script setup lang="ts">
	import { storeToRefs } from "pinia";
	import { useCrudStore } from "/@src/stores/modules/crud";
	import { userHasPermissions } from "/@src/utils/permission";
	import statusColor from "/@src/utils/status-color";
	import { useDate } from "vue3-dayjs-plugin/useDate";

	const { can } = userHasPermissions();
	const crudStore = useCrudStore();
	const { rows, meta, loading, url } = storeToRefs(crudStore);
	const options = ref({});
	const dayjs = useDate();

	const getItems = async (metadata) => {
		url.value = "/my-treated-demands";
		await crudStore.fetchRows(metadata);
	};

	const headers = [
		{ title: "Référence", key: "reference", sortable: true },
		{ title: "Statut", key: "status_label" },
		{ title: "VIN", key: "vin" },
		{ title: "Type de service", key: "service" },
		{ title: "Initiateur", key: "author" },
		{ title: "Date", key: "created_at", sortable: true },
	];
	const filter_status = ref("All");
	const filterWithSTatus = () => {
		if (filter_status.value != "All") options.value.status = filter_status.value;
		else {
			getItems({});
		}
	};

	watch(
		options,
		(newOptions) => {
			getItems(newOptions);
		},
		{ deep: true, immediate: true },
	);

	onUnmounted(() => {
		crudStore.reset();
	});

	onBeforeRouteLeave(() => {
		crudStore.reset();
	});
</script>

<style lang="scss">
	@import "/@src/scss/abstracts/all";
	.color-box {
		width: 20px;
		height: 20px;
		border-radius: 50%;
	}

	.analytics-dashboard {
		.text-h-green {
			color: var(--green);
		}
		.text-h-primary {
			color: var(--primary);
		}

		.text-h-red {
			color: var(--red);
		}

		.text-widget {
			color: var(--widget-grey);
		}

		.dashboard-tile {
			@include vuero-s-card;

			font-family: var(--font);

			.tile-head {
				display: flex;
				align-items: center;
				justify-content: space-between;

				h3 {
					font-family: var(--font-alt);
					color: var(--dark-text);
					font-weight: 600;
				}
			}

			.tile-body {
				font-size: 2rem;
				padding: 4px 0 8px;

				span {
					color: var(--dark-text);
					font-weight: 600;
				}
			}

			.tile-foot {
				span {
					&:first-child {
						font-weight: 500;

						svg {
							height: 16px;
							width: 16px;
							margin-inline-end: 6px;
							stroke-width: 3px;
						}
					}

					&:nth-child(2) {
						color: var(--light-text);
						font-size: 0.9rem;
					}
				}
			}
		}

		.dashboard-card {
			@include vuero-s-card;

			font-family: var(--font);
			height: 100%;

			.card-head {
				display: flex;
				align-items: center;
				justify-content: space-between;
				margin-bottom: 20px;

				h3 {
					font-family: var(--font-alt);
					font-size: 1rem;
					font-weight: 600;
					color: var(--dark-text);
				}
			}

			.revenue-stats {
				display: flex;

				.revenue-stat {
					margin-inline-end: 30px;
					font-family: var(--font);

					span {
						display: block;

						&:first-child {
							color: var(--light-text);
							font-size: 0.9rem;
						}

						&:nth-child(2) {
							color: var(--dark-text);
							font-size: 1.2rem;
							font-weight: 600;
						}

						&.current {
							color: var(--primary);
						}
					}
				}
			}
		}
	}

	.is-dark {
		.analytics-dashboard {
			.dashboard-tile,
			.dashboard-card {
				@include vuero-card--dark;
			}
		}
	}
</style>
