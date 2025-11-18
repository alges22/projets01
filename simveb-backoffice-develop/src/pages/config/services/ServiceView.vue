<script setup lang="ts">
	import { storeToRefs } from "pinia";
	import { ref } from "vue";
	import { useCrudStore } from "/@src/stores/modules/crud";
	import { userHasPermissions } from "/@src/utils/permission";
	import AlertComponent from "/@src/components/AlertComponent.vue";
	import client from "/@src/composable/axiosClient";
	import Swal from "sweetalert2";
	import { useNotyf } from "/@src/composable/useNotyf";

	const crudStore = useCrudStore();
	const { url, row: service, loading } = storeToRefs(crudStore);
	const props = defineProps(["serviceId"]);
	const { can } = userHasPermissions();

	const notyf = useNotyf();

	const tabs = ref([
		{ label: "Information générale du service", value: "information", icon: "lightbulb" },
		{ label: "Parcours de traitement des demandes de ce service", value: "step", icon: "step" },
	]);

	const enableService = async (enable = true) => {
		await Swal.fire({
			title: enable ? "Activer le service." : "Désactiver le service",
			text: enable
				? "Souhaitez-vous vraiment activer le service."
				: "Souhaitez-vous vraiment désactiver le service.",
			icon: "warning",
			showCancelButton: true,
			confirmButtonText: "Oui, Je confirme",
			cancelButtonText: "Annuler",
		}).then(async (result) => {
			if (result.isConfirmed) {
				await client
					.post(`toggle-service/${service.value.id}`, {
						is_active: enable ? 1 : 0,
						_method: "put",
					})
					.then((res) => {
						crudStore.fetchRow(props.serviceId);

						notyf.success(`Le service a bien été ${enable ? "activé" : "désactivé"}`);
					});
			}
		});
	};

	onBeforeMount(() => {
		loading.value = true;
		url.value = "services";
		service.value = null;
	});

	onMounted(async () => {
		await crudStore.fetchRow(props.serviceId);
	});

	onUnmounted(() => {
		service.value = null;
	});
</script>

