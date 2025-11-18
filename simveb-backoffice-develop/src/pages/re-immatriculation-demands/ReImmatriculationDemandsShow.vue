<script setup lang="ts">
	import { useHead } from "@vueuse/head";

	import { useViewWrapper } from "/@src/stores/viewWrapper";

	const viewWrapper = useViewWrapper();
	viewWrapper.setPageTitle("Dossier de demande de ré-immatriculation #REF");
	const { text, copy, copied } = useClipboard();

	const reference = "IM-8894F8REA";
	useHead({
		title: "Dossier de demande de ré-immatriculation #REF - SIMVEB",
	});

	const props = withDefaults(
		defineProps<{
			activeTab?: "pending" | "assigned" | "passage_to_print";
		}>(),
		{
			activeTab: "pending",
		}
	);

	const tab = ref(props.activeTab);
</script>

<template>
	<div class="page-content-inner">
		<div class="tab-details">
			<div class="tabs-wrapper">
				<div class="tabs-inner">
					<div class="tabs">
						<ul>
							<li :class="[tab === 'pending' && 'is-active']">
								<a
									tabindex="0"
									role="button"
									@keydown.space.prevent="tab = 'pending'"
									@click="tab = 'pending'"
									><span>En attente</span></a
								>
							</li>
							<li :class="[tab === 'assigned' && 'is-active']">
								<a
									tabindex="0"
									role="button"
									@keydown.space.prevent="tab = 'assigned'"
									@click="tab = 'assigned'"
									><span>Assigné</span></a
								>
							</li>
							<li :class="[tab === 'passage_to_print' && 'is-active']">
								<a
									tabindex="0"
									role="button"
									@keydown.space.prevent="tab = 'passage_to_print'"
									@click="tab = 'passage_to_print'"
									><span>Passage à impression</span></a
								>
							</li>
							<li class="tab-naver"></li>
						</ul>
					</div>
				</div>

				<div v-if="tab === 'pending'" class="tab-content is-active">
					<div class="columns tab-details-inner">
						<div class="column is-8">
							<VCard class="side-card">
								<div class="card-head">
									<h4>Information sur la demande</h4>
								</div>
								<div class="card-inner is-size-6-5">
									<div class="columns">
										<div class="column is-one-quarter">
											<span class="has-text-weight-semibold">Référence</span>
										</div>
										<div class="column is-flex is-justify-content-space-between">
											<div>
												<span>{{ reference }}</span>
											</div>
											<VButton
												v-tooltip.primary.rounded="'Copier'"
												tabindex="0"
												class="has-text-primary"
												@keydown.space.prevent="copy(reference)"
												@click="copy(reference)"
											>
												<Transition name="fade-fast" mode="out-in">
													<span v-if="copied && text === reference" class="is-copied">
														<i
															class="iconify"
															data-icon="check"
															aria-hidden="true"
															color="primary"
														></i>
													</span>
													<span v-else>
														<i class="iconify" data-icon="copy" aria-hidden="true"></i>
													</span>
												</Transition>
											</VButton>
										</div>
									</div>
									<div class="columns">
										<div class="column is-one-quarter">
											<span class="has-text-weight-semibold">Date de création</span>
										</div>
										<div class="column">
											<span class="">27-07-2023 15h57</span>
										</div>
									</div>
									<div class="columns">
										<div class="column is-one-quarter">
											<span class="has-text-weight-semibold">Statut</span>
										</div>
										<div class="column">
											<VTag color="orange" label="En attente" />
										</div>
									</div>
									<div class="columns">
										<div class="column is-one-quarter">
											<span class="has-text-weight-semibold">Type de véhicule</span>
										</div>
										<div class="column">
											<span>Neuf</span>
										</div>
									</div>
									<div class="columns">
										<div class="column is-one-quarter">
											<span class="has-text-weight-semibold">Type de vitre</span>
										</div>
										<div class="column">
											<span>Nomale</span>
										</div>
									</div>
									<div class="columns">
										<div class="column is-one-quarter">
											<span class="has-text-weight-semibold">Couleur de plaque</span>
										</div>
										<div class="column">
											<VTag color="light" label="Blanche" />
										</div>
									</div>
								</div>
							</VCard>
							<VCard class="side-card">
								<div class="card-head">
									<h4>Véhicule</h4>
								</div>
								<div class="card-inner is-size-6-5">
									<div class="columns">
										<div class="column is-one-quarter">
											<span class="has-text-weight-semibold"> Numéro de chassis (VIN) </span>
										</div>
										<div class="column">
											<span>77f78-WOIROIW-5884845-5568488</span>
										</div>
									</div>
									<div class="columns">
										<div class="column is-one-quarter">
											<span class="has-text-weight-semibold">Etat du véhicule</span>
										</div>
										<div class="column">
											<span>Usage</span>
										</div>
									</div>
									<div class="columns">
										<div class="column is-one-quarter">
											<span class="has-text-weight-semibold">Marque</span>
										</div>
										<div class="column">
											<span>Yundai</span>
										</div>
									</div>
									<div class="columns">
										<div class="column is-one-quarter">
											<span>...</span>
										</div>
									</div>
								</div>
							</VCard>
						</div>

						<div class="column is-4">
							<div class="side-card">
								<h4>Demandeur</h4>

								<VBlock center title="Nom Complet" subtitle="Todd Hood">
									<template #icon>
										<VIconBox size="small" color="primary" rounded bordered>
											<i class="iconify" data-icon="user"></i>
										</VIconBox>
									</template>
								</VBlock>

								<VBlock center title="Email" subtitle="toddho@gmail.com">
									<template #icon>
										<VIconBox size="small" color="primary" rounded bordered>
											<i class="iconify" data-icon="mail"></i>
										</VIconBox>
									</template>
								</VBlock>

								<VBlock center title="Téléphone" subtitle="+229 51112203">
									<template #icon>
										<VIconBox size="small" color="primary" rounded bordered>
											<i class="iconify" data-icon="phone"></i>
										</VIconBox>
									</template>
								</VBlock>
								<VBlock center title="IFU" subtitle="00114892203">
									<template #icon>
										<VIconBox size="small" color="primary" rounded bordered>
											<i class="iconify" data-icon="align-justify"></i>
										</VIconBox>
									</template>
								</VBlock>
								<VBlock center title="NPI" subtitle="2258948965056">
									<template #icon>
										<VIconBox size="small" color="primary" rounded bordered>
											<i class="iconify" data-icon="info"></i>
										</VIconBox>
									</template>
								</VBlock>
							</div>
							<div class="side-card">
								<h4>Transitaire</h4>

								<VBlock center title="Nom" subtitle="Le transitaire">
									<template #icon>
										<VIconBox size="small" color="primary" rounded bordered>
											<i class="iconify" data-icon="user"></i>
										</VIconBox>
									</template>
								</VBlock>

								<VBlock center title="Email" subtitle="toddho@gmail.com">
									<template #icon>
										<VIconBox size="small" color="primary" rounded bordered>
											<i class="iconify" data-icon="mail"></i>
										</VIconBox>
									</template>
								</VBlock>

								<VBlock center title="Téléphone" subtitle="+229 51112203">
									<template #icon>
										<VIconBox size="small" color="primary" rounded bordered>
											<i class="iconify" data-icon="phone"></i>
										</VIconBox>
									</template>
								</VBlock>
								<VBlock center title="IFU" subtitle="00114892203">
									<template #icon>
										<VIconBox size="small" color="primary" rounded bordered>
											<i class="iconify" data-icon="align-justify"></i>
										</VIconBox>
									</template>
								</VBlock>
							</div>
						</div>
					</div>
					<div class="is-flex is-justify-content-end">
						<div v-tooltip="'Valider'">
							<VButton outlined color="success">Valider</VButton>
						</div>
						<div v-tooltip="'Rejeter'" class="pl-2">
							<VButton color="danger">Rejeter</VButton>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</template>

