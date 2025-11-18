<template>
	<div class="page-content-inner">
		<VLoader :active="loading" size="large">
			<CreateFormWrapper :col="12" @submit="submit">
				<template #form-head-inner>
					<div class="left">
						<h3>{{ update ? "Modification" : "Création" }}</h3>
					</div>
					<div class="right">
						<div class="buttons">
							<VButton
								dark-outlined
								icon="fa-light fa-arrow-left rem-100"
								light
								type="reset"
								@click="returnPreviousPage($router)"
							>
								Retour
							</VButton>
							<VButton :loading="formLoading" color="primary" raised tabindex="0" type="submit">
								Enregistrer
							</VButton>
						</div>
					</div>
				</template>
				<template #form-body>
					<div class="form-fieldset">
						<div class="fieldset-heading">
							<h4>Personnalisation</h4>
						</div>

						<div class="columns is-multiline">
							<div class="column is-4">
								<VField>
									<VLabel required>Nom</VLabel>
									<VControl fullwidth :errors="errors.name || []">
										<VInput v-model="row.name" name="name" placeholder="Nom" required type="text" />
									</VControl>
								</VField>
							</div>
							<div class="column is-4">
								<VField>
									<VLabel required>Titre du dirigeant</VLabel>
									<VControl fullwidth :errors="errors.manager_title || []">
										<VInput
											v-model="row.manager_title"
											name="manager_title"
											placeholder="Titre du dirigeant"
											required
										/>
									</VControl>
								</VField>
							</div>
							<div class="column is-4">
								<VField>
									<VLabel required>Type de centre de gestion</VLabel>
									<VControl fullwidth :errors="errors.management_center_type_id || []">
										<v-select
											v-model="row.management_center_type_id"
											:options="formData.management_center_types"
											:reduce="(item) => item.id"
											label="label"
										></v-select>
									</VControl>
								</VField>
							</div>
						</div>
					</div>

					<div class="is-divider" />

					<div class="form-fieldset">
						<div class="fieldset-heading">
							<h4>Adresse</h4>
						</div>

						<div class="columns is-multiline">
							<div class="column is-3">
								<VField>
									<VLabel required>Département</VLabel>
									<VControl fullwidth :errors="errors.state_id || []">
										<v-select
											v-model="row.state_id"
											:options="formData.states"
											:reduce="(item) => item.id"
											label="name"
										></v-select>
									</VControl>
								</VField>
							</div>
							<div class="column is-3">
								<VField>
									<VLabel required>Communes</VLabel>
									<VControl fullwidth :errors="errors.town_id || []">
										<v-select
											v-model="row.town_id"
											:options="filteredCommunes"
											:reduce="(item) => item.id"
											label="name"
											:placeholder="spendingRequest ? 'Chargement...' : ''"
										>
										</v-select>
									</VControl>
								</VField>
							</div>
							<div class="column is-3">
								<VField>
									<VLabel required>Arrondissement</VLabel>
									<VControl fullwidth :errors="errors.district_id || []">
										<v-select
											v-model="row.district_id"
											:options="filteredArrondissements"
											:reduce="(item) => item.id"
											label="name"
											:placeholder="spendingRequest ? 'Chargement...' : ''"
										></v-select>
									</VControl>
								</VField>
							</div>
							<div class="column is-3">
								<VField>
									<VLabel required>Quartier ou village</VLabel>
									<VControl fullwidth :errors="errors.village_id || []">
										<v-select
											v-model="row.village_id"
											:options="filteredVillages"
											:reduce="(item) => item.id"
											label="name"
											:placeholder="spendingRequest ? 'Chargement...' : ''"
										></v-select>
									</VControl>
								</VField>
							</div>
						</div>
					</div>

					<div class="is-divider" />

					<div class="form-fieldset">
						<div class="fieldset-heading">
							<h4>Relations</h4>
						</div>

						<div class="columns is-multiline">
							<div class="column is-6">
								<VField>
									<VLabel required>Responsable</VLabel>
									<VControl fullwidth :errors="errors.responsible_id || []">
										<CustomVSelect v-model="row.responsible_id">
											<VOption
												v-for="profile in formData.profiles"
												:key="profile.id"
												:value="profile.id"
											>
												{{ profile.name }}
											</VOption>
										</CustomVSelect>
									</VControl>
								</VField>
							</div>
							<div class="column is-6">
								<!--							Revoir pour mettre des cases à cocher-->
								<VField>
									<VLabel>Services de l'anatt</VLabel>
									<VControl fullwidth :errors="errors.services || []">
										<v-select
											v-model="row.services"
											:options="formData.services"
											:reduce="(item) => item.id"
											label="name"
											multiple
										></v-select>
									</VControl>
								</VField>
							</div>
							<div class="column is-6">
								<VField>
									<VLabel required>Parcs</VLabel>
									<VControl fullwidth :errors="errors.parks || []">
										<v-select
											v-model="row.parks"
											:options="formData.parks"
											:reduce="(item) => item.id"
											label="name"
											multiple
										></v-select>
									</VControl>
								</VField>
							</div>
							<div class="column is-6">
								<VField>
									<VLabel required>Type</VLabel>
									<VControl fullwidth :errors="errors.management_center_type_id || []">
										<v-select
											v-model="row.management_center_type_id"
											:options="formData.management_center_types"
											:reduce="(item) => item.id"
											label="label"
										></v-select>
									</VControl>
								</VField>
							</div>
							<div class="column is-6">
								<VField>
									<VLabel required>Zones</VLabel>
									<VControl fullwidth :errors="errors.zones || []">
										<v-select
											v-model="row.zones"
											:options="formData.zones"
											:reduce="(item) => item.id"
											label="name"
											multiple
										></v-select>
									</VControl>
								</VField>
							</div>
						</div>
					</div>
				</template>
			</CreateFormWrapper>
		</VLoader>
	</div>
