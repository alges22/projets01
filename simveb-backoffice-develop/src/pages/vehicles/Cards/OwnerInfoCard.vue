<template>
	<VCard class="side-card" style="height: 500px; overflow: scroll">
		<div class="card-head">
			<h3>{{ title || "Détails du propriétaire" }}</h3>
		</div>
		<div class="card-inner is-one-third pt-1 mx-4 mt-4" style="font-size: 12px">
			<div class="columns">
				<div class="column is-half">
					<span class="has-text-weight-semibold">Nom</span>
				</div>
				<div class="column">{{ owner.lastname }}</div>
			</div>
			<div class="columns">
				<div class="column is-half">
					<span class="has-text-weight-semibold">Prénoms</span>
				</div>
				<div class="column">{{ owner.firstname }}</div>
			</div>
			<div class="columns">
				<div class="column is-half">
					<span class="has-text-weight-semibold">Date de naissance</span>
				</div>
				<div class="column">{{ date(owner.birthdate).format("DD/MM/YYYY") }}</div>
			</div>
			<div class="columns">
				<div class="column is-half">
					<span class="has-text-weight-semibold">Lieu de naissance</span>
				</div>
				<div class="column">{{ owner.birth_place }}</div>
			</div>
			<div class="columns">
				<div class="column is-half">
					<span class="has-text-weight-semibold">Nationalité</span>
				</div>
				<div class="column">{{ owner.origin_country }}</div>
			</div>
			<div class="columns">
				<div class="column is-half">
					<span class="has-text-weight-semibold">Sexe</span>
				</div>
				<div class="column">{{ owner.gender }}</div>
			</div>
			<div class="columns">
				<div class="column is-half">
					<span class="has-text-weight-semibold">NPI</span>
				</div>
				<div class="column">{{ owner.npi }}</div>
			</div>
			<div class="columns">
				<div class="column is-half">
					<span class="has-text-weight-semibold">Référence du BFU</span>
				</div>
				<div class="column">{{ owner.bfu }}</div>
			</div>
			<div class="columns">
				<div class="column is-half">
					<span class="has-text-weight-semibold">Téléphone</span>
				</div>
				<div class="column">{{ owner.telephone }}</div>
			</div>
			<div class="columns">
				<div class="column is-half">
					<span class="has-text-weight-semibold">Adresse email</span>
				</div>
				<div class="column">{{ owner.email }}</div>
			</div>
			<div class="columns">
				<div class="column is-half">
					<span class="has-text-weight-semibold">Domicile</span>
				</div>
				<div class="column">{{ owner.address }}</div>
			</div>
		</div>
	</VCard>
</template>

<script setup lang="ts">
	import { useDate } from "vue3-dayjs-plugin/useDate.js";

	defineProps<{
		owner: any;
		title: string;
	}>();
	const date = useDate();
</script>

<style lang="scss">
	@import "/@src/scss/abstracts/all";

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
