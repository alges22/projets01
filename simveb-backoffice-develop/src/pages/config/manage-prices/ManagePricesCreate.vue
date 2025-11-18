<script lang="ts" setup>
	import { useViewWrapper } from "/@src/stores/viewWrapper";
	import { userHasPermissions } from "/@src/utils/permission";
	import { useHead } from "@vueuse/head";
	import { returnPreviousPage } from "/@src/utils/helpers";
	import client from "/@src/composable/axiosClient";
	import { ref } from "vue";
	import { storeToRefs } from "pinia";
	import { useCrudStore } from "/@src/stores/modules/crud";
	import { useNotyf } from "/@src/composable/useNotyf";

	const viewWrapper = useViewWrapper();
	viewWrapper.setPageTitle("Gestion des prix des services");
	const { can } = userHasPermissions();

	useHead({
		title: "Gestion des prix des services - Simveb",
	});

	const crudStore = useCrudStore();
	const { url } = storeToRefs(crudStore);
	const notyf = useNotyf();

	const isLoading = ref(true);
	const isSaving = ref(false);

	const data = ref({
		characteristics: [],
		vehicle_types: [],
		owner_types: [],
		vehicle_categories: [],
		services: [],
		categories: [],
	});

	const selected = ref({
		selectedService: 0,
		selectedVehiculeCategory: 0,
		selectedVehiculeType: 0,
		selectedVehiculeCaracteristic: 0,
		selectedOwnerType: 0,
		selectedCategory: null,
		price: 0,
	});

	onBeforeMount(() => {
		url.value = "/prices";
	});

	onMounted(() => {
		client({
			method: "GET",
			url: "/prices/create",
		})
			.then((response) => response.data)
			.then((response) => {
				data.value = {
					characteristics: response.characteristics,
					vehicle_types: response.vehicle_types,
					owner_types: response.owner_types,
					vehicle_categories: response.vehicle_categories,
					services: response.services,
					categories: response.categories,
				};
			})
			.finally(() => {
				isLoading.value = false;
			});
	});

	function filterCaracteristics(selectedCategory) {
		return data.value.characteristics.filter((element) => element.category.id === selectedCategory);
	}

	const submit = () => {
		if (!isSaving.value) {
			isSaving.value = true;

			crudStore
				.createRow({
					service_id: selected.value.selectedService,
					characteristic_id: selected.value.selectedVehiculeCaracteristic,
					owner_type_id: selected.value.selectedOwnerType,
					vehicle_type_id: selected.value.selectedVehiculeType,
					vehicle_category_id: selected.value.selectedVehiculeCategory,
					price: selected.value.price,
				})
				.then(() => {
					notyf.success("Enregistrement effectué avec succès!");

					// router.push({ name: "parcs" });
				})
				.finally(() => {
					isSaving.value = false;
				});
		}
	};
</script>

<template>
	<CreateFormWrapper :col="12">
		<template #form-head-inner>
			<!-- TODO: put this header on another component -->
			<div class="left">
				<h3>Gestion des prix des services</h3>
				<p>Mise en place</p>
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
				<div>
					<span class="has-text-weight-bold">Services</span>
				</div>

				<VButtons class="mt-2">
					<template v-for="service in data.services">
						<VAction
							:active="selected.selectedService === service.id"
							@click="selected.selectedService = service.id"
							type="button"
						>
							{{ service.name }}
						</VAction>
					</template>
				</VButtons>

				<hr />

				<div>
					<span class="has-text-weight-medium">Catégorie de véhicule</span>
				</div>

				<VButtons class="mt-2">
					<template v-for="category in data.vehicle_categories">
						<VAction
							:active="selected.selectedVehiculeCategory === category.id"
							@click="selected.selectedVehiculeCategory = category.id"
							type="button"
						>
							{{ category.label }}
						</VAction>
					</template>
				</VButtons>

				<hr />

				<div>
					<span class="has-text-weight-medium">Type de véhicule</span>
				</div>

				<VButtons class="mt-2">
					<template v-for="type in data.vehicle_types">
						<VAction
							:active="selected.selectedVehiculeType === type.id"
							@click="selected.selectedVehiculeType = type.id"
							type="button"
						>
							{{ type.label }}
						</VAction>
					</template>
				</VButtons>

				<hr />

				<div>
					<span class="has-text-weight-medium">Caractéristiques du véhicule</span>
				</div>

				<VControl fullwidth>
					<v-select
						v-model="selected.selectedCategory"
						:options="data.categories"
						:reduce="(item) => item.id"
						label="label"
						placeholder="Sélectionnez le type de caractéridtique"
					></v-select>
				</VControl>

				<div class="mt-4"></div>

				<VButtons class="mt-2">
					<template v-for="characteristic in filterCaracteristics(selected.selectedCategory)">
						<VAction
							:active="selected.selectedVehiculeCaracteristic === characteristic.id"
							@click="selected.selectedVehiculeCaracteristic = characteristic.id"
							type="button"
						>
							{{ characteristic.value }}
						</VAction>
					</template>
				</VButtons>

				<hr />

				<div>
					<span class="has-text-weight-medium">Type de client</span>
				</div>

				<VButtons class="mt-2">
					<template v-for="owner in data.owner_types">
						<VAction
							:active="selected.selectedOwnerType === owner.id"
							@click="selected.selectedOwnerType = owner.id"
							type="button"
						>
							{{ owner.label }}
						</VAction>
					</template>
				</VButtons>

				<hr />

				<VField horizontal label="Prix du service">
					<VControl fullwidth>
						<VInput
							v-model="selected.price"
							name="prix"
							placeholder="Prix du service"
							required
							type="text"
						/>
					</VControl>
				</VField>
			</form>
		</template>
	</CreateFormWrapper>
</template>

<style lang="scss" scoped></style>
