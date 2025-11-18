<script setup lang="ts">
	import { storeToRefs } from "pinia";
	import { onBeforeMount, onUnmounted } from "vue";
	import { useCrudStore } from "/@src/stores/modules/crud";
	import statusColor from "/@src/utils/status-color";
	import client from "/@src/composable/axiosClient";
	import { userHasPermissions } from "/@src/utils/permission";

	const route = useRoute();

	const crudStore = useCrudStore();
	const { url, row } = storeToRefs(crudStore);
	const { can } = userHasPermissions();

	const itemId = route.params.id;

	const urlPath = "/reserved-plate-numbers";

	const accept = () => {
		client
			.post("/reserved-plate-numbers/validate-or-invalidate", {
				reserved_plate_number_id: itemId,
				action: "validate",
			})
			.then(() => {
				crudStore.fetchRow(itemId);
			});
	};
	const reject = () => {
		client
			.post("/reserved-plate-numbers/validate-or-invalidate", {
				reserved_plate_number_id: itemId,
				action: "reject",
			})
			.then(() => {
				crudStore.fetchRow(itemId);
			});
	};

	onBeforeMount(() => {
		url.value = urlPath;
	});

	onUnmounted(() => {
		crudStore.reset();
	});

	onMounted(() => {
		crudStore.fetchRow(itemId);
	});
</script>

<template>
	<div class="page-content-inner">
		<h3 class="title is-5 mb-2">Détails d'une réservation de plaque</h3>
		<VTabs class="mt-4" selected="details" :tabs="[{ label: 'Détails', value: 'details' }]">
			<template #tab="{ activeValue }">
				<div class="grid grid-cols-2 xl:grid-cols-3 gap-4 tab-details-inner">
					<div v-if="activeValue === 'details' && row.alphabetic_label">
						<VCard class="side-card" style="height: 500px; overflow: scroll">
							<div class="card-head">
								<h3>Détails</h3>
							</div>
							<div class="card-inner is-one-third pt-1 mx-4 mt-4" style="font-size: 12px">
								<div class="columns">
									<div class="column flex items-center">
										<span>Label alphabetic</span>
									</div>
									<div class="column">{{ row.alphabetic_label }}</div>
								</div>
							</div>
							<div class="card-inner is-one-third pt-1 mx-4 mt-4" style="font-size: 12px">
								<div class="columns">
									<div class="column flex items-center">
										<span>Label numérique</span>
									</div>
									<div class="column">{{ row.numeric_label }}</div>
								</div>
							</div>
							<div class="card-inner is-one-third pt-1 mx-4 mt-4" style="font-size: 12px">
								<div class="columns">
									<div class="column flex items-center">
										<span>Numéro minimum</span>
									</div>
									<div class="column">{{ row.min != null ? row.min : "_" }}</div>
								</div>
							</div>
							<div class="card-inner is-one-third pt-1 mx-4 mt-4" style="font-size: 12px">
								<div class="columns">
									<div class="column flex items-center">
										<span>Numéro maximum</span>
									</div>
									<div class="column">{{ row.max != null ? row.max : "_" }}</div>
								</div>
							</div>
							<div class="card-inner is-one-third pt-1 mx-4 mt-4" style="font-size: 12px">
								<div class="columns">
									<div class="column flex items-center">
										<span>Status</span>
									</div>
									<div class="column">
										<VTag :label="row.status" :color="statusColor(row?.status)"></VTag>
									</div>
								</div>
							</div>
							<div
								v-if="can('validate-reserved-plate-number')"
								class="flex items-center justify-center mt-5"
							>
								<VButton class="button is-success" :disabled="row.status != 'pending'" @click="accept">
									Valider
								</VButton>
								<button
									class="button is-danger ml-3"
									:disabled="row.status != 'pending'"
									@click="reject"
								>
									Rejeter
								</button>
							</div>
						</VCard>
					</div>
				</div>
			</template>
		</VTabs>
	</div>
</template>
