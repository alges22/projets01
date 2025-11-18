<script setup lang="ts">
	import { useDemandStore } from "/src/stores/modules/demand";
	import CountryFlag from "vue-country-flag-next";
	import dayjs from "dayjs";
	import { storeToRefs } from "pinia";

	export interface ImmatriculationDemandGrayCardViewProps {
		demandId: string;
	}

	const props = withDefaults(defineProps<ImmatriculationDemandGrayCardViewProps>(), {
		demandId: undefined,
	});

	const demandStore = useDemandStore();
	const { demand, url } = storeToRefs(demandStore);
	const owner = ref(null);
	const vehicle = ref(null);
	const gray_card = ref(null);

	onMounted(() => {
		if (!demand.value.id) {
			demandStore.fetchDemand(props.demandId).then((res) => {
				owner.value = res.vehicle_owner;
				vehicle.value = res.vehicle;
				gray_card.value = res.gray_card;
			});
		} else {
			owner.value = demand.value.vehicle_owner;
			vehicle.value = demand.value.vehicle;
			gray_card.value = demand.value.gray_card;
		}
	});

	onBeforeMount(() => {
		url.value = "gray-card-duplicates";
	});
</script>

<template>
	<div class="columns tab-details-inner is-multiline">
		<div class="column is-12">
			<VCard class="side-card">
				<div class="card-head">
					<h4>Carte grise</h4>
				</div>
				<div v-if="gray_card" class="card-inner is-size-6-5">
					<div class="columns is-multiline">
						<div class="column is-half">
							<div class="is-flex is-justify-content-center">
								<div class="gray-card-front">
									<div class="bg-image">
										<img src="/images/logos/logo/logo_anatt-crop.png" alt="benin-seal" />
									</div>
									<div class="content">
										<div class="content-header">
											<span class="maintitle-text">République du bénin</span>
											<span class="subtitle-text">
												Direction Générale des Transports Terrestres
											</span>
										</div>
										<div>
											<span>N° {{ gray_card.number }}</span>
										</div>
									</div>
								</div>
							</div>
						</div>
						<div class="column is-half">
							<div class="is-flex is-justify-content-center">
								<div class="gray-card-back">
									<div class="bg-image"></div>
									<div class="content">
										<div class="content-header mb-2">
											<div class="header-seal">
												<img src="/images/logos/logo/logo_anatt-crop.png" alt="benin-seal" />
											</div>
											<div class="main">
												<span class="maintitle-text">République du bénin</span>
												<span class="subtitle-text">
													Direction Générale des Transports Terrestres
												</span>
												<span class="title-text">Certificat d'immatriculation</span>
											</div>
											<div class="header-flag">
												<country-flag country="bj" size="big" />
											</div>
										</div>
										<div class="content-info px-4">
											<div class="columns is-multiline">
												<div class="column is-half content-info-row">
													<div class="">
														<span class="content-info-title">
															Numéro d'immatriculation
														</span>
													</div>
													<div class="">
														<span class="content-info-subtitle">
															{{ vehicle?.immatriculation?.formatLabel }}
														</span>
													</div>
												</div>
												<div class="column is-half content-info-row">
													<div class="">
														<span class="content-info-title">Numéro de chassis</span>
													</div>
													<div class="">
														<span class="content-info-subtitle">
															{{ vehicle?.chassis_number }}
														</span>
													</div>
												</div>
												<div class="column is-half content-info-row">
													<div class="">
														<span class="content-info-title">Nom du propriétaire</span>
													</div>
													<div class="">
														<span class="content-info-subtitle">
															{{ owner.lastname }}
														</span>
													</div>
												</div>
												<div class="column is-half content-info-row">
													<div class="">
														<span class="content-info-title">
															Prénoms du propriétaire
														</span>
													</div>
													<div class="">
														<span class="content-info-subtitle">
															{{ owner?.firstname }}
														</span>
													</div>
												</div>
												<div class="column is-half content-info-row">
													<div class="">
														<span class="content-info-title">Date de Délivrance</span>
													</div>
													<div class="">
														<span class="content-info-subtitle">
															{{ dayjs(new Date()).format("DD-MM-YYYY") }}
														</span>
													</div>
												</div>
												<div class="column is-half content-info-row">
													<div class="">
														<span class="content-info-title">Date de naissance</span>
													</div>
													<div class="">
														<span class="content-info-subtitle">
															{{ dayjs(owner.birthdate).format("DD-MM-YYYY") }}
														</span>
													</div>
												</div>
												<div
													class="column is-12 has-text-centered content-info-row text-size-subtitle"
												>
													<span class="">Carte N° </span>
													<span>{{ gray_card.number }}</span>
												</div>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
						<div class="column is-12 mt-5 is-flex is-justify-content-flex-end">
							<VDropdown title="Imprimer" color="primary" right="false" modern>
								<template #content>
									<a href="#" class="dropdown-item">Première face</a>
									<a href="#" class="dropdown-item">Deuxième face</a>
									<hr class="dropdown-divider" />
									<a href="#" class="dropdown-item">Les deux faces</a>
								</template>
							</VDropdown>
						</div>
						<div class="column is-8 mt-5">
							<div class="columns">
								<div class="column">
									<h4>Informations</h4>
								</div>
							</div>
							<div class="columns">
								<div class="column is-4">
									<span>Imprimé par</span>
								</div>
								<div class="column">
									<span></span>
								</div>
							</div>
							<div class="columns">
								<div class="column is-4">
									<span>Date d'impression</span>
								</div>
								<div class="column">
									<span>{{ treatment?.printed_at }}</span>
								</div>
							</div>
						</div>
					</div>
				</div>
			</VCard>
		</div>
	</div>
</template>
