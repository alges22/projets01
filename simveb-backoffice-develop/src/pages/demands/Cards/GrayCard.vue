<template>
	<div class="flex flex-col md:flex-row items-center justify-around w-full">
		<div class="is-flex is-justify-content-center">
			<div class="gray-card-front">
				<div class="bg-image">
					<img src="/images/logos/logo/logo_anatt-crop.png" alt="benin-seal" />
				</div>
				<div class="content">
					<div class="content-header">
						<span class="maintitle-text">République du bénin</span>
						<span class="subtitle-text"> Direction Générale des Transports Terrestres </span>
					</div>
					<div>
						<span>N° {{ demandInfo.model.gray_card.number }}</span>
					</div>
				</div>
			</div>
		</div>

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
							<span class="subtitle-text"> Direction Générale des Transports Terrestres </span>
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
									<span class="content-info-title"> Numéro d'immatriculation </span>
								</div>
								<div class="">
									<span class="content-info-subtitle">
										{{ demandInfo.model.number_label }}
									</span>
								</div>
							</div>
							<div v-if="demandInfo.model.label" class="column is-half content-info-row">
								<div class="">
									<span class="content-info-title"> Label </span>
								</div>
								<div class="">
									<span class="content-info-subtitle">
										{{ demandInfo.model.label }}
									</span>
								</div>
							</div>
							<div class="is-half content-info-row">
								<div class="">
									<span class="content-info-title"> Numéro de chassis </span>
								</div>
								<div class="">
									<span class="content-info-subtitle">
										{{ demandInfo.vehicle.vin }}
									</span>
								</div>
							</div>
							<div class="column is-half content-info-row">
								<div class="">
									<span class="content-info-title"> Nom du propriétaire </span>
								</div>
								<div class="">
									<span class="content-info-subtitle">
										{{ demandInfo.vehicle_owner.lastname }}
									</span>
								</div>
							</div>
							<div class="column is-half content-info-row">
								<div class="">
									<span class="content-info-title"> Prénoms du propriétaire </span>
								</div>
								<div class="">
									<span class="content-info-subtitle">
										{{ demandInfo.vehicle_owner.firstname }}
									</span>
								</div>
							</div>
							<div class="column is-half content-info-row">
								<div class="">
									<span class="content-info-title"> Date de Délivrance </span>
								</div>
								<div class="">
									<span class="content-info-subtitle">
										{{ dayjs(demandInfo.demand.created_at).format("DD-MM-YYYY") }}
									</span>
								</div>
							</div>
							<div class="column is-half content-info-row">
								<div class="">
									<span class="content-info-title"> Date de naissance </span>
								</div>
								<div class="">
									<span class="content-info-subtitle">
										{{ demandInfo.vehicle_owner.birth_date }}
									</span>
								</div>
							</div>
							<div class="column is-12 has-text-centered content-info-row text-size-subtitle">
								<span class=""></span>
								<span>{{ demandInfo.model.gray_card.number }}</span>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</template>

<script lang="ts" setup>
	import CountryFlag from "vue-country-flag-next";
	import { useDate } from "vue3-dayjs-plugin/useDate";

	defineProps<{
		demandInfo: any;
	}>();

	const dayjs = useDate();
</script>

<style lang="scss">
	@import "/@src/scss/abstracts/all";

	@media only screen and (width <=767px) {
	}

	@media only screen and (width >=768px) and (width <=1024px) and (orientation: portrait) {
		.columns {
			display: flex;

			.column {
				min-width: 50%;
			}
		}
	}
</style>
