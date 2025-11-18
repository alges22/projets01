<script lang="ts" setup>
	import VLoader from "/@src/components/base/loader/VLoader.vue";
	import ApexChart from "vue3-apexcharts";
	import StatCard from "/@src/pages/StatCard.vue";
	import client from "/@src/composable/axiosClient";
	import { useDate } from "vue3-dayjs-plugin/useDate";

	const loading = ref(true);
	const stats = ref(null);
	const filter = ref("year");
	const dayjs = useDate();

	const loadStats = async () => {
		const response = await client({
			method: "GET",
			url: "dashboard-stats?date_from=15-01-2023&date_to=12-12-2025",
		});

		stats.value = response.data;
		loading.value = false;
	};

	const generateRandomColor = () => {
		return `#${Math.floor(Math.random() * 16777215)
			.toString(16)
			.padStart(6, "0")}`;
	};

	const chartData = computed(() => {
		if (!stats.value.total_demands_by_service) return { series: [], labels: [], colors: [] };

		return {
			series: stats.value.total_demands_by_service.map((item) => item.demand_count),
			labels: stats.value.total_demands_by_service.map((item) => item.service),
			colors: stats.value.total_demands_by_service.map(() => generateRandomColor()),
		};
	});

	const monthLabels = ["Jan", "Fev", "Mar", "Avr", "Mai", "Jun", "Jul", "Aout", "Sep", "Oct", "Nov", "Dec"];

	const paiementChartData = computed(() => {
		if (!stats.value.total_payments_per_month) return { series: [], categories: [] };

		const paymentsByMonth = new Array(12).fill(0);

		stats.value.total_payments_per_month.forEach(({ month, total_payments }) => {
			paymentsByMonth[parseInt(month) - 1] = parseInt(total_payments);
		});

		return {
			series: [
				{
					name: "Total Paiements",
					data: paymentsByMonth,
				},
			],
			categories: monthLabels,
		};
	});

	onMounted(() => {
		loadStats();
	});
</script>

<template>
	<VLoader :active="loading" size="xl">
		<div class="dashboard-card is-large">
			<!--			<div class="columns m-b-15">-->
			<!--				<div class="card column is-2">-->
			<!--					<VControl class="has-icons-left" icon="slider">-->
			<!--						<SelectInput v-model="filter" class="border-none" placeholder="Cette semaine">-->
			<!--							<VOption value="week"> Cette semaine</VOption>-->
			<!--							<VOption value="month"> Ce mois</VOption>-->
			<!--							<VOption value="year"> Cette année</VOption>-->
			<!--						</SelectInput>-->
			<!--					</VControl>-->
			<!--				</div>-->
			<!--			</div>-->

			<DashboardCards>
				<StatCard title="Total demande">
					<template #default>
						<div>
							<i class="fa-solid fa-cube text-h-primary fa-xl"></i>
						</div>
						<div>
							<span>{{ stats.total_demands }}</span>
						</div>
					</template>
				</StatCard>

				<StatCard title="Total de véhicules à 02 roues">
					<template #default>
						<div>
							<i class="fa-solid fa-motorcycle text-h-primary fa-xl"></i>
						</div>
						<div>
							<span>{{ stats.total_2_wheels_vehicles }}</span>
						</div>
					</template>
				</StatCard>

				<StatCard title="Total de véhicules à 04 roues">
					<template #default>
						<div>
							<i class="fa-solid fa-car text-h-primary fa-xl"></i>
						</div>
						<div>
							<span>{{ stats.total_4_wheels_vehicles }}</span>
						</div>
					</template>
				</StatCard>

				<StatCard title="Total immatriculations">
					<template #default>
						<div>
							<i class="fa-solid fa-rug text-h-primary fa-xl"></i>
						</div>
						<div>
							<span>{{ stats.total_immatriculations }}</span>
						</div>
					</template>
				</StatCard>
			</DashboardCards>
		</div>

		<div class="grid gap-4 grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 m-t-15">
			<div class="dashboard-card has-fullheight">
				<ApexChart
					:height="300"
					:options="{
						title: { text: 'Services' },
						colors: chartData.colors,
						labels: chartData.labels,
						dataLabels: { enabled: false },
						responsive: [
							{
								breakpoint: 480,
								options: {
									chart: { width: 315, toolbar: { show: false } },
									legend: { position: 'top' },
								},
							},
						],
						legend: {
							position: 'bottom',
							horizontalAlign: 'left',
							fontSize: '14px',
							fontWeight: 300,
							markers: { width: '13px', height: '13px', radius: 5 },
							labels: { colors: '#809FB8', useSeriesColors: false },
							itemMargin: { vertical: 5 },
						},
					}"
					:series="chartData.series"
					type="pie"
				/>
			</div>

			<div class="dashboard-card has-fullheight is-transactions">
				<div class="title-wrap">
					<h3 class="dark-inverted">Véhicules récemment immatriculés</h3>
				</div>

				<div class="transactions">
					<div
						class="media-flex colored"
						v-for="(immatriculation, index) in stats.latest_immatriculed_vehicles"
						:key="index"
					>
						<div :style="{ backgroundColor: '#F0517A' }" class="color-box"></div>
						<div class="flex-meta is-lighter">
							<span>{{ immatriculation.number_label }}</span>
							<span>{{ immatriculation.vin }}</span>
						</div>
					</div>
				</div>
			</div>

			<div class="col-span-2 dashboard-card has-fullheight">
				<div class="title-wrap">
					<h3 class="dark-inverted">Total des paiements</h3>
					<VDropdown color="primary" icon="fa-xl fa-solid fa-ellipsis-vertical" right />
				</div>
				<ApexChart
					id="line-chart"
					:height="350"
					type="line"
					:series="paiementChartData.series"
					:options="{
						chart: {
							height: 350,
							type: 'line',
							zoom: { enabled: false },
							toolbar: { show: false },
						},
						colors: ['#51E5F0'],
						dataLabels: { enabled: false },
						stroke: { width: [2], curve: 'smooth' },
						legend: {
							tooltipHoverFormatter: function (val, opts) {
								return val + ' - ' + opts.w.globals.series[opts.seriesIndex][opts.dataPointIndex];
							},
							position: 'top',
						},
						markers: { size: 3 },
						xaxis: { categories: paiementChartData.categories },
						grid: { borderColor: '#f1f1f1' },
					}"
				/>
			</div>
		</div>

		<div class="dashboard-card mt-8">
			<div class="title-wrap">
				<h3 class="dark-inverted">Activités récentes</h3>
			</div>

			<table class="table w-full table-auto">
				<thead>
					<tr>
						<th>Utilisateur</th>
						<th>Activité</th>
						<th>Date</th>
					</tr>
				</thead>
				<tbody>
					<tr v-for="(activity, index) in stats.latest_activities" :key="index">
						<td>{{ activity.causer?.identity?.fullName }}</td>
						<td>{{ activity.log_action }} - {{ activity.description }}</td>
						<td>{{ dayjs(activity.created_at).format("DD/MM/YYYY HH:mm") }}</td>
					</tr>
				</tbody>
			</table>
		</div>
	</VLoader>
