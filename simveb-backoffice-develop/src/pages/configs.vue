<template>
	<div class="page-content-inner">
		<div class="config-wrapper">
			<div class="config-header has-text-centered mb-5">
				<h1>Ici vous pouvez gérer les configurations globales.</h1>
			</div>
			<div class="config-body">
				<div
					class="grid gap-4 2xl:grid-cols-6"
					:class="[
						panelOpen && 'grid-cols-1 sm:grid-cols-2 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4',
						!panelOpen && 'grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5',
					]"
				>
					<TransitionGroup name="list">
						<template v-for="(config, index) in filteredConfigs" :key="index">
							<RouterLink
								v-if="config"
								:to="config.to"
								class="config-box"
								:class="{
									'border-danger border-2': !config.permission,
									'cursor-not-allowed opacity-50 pointer-events-none':
										config.permission && !can(config.permission),
								}"
							>
								<div class="edit-icon">
									<i aria-hidden="true" class="fa-light fa-pencil"></i>
								</div>

								<VIconWrap dark="6" :icon="'fa-light fa-' + config.icon" />

								<span>{{ config.label }}</span>
								<h3 v-if="config.title">{{ config.title }}</h3>
							</RouterLink>
						</template>
						<template v-if="!filteredConfigs.length"> Aucune configuration trouvée. </template>
					</TransitionGroup>
				</div>
			</div>
		</div>
	</div>
</template>

