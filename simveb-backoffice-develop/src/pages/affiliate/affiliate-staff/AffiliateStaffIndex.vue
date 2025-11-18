<script setup lang="ts">
	import { storeToRefs } from "pinia";
	import { onBeforeMount, ref } from "vue";
	import { useCrudStore } from "/@src/stores/modules/crud";
	import { useAffiliateStore } from "/@src/stores/modules/affiliate";
	import { useUserSession } from "/@src/stores/userSession";
	import AffiliteStaffLeadersView from "/@src/pages/affiliate/affiliate-staff/views/LeadersView.vue";
	import AffiliteStaffEmployeesView from "/@src/pages/affiliate/affiliate-staff/views/EmployeesView.vue";

	const affiliateStore = useAffiliateStore();
	const { affiliateLeader } = storeToRefs(useUserSession());
	const crudStore = useCrudStore();
	const { url } = storeToRefs(crudStore);

	const positions = ref([]);
	const zones = ref([]);
	const leaders = ref([]);
	const employees = ref([]);

	onBeforeMount(() => {
		url.value = "/affiliates";
	});

	const loadUsers = () => {
		crudStore.getRow(affiliateLeader.value.affiliate_id).then((res) => {
			leaders.value = res.leaders;
			employees.value = res.members;
		});
	};

	onMounted(() => {
		loadUsers();
		affiliateStore.loadInvitationCreateData().then((res) => {
			positions.value = res.positions;
			zones.value = res.zones;
		});
	});

	withDefaults(
		defineProps<{
			activeTab?: "leaders" | "members";
		}>(),
		{
			activeTab: "leaders",
		}
	);
</script>

<template>
	<div class="page-content-inner">
		<div>
			<VTabs
				slider
				type="rounded"
				selected="leaders"
				:tabs="[
					{ label: 'Dirigeants', value: 'leaders' },
					{ label: 'Employés', value: 'employees' },
				]"
			>
				<template #tab="{ activeValue }">
					<AffiliteStaffLeadersView
						v-if="activeValue === 'leaders'"
						:leaders="leaders"
						:positions="positions"
						@on-invitation-sent="loadUsers"
					/>
					<AffiliteStaffEmployeesView
						v-if="activeValue === 'employees'"
						:employees="employees"
						:positions="positions"
						:zones="zones"
						@on-invitation-sent="loadUsers"
					/>
				</template>
			</VTabs>
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
