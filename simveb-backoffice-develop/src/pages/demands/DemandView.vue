<template>
	<div class="page-content-inner">
		<div class="bg-white rounded-lg mb-4 p-4 flex items-center justify-between space-x-4">
			<BackButton />
			<!-- Dossier number -->
			<div class="text-primary-dark text-2xl font-bold w-1/2 text-ellipsis">
				Dossier #{{ demandInfo?.demand.reference }}
			</div>

			<!-- Button Opérer -->
			<div v-if="nextAction?.post_status && can(nextAction.permission)">
				<button
					type="button"
					class="bg-blue-600 w-36 text-white text-xl px-4 py-2 rounded-full me-2"
					:disabled="operate"
					@click="operate = true"
				>
					<i v-if="operate" class="fa-light fa-spinner-third animate-spin" />
					Opérer
				</button>
				<span v-tooltip="nextAction.description">
					<i class="fa-light fa-question-circle text-xl cursor-pointer"></i>
				</span>
			</div>

			<!-- Retard information -->
			<template v-if="demandInfo">
				<div v-if="demandInfo.is_delayed" class="flex items-center space-x-2 bg-red-100 px-4 py-2 rounded-md">
					<div class="font-semibold text-red-500 ms-2">Retard</div>
					<div class="font-semibold bg-red-200 text-red-600 px-2 py-1 rounded-md">
						{{ formatHours(Number.parseInt(demandInfo.delayed_hours)) }}
					</div>
				</div>
				<div
					v-else-if="nextAction?.post_status"
					class="flex items-center space-x-2 bg-success-100 px-4 py-2 rounded-md"
				>
					<div class="font-semibold text-success-500 ms-2">Temps restant</div>
					<div class="font-semibold bg-success-200 text-success-600 px-2 py-1 rounded-md">
						{{ formatHours(Number.parseInt(demandInfo.delayed_hours)) }}
					</div>
				</div>
			</template>
		</div>

		<div class="tabs-inner">
			<div class="tabs">
				<ul>
					<li v-for="(tab, key) in tabs" :key="key" :class="[activeTab === tab.value && 'is-active']">
						<a
							:href="tab.to ?? '#'"
							@keydown.prevent.enter="() => toggle(tab.value)"
							@click.prevent="() => toggle(tab.value)"
						>
							<i :class="`fa-light fa-${tab.icon}`" />
							<span>
								{{ tab.label }}
							</span>
						</a>
					</li>
				</ul>
			</div>
		</div>
		<VLoader size="large" :active="loading">
			<template v-if="demandInfo">
				<Transition name="fade-fast" mode="out-in">
					<DemandInfoPart v-if="activeTab == 'information'" :demand-info="demandInfo" />

					<GrayCardPart v-else-if="activeTab == 'gray_card'" :demand-info="demandInfo" />

					<ImmatriculationPart v-else-if="activeTab == 'immatriculation'" :demand-info="demandInfo" />

					<AssignationPart v-else-if="activeTab == 'assignation'" :demand-info="demandInfo" />

					<SaleInfoPart v-else-if="activeTab == 'sale_info'" :demand-info="demandInfo" />

					<CertificateInfoPart v-else-if="activeTab == 'certificate_info'" :demand-info="demandInfo" />

					<MutationInfoPart v-else-if="activeTab == 'mutation_info'" :demand-info="demandInfo" />

					<TitleInfoPart v-else-if="activeTab == 'title_info'" :demand-info="demandInfo" />

					<ImpressionPart v-else-if="activeTab == 'print'" :order="demandInfo.print_order" />
				</Transition>
			</template>
		</VLoader>

		<DemandActions />
	</div>
</template>

