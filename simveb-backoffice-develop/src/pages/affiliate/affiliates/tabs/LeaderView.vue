<script setup lang="ts">
	import dayjs from "dayjs";

	export interface AffiliateLeaderViewProps {
		leaders: [];
	}

	withDefaults(defineProps<AffiliateLeaderViewProps>(), {
		leaders: undefined,
	});
</script>

<template>
	<div class="tab-details-inner">
		<div class="user-grid user-grid-v2">
			<VPlaceholderPage
				:class="[leaders.length !== 0 && 'is-hidden']"
				title="Aucun résultat."
				subtitle="Dommage. Il semble que nous n'ayons aucune donnée à ce niveau."
				larger
			>
				<template #image>
					<img class="light-image" src="/@src/assets/illustrations/placeholders/search-4.svg" alt="" />
					<img class="dark-image" src="/@src/assets/illustrations/placeholders/search-4-dark.svg" alt="" />
				</template>
			</VPlaceholderPage>

			<TransitionGroup name="list" tag="div" class="columns is-multiline">
				<div v-for="item in leaders" :key="item.id" class="column is-3">
					<div class="grid-item-wrap">
						<div class="grid-item-head">
							<div class="flex-head">
								<div class="meta">
									<span v-if="item.invitation_status === 'pending'" class="dark-inverted">
										Invitation en attente
									</span>
									<span v-if="item.invitation_status === 'confirmed'" class="dark-inverted">
										Invitation confirmée
									</span>
									<span v-if="item.invitation_status === 'blocked'" class="dark-inverted">
										Invitation bloquée
									</span>
									<span class="is-size-7"
										>Créé le {{ dayjs(item.created_at).format("DD-MM-YYYY H:m") }}</span
									>
								</div>
								<div v-if="item.invitation_status === 'confirmed'" class="status-icon is-success">
									<i aria-hidden="true" class="fas fa-check"></i>
								</div>
								<div v-if="item.invitation_status === 'pending'" class="status-icon is-warning">
									<i aria-hidden="true" class="fas fa-exclamation"></i>
								</div>
								<div v-if="item.invitation_status === 'blocked'" class="status-icon is-danger">
									<i aria-hidden="true" class="fas fa-times"></i>
								</div>
							</div>
						</div>
						<div class="grid-item">
							<VAvatar :picture="item.identity?.picture" size="big" />
							<h3 class="dark-inverted">{{ item.identity.fullName }}</h3>
							<p>{{ item.position.name }}</p>
							<div class="people">
								<VTag v-if="item.is_main_leader" label="Principal" color="primary" />
							</div>
							<div class="px-5 is-size-84">
								<div class="is-flex is-justify-content-space-between">
									<div class="is-flex is-flex-wrap-wrap is-align-content-center">
										<span class="has-text-primary">NPI</span>
									</div>
									<div class="is-flex is-flex-wrap-wrap is-align-content-center">
										<span class="has-text-light">{{ item.identity.npi }}</span>
									</div>
								</div>
								<div class="is-flex is-justify-content-space-between">
									<div class="is-flex is-flex-wrap-wrap is-align-content-center">
										<span class="has-text-primary">Téléphone</span>
									</div>
									<div class="is-flex is-flex-wrap-wrap is-align-content-center">
										<span class="has-text-light">{{ item.identity.telephone }}</span>
									</div>
								</div>
								<div class="is-flex is-justify-content-space-between">
									<div class="is-flex is-flex-wrap-wrap is-align-content-center">
										<span class="has-text-primary">Email</span>
									</div>
									<div class="is-flex is-flex-wrap-wrap is-align-content-center">
										<span class="has-text-light">{{ item.identity.email }}</span>
									</div>
								</div>
								<div class="is-flex is-justify-content-space-between">
									<div class="is-flex is-flex-wrap-wrap is-align-content-center">
										<span class="has-text-primary">IFU</span>
									</div>
									<div class="is-flex is-flex-wrap-wrap is-align-content-center">
										<span class="has-text-light">{{ item.identity.ifu }}</span>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</TransitionGroup>
		</div>
	</div>
</template>

<style lang="scss">
	@import "/@src/scss/abstracts/all";

	.is-size-84 {
		font-size: 0.84rem;
	}

	.user-grid-v2 {
		.columns {
			margin-inline-start: -0.5rem !important;
			margin-inline-end: -0.5rem !important;
			margin-top: -0.5rem !important;
		}

		.column {
			padding: 0.5rem !important;
		}

		.grid-item {
			@include vuero-s-card;

			text-align: center;

			> .v-avatar {
				display: block;
				margin: 0 auto 4px;
			}

			h3 {
				font-family: var(--font-alt);
				font-size: 1.1rem;
				font-weight: 600;
				color: var(--dark-text);
			}

			p {
				font-size: 0.85rem;
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

		.grid-item-wrap {
			border: 1px solid var(--fade-grey-dark-3);
			border-radius: var(--radius-large);
			transition: all 0.3s; // transition-all test

			.grid-item-head {
				background: #fafafa;
				border-radius: var(--radius-large) 6px 0 0;
				padding: 20px;

				.flex-head {
					display: flex;
					align-items: center;
					justify-content: space-between;
					margin-bottom: 12px;

					.meta {
						span {
							display: flex;

							&:first-child {
								font-family: var(--font-alt);
								font-weight: 600;
								font-size: 0.95rem;
								color: var(--dark-text);
							}

							&:nth-child(2) {
								font-size: 0.9rem;
								color: var(--light-text);
							}
						}
					}

					.status-icon {
						height: 28px;
						width: 28px;
						min-width: 28px;
						border-radius: var(--radius-rounded);
						border: 1px solid var(--fade-grey-dark-3);
						display: flex;
						align-items: center;
						justify-content: center;

						&.is-success {
							background: var(--success);
							border-color: var(--success);
							color: var(--white);
						}

						&.is-warning {
							background: var(--orange);
							border-color: var(--orange);
							color: var(--white);
						}

						&.is-danger {
							background: var(--danger);
							border-color: var(--danger);
							color: var(--white);
						}

						i {
							font-size: 8px;
						}
					}
				}

				.buttons {
					display: flex;
					justify-content: space-between;
					margin-bottom: 0;

					.button,
					.v-button {
						width: calc(50% - 4px);
						color: var(--light-text);
						margin-bottom: 0;

						&:hover,
						&:focus {
							border-color: var(--fade-grey-dark-4);
							color: var(--primary);
							box-shadow: var(--light-box-shadow);
						}
					}
				}
			}

			.grid-item {
				border-start-start-radius: 0;
				border-start-end-radius: 0;
				border: none;
			}
		}
	}

	.is-dark {
		.user-grid {
			.grid-item {
				@include vuero-card--dark;
			}
		}

		.user-grid-v2 {
			.grid-item-wrap {
				border-color: var(--dark-sidebar-light-12);

				.grid-item-head {
					background: var(--dark-sidebar-light-4);
				}
			}
		}
	}

	@media only screen and (width >=768px) and (width <=1024px) and (orientation: portrait) {
		.user-grid-v2 {
			.columns {
				display: flex;

				.column {
					min-width: 50% !important;
				}
			}
		}
	}

	@media only screen and (width >=768px) and (width <=1024px) and (orientation: landscape) {
		.user-grid-v2 {
			.columns {
				.column {
					min-width: 33.3% !important;
				}
			}
		}
	}
</style>
