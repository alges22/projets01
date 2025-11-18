<script setup lang="ts">
	import { useDemandStore } from "/src/stores/modules/demand";
	import { storeToRefs } from "pinia";

	export interface ImmatriculationDemandImmatriculationViewProps {
		demandId: string;
	}

	const props = withDefaults(defineProps<ImmatriculationDemandImmatriculationViewProps>(), {
		demandId: undefined,
	});

	const demandStore = useDemandStore();
	const { demand, url } = storeToRefs(demandStore);

	onMounted(() => {
		if (!demand.value.id) {
			demandStore.fetchDemand(props.demandId);
		}
	});
	const rfidRegistrationModalOpen = ref(false);
	const rfid = ref("");

	onBeforeMount(() => {
		url.value = "/immatriculation-demands";
	});
</script>

<template>
	<div class="columns tab-details-inner is-multiline">
		<div class="column is-12">
			<VCard class="side-card">
				<div class="card-head">
					<h4>Plaque d'immatriculation</h4>
				</div>
				<div v-if="demand" class="card-inner is-size-6-5 columns is-multiline">
					<div class="column is-half">
						<div class="mb-2 is-flex is-justify-content-center is-uppercase">
							<span class="has-text-primary">Avant</span>
						</div>
						<square-plate-view
							v-if="demand.vehicle.immatriculation"
							:bg-color="demand.plate_color.color_code"
							:text-color="demand.plate_color.text_color"
							:immatriculation-number="demand.vehicle.immatriculation.number"
						/>
					</div>
					<div v-if="demand.vehicle.category.nb_plate > 1" class="column is-half">
						<div class="mb-2 is-flex is-justify-content-center is-uppercase">
							<span class="has-text-primary">Arrière</span>
						</div>
						<rectangle-plate-view
							v-if="demand.vehicle.immatriculation"
							:bg-color="demand.plate_color.color_code"
							:text-color="demand.plate_color.text_color"
							:immatriculation-number="demand.vehicle.immatriculation.number"
						/>
					</div>
					<div class="column is-12 mt-2">
						<div class="is-flex is-justify-content-space-between">
							<div class="is-flex is-flex-wrap-wrap align-items is-align-content-center">
								<span>
									Immatriculation:
									{{ demand.vehicle.immatriculation.formatLabel }}
								</span>
							</div>
							<!--              <VButton tabindex="0" color="primary" raised>
                Générer l'immatriculation
              </VButton>-->
						</div>
					</div>
				</div>
				<div v-else class="is-flex is-justify-content-center">
					Le numéro d'immatriculation n'est pas encore disponible
				</div>
			</VCard>
		</div>
		<div class="column is-8">
			<VCard class="side-card">
				<div class="card-head">
					<h4>Liaison du RFID</h4>
				</div>
				<div class="card-inner is-size-6-5">
					<div class="columns">
						<div class="column is-on-quarter">
							<span>Statut</span>
						</div>
						<div class="column">
							<VTag label="En attente d'enregisrement" color="secondary" />
							<!-- <VTag label="Enregistré" color="success" /> -->
						</div>
					</div>
					<div class="columns">
						<div class="column is-on-quarter">
							<span>Enregistré par</span>
						</div>
						<div class="column">
							<!-- <span>Ianus Consus</span> -->
						</div>
					</div>
					<div class="columns">
						<div class="column is-on-quarter">
							<span>Date d'enregistrement</span>
						</div>
						<div class="column">
							<!-- <span>28-07-2023 15h06</span> -->
						</div>
					</div>
					<div class="columns is-flex is-justify-content-flex-end">
						<VButton color="primary" raised @click="rfidRegistrationModalOpen = true">
							Enregistrer le RFID
						</VButton>
					</div>
				</div>
			</VCard>
		</div>
	</div>

	<VModal
		:open="rfidRegistrationModalOpen"
		actions="right"
		title="Enregistrer le RFID"
		@close="rfidRegistrationModalOpen = false"
	>
		<template #content>
			<div class="container">
				<VField horizontal>
					<VControl fullwidth>
						<VInput v-model="rfid" type="password" placeholder="RFID" name="rfid" required />
					</VControl>
				</VField>
			</div>
		</template>
		<template #action>
			<VButton color="primary" raised @click="rfidRegistrationModalOpen = false"> Confirmer </VButton>
		</template>
	</VModal>
</template>