<script setup lang="ts">
	import { useDemandStore } from "/@src/stores/modules/demand";
	import { storeToRefs } from "pinia";
	import ImmatriculationPart from "/@src/pages/demands/Parts/ImmatriculationPart.vue";
	import DemandInfoPart from "/@src/pages/demands/Parts/DemandInfoPart.vue";
	import GrayCardPart from "/@src/pages/demands/Parts/GrayCardPart.vue";
	import SaleInfoPart from "/@src/pages/demands/Parts/SaleInfoPart.vue";
	import MutationInfoPart from "/@src/pages/demands/Parts/MutationInfoPart.vue";
	import TitleInfoPart from "/@src/pages/demands/Parts/TitleInfoPart.vue";
	import DemandActions from "/@src/pages/demands/DemandActions.vue";
	import AssignationPart from "/@src/pages/demands/Parts/AssignationPart.vue";
	import { userHasPermissions } from "/@src/utils/permission";
	import ImpressionPart from "/@src/pages/demands/Parts/ImpressionPart.vue";
	import { formatHours } from "/@src/utils/helpers";
	import BackButton from "/@src/pages/activity-logs/BackButton.vue";
	import CertificateInfoPart from "/@src/pages/demands/Parts/CertificateInfoPart.vue";

	const demandStore = useDemandStore();
	const { demand: demandInfo, loading, operate, nextAction } = storeToRefs(demandStore);
	const { can } = userHasPermissions();

	const props = defineProps({
		demandId: {
			type: String,
			required: true,
		},
	});

	const activeTab = ref("information");
	const tabs = ref([{ label: "Informations", value: "information", icon: "lightbulb" }]);

	const toggle = (tab: string) => {
		activeTab.value = tab;
	};

	onUnmounted(() => {
		demandInfo.value = null;
	});

	onMounted(async () => {
		await demandStore.getDemand(props.demandId).then(() => {
			loading.value = false;
			if (demandInfo.value.demand.service_code === "SALE_DECLARATION") {
				tabs.value.push({ label: "Informations de vente", value: "sale_info", icon: "id-card" });
			} else if (demandInfo.value.demand.service_code === "MUTATION") {
				tabs.value.push({ label: "Informations de mutation", value: "mutation_info", icon: "id-card" });
			} else if (demandInfo.value.demand.service_code === "MUTATION") {
				tabs.value.push({ label: "Informations de mutation", value: "mutation_info", icon: "id-card" });
			} else if (demandInfo.value.demand.service_code === "TITLE_DEPOSIT") {
				tabs.value.push({ label: "Informations de dépôt", value: "title_info", icon: "id-card" });
			} else if (demandInfo.value.demand.service_code === "TITLE_RECOVERY") {
				tabs.value.push({ label: "Informations de reprise", value: "title_info", icon: "id-card" });
			} else if (
				demandInfo.value.demand.service_code === "GLASS_ENGRAVING" ||
				demandInfo.value.demand.service_code === "TINTED_WINDOW_AUTHORIZATION"
			) {
				tabs.value.push({ label: "Informations supplémentaires", value: "certificate_info", icon: "id-card" });
			}

			tabs.value.push(
				{ label: "Assignation", value: "assignation", icon: "user-plus" },
				// { label: "Traitement", value: "treatment", icon: "cog" },
			);

			if (demandInfo.value.print_order) {
				tabs.value.push({ label: "Impression", value: "print", icon: "print" });
			}

			if (demandInfo.value.model.plate_color) {
				tabs.value.push({ label: "Immatriculation", value: "immatriculation", icon: "rug" });
			}
			if (demandInfo.value.model.gray_card) {
				tabs.value.push({ label: "Carte grise", value: "gray_card", icon: "id-card" });
			}
		});
	});
</script>

