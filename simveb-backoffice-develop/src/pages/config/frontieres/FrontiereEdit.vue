<script setup lang="ts">
	import { storeToRefs } from "pinia";
	import { returnPreviousPage } from "/@src/utils/helpers";
	import { useCrudStore } from "/@src/stores/modules/crud";
	import { useNotyf } from "/@src/composable/useNotyf";
	import client from "/@src/composable/axiosClient";

	const name = ref("");
	const longitude = ref("");
	const latitude = ref("");
	const country = ref("");
	const town = ref("");

	const router = useRouter();
	const notyf = useNotyf();
	const crudStore = useCrudStore();
	const { url } = storeToRefs(crudStore);
	const route = useRoute();

	const isLoading = ref(true);
	const isSaving = ref(false);

	const data = ref({
		countries: {},
		towns: {},
	});

	const itemId = route.params.id;

	onBeforeMount(() => {
		url.value = "/borders";
	});

	onMounted(() => {
		crudStore.fetchRow(itemId).then((res) => {
			name.value = res.name;
			longitude.value = res.longitude;
			latitude.value = res.latitude;
			country.value = res.border_country_id;
			town.value = res.town_id;
		});

		client({
			method: "GET",
			url: "/borders/create",
		})
			.then((response) => response.data)
			.then((response) => {
				data.value = {
					countries: response.countries,
					towns: response.towns,
				};
			})
			.finally(() => {
				isLoading.value = false;
			});
	});

	const submit = () => {
		crudStore
			.updateRow(itemId, {
				name: name.value,
				longitude: longitude.value,
				latitude: latitude.value,
				border_country_id: country.value,
				town_id: town.value,
			})
			.then(() => {
				notyf.success("Modification effectué avec succès!");
				router.push({ name: "frontieres" });
			})
			.finally(() => {
				isSaving.value = false;
			});
	};
</script>

<template>
	<VLoader :active="isLoading" size="large">
		<!-- content ... --->
		<CreateFormWrapper :col="10">
			<template #form-head-inner>
				<!-- TODO: put this header on another component -->
				<div class="left">
					<h3>Frontières</h3>
					<p>Modification</p>
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
							<VField horizontal label="Pays frontalier">
								<VControl fullwidth>
									<v-select
										v-model="country"
										:options="data.countries"
										:reduce="(item) => item.id"
										label="name"
										placeholder="Sélectionnez le pays"
									></v-select>
								</VControl>
							</VField>
							<VField horizontal label="Villes">
								<VControl fullwidth>
									<v-select
										v-model="town"
										:options="data.towns"
										:reduce="(item) => item.id"
										label="name"
										placeholder="Sélectionnez la ville"
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

<style scoped lang="scss"></style>
