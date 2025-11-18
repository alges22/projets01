<script setup lang="ts">
	import { storeToRefs } from "pinia";
	import { returnPreviousPage } from "/@src/utils/helpers";
	import { useCrudStore } from "/@src/stores/modules/crud";
	import { useNotyf } from "/@src/composable/useNotyf";

	const route = useRoute();
	const router = useRouter();
	const notyf = useNotyf();
	const crudStore = useCrudStore();
	const { url, formLoading, errors, row } = storeToRefs(crudStore);

	const itemId = route.params.id;
	const dataLoaded = ref(true);
	// uniquePlate - plageOfPlate - serieOfPlate
	const typeOfCreation = ref("undefined");

	onBeforeMount(() => {
		url.value = "/reserved-plate-numbers";
	});

	onMounted(() => {
		if (itemId) {
			crudStore.fetchRow(itemId).then(() => {
				if (row.value.min && row.value.max) typeOfCreation.value = "plageOfPlate";
				else if (row.value.numeric_label) typeOfCreation.value = "uniquePlate";
				else typeOfCreation.value = "serieOfPlate";
			});
		} else {
			typeOfCreation.value = "uniquePlate";
		}
	});

	watch(
		typeOfCreation,
		(newValue) => {
			if (newValue == "uniquePlate") {
				row.value.min = null;
				row.value.max = null;
			} else if (newValue == "plageOfPlate") {
				row.value.numeric_label = null;
			} else {
				row.value.numeric_label = null;
				row.value.min = null;
				row.value.max = null;
			}
		},
		{
			immediate: true,
		}
	);

	const submit = () => {
		if (itemId) {
			crudStore.updateRow(itemId, row.value).then(() => {
				notyf.success("Modification effectuée avec succès!");
				router.push({ name: "reserved-plate-numbers" });
			});
		} else {
			crudStore.createRow(row.value).then(() => {
				notyf.success("Enregistrement effectué avec succès!");
				router.push({ name: "reserved-plate-numbers" });
			});
		}
	};
</script>

<template>
	<CreateFormWrapper v-if="dataLoaded" :col="10" @submit="submit">
		<template #form-head-inner>
			<div class="left">
				<h3>{{ itemId ? "Modification" : "Création" }}</h3>
			</div>
			<div class="right">
				<div class="buttons">
					<VButton
						icon="fa-light fa-arrow-left rem-100"
						light
						dark-outlined
						type="reset"
						@click="returnPreviousPage($router)"
					>
						Retour
					</VButton>
					<VButton color="primary" raised :loading="formLoading" tabindex="0" type="submit">
						Enregistrer
					</VButton>
				</div>
			</div>
		</template>
		<template #form-body>
			<div class="columns is-centered pt-2">
				<div class="column is-8">
					<VField>
						<VLabel required>Quel tpe de réservation voulez vous effectuer ?</VLabel>
					</VField>
					<div class="mb-2 inline-block mx-3">
						<input
							id="uniquePlate"
							name="uniquePlate"
							class="focus:ring-primary-500 h-4 w-4 text-primary-600 border-primary-300 me-2"
							type="radio"
							v-model="typeOfCreation"
							value="uniquePlate"
						/>
						<label for="uniquePlate">Plaque unique</label>
					</div>
					<div class="mb-2 inline-block mx-3">
						<input
							id="plageOfPlate"
							name="plageOfPlate"
							class="focus:ring-primary-500 h-4 w-4 text-primary-600 border-primary-300 me-2"
							type="radio"
							v-model="typeOfCreation"
							value="plageOfPlate"
						/>
						<label for="plageOfPlate">Plage de plaque</label>
					</div>
					<div class="mb-2 inline-block mx-3">
						<input
							id="serieOfPlate"
							name="serieOfPlate"
							class="focus:ring-primary-500 h-4 w-4 text-primary-600 border-primary-300 me-2"
							type="radio"
							v-model="typeOfCreation"
							value="serieOfPlate"
						/>
						<label for="serieOfPlate">Série de plaques</label>
					</div>
				</div>
			</div>

			<div class="columns is-centered pt-2">
				<div class="column is-8">
					<VField>
						<VLabel required>Label alphabetique</VLabel>
						<VControl fullwidth :errors="errors.alphabetic_label || []">
							<VInput v-model="row.alphabetic_label" name="alphabetic_label" required />
						</VControl>
					</VField>
				</div>
			</div>
			<div class="columns is-centered pt-2" v-if="typeOfCreation == 'uniquePlate'">
				<div class="column is-8">
					<VField>
						<VLabel :required="typeOfCreation == 'uniquePlate'">Label numérique</VLabel>
						<VControl fullwidth :errors="errors.numeric_label || []">
							<VInput
								v-model="row.numeric_label"
								name="numeric_label"
								:required="typeOfCreation == 'uniquePlate'"
							/>
						</VControl>
					</VField>
				</div>
			</div>
			<div class="columns is-centered pt-2" v-if="typeOfCreation == 'plageOfPlate'">
				<div class="column is-8">
					<VField>
						<VLabel :required="typeOfCreation == 'plageOfPlate'">Numéro minimum</VLabel>
						<VControl fullwidth :errors="errors.min || []">
							<VInput v-model="row.min" name="min" :required="typeOfCreation == 'plageOfPlate'" />
						</VControl>
					</VField>
				</div>
			</div>
			<div class="columns is-centered pt-2" v-if="typeOfCreation == 'plageOfPlate'">
				<div class="column is-8">
					<VField>
						<VLabel :required="typeOfCreation == 'plageOfPlate'">Numéro maximum</VLabel>
						<VControl fullwidth :errors="errors.max || []">
							<VInput v-model="row.max" name="max" :required="typeOfCreation == 'plageOfPlate'" />
						</VControl>
					</VField>
				</div>
			</div>
		</template>
	</CreateFormWrapper>
</template>
