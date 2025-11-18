<template>
	<div class="page-content-inner">
		<div class="mb-4">
			<BackButton />
		</div>
		<VLoader size="large" :active="loading">
			<VCard class="side-card">
				<div class="card-head">
					<h3>Journalisation</h3>
				</div>
				<div class="card-inner is-one-third">
					<div class="columns">
						<div class="column is-half">
							<span class="has-text-weight-semibold">Nom</span>
						</div>
						<div class="column">{{ log.log.log_name }}</div>
					</div>
					<div class="columns">
						<div class="column is-half">
							<span class="has-text-weight-semibold">Événement</span>
						</div>
						<div class="column">{{ log.log.event }}</div>
					</div>
					<div class="columns">
						<div class="column is-half">
							<span class="has-text-weight-semibold">Description</span>
						</div>
						<div class="column">{{ log.log.description }}</div>
					</div>
					<div class="columns">
						<div class="column is-half">
							<span class="has-text-weight-semibold">Action</span>
						</div>
						<div class="column">{{ log.log.log_action }}</div>
					</div>
					<div class="columns">
						<div class="column is-half">
							<span class="has-text-weight-semibold">Date</span>
						</div>
						<div class="column">{{ log.log.created_at }}</div>
					</div>

					<!-- <div class="columns">
							<div class="column is-half">
								<span class="has-text-weight-semibold">Statut</span>
							</div>
							<div class="column">{{demand.model.status}}</div>
						</div>
						<div class="columns">
							<div class="column is-half">
								<span class="has-text-weight-semibold">Statut du paiement</span>
							</div>
							<div class="column">statut du paiement</div>
						</div>
						<div class="columns">
							<div class="column is-half">
								<span class="has-text-weight-semibold">Date</span>
							</div>
							<div class="column">date</div>
						</div> -->
				</div>

				<div class="card-head bg-primary-100 mt-8">
					<h3 class="!text-primary-dark">Détails du sujet</h3>
				</div>
				<div class="card-inner is-one-third">
					<div class="columns">
						<div class="column is-half">
							<span class="has-text-weight-semibold">Référence</span>
						</div>
						<div class="column">{{ log.log.subject.reference }}</div>
					</div>
					<div class="columns">
						<div class="column is-half">
							<span class="has-text-weight-semibold">Montant</span>
						</div>
						<div class="column">{{ log.log.subject.amount }}</div>
					</div>
					<div class="columns">
						<div class="column is-half">
							<span class="has-text-weight-semibold">Statut</span>
						</div>
						<div class="column">{{ log.log.subject.status }}</div>
					</div>
					<div class="columns">
						<div class="column is-half">
							<span class="has-text-weight-semibold">Date</span>
						</div>
						<div class="column">{{ log.log.subject.created_at }}</div>
					</div>
				</div>
			</VCard>
		</VLoader>
	</div>
</template>

<script setup lang="ts">
	import client from "/@src/composable/axiosClient";
	import BackButton from "/@src/pages/activity-logs/BackButton.vue";

	const log = ref(null);
	const loading = ref(true);

	const props = defineProps({
		logId: {
			type: String,
			required: true,
		},
	});

	onUnmounted(() => {
		log.value = null;
	});

	onMounted(async () => {
		client({
			method: "GET",
			url: `activity-logs/${props.logId}`,
		})
			.then((response) => {
				log.value = response.data;
			})
			.finally(() => {
				loading.value = false;
			});
	});
</script>
