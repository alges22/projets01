<script setup lang="ts">
	import { storeToRefs } from "pinia";
	import { onBeforeMount, onUnmounted, ref } from "vue";
	import { useCrudStore } from "/@src/stores/modules/crud";
	import { Notyf } from "notyf";
	import client from "/@src/composable/axiosClient";
	import OwnerInfoCard from "/@src/pages/vehicles/Cards/OwnerInfoCard.vue";
	import { userHasPermissions } from "/@src/utils/permission";

	const route = useRoute();

	const positions = ref([]);
	const organizations = ref([]);
	const roles = ref([]);
	const centers = ref([]);

	const crudStore = useCrudStore();
	const { url, row: staff } = storeToRefs(crudStore);
	const { can } = userHasPermissions();
	const notyf = new Notyf();

	const itemId = route.params.id;

	const item = ref({
		profile_id: null,
		center_id: null,
		organizations: [],
	});

	const data = ref({
		center_id: null,
		organizations: [],
	});

	const urlPath = "/staff";

	onBeforeMount(() => {
		url.value = urlPath;
	});

	onUnmounted(() => {
		crudStore.reset();
	});

	onMounted(() => {
		crudStore.loadCreateData().then((res) => {
			positions.value = res.positions;
			organizations.value = res.organizations;
			roles.value = res.roles;
			centers.value = res.centers;
		});

		crudStore.loadEditData(itemId).then((response) => {
			data.value.organizations = response.staff.organizations.map((organization) => {
				return organization.id;
			});

			data.value.center_id = response.staff.center_id;

			item.value = response.staff;

			item.value.organizations = response.staff.organizations.map((organization) => {
				return organization.id;
			});

			item.value.center_id = response.staff.center_id;

			item.value.profile_id = response.staff.profile_id;
		});

		crudStore.fetchRow(itemId);
	});

	function desactiver(type) {
		if (type === "organizations") {
			client({
				method: "PUT",
				url: "/update-staff-organizations",
				data: {
					organizations: [],
					profile_id: item.value.profile_id,
				},
			}).then(() => {
				item.value.organizations = [];
				data.value.organizations = [];

				notyf.success("Désaffectation effectuée avec succès");
			});
		} else if (type === "center") {
			client({
				method: "PUT",
				url: "/update-staff-center",
				data: {
					center_id: null,
					profile_id: item.value.profile_id,
				},
			}).then(() => {
				item.value.center_id = null;
				data.value.center_id = null;

				notyf.success("Désaffectation effectuée avec succès");
			});
		}
	}

	function affecter(type) {
		if (type === "organizations") {
			client({
				method: "PUT",
				url: "/update-staff-organizations",
				data: {
					organizations: data.value.organizations,
					profile_id: item.value.profile_id,
				},
			})
				.then((response) => response.data)
				.then((response) => {
					item.value.organizations = response.organizations.map((organization) => {
						return organization.id;
					});

					data.value.organizations = response.organizations.map((organization) => {
						return organization.id;
					});

					notyf.success("Affectation effectuée avec succès");
				});
		} else if (type === "center") {
			client({
				method: "PUT",
				url: "/update-staff-center",
				data: {
					center_id: data.value.center_id,
					profile_id: item.value.profile_id,
				},
			})
				.then((response) => response.data)
				.then((response) => {
					item.value.center_id = response.center_id;
					data.value.center_id = response.center_id;

					notyf.success("Affectation effectuée avec succès");
				});
		}
	}
</script>