<template>
	<div class="page-content-inner">
		<div class="bg-white rounded-lg mb-4 p-4 flex items-center justify-between space-x-4">
			<div class="text-lg font-bold">
				<span v-if="service" class="uppercase">{{ service.name }}</span>
			</div>
			<div>
				<VButton
					v-if="service?.is_active && can('update-service')"
					class="me-2"
					color="danger"
					size="medium"
					raised
					@click="enableService(false)"
				>
					<i class="fa fa-ban" aria-hidden="true"></i>
					Désactiver
				</VButton>
				<!--<VButton
					v-if="service && can('update-service'"
					style="grid-column: 2; grid-row: 1"
					class="has-text-primary"
					:to="{
						name: 'pricing_management',
						params: { serviceId: service.id },
					}"
				>
					<i class="fa fa-money-bill" aria-hidden="true"></i>
					Configurer les prix
				</VButton>-->
			</div>
		</div>

		<AlertComponent
			v-if="service && !service.is_active"
			class="border-l-4 mb-4 rounded-md"
			icon="exclamation-triangle"
			color="danger"
		>
			<p class="text-lg text-yellow-700">
				Ce service est actuellement désactiver. Souhaitez-vous
				<button
					v-if="can('update-service')"
					class="font-medium text-yellow-700 underline hover:text-yellow-600"
					@click="enableService(true)"
				>
					le réactiver ?
				</button>
			</p>
		</AlertComponent>

		<VTabs v-if="service" selected="information" :tabs="tabs">
			<template #tab="{ activeValue }">
				<div v-if="activeValue == 'information'" class="columns-1 lg:columns-2">
					<div>
						<div class="tab-details-inner">
							<VCard class="side-card">
								<div class="card-head has-text-white-bis has-text-weight-semibold">
									<h3 class="sm:text-rose-700">Informations générale</h3>
								</div>
								<div class="card-inner">
									<div class="columns">
										<div class="column is-half">
											<span class="has-text-weight-semibold">Nom du service</span>
										</div>
										<div class="column">{{ service.name }}</div>
									</div>
									<div class="columns">
										<div class="column is-half">
											<span class="has-text-weight-semibold">Description</span>
										</div>
										<div class="column">{{ service.description }}</div>
									</div>

									<div class="columns">
										<div class="column is-half">
											<span class="has-text-weight-semibold">Durée</span>
										</div>
										<div class="column">{{ service.duration }}</div>
									</div>
									<div class="columns">
										<div class="column is-half">
											<span class="has-text-weight-semibold">Prix</span>
										</div>
										<div class="column">{{ service.cost }}</div>
									</div>
									<div class="columns">
										<div class="column is-half">
											<span class="has-text-weight-semibold">Qui peut postuler</span>
										</div>
										<div class="column">{{ service.who_can_apply }}</div>
									</div>
									<div class="columns">
										<div class="column is-half">
											<span class="has-text-weight-semibold">Status du service</span>
										</div>
										<div class="column">{{ service.status }}</div>
									</div>
									<div class="columns">
										<div class="column is-half">
											<span class="has-text-weight-semibold">Lien</span>
										</div>
										<div class="column">{{ service.link ? service.link : "Non définis" }}</div>
									</div>
									<div class="columns">
										<div class="column is-half">
											<span class="has-text-weight-semibold">Actif</span>
										</div>
										<div class="column">{{ service.is_active ? "oui" : "non" }}</div>
									</div>
									<div class="columns">
										<div class="column is-half">
											<span class="has-text-weight-semibold">
												Peut faire l'objet d'une demande
											</span>
										</div>
										<div class="column">{{ service.can_be_demanded ? "oui" : "non" }}</div>
									</div>
									<div class="columns">
										<div class="column is-half">
											<span class="has-text-weight-semibold">Organization</span>
										</div>
										<div class="column">{{ service.organization?.name }}</div>
									</div>
									<div class="columns">
										<div class="column is-half">
											<span class="has-text-weight-semibold">Type</span>
										</div>
										<div class="column">{{ service.type?.name }}</div>
									</div>
								</div>
							</VCard>
						</div>
						<div class="tab-details-inner">
							<VCard class="side-card">
								<div class="card-head has-text-white-bis has-text-weight-semibold">
									<h3>Services enfants</h3>
								</div>
								<div class="card-inner">
									<div v-if="service.children.length > 0">
										<div class="columns">
											<div class="column is-half">
												<span class="has-text-weight-semibold">Nom du service</span>
											</div>
											<div class="column">{{ service.name }}</div>
										</div>
										<div class="columns">
											<div class="column is-half">
												<span class="has-text-weight-semibold">Description</span>
											</div>
											<div class="column">{{ service.description }}</div>
										</div>
										<div class="columns">
											<div class="column is-half">
												<span class="has-text-weight-semibold">Durée</span>
											</div>
											<div class="column">{{ service.description }}</div>
										</div>
										<div class="columns">
											<div class="column is-half">
												<span class="has-text-weight-semibold">Prix</span>
											</div>
											<div class="column">{{ service.cost }}</div>
										</div>
										<div class="columns">
											<div class="column is-half">
												<span class="has-text-weight-semibold">Qui peut postuler</span>
											</div>
											<div class="column">{{ service.who_can_apply }}</div>
										</div>
									</div>
									<div v-else>
										<p class="text-center is-black">Ce service n'a pas de services enfants</p>
									</div>
								</div>
							</VCard>
						</div>
					</div>
					<div>
						<div class="tab-details-inner">
							<VCard class="side-card">
								<div class="card-head has-text-white-bis has-text-weight-semibold">
									<h3>Listes des documents</h3>
								</div>
								<div class="card-inner">
									<div v-if="service.documents.length > 0">
										<div v-for="(doc, index) in service.documents" :key="index">
											<div class="columns">
												<div class="column is-half">
													<span class="has-text-weight-semibold">{{ doc.description }}</span>
												</div>
											</div>
										</div>
									</div>
									<div v-else>
										<p class="text-center is-black">Ce service n'a pas de services enfants</p>
									</div>
								</div>
							</VCard>
						</div>
						<div class="tab-details-inner">
							<VCard class="side-card">
								<div class="card-head has-text-white-bis has-text-weight-semibold">
									<h3>Listes des extras-services</h3>
								</div>
								<div class="card-inner">
									<div v-if="service.service_extra_services.length > 0">
										<div v-for="(ser, index) in service.service_extra_services" :key="index">
											<div class="columns">
												<div class="column is-half">
													<span class="has-text-weight-semibold">Code</span>
												</div>
												<div class="column">{{ ser.code }}</div>
											</div>
											<div class="columns">
												<div class="column is-half">
													<span class="has-text-weight-semibold">Description</span>
												</div>
												<div class="column">{{ ser.description }}</div>
											</div>
											<hr />
										</div>
									</div>
									<div v-else>
										<p class="text-center is-black">Ce service n'a pas d'extra services enfants</p>
									</div>
								</div>
							</VCard>
						</div>
					</div>
				</div>
				<div v-else-if="activeValue == 'step'" class="columns-1 lg:columns-2">
					<VCard v-for="step in service.steps" class="side-card column is-12" :key="step.id">
						<div class="card-head has-text-white-bis has-text-weight-semibold">{{ step.label }}</div>
						<div class="card-inner">
							<div class="columns">
								<div class="column is-half">
									<span class="has-text-weight-semibold">Position</span>
								</div>
								<div class="column">{{ step.pivot.position }}</div>
							</div>
							<div class="columns">
								<div class="column is-half">
									<span class="has-text-weight-semibold">Type de processus</span>
								</div>
								<div class="column">{{ step.pivot.process_type }}</div>
							</div>
							<div class="columns">
								<div class="column is-half">
									<span class="has-text-weight-semibold">Durée</span>
								</div>
								<div class="column">{{ step.pivot.duration }}</div>
							</div>
							<div class="columns">
								<div class="column is-half">
									<span class="has-text-weight-semibold">Action</span>
								</div>
								<div class="column">
									<VButton
										class="is-primary"
										:to="{
											name: 'actions',
											params: { serviceId: props.serviceId, stepId: step.id },
										}"
									>
										<span class="has-text-white-bis has-text-weight-semibold"
											>Gérer les actions</span
										>
									</VButton>
								</div>
							</div>
						</div>
					</VCard>
				</div>
			</template>
		</VTabs>
	</div>
</template>
