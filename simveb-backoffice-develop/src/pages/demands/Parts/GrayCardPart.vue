<template>
	<div class="columns tab-details-inner">
		<div class="columns tab-details-inner is-multiline">
			<div class="column is-12">
				<VCard class="side-card">
					<div class="card-head">
						<h3>Carte grise</h3>
					</div>
					<div class="card-inner is-size-6-5">
						<div class="columns is-multiline">
							<GrayCard v-if="demandInfo.model.gray_card" :demand-info="demandInfo" />

							<p v-else>Carte grise non disponible pour le moment</p>
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
										<span></span>
									</div>
								</div>
							</div>
						</div>
						<div class="flex">
							<button class="ml-auto button is-primary" :disabled="formLoading" @click="printGrayCard()">
								<i v-if="formLoading" class="fa fa-spinner fa-spin"></i>
								Imprimer
							</button>
						</div>
					</div>
				</VCard>
			</div>
		</div>
	</div>
</template>

<script lang="ts" setup>
	import GrayCard from "/src/pages/demands/Cards/GrayCard.vue";
	import printGrayCard from "/@src/pages/demands/Actions/GrayCardImpression";
	import { storeToRefs } from "pinia";
	import { useDemandStore } from "/@src/stores/modules/demand";

	const { formLoading } = storeToRefs(useDemandStore());

	defineProps<{
		demandInfo: any;
	}>();
</script>

<style lang="scss">
	@import "/@src/scss/abstracts/all";

	.is-size-6-5 {
		font-size: 0.95rem;
		font-family: var(--font-alt);
	}

	.tab-details-inner {
		flex-wrap: wrap !important;
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
