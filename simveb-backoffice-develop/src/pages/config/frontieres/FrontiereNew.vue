<script setup lang="ts">
	import client from "/@src/composable/axiosClient";
	import { ref } from "vue";
	import { useNotyf } from "/@src/composable/useNotyf";
	import { useCrudStore } from "/@src/stores/modules/crud";
	import { storeToRefs } from "pinia";
	import { returnPreviousPage } from "/@src/utils/helpers";

	const name = ref("");
	const longitude = ref("");
	const latitude = ref("");
	const country = ref("");
	const town = ref("");

	const router = useRouter();
	const notyf = useNotyf();
	const crudStore = useCrudStore();
	const { url } = storeToRefs(crudStore);
	const isLoading = ref(true);
	const isSaving = ref(false);

	const data = ref({
		countries: {},
		towns: {},
	});

	onBeforeMount(() => {
		url.value = "/borders";
	});

	const submit = () => {
		if (!isSaving.value) {
			isSaving.value = true;

			crudStore
				.createRow({
					name: name.value,
					longitude: longitude.value,
					latitude: latitude.value,
					border_country_id: country.value,
					town_id: town.value,
				})
				.then(() => {
					notyf.success("Enregistrement effectué avec succès!");
					router.push({ name: "frontieres" });
				})
				.finally(() => {
					isSaving.value = false;
				});
		}
	};

	onMounted(() => {
		client({
			method: "GET",
			url: "/borders/create",
		})
			.then((response) => response.data)
			.then((response) => {
				data.value = {
					towns: response.towns,
					countries: response.countries,
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
					<h3>Frontières</h3>
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
