<script setup lang="ts">
	import { storeToRefs } from "pinia";
	import { onBeforeMount, onUnmounted, ref } from "vue";
	import { useCrudStore } from "/@src/stores/modules/crud";
	import { userHasPermissions } from "/@src/utils/permission";
	import VLoader from "/@src/components/base/loader/VLoader.vue";
	import { Notyf } from "notyf";
	import { useDeleteConfirmation } from "/@src/composable/useDeleteConfirmation";
	import { useSearchStore } from "/@src/stores/search";

	const { can } = userHasPermissions();
	const crudStore = useCrudStore();
	const searchStore = useSearchStore();
	const { rows, loading, url } = storeToRefs(crudStore);
	const { query, enabled: searchEnabled, placeholder } = storeToRefs(searchStore);

	const options = ref({});
	const notyf = new Notyf();
	const filteredServices = computed(() => {
		if (query.value) {
			return rows.value.filter((item) => {
				return (
					item.name.toLowerCase().includes(query.value.toLowerCase()) ||
					item.code.toLowerCase().includes(query.value.toLowerCase())
				);
			});
		}

		return rows.value;
	});

	const getItems = async (metadata) => {
		await crudStore.fetchRows(metadata);
	};

	const { handleDelete } = useDeleteConfirmation(async (item) => {
		await crudStore.deleteRow(item.id).then(async () => {
			notyf.success("Le service a bien été supprimé");
			await getItems(options.value);
		});
	});

	onBeforeMount(() => {
		url.value = "/services";
	});

	onMounted(async () => {
		searchEnabled.value = true;
		query.value = "";
		placeholder.value = "Rechercher un service";

		await getItems({}).then(() => {
			if (rows.value.length > 0) {
				filteredServices.value = [...rows.value];
			}
		});
	});

	onUnmounted(() => {
		crudStore.reset();
		searchStore.reset();
	});
</script>

<template>
	<div class="page-content-inner">
		<div class="card-grid-toolbar">
			<p class="text-lg text-black">Liste des services actuellement disponibles sur Simveb</p>
			<div class="buttons right">
				<!-- <VButton v-if="can('store-service')" :to="{ name: 'service_creation' }" color="primary" raised>
					<span class="icon">
						<i aria-hidden="true" class="fas fa-plus" />
					</span>
					<span>Ajouter un service</span>
				</VButton> -->
			</div>
		</div>
		<VLoader :active="loading" size="large">
			<div class="card-grid card-grid-v3">
				<VPlaceholderPage
					v-if="!loading"
					:class="[filteredServices.length !== 0 && 'is-hidden']"
					title="Aucun service"
					subtitle="Aucun service ne correspond à votre recherche."
					larger
				>
				</VPlaceholderPage>

				<TransitionGroup
					v-if="!loading"
					name="list"
					tag="div"
					class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xxl:grid-cols-4 gap-4"
					appear
				>
					<div
						v-for="item in filteredServices"
						:key="item.id"
						class="card-grid-item h-96 overflow-hidden !flex flex-col"
						:class="{
							'opacity-50 hover:opacity-75': !item.is_active,
						}"
					>
						<label v-if="item.lockable" class="h-toggle">
							<input type="checkbox" :checked="item.locked" />
							<span class="toggler">
								<span class="active">
									<i aria-hidden="true" class="iconify" data-icon="feather:lock" />
								</span>
								<span class="inactive">
									<i aria-hidden="true" class="iconify" data-icon="feather:check" />
								</span>
							</span>
						</label>
						<h3 class="dark-inverted is-size-5">
							{{ item.name }}
						</h3>
						<p>{{ item.code }}</p>
						<div class="description is-size-6 has-text-weight-semibold">
							<p>{{ item.description }}</p>
						</div>
						<div v-if="item.vehicle_category" class="description is-size-6 has-text-weight-semibold">
							<p>{{ item.vehicle_category.name }}</p>
						</div>
						<div v-if="item.organization" class="description is-size-6 has-text-weight-semibold">
							<p>{{ item.organization.name }}</p>
						</div>
						<div class="pl-3 flex justify-around items-center mt-auto">
							<VButton
								v-if="can('show-service')"
								class="has-text-primary"
								:to="{
									name: 'service_show',
									params: { serviceId: item.id },
								}"
							>
								Voir
							</VButton>

							<VButton
								v-if="can('update-service')"
								class="has-text-warning"
								:to="{
									name: 'service_creation',
									params: { serviceId: item.id },
								}"
							>
								Modifier
							</VButton>

							<!-- <VIconButton
								v-if="can('delete-service')"
								v-tooltip.top="'Supprimer le service'"
								class="has-text-danger"
								icon="trash"
								@click="handleDelete(item)"
							/> -->
						</div>
					</div>
				</TransitionGroup>
			</div>
		</VLoader>
	</div>
</template>

<style scoped lang="scss">
	@import "/@src/scss/abstracts/all";

	.card-grid {
		.columns {
			margin-inline-start: -0.5rem !important;
			margin-inline-end: -0.5rem !important;
			margin-top: -0.5rem !important;
		}

		.column {
			padding: 0.5rem !important;
		}
	}

	.card-grid-v3 {
		.card-grid-item {
			@include vuero-s-card;

			position: relative;
			text-align: center;
			padding: 30px;

			.h-toggle {
				position: absolute;
				top: 28px;
				inset-inline-end: 10px;
				transform: scale(0.85);
			}

			> .v-avatar {
				display: block;
				margin: 0 auto 10px;
				border-radius: 16px;

				.avatar {
					object-fit: cover;
					border: 1px solid var(--fade-grey-dark-4);
					box-shadow: var(--light-box-shadow);
				}

				.badge {
					bottom: 22px;
					inset-inline-end: -12px;
				}
			}

			> h3 {
				font-size: 1.1rem;
				font-weight: 600;
				font-family: var(--font-alt);
				color: var(--dark-text);
			}

			> p {
				font-size: 0.9rem;
			}

			.description {
				padding: 12px 0;
			}

			.people {
				display: flex;
				justify-content: center;
				padding: 8px 0 30px;

				.v-avatar {
					margin: 0 4px;
				}
			}

			.buttons {
				display: flex;
				justify-content: space-between;

				.button {
					width: calc(50% - 4px);
					color: var(--light-text);

					&:hover,
					&:focus {
						border-color: var(--fade-grey-dark-4);
						color: var(--primary);
						box-shadow: var(--light-box-shadow);
					}
				}
			}
		}
	}

	.is-dark {
		.card-grid-v3 {
			.card-grid-item {
				@include vuero-card--dark;
			}
		}
	}

	@media only screen and (width >=768px) and (width <=1024px) and (orientation: landscape) {
		.card-grid-v3 .card-grid-item > h3 {
			font-size: 1rem;
		}
	}
</style>
