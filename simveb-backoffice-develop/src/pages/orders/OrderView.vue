<template>
	<div class="page-content-inner">
		<div class="tabs-inner flex">
			<button
				type="button"
				class="button is-circle is-outlined is-primary button px-4 py-2 rounded-full me-4"
				@click="$router.back()"
			>
				<i class="fa-light fa-arrow-left" />
			</button>
			<div class="tabs">
				<ul>
					<li v-for="(tab, key) in tabs" :key="key" :class="[activeTab === tab.value && 'is-active']">
						<a
							href="#"
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
			<template v-if="row">
				<Transition name="fade-fast" mode="out-in">
					<div v-if="activeTab == 'information'" class="columns tab-details-inner">
						<OrderInfoCard :order="row" />
					</div>
					<div v-else-if="activeTab == 'demand'" class="columns tab-details-inner">
						<DemandInfoCard :demands="row.demands" />
					</div>
					<div v-else-if="activeTab == 'transaction'" class="columns tab-details-inner">
						<TransactionInfoCard :transaction="row.transaction" />
					</div>
				</Transition>
			</template>
		</VLoader>
	</div>
</template>

<script setup lang="ts">
	import { storeToRefs } from "pinia";
	import OrderInfoCard from "/@src/pages/orders/Cards/OrderInfoCard.vue";
	import TransactionInfoCard from "/@src/pages/orders/Cards/TransactionInfoCard.vue";
	import DemandInfoCard from "/@src/pages/orders/Cards/DemandInfoCard.vue";
	import { useCrudStore } from "/@src/stores/modules/crud";

	const crudStore = useCrudStore();
	const { row, loading, url } = storeToRefs(crudStore);

	const props = defineProps({
		orderId: {
			type: String,
			required: true,
		},
	});

	const activeTab = ref("information");
	const tabs = [
		{ label: "Informations", value: "information", icon: "lightbulb" },
		{ label: "Demande", value: "demand", icon: "id-card" },
		{ label: "Transaction", value: "transaction", icon: "rug" },
	];

	const toggle = (tab: string) => {
		activeTab.value = tab;
	};

	onUnmounted(() => {
		row.value = {};
	});

	onBeforeMount(async () => {
		url.value = "/admin-orders";
		await crudStore.fetchRow(props.orderId).then(() => {
			loading.value = false;
		});
		// await orderStore.fetchOrder(props.orderId).then(() => {
		// 	loading.value = false;
		// });
	});
</script>

<style lang="scss"></style>