<style lang="scss">
	@import "/@src/scss/abstracts/all";

	.is-navbar {
		.tab-details {
			padding-top: 30px;
		}
	}
	.tab-details-inner {
		flex-wrap: wrap !important;
	}
	.r-card .card-inner {
		padding-left: 15px;
	}
	.tabs-inner .tabs {
		a {
			//margin-bottom: -5px !important;
		}
		i {
			margin-inline-end: 10px;
		}
	}

	.card-head {
		display: flex;
		align-items: center;
		justify-content: space-between;
		//margin-bottom: 20px;
		background: var(--primary);
		padding: 20px;
		border-radius: 8px;

		h3 {
			font-family: var(--font-alt);
			font-size: 1.2rem;
			font-weight: 700;
			color: var(--white);
			line-height: 1.2;
			transition: all 0.3s; // transition-all test
		}
	}

	.tab-overview {
		display: flex;
		align-items: center;
		justify-content: space-between;
		padding: 20px 0;

		p {
			max-width: 420px;
		}
	}

	.tab-features {
		display: flex;
		padding: 25px 0;
		border-top: 1px solid var(--fade-grey-dark-3);
		border-bottom: 1px solid var(--fade-grey-dark-3);

		.tab-feature {
			margin-inline-end: 20px;
			width: calc(25% - 20px);

			i {
				font-size: 1.6rem;
				color: var(--primary);
				margin-bottom: 8px;
			}

			h4 {
				font-family: var(--font-alt);
				font-size: 0.85rem;
				font-weight: 600;
				color: var(--dark);
			}

			p {
				line-height: 1.2;
				font-size: 0.85rem;
				color: var(--light-text);
				margin-bottom: 0;
			}
		}
	}

	.tab-files {
		padding: 20px 0;

		h4 {
			font-family: var(--font-alt);
			font-weight: 600;
			font-size: 0.8rem;
			text-transform: uppercase;
			color: var(--primary);
			margin-bottom: 12px;
		}

		.file-box {
			display: flex;
			align-items: center;
			padding: 8px;
			background: var(--white);
			border: 1px solid transparent;
			border-radius: 12px;
			cursor: pointer;
			transition: all 0.3s; // transition-all test

			&:hover {
				border-color: var(--fade-grey-dark-3);
				box-shadow: var(--light-box-shadow);
			}

			img {
				display: block;
				width: 48px;
				min-width: 48px;
				height: 48px;
			}

			.meta {
				margin-inline-start: 12px;
				line-height: 1.3;

				span {
					display: block;

					&:first-child {
						font-family: var(--font-alt);
						font-size: 0.9rem;
						font-weight: 600;
						color: var(--dark-text);
					}

					&:nth-child(2) {
						font-size: 0.9rem;
						color: var(--light-text);

						i {
							position: relative;
							top: -3px;
							font-size: 0.3rem;
							color: var(--light-text);
							margin: 0 4px;
						}
					}
				}
			}

			.dropdown {
				margin-inline-start: auto;
			}
		}
	}

	.side-card {
		@include vuero-s-card;

		padding: 40px;
		margin-bottom: 1.5rem;

		h4 {
			font-family: var(--font-alt);
			font-weight: 600;
			font-size: 0.8rem;
			text-transform: uppercase;
			color: var(--primary);
			margin-bottom: 16px;
		}
	}

	.tab-team-card {
		@include vuero-s-card;

		padding: 40px;
		max-width: 940px;
		display: block;
		margin: 0 auto;

		.column {
			padding: 1.5rem;

			&:nth-child(odd) {
				border-inline-end: 1px solid var(--fade-grey-dark-3);
			}

			&.has-border-bottom {
				border-bottom: 1px solid var(--fade-grey-dark-3);
			}
		}
	}

	.task-grid {
		.grid-header {
			display: flex;
			align-items: center;
			justify-content: space-between;
			margin-bottom: 20px;

			h3 {
				font-family: var(--font-alt);
				font-size: 1.5rem;
				font-weight: 700;
				color: var(--dark-text);
				line-height: 1.2;
			}

			.filter {
				display: flex;
				align-items: center;
				background: var(--white);
				padding: 8px 24px;
				border-radius: 100px;

				> span {
					font-family: var(--font-alt);
					font-size: 0.85rem;
					font-weight: 600;
					color: var(--dark-text);
					margin-inline-end: 20px;
				}

				.multiselect {
					min-width: 140px;
					border: none;
				}
			}
		}

		.task-card {
			@include vuero-s-card;

			min-height: 200px;
			display: flex !important;
			flex-direction: column;
			justify-content: space-between;
			padding: 30px;
			cursor: pointer;
			transition: all 0.3s; // transition-all test

			&:hover {
				transform: translateY(-5px);
				box-shadow: var(--light-box-shadow);
			}

			.title-wrap {
				h3 {
					font-size: 1.1rem;
					font-family: var(--font-alt);
					font-weight: 600;
					color: var(--dark-text);
					line-height: 1.2;
					margin-bottom: 4px;
				}

				span {
					font-family: var(--font);
					font-size: 0.9rem;
					color: var(--light-text);
				}
			}

			.content-wrap {
				display: flex;
				align-items: center;
				justify-content: space-between;

				.left {
					.avatar-stack {
						margin-bottom: 6px;
					}

					.attachments {
						display: flex;
						align-items: center;

						i {
							font-size: 15px;
							color: var(--light-text);
						}

						span {
							margin-inline-start: 2px;
							font-size: 0.9rem;
							font-family: var(--font);
							color: var(--light-text);
						}
					}
				}
			}
		}
	}

	.is-dark {
		.card-head {
			.title-wrap {
				h3 {
					color: var(--dark-dark-text) !important;
				}
			}
		}
		.tab-features {
			border-color: var(--dark-sidebar-light-12);

			.tab-feature {
				i {
					color: var(--primary);
				}

				h4 {
					color: var(--dark-dark-text);
				}
			}
		}

		.tab-files {
			h4 {
				color: var(--primary);
			}
			.file-box {
				background: var(--dark-sidebar-light-3);
				&:hover,
				&:focus {
					border-color: var(--dark-sidebar-light-10);
				}

				.meta {
					span {
						&:first-child {
							color: var(--dark-dark-text);
						}
					}
				}
			}
		}

		.side-card {
			@include vuero-card--dark;

			h4 {
				color: var(--primary);
			}
		}

		.tab-team-card {
			@include vuero-card--dark;

			.column {
				border-color: var(--dark-sidebar-light-12);
			}
		}

		.task-grid {
			.grid-header {
				h3 {
					color: var(--dark-dark-text);
				}

				.filter {
					background: var(--dark-sidebar-light-1) !important;
					border-color: var(--dark-sidebar-light-4) !important;
					> span {
						color: var(--dark-dark-text);
					}
				}
			}

			.task-card {
				@include vuero-card--dark;

				.title-wrap {
					h3 {
						color: var(--dark-dark-text);
					}
				}
			}
		}
	}

	@media only screen and (width <=767px) {
		.tab-features {
			flex-wrap: wrap;
			.tab-feature {
				width: calc(50% - 20px);
				text-align: center;
				margin: 0 10px;

				&:first-child,
				&:nth-child(2) {
					margin-bottom: 20px;
				}
			}

			.tab-team-card {
				padding: 30px;

				.column {
					border-inline-end: none !important;
				}
			}
		}
	}

	@media only screen and (width >=768px) and (width <=1024px) and (orientation: portrait) {
		.columns {
			display: flex;

			.column {
				min-width: 50%;
			}
		}

		.tab-team-card {
			.columns {
				display: flex;

				.column {
					min-width: 50%;
				}
			}
		}

		.task-grid {
			.columns {
				display: flex;

				.column {
					min-width: 50%;
				}
			}
		}
	}
</style>
