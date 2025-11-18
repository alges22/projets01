<!-- eslint-disable prettier-vue/prettier -->
<template>
	<div class="page-content-inner">
		<VTabs
			selected="general"
			:tabs="[
				// { label: 'General', value: 'general', icon: 'fas fa-users' },
				// { label: 'Permission', value: 'permission', icon: 'fas fa-users' },
				// { label: 'Étape du service', value: 'service_step', icon: 'lucide:box' },
				{ label: 'General', value: 'general', icon: '' },
				{ label: 'Permission', value: 'permission', icon: '' },
				{ label: 'Étape du service', value: 'service_step', icon: '' },
			]"
		>
			<template #tab="{ activeValue }">
				<div v-if="activeValue === 'general'" class="columns tab-details-inner">
					<VCard v-if="row.position != undefined" class="side-card">
						<div class="card-head">
							<h3>Géneral</h3>
						</div>
						<div class="card-inner is-one-third">
							<div class="columns">
								<div class="column is-half">
									<span class="has-text-weight-semibold">Position</span>
								</div>
								<div class="column">{{ row.position }}</div>
							</div>
							<div class="columns">
								<div class="column is-half">
									<span class="has-text-weight-semibold">Durée (en heure)</span>
								</div>
								<div class="column">{{ row.duration }}</div>
							</div>
							<div class="columns">
								<div class="column is-half">
									<span class="has-text-weight-semibold">Mode de déclenchement</span>
								</div>
								<div class="column">{{ row.process_type == "manual" ? "Manuel" : "Automatique" }}</div>
							</div>
						</div>
					</VCard>
				</div>
				<div v-else-if="activeValue === 'permission'" class="columns tab-details-inner">
					<VCard v-if="row.permission_service.permission != undefined" class="side-card">
						<div class="card-head">
							<h3>Permission</h3>
						</div>
						<div class="card-inner is-one-third">
							<!-- <div class="columns">
								<div class="column is-half">
									<span class="has-text-weight-semibold">Nom</span>
								</div>
								<div class="column">{{ row.permission_service.permission.name }}</div>
							</div> -->
							<div class="columns">
								<div class="column is-half">
									<span class="has-text-weight-semibold">Label</span>
								</div>
								<div class="column">{{ row.permission_service.permission.label }}</div>
							</div>
						</div>
					</VCard>
				</div>
				<div v-else-if="activeValue === 'service_step'" class="columns tab-details-inner">
					<VCard v-if="row.service_step.id != undefined" class="side-card">
						<div class="card-head">
							<h3>Étape du service</h3>
						</div>
						<div class="card-inner is-one-third">
							<div class="columns">
								<div class="column is-half">
									<span class="has-text-weight-semibold">Nom du service</span>
								</div>
								<div class="column">{{ service.name }}</div>
							</div>
							<div class="columns">
								<div class="column is-half">
									<span class="has-text-weight-semibold">Nom de l'étape</span>
								</div>
								<div class="column">{{ step.label }}</div>
							</div>
							<div class="columns">
								<div class="column is-half">
									<span class="has-text-weight-semibold">Position de l'étape</span>
								</div>
								<div class="column">{{ row.service_step.position }}</div>
							</div>
							<div class="columns">
								<div class="column is-half">
									<span class="has-text-weight-semibold">Durée de l'étape (en heure)</span>
								</div>
								<div class="column">{{ row.service_step.duration }}</div>
							</div>
							<div class="columns">
								<div class="column is-half">
									<span class="has-text-weight-semibold">Processus de l'étape</span>
								</div>
								<div class="column">{{ row.process_type == "manual" ? "Manuel" : "Automatique" }}</div>
							</div>
						</div>
					</VCard>
				</div>
			</template>
		</VTabs>
	</div>
</template>

<script setup>
	import { onBeforeMount } from "vue";
	import { useCrudStore } from "/@src/stores/modules/crud";
	import { useActionStore } from "/@src/stores/modules/action";
	import { storeToRefs } from "pinia";

	const crudStore = useCrudStore();
	const actionStore = useActionStore();
	const { service, step } = storeToRefs(actionStore);
	const { url, row } = storeToRefs(crudStore);
	const props = defineProps(["actionId"]);

	url.value = "/actions";
	onBeforeMount(() => {
		crudStore.fetchRow(props.actionId);
	});
</script>

<style lang="scss" scoped></style>