</template>

<style lang="scss">
	@import "/@src/scss/abstracts/all";

	.columns {
		.card {
			margin-inline-end: 12px;
		}
	}

	.dashboard-card {
		.columns {
			width: 100%;
		}

		display: flex;
		flex-direction: column;
		width: 100%;
		padding: 15px;
		background-color: var(--white);
		border-radius: 10px;
		transition: all 0.3s;
		box-shadow: 1px 1px 5px var(--fade-grey-dark-3);

		&.is-large {
			padding: 40px;
		}

		.card {
			border-radius: 8px;
			transition: all 0.3s;
			padding: 0;
			box-shadow: 1px 1px 5px var(--fade-grey-dark-3);

			.vs__dropdown-toggle {
				border: none !important;
			}
		}

		&.is-transactions {
			.transactions {
				padding: 10px 0;

				.media-flex-center {
					.flex-meta {
						span {
							&:nth-child(2) {
								font-size: 0.85rem;
							}
						}
					}

					.flex-end {
						font-family: var(--font);
						font-size: 1rem;
						font-weight: 500;
						color: var(--dark-text);
					}
				}
			}
		}

		> .title-wrap {
			display: flex;
			align-items: center;
			justify-content: space-between;
			margin-bottom: 12px;
			font-family: var(--font);

			h3 {
				font-family: var(--font-alt);
				font-size: 1rem;
				font-weight: 600;
				color: var(--dark-text);
			}

			.title-meta {
				font-size: 1rem;
				font-weight: 500;
				color: var(--dark-text);
			}

			.action-link {
				font-size: 0.9rem;
			}
		}

		.dashboard-card-footer {
			display: flex;
			justify-content: space-between;
			padding-top: 15px;
			margin-top: auto;

			.left {
				margin-right: auto;
				display: flex;
				justify-content: flex-start;
				align-items: center;
				font-size: 12px;
				color: #809fb8;

				i {
					color: var(--primary);
				}
			}

			.right {
				margin-left: auto;
				display: flex;
				justify-content: flex-end;
				align-items: center;
				font-size: 13px;
				font-weight: 600;

				a {
					color: var(--primary);
				}
			}
		}
	}

	.tabbed-controls {
		position: relative;
		display: flex;
		min-width: 140px;
		height: 100%;
		background: #eff1f9;
		color: #626679;
		font-family: var(--font-alt);
		border-radius: 8px;

		.tabbed-control {
			position: relative;
			width: 50%;
			display: flex;
			justify-content: center;
			align-items: center;
			font-size: 1rem;
			color: var(--light-text);
			z-index: 1;

			&.is-active {
				color: var(--smoke-white);

				&:first-child {
					~ .tabbed-naver {
						margin-inline-start: 0;
					}
				}

				&:nth-child(2) {
					~ .tabbed-naver {
						margin-inline-start: 50%;
					}
				}
			}
		}

		.tabbed-naver {
			position: absolute;
			top: 0;
			inset-inline-start: 0;
			width: 50%;
			height: 100%;
			border-radius: 8px;
			background: var(--primary);
			margin-inline-start: 0;
			transition: all 0.3s; // transition-all test
			z-index: 0;
		}
	}

	.color-box {
		width: 50px;
		height: 50px;
		border-radius: 7px;
	}

	.media-flex {
		display: flex;
		align-items: center;
		margin-bottom: 1rem;
		width: 100%;
		padding: 0.5rem 0;

		&.colored {
			background: #f8f8f8 0 0 no-repeat padding-box;
			box-shadow: 0 2px 4px #00000029;
			border-radius: 6px;
		}

		&:last-child,
		&.no-margin {
			margin-bottom: 0;
		}

		.tag {
			font-size: 10px;
			margin-left: auto;

			&.has-text-danger {
				border: 1px solid #f0517a;
			}
		}

		span,
		> a {
			display: block;

			&:first-child {
				font-family: var(--font-alt);
				font-size: 15px;
				color: #3f4254;
				font-weight: bold;
			}

			&:nth-child(2) {
				font-family: var(--font-alt);
				color: #626679;
				font-size: 12px;
			}
		}
	}

	.is-bolder {
		font-weight: bold !important;
	}
</style>