<script lang="ts" setup>
	import { userHasPermissions } from "/@src/utils/permission";
	import { useSidebar } from "/@src/stores/sidebar";
	import { storeToRefs } from "pinia";
	import { useSearchStore } from "/@src/stores/search";

	const { panelOpen } = storeToRefs(useSidebar());
	const searchStore = useSearchStore();

	const { query, enabled: searchEnabled, placeholder } = storeToRefs(searchStore);
	const { can } = userHasPermissions();

	const configs = [
		{
			permission: "browse-permission",
			label: "Permissions",
			icon: "flag-swallowtail",
			to: { name: "permissions" },
			title: "Gérer les permissions",
		},
		{
			permission: "browse-role",
			label: "Rôles",
			icon: "user-lock",
			to: { name: "roles" },
			title: "Gérer les rôles",
		},
		{
			permission: "browse-blacklist-person",
			label: "Liste noire",
			icon: "ban",
			to: { name: "blacklist" },
			title: "Gérer la liste noire",
		},
		{
			permission: "browse-document-type",
			label: "Type de document",
			icon: "file-lines",
			to: { name: "document_types" },
			title: null,
		},
		{
			permission: "browse-required-document-type",
			label: "Type de document requis",
			icon: "file-circle-question",
			to: { name: "required_document_types" },
			title: null,
		},
		{
			permission: "browse-institution-type",
			label: "Type d'institutions",
			icon: "building-memo",
			to: { name: "institutions_type" },
			title: null,
		},
		/*{
			permission: "browse-meta-data",
			label: "Gestion des meta données",
			icon: "gears",
			to: { name: "meta_data" },
			title: null,
		},*/
		{
			permission: "browse-institution",
			label: "Institutions",
			icon: "building-columns",
			to: { name: "institutions" },
			title: null,
		},
		{
			permission: "browse-park",
			label: "Parcs",
			icon: "car-building",
			to: { name: "parcs" },
			title: "Gestion des parcs",
		},
		{
			permission: "browse-border",
			label: "Frontières",
			icon: "tally",
			to: { name: "frontieres" },
			title: "Gestion des frontières",
		},
		{
			permission: "browse-management-center-type",
			label: "Type de centre de gestion",
			icon: "house-circle-xmark",
			to: { name: "management_center_types" },
			title: "Gestion des type de centre de gestion",
		},
		{
			permission: "browse-management-center",
			label: "Gestion des centres de gestion",
			icon: "building-circle-check",
			to: { name: "management_centers" },
			title: null,
		},
		// {
		// 	permission: "browse-price",
		// 	label: "Gestion des prix",
		// 	icon: "money-bills",
		// 	to: { name: "manage_prices" },
		// 	title: null,
		// },
		{
			permission: "browse-alert-type",
			label: "Types d'alerte",
			icon: "bell-exclamation",
			to: { name: "alert_types" },
			title: null,
		},
		{
			permission: "browse-title-reason",
			label: "Motifs de titre",
			icon: "memo-circle-info",
			to: { name: "title_reasons" },
			title: null,
		},
		{
			permission: "browse-zone",
			label: "Gestion des zones",
			icon: "map",
			to: { name: "zones" },
			title: "Zones",
		},
		{
			permission: "browse-town",
			label: "Gestion des communes",
			icon: "signs-post",
			to: { name: "towns" },
			title: "Communes",
		},
		{
			permission: "browse-district",
			label: "Gestion des arrondissements",
			icon: "road",
			to: { name: "districts" },
			title: "Arrondissements",
		},
		{
			permission: "browse-village",
			label: "Quartiers ou Village",
			icon: "road",
			to: { name: "villages" },
			title: "Villages",
		},
		/*{
			permission: "browse-owner-type",
			label: "Type de propriétaire",
			icon: "car-alt",
			to: { name: "owner_types" },
			title: null,
		},*/
		{
			permission: "browse-vehicle-category",
			label: "Catégories de véhicule",
			icon: "cars",
			to: { name: "vehicle_categories" },
			title: null,
		},
		{
			permission: "browse-vehicle-type",
			label: "Type de véhicule",
			icon: "car-bus",
			to: { name: "vehicle_types" },
			title: null,
		},
		// {
		// 	permission: "browse-vehicle-brand",
		// 	label: "Marque de véhicule",
		// 	icon: "copyright",
		// 	to: { name: "vehicle_brands" },
		// 	title: null,
		// },
		// {
		// 	permission: "browse-vehicle-power",
		// 	label: "Puissance de véhicule",
		// 	icon: "car-circle-bolt",
		// 	to: { name: "vehicle_powers" },
		// 	title: null,
		// },
		{
			permission: "browse-vehicle-characteristic-category",
			label: "Catégories de caractéristique de véhicule",
			icon: "car",
			to: { name: "vehicle_characteristic_categories" },
			title: null,
		},
		{
			permission: "browse-vehicle-characteristic",
			label: "Caractéristiques de véhicule",
			icon: "car-alt",
			to: { name: "vehicle_characteristics" },
			title: null,
		},
		{
			permission: "browse-plate-color",
			label: "Couleurs de plaque",
			icon: "paint-roller",
			to: { name: "plate_colors" },
			title: null,
		},
		{
			permission: "browse-plate-shape",
			label: "Forme de plaque",
			icon: "chart-tree-map",
			to: { name: "plate_shapes" },
			title: null,
		},
		{
			permission: "browse-immatriculation-format",
			label: "Format d'immatriculations",
			icon: "chart-bullet",
			to: { name: "immatriculation_formats" },
			title: null,
		},
		{
			permission: "browse-number-template",
			label: "Modèle de numéros",
			icon: "hashtag",
			to: { name: "number_template" },
			title: null,
		},
		{
			permission: "browse-organization",
			label: "Organisations",
			icon: "globe",
			to: { name: "organizations" },
			title: null,
		},
		{
			permission: "browse-staff",
			label: "Staff",
			icon: "users",
			to: { name: "staff" },
			title: null,
		},
		{
			permission: "browse-immatriculation-type",
			label: "Types d'immatriculation",
			icon: "rectangle",
			to: { name: "immatriculation_types" },
			title: null,
		},
		{
			permission: "browse-reserved-plate-number",
			label: "Plaques réservées",
			icon: "triangle-exclamation",
			to: { name: "reserved-plate-numbers" },
			title: null,
		},
		{
			permission: "browse-service",
			label: "Services",
			icon: "users-cog",
			to: { name: "services" },
			title: null,
		},
		{
			permission: "browse-space",
			label: "Espaces",
			icon: "ufo-beam",
			to: { name: "spaces" },
			title: "Gérer les espaces",
		},
	];

	const filteredConfigs = computed(() => {
		if (!query.value) return configs;
		return configs.filter(
			(config) => config.label && config.label.toLowerCase().includes(query.value.toLowerCase()),
		);
	});

	onBeforeMount(() => {
		searchEnabled.value = true;
		query.value = "";
		placeholder.value = "Rechercher une configuration";
	});

	onUnmounted(() => {
		searchEnabled.value = false;
	});