<style lang="scss">
	@import "/@src/scss/abstracts/all";

	.is-size-6-5 {
		font-size: 0.95rem;
		font-family: var(--font-alt);
	}

	.is-navbar {
		.tab-details {
			padding-top: 30px;
		}
	}

	.tab-details {
		.tabs-wrapper {
			.tabs-inner {
				.tabs {
					margin: 0 auto;
					background: var(--white);
				}
			}
		}

		.tab-details-inner {
			padding: 20px 0;

			.tab-details-card {
				@include vuero-s-card;

				padding: 40px;

				.card-head {
					display: flex;
					align-items: center;
					justify-content: space-between;
					margin-bottom: 20px;

					.title-wrap {
						h3 {
							font-family: var(--font-alt);
							font-size: 1.5rem;
							font-weight: 700;
							color: var(--dark-text);
							line-height: 1.2;
							transition: all 0.3s; // transition-all test
						}
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
		}
	}

	.is-dark {
		.tab-details {
			.tab-details-inner {
				.tab-details-card {
					@include vuero-card--dark;

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
		}
	}

	@media only screen and (width <= 767px) {
		.tab-details {
			.tab-details-inner {
				.tab-details-card {
					padding: 30px;

					.tab-overview {
						flex-direction: column;

						p {
							margin-bottom: 20px;
						}
					}

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
	}

	@media only screen and (width >= 768px) and (width <= 1024px) and (orientation: portrait) {
		.tab-details {
			.tab-details-inner {
				.tab-details-card {
					.tab-files {
						.columns {
							display: flex;

							.column {
								min-width: 50%;
							}
						}
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
		}
	}
</style>