<template>
	<div class="page-content-inner">
		<div class="bg-white rounded-lg mb-4 p-4 flex items-center justify-between accreditation-x-4">
			<VIconButton color="primary" outlined circle icon="fa-light fa-arrow-left" @click="$router.back" />
			<div class="text-primary-dark text-2xl font-bold">
				Détails du staff
				<span v-if="staff.identity" class="uppercase">
					{{ staff.identity.fullName }}
				</span>
			</div>
			<div></div>
		</div>
		<div>
			<div class="grid grid-cols-2 xl:grid-cols-3 gap-4 tab-details-inner">
				<TransitionGroup appear name="list">
					<!-- profile information -->
					<OwnerInfoCard v-if="item.identity" :owner="item.identity" title="Détails du staff" />
					<!-- // centre de gestion -->
					<VCard v-if="centers.length > 0" class="side-card" style="height: 500px; overflow: scroll">
						<div class="card-head">
							<h3>Centre de gestion</h3>
						</div>
						<div class="card-inner is-one-third pt-1 mx-4 mt-4" style="font-size: 12px">
							<div v-for="(center, index) in centers" :key="center" class="columns">
								<div class="column flex items-center">
									<input
										:id="`nom-${index}`"
										v-model="data.center_id"
										type="radio"
										name="center"
										class="mr-2"
										:value="center.id"
									/>
									<label :for="`nom-${index}`" class="has-text-weight-semibold w-full">
										{{ center.name }}
									</label>
								</div>
								<!-- <div class="column">{{ center.name }}</div> -->
							</div>
							<div class="flex items-center justify-start">
								<VButton
									v-if="can('update-staff') && item.center_id"
									class="ml-2 has-text-danger"
									@click="desactiver('center')"
								>
									Désactiver l'affectation
								</VButton>
								<VButton
									v-if="can('update-staff') && !item.center_id"
									color="success"
									class="ml-2"
									@click="affecter('center')"
								>
									Affecter
								</VButton>
							</div>
						</div>
					</VCard>
					<!-- // organisation -->
					<VCard v-if="organizations.length > 0" class="side-card" style="height: 500px; overflow: scroll">
						<div class="card-head">
							<h3>Organisations</h3>
						</div>
						<div class="card-inner is-one-third pt-1 mx-4 mt-4" style="font-size: 12px">
							<div v-for="(organization, index) in organizations" :key="organization" class="columns">
								<div class="column flex items-center">
									<input
										:id="`organization-${index}`"
										class="focus:ring-primary-500 h-4 w-4 text-primary-600 border-primary-300 me-2"
										type="checkbox"
										:checked="data.organizations.includes(organization.id)"
										:value="organization.id"
										@change="
											($event) => {
												$event.target.checked
													? data.organizations.push(organization.id)
													: data.organizations.splice(
															data.organizations.indexOf(organization.id),
															1
													  );
											}
										"
									/>
									<label :for="`organization-${index}`">{{ organization.name }}</label>
								</div>
								<!-- <div class="column">{{ center.name }}</div> -->
							</div>
							<div class="flex items-center justify-start">
								<VButton
									v-if="can('update-staff') && item.organizations.length > 0"
									class="ml-2 has-text-danger"
									@click="desactiver('organizations')"
								>
									Désactiver l'affectation
								</VButton>
								<VButton
									v-if="can('update-staff') && item.organizations.length <= 0"
									color="success"
									class="ml-2"
									@click="affecter('organizations')"
								>
									Affecter
								</VButton>
							</div>
						</div>
					</VCard>
					<!-- // demandes traités -->
					<VCard
						v-if="staff.treatedDemands?.length"
						class="side-card"
						style="height: 500px; overflow: scroll"
					>
						<div class="card-head">
							<h3>Demandes traitées</h3>
						</div>
						<div class="card-inner is-one-third pt-1 mx-4 mt-4" style="font-size: 12px">
							<div v-for="demand in staff.treatedDemands" :key="demand" class="columns">
								<div class="flex flex-col w-full">
									<div class="flex w-full">
										<div class="column flex items-center w-1/2">Référence</div>
										<div class="column">
											<KeyField
												:to="{
													name: 'demands_show',
													params: {
														demandId: demand.id,
													},
												}"
												:value="demand.reference"
											/>
										</div>
									</div>
								</div>
							</div>
						</div>
					</VCard>
					<!-- // role -->
					<VCard
						v-if="staff.profile?.roles.length > 0"
						class="side-card"
						style="height: 500px; overflow: scroll"
					>
						<div class="card-head">
							<h3>Rôles</h3>
						</div>
						<div class="card-inner is-one-third pt-1 mx-4 mt-4" style="font-size: 12px">
							<div v-for="role in staff.profile?.roles" :key="role" class="columns">
								<div class="column flex items-center">Nom</div>
								<div class="column">{{ role.label }}</div>
							</div>
						</div>
					</VCard>
					<!-- // dernières activités -->
					<VCard
						v-if="staff.latestActivityLogs?.length"
						class="side-card"
						style="height: 500px; overflow: scroll"
					>
						<div class="card-head">
							<h3>Dernières activités</h3>
						</div>
						<div class="card-inner is-one-third pt-1 mx-4 mt-4" style="font-size: 12px">
							<div v-for="activity in staff.latestActivityLogs" :key="activity" class="">
								<div class="columns my-1 flex flex-col">
									<div class="flex w-full">
										<div class="w-1/2 flex items-center font-semibold">Evennement</div>
										<div class="column w-1/2">
											{{ activity.log_name }}
										</div>
									</div>
									<div class="flex w-full">
										<div class="w-1/2 flex items-center font-semibold">Type d'action</div>
										<div class="column w-1/2">
											{{ activity.log_action }}
										</div>
									</div>
									<div class="flex w-full">
										<div class="w-1/2 flex items-center font-semibold">Description</div>
										<div class="column w-1/2">
											{{ activity.description }}
										</div>
									</div>
								</div>
							</div>
						</div>
					</VCard>
				</TransitionGroup>
			</div>
		</div>
	</div>
</template>
