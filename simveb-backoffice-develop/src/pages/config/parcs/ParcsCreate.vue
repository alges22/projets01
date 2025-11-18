<script lang="ts" setup>
	import { storeToRefs } from "pinia";
	import { useHead } from "@vueuse/head";
	import { useViewWrapper } from "/@src/stores/viewWrapper";
	import { returnPreviousPage } from "/@src/utils/helpers";
	import { useCrudStore } from "/@src/stores/modules/crud";
	import { useNotyf } from "/@src/composable/useNotyf";
	import client from "/@src/composable/axiosClient";
	import { ref } from "vue";

	const viewWrapper = useViewWrapper();
	viewWrapper.setPageTitle("Parcs");

	useHead({
		title: "Parcs",
	});

	const name = ref("");
	const address = ref("");
	const description = ref("");
	const longitude = ref("");
	const latitude = ref("");
	const affiliate = ref("");
	const vehicle_types = ref("");
	const vehicle_categories = ref("");
	const towns = ref("");

	const router = useRouter();
	const notyf = useNotyf();
	const crudStore = useCrudStore();
	const { url } = storeToRefs(crudStore);
	const isLoading = ref(true);
	const isSaving = ref(false);

	const data = ref({
		affiliate: [],
		towns: [],
		vehicle_categories: [],
		vehicle_types: [],
	});

	onBeforeMount(() => {
		url.value = "/parks";
	});

	const submit = () => {
		if (!isSaving.value) {
			isSaving.value = true;

			crudStore
				.createRow({
					name: name.value,
					address: address.value,
					description: description.value,
					longitude: longitude.value,
					latitude: latitude.value,
					affiliate_id: affiliate.value,
					vehicle_types: vehicle_types.value,
					vehicle_categories: vehicle_categories.value,
					towns: towns.value,
				})
				.then(() => {
					notyf.success("Enregistrement effectué avec succès!");
					router.push({ name: "parcs" });
				})
				.finally(() => {
					isSaving.value = false;
				});
		}
	};

	onMounted(() => {
		client({
			method: "GET",
			url: "/parks/create",
		})
			.then((response) => response.data)
			.then((response) => {
				data.value = {
					affiliate: response.affiliate,
					towns: response.towns,
					vehicle_categories: response.vehicle_categories,
					vehicle_types: response.vehicle_types,
				};
			})
			.finally(() => {
				isLoading.value = false;
			});
	});
</script>

<template>
	<VLoader :active="isLoading" size="large">
		<!-- content ... --->
		<CreateFormWrapper :col="10">
			<template #form-head-inner>
				<!-- TODO: put this header on another component -->
				<div class="left">
					<h3>Parcs</h3>
					<p>Création</p>
				</div>
				<div class="right">
					<div class="buttons">
						<VButton
							dark-outlined
							icon="fa-light fa-arrow-left rem-100"
							light
							@click="returnPreviousPage($router)"
						>
							Retour
						</VButton>
						<VButton :loading="isSaving" color="primary" raised tabindex="0" @click="submit">
							Enregistrer
						</VButton>
					</div>
				</div>
			</template>
			<template #form-body>
				<form>
					<div class="columns is-centered pt-2">
						<div class="column is-8">
							<VField horizontal label="Nom">
								<VControl fullwidth>
									<VInput v-model="name" name="name" placeholder="Nom" required type="text" />
								</VControl>
							</VField>
							<VField horizontal label="Adresse">
								<VControl fullwidth>
									<VInput
										v-model="address"
										name="address"
										placeholder="Adresse"
										required
										type="text"
									/>
								</VControl>
							</VField>
							<VField horizontal label="Description">
								<VControl fullwidth>
									<VInput
										v-model="description"
										name="description"
										placeholder="Description"
										required
										type="text"
									/>
								</VControl>
							</VField>
							<VField horizontal label="Longitude">
								<VControl fullwidth>
									<VInput
										v-model="longitude"
										name="longitude"
										placeholder="Longitude"
										required
										type="number"
									/>
								</VControl>
							</VField>
							<VField horizontal label="Latitude">
								<VControl fullwidth>
									<VInput
										v-model="latitude"
										name="latitude"
										placeholder="Latitude"
										required
										type="number"
									/>
								</VControl>
							</VField>
							<VField horizontal label="Affilié">
								<VControl fullwidth>
									<v-select
										v-model="affiliate"
										:options="data.affiliate"
										:reduce="(item) => item.id"
										label="company_name"
										placeholder="Sélectionnez l'affilié"
									></v-select>
								</VControl>
							</VField>
							<VField horizontal label="Types de véhicule">
								<VControl fullwidth>
									<v-select
										multiple
										v-model="vehicle_types"
										:options="data.vehicle_types"
										:reduce="(item) => item.id"
										label="label"
										placeholder="Sélectionnez le type de véhicule"
									></v-select>
								</VControl>
							</VField>
							<VField horizontal label="Categorie de véhicule">
								<VControl fullwidth>
									<v-select
										multiple
										v-model="vehicle_categories"
										:options="data.vehicle_categories"
										:reduce="(item) => item.id"
										label="label"
										placeholder="Sélectionnez la catégorie de véhicule"
									></v-select>
								</VControl>
							</VField>
							<VField horizontal label="Communes">
								<VControl fullwidth>
									<v-select
										multiple
										v-model="towns"
										:options="data.towns"
										:reduce="(item) => item.id"
										label="name"
										placeholder="Sélectionnez les communes"
									></v-select>
								</VControl>
							</VField>
						</div>
					</div>
				</form>
			</template>
		</CreateFormWrapper>
	</VLoader>
</template>
