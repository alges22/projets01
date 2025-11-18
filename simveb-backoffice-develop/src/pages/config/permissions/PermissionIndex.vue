<script setup lang="ts">
	import { storeToRefs } from "pinia";
	import { onBeforeMount, onUnmounted, ref, watch } from "vue";
	import { useCrudStore } from "/@src/stores/modules/crud";

	const crudStore = useCrudStore();
	const { rows: permissions, url } = storeToRefs(crudStore);
	const options = ref({});
	const query = ref("");

	const filteredModules = computed(() => {
		if (!query.value) return permissions.value.modules ?? [];
		return permissions.value.modules.filter(
			(module) => module.name && module.name.toLowerCase().includes(query.value.trim().toLowerCase()),
		);
	});

	onBeforeMount(() => {
		url.value = "/permissions";
	});

	const getItems = async (metadata) => {
		url.value = "/permissions";
		await crudStore.fetchRows(metadata);
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
</script>

<template>
	<div class="page-content-inner">
		<div>
			<div class="card-grid-toolbar">
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
				<VControl icon="search">
					<input v-model="query" class="input custom-text-filter" placeholder="Filtrer..." />
				</VControl>
			</div>

			<div class="card-grid card-grid-v3">
				<!--List Empty Search Placeholder -->
				<VPlaceholderPage
					:class="[filteredModules.length !== 0 && 'is-hidden']"
					title="Aucun résultat."
					subtitle="Dommage. Il semble que nous n'ayons trouvé aucun résultat correspondant pour le
          termes de recherche que vous avez saisis. Veuillez essayer différents termes de recherche ou
          critères."
					larger
				>
					<template #image>
						<img class="light-image" src="/@src/assets/illustrations/placeholders/search-3.svg" alt="" />
						<img
							class="dark-image"
							src="/@src/assets/illustrations/placeholders/search-3-dark.svg"
							alt=""
						/>
					</template>
				</VPlaceholderPage>

				<!--Card Grid v3-->
				<TransitionGroup name="list" tag="div" class="columns is-multiline is-flex-tablet-p is-half-tablet-p">
					<!--Grid Item-->
					<div v-for="item in filteredModules" :key="item.id" class="column is-3">
						<div class="card-grid-item h-full">
							<h3 class="dark-inverted has-text-primary">
								{{ item.name }}
							</h3>
							<div class="description mt-1">
								<ul>
									<li
										v-for="(permission, index) in item.permissions"
										:key="index"
										:class="index > 0 ? 'mt-1' : ''"
									>
										<span class="has-text-light">{{ "- " + permission.label }}</span>
									</li>
								</ul>
							</div>
						</div>
					</div>
				</TransitionGroup>
			</div>
		</div>
	</div>
</template>

<style lang="scss">
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