</script>

<style lang="scss">
	@import "/@src/scss/abstracts/all";
	@import "/@src/scss/components/profile-stats";

	.config-wrapper {
		.config-body {
			.config-section {
				display: flex;
				flex-wrap: wrap;
				//max-width: 880px;
				margin: 0 auto;
			}
		}
	}

	.config-box {
		position: relative;
		background: var(--white);
		text-align: center;
		padding: 16px;
		border-radius: 8px;
		border: 1px solid var(--fade-grey-dark-3);
		transition: all 0.3s; // transition-all test
		cursor: pointer;

		&:hover,
		&:focus {
			border-color: var(--primary);
			box-shadow: var(--light-box-shadow);

			.edit-icon {
				opacity: 1;
				pointer-events: all;
			}

			.icon-wrap {
				i {
					color: var(--primary);
				}
			}

			h3 {
				color: var(--primary);
			}
		}

		&.is-active {
			.icon-wrap {
				i {
					color: var(--primary);
				}
			}

			h3 {
				color: var(--primary);
			}
		}

		.edit-icon {
			position: absolute;
			top: 6px;
			inset-inline-start: 6px;
			display: flex;
			justify-content: center;
			align-items: center;
			height: 18px;
			width: 18px;
			border-radius: var(--radius-rounded);
			background: var(--fade-grey-light-3);
			opacity: 0;
			pointer-events: none;
			transition: all 0.3s; // transition-all test

			i {
				font-size: 0.8rem;
			}
		}

		.icon-wrap {
			display: flex;
			justify-content: center;
			align-items: center;
			background: none !important;
			border: none;
			box-shadow: none;
			height: 52px;
			width: 100%;

			i {
				font-size: 2.7rem;
				color: var(--primary-dark);
				margin-bottom: 4px;
				transition: color 0.3s;
			}

			img {
				display: block;
				max-width: 90px;
			}
		}

		span {
			text-align: center;
			display: block;
			color: var(--primary-dark);
			font-family: var(--font-alt);
			font-size: 0.875rem;
			font-weight: 700;
		}

		h3 {
			font-family: var(--font);
			font-size: 0.9rem;
			font-weight: 400;
			color: var(--primary-dark);
			transition: color 0.3s;
		}
	}

	.is-dark {
		.config-box {
			background: var(--dark-sidebar-light-6);
			border-color: var(--dark-sidebar-light-12);

			&:hover,
			&:focus {
				border-color: var(--primary);

				h3 {
					color: var(--primary);
				}

				.icon-wrap i {
					color: var(--primary);
				}
			}

			&.is-active {
				h3 {
					color: var(--primary);
				}

				.icon-wrap i {
					color: var(--primary);
				}
			}

			.edit-icon {
				background: var(--dark-sidebar-light-2);

				i {
					color: var(--primary);
				}
			}

			.icon-wrap {
				background: none !important;

				i {
					color: var(--light-text-dark-20);

					&.is-solid {
						color: var(--primary);
					}
				}
			}
		}
	}

	.list-move,
	.list-enter-active,
	.list-leave-active {
		transition: all 0.2s ease;
	}

	.list-enter-from,
	.list-leave-to {
		opacity: 0;
		transform: translateX(30px);
	}

	.list-leave-active {
		position: absolute;
	}
</style>