</template>

<script lang="ts" setup>
	import { ref } from "vue";
	import { useNotyf } from "/@src/composable/useNotyf";
	import { useCrudStore } from "/@src/stores/modules/crud";
	import { storeToRefs } from "pinia";
	import VLoader from "/@src/components/base/loader/VLoader.vue";
	import { returnPreviousPage } from "/@src/utils/helpers";
	import client from "/@src/composable/axiosClient";

	const props = defineProps({
		centerId: {
			type: String,
			default: null,
		},
	});

	const router = useRouter();
	const notyf = useNotyf();
	const crudStore = useCrudStore();
	const { url, formLoading, row, errors, loading, formData } = storeToRefs(crudStore);
	const update = ref(false);
	const spendingRequest = ref(false);

	onBeforeMount(() => {
		loading.value = true;
		url.value = "/management-centers";
	});

	const submit = () => {
		if (update.value) {
			crudStore.updateRow(row.value.id, row.value).then(() => {
				notyf.success("Modification effectué avec succès!");
				router.push({ name: "management_centers" });
			});
		} else {
			crudStore.createRow(row.value).then(() => {
				notyf.success("Enregistrement effectué avec succès!");
				router.push({ name: "management_centers" });
			});
		}
	};

	const filteredCommunes = ref([]);
	watch(
		() => row.value.state_id,
		async (id) => {
			if (id != undefined) {
				spendingRequest.value = true;
				row.value.town_id = undefined;
				await client.get(`/registration/search/towns?state_id=${id}`).then((res) => {
					filteredCommunes.value = res.data;
					spendingRequest.value = false;
				});
			}
		}
	);
	const filteredArrondissements = ref([]);
	watch(
		() => row.value.town_id,
		async (id) => {
			if (id != undefined) {
				spendingRequest.value = true;
				row.value.district_id = undefined;
				await client.get(`/registration/search/districts?town_id=${id}`).then((res) => {
					filteredArrondissements.value = res.data;
					spendingRequest.value = false;
				});
			}
		}
	);
	const filteredVillages = ref([]);
	watch(
		() => row.value.district_id,
		async (id) => {
			if (id != undefined) {
				spendingRequest.value = true;
				row.value.village_id = undefined;
				await client.get(`/registration/search/villages?district_id=${id}`).then((res) => {
					filteredVillages.value = res.data;
					spendingRequest.value = false;
				});
			}
		}
	);
	onMounted(() => {
		if (props.centerId) {
			update.value = true;
			crudStore.loadEditData(props.centerId).then((res: Array) => {
				row.value = res.management_center;
				row.value.district_id = null;
				row.value.village_id = null;
				row.value.town_id = null;
				formData.value = res;
			});
		} else {
			crudStore.loadCreateData();
			row.value = {};
		}
	});
</script>

<style lang="scss">
	.form-fieldset {
		width: 80%;
		max-width: 100% !important;
	}
</style>
