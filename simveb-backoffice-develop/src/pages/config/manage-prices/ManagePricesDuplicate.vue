<script lang="ts" setup>
	import { useViewWrapper } from "/@src/stores/viewWrapper";
	import { userHasPermissions } from "/@src/utils/permission";
	import { useHead } from "@vueuse/head";
	import { returnPreviousPage } from "/@src/utils/helpers";
	import client from "/@src/composable/axiosClient";
	import { ref } from "vue";

	const viewWrapper = useViewWrapper();
	viewWrapper.setPageTitle("Gestion des prix des services");
	const { can } = userHasPermissions();

	useHead({
		title: "Gestion des prix des services - Simveb",
	});

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

	// const variables = ['Services', 'Catégorie de véhicule', 'Type de véhicule', 'Caractéristiques du véhicule', 'Type de client']
	const variables = [
		{
			label: "Services",
			data: data.value.services,
		},
		{
			label: "Catégorie de véhicule",
			data: data.value.vehicle_categories,
		},
		{
			label: "Type de véhicule",
			data: data.value.vehicle_types,
		},
		{
			label: "Caractéristiques du véhicule",
			data: data.value.characteristics,
		},
		{
			label: "Type de client",
			data: data.value.owner_types,
		},
	];

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

	function submit() {}
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
				<template v-for="variable in variables">
					<div>
						<span class="has-text-weight-bold">{{ variable }}</span>
					</div>

					<VButtons class="mt-2">
						<template v-for="service in data.services">
							<VAction @click="" type="button"> {{ service.name }} </VAction>
						</template>
					</VButtons>
				</template>

				<!--				<hr />-->

				<!--				<div>-->
				<!--					<span class="has-text-weight-medium">Catégorie de véhicule</span>-->
				<!--				</div>-->

				<!--                <VButtons class="mt-2">-->
				<!--                    <template v-for="category in data.vehicle_categories">-->
				<!--                        <VAction type="button"> {{ category.label }} </VAction>-->
				<!--                    </template>-->
				<!--                </VButtons>-->

				<!--				<hr />-->

				<!--				<div>-->
				<!--					<span class="has-text-weight-medium">Type de véhicule</span>-->
				<!--				</div>-->

				<!--                <VButtons class="mt-2">-->
				<!--                    <template v-for="type in data.vehicle_types">-->
				<!--                        <VAction type="button"> {{ type.label }} </VAction>-->
				<!--                    </template>-->
				<!--                </VButtons>-->

				<!--				<hr />-->

				<!--				<div>-->
				<!--					<span class="has-text-weight-medium">Caractéristiques du véhicule</span>-->
				<!--				</div>-->

				<!--                <VControl fullwidth>-->
				<!--                    <v-select-->
				<!--                        :options="data.categories"-->
				<!--                        :reduce="(item) => item.id"-->
				<!--                        label="label"-->
				<!--                        placeholder="Sélectionnez le type de caractéridtique"-->
				<!--                    ></v-select>-->
				<!--                </VControl>-->

				<!--                <VButtons class="mt-2">-->
				<!--                    <template v-for="characteristic in data.characteristics">-->
				<!--                        <VAction type="button"> {{ characteristic.value }} </VAction>-->
				<!--                    </template>-->
				<!--                </VButtons>-->

				<!--				<hr />-->

				<!--				<div>-->
				<!--					<span class="has-text-weight-medium">Type de client</span>-->
				<!--				</div>-->

				<!--                <VButtons class="mt-2">-->
				<!--                    <template v-for="owner in data.owner_types">-->
				<!--                        <VAction type="button"> {{ owner.label }} </VAction>-->
				<!--                    </template>-->
				<!--                </VButtons>-->

				<VField horizontal label="Prix du service">
					<VControl fullwidth>
						<VInput name="prix" placeholder="Prix du service" required type="text" />
					</VControl>
				</VField>
			</form>
		</template>
	</CreateFormWrapper>
</template>

<style lang="scss" scoped></style>
