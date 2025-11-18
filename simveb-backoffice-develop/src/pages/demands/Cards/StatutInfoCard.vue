<template>
	<VCard class="side-card">
		<div class="card-inner is-one-third">
			<div class="columns">
				<div class="column">
					<span>Statut</span>
				</div>
				<div class="column is-flex is-justify-content-space-between">
					<VTag :label="demand.status_label" :color="statusColor(demand?.status)" />
				</div>
			</div>
			<div class="columns">
				<div class="column">
					<span>Référence</span>
				</div>
				<div class="column">
					<KeyField :value="demand.reference" />
				</div>
			</div>
			<div class="columns">
				<div class="column">
					<span>Service</span>
				</div>
				<div class="column">
					<VTag :label="demand.service" color="primary" />
				</div>
			</div>

			<div class="columns">
				<div class="column">
					<span>Initiateur</span>
				</div>
				<div class="column is-flex">
					<span>{{ author.name }}</span>
				</div>
			</div>
			<div class="columns">
				<div class="column">
					<span>NPI</span>
				</div>
				<div class="column">
					<KeyField :value="author.npi" />
				</div>
			</div>
			<div class="columns">
				<div class="column">
					<span>ID Profil</span>
				</div>
				<div class="column">
					<span>{{ author.profile }}</span>
				</div>
			</div>
			<div class="columns">
				<div class="column">
					<span>Date de création</span>
				</div>
				<div class="column">
					<span>
						{{ dayjs(demand.created_at).format("DD-MM-YYYY HH:mm:ss") }}
					</span>
				</div>
			</div>
		</div>

		<div class="card-head bg-primary-100 mt-8">
			<h3 class="!text-primary-dark">Vérification du dossier</h3>
		</div>
		<div class="card-inner is-one-third">
			<ul class="steps has-content-centered">
				<template v-for="(step, index) in steps" :key="index">
					<li :class="{ 'is-active': step.is_current }" class="steps-segment">
						<span class="steps-marker">
							<i class="fa-light" :class="{ 'fa-check': step.is_done }"></i>
						</span>
						<div class="steps-content">
							<p class="is-size-8">{{ step.label }}</p>
						</div>
					</li>
				</template>
			</ul>

			<p class="text-xs text-primary-dark font-semibold mb-8">
				Votre plaque d'immatriculation est actuellement en cours d'impression et sera disponible très
				prochainement.
			</p>

			<div v-if="treatment.management_center_id" class="columns mt-8">
				<div class="column">
					<span>Assigné le</span>
				</div>
				<div class="column is-flex">
					<span>{{ treatment.assigned_to_center_at }}</span>
				</div>
				<div class="column is-flex text-sm text-blue-500">{{ treatment.management_center.name }}</div>
			</div>

			<div v-if="treatment.organization_id" class="columns mt-8">
				<div class="column">
					<span>Assigné le</span>
				</div>
				<div class="column is-flex">
					<span>{{ treatment.assigned_to_organization_at }}</span>
				</div>
				<div class="column is-flex text-sm text-blue-500">{{ treatment.organization.name }}</div>
			</div>

			<div v-if="treatment.responsible_id" class="columns mt-8">
				<div class="column">
					<span>Assigné le</span>
				</div>
				<div class="column is-flex">
					<span>{{ treatment.assigned_to_staff_at }}</span>
				</div>
				<div class="column is-flex text-sm text-blue-500">{{ treatment.responsible.identity.fullName }}</div>
			</div>

			<div v-if="treatment.verified_at" class="columns mt-8">
				<div class="column">
					<span>Vérifié le</span>
				</div>
				<div class="column is-flex">
					<span>{{ treatment.verified_at }}</span>
				</div>
				<div class="column is-flex text-sm text-blue-500">
					{{ treatment.verified_by?.identity.fullName }}
				</div>
			</div>

			<div v-if="treatment.affected_to_interpol_at" class="columns mt-8">
				<div class="column">
					<span>Affecté le</span>
				</div>
				<div class="column is-flex">
					<span>{{ treatment.affected_to_interpol_at }}</span>
				</div>
				<div class="column is-flex text-sm text-blue-500">Interpol</div>
			</div>

			<div v-if="treatment.pre_validated_at" class="columns mt-8">
				<div class="column">
					<span>Pré-validé par</span>
				</div>
				<div class="column is-flex">
					<span>{{ treatment.pre_validated_at }}</span>
				</div>
				<div class="column is-flex text-sm text-blue-500">
					{{ treatment.pre_validated_by?.identity.fullName }}
				</div>
			</div>

			<div v-if="treatment.validated_at" class="columns mt-8">
				<div class="column">
					<span>Validé par</span>
				</div>
				<div class="column is-flex">
					<span>{{ treatment.validated_at }}</span>
				</div>
				<div class="column is-flex text-sm text-blue-500">
					{{ treatment.validated_by?.identity.fullName }}
				</div>
			</div>
		</div>
	</VCard>
</template>

<script setup lang="ts">
	import statusColor from "/src/utils/status-color";
	import { useDate } from "vue3-dayjs-plugin/useDate";

	defineProps<{
		steps: any[];
		demand: any;
		author: any;
		treatment: any;
	}>();

	const dayjs = useDate();
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

	.steps.has-content-centered {
		width: 100%;
		overflow-x: scroll;
		// customize the scroll bar
		scrollbar-width: thin;
		scrollbar-color: var(--primary) var(--white);
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
