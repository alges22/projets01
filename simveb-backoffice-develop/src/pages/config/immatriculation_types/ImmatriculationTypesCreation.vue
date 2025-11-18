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
	const item = ref({
		label: "",
		code: "",
		description: "",
		plate_colors: [],
	});

	const colors = ref([]);

	const dataLoaded = ref(false);

	onBeforeMount(() => {
		url.value = "/immatriculation-types";
	});

	onMounted(() => {
		crudStore.loadCreateData().then((res) => {
			colors.value = res.plate_colors;
			dataLoaded.value = true;
		});
		if (itemId) {
			crudStore.fetchRow(itemId).then((res) => {
				item.value = row.value;
				item.value.plate_colors = res.plate_colors.map((el) => el.id);
				dataLoaded.value = true;
			});
		}
	});

	const submit = () => {
		if (itemId) {
			crudStore.updateRow(itemId, item.value).then(() => {
				notyf.success("Modification effectuée avec succès!");
				router.push({ name: "immatriculation_types" });
			});
		} else {
			crudStore.createRow(item.value).then(() => {
				notyf.success("Enregistrement effectué avec succès!");
				router.push({ name: "immatriculation_types" });
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
						<VLabel required>Libellé</VLabel>
						<VControl fullwidth :errors="errors.label || []">
							<VInput v-model="item.label" name="label" required />
						</VControl>
					</VField>
				</div>
			</div>
			<div class="columns is-centered pt-2">
				<div class="column is-8">
					<VField>
						<VLabel required>Code</VLabel>
						<VControl fullwidth :errors="errors.code || []">
							<VInput v-model="item.code" name="code" required />
						</VControl>
					</VField>
				</div>
			</div>
			<div class="columns is-centered pt-2">
				<div class="column is-8">
					<VField>
						<VLabel required>Description</VLabel>
						<VControl fullwidth :errors="errors.description || []">
							<VInput v-model="item.description" name="description" required />
						</VControl>
					</VField>
				</div>
			</div>
			<div class="columns is-centered pt-2">
				<div class="column is-8">
					<VField>
						<VLabel required>Couleur</VLabel>
						<VControl fullwidth :errors="errors.plate_colors || []">
							<div v-for="(color, index) in colors" :key="index" class="mb-2 inline-block mx-3">
								<input
									:id="`color-${index}`"
									class="focus:ring-primary-500 h-4 w-4 text-primary-600 border-primary-300 me-2"
									type="checkbox"
									:checked="item.plate_colors.includes(color.id)"
									:value="color.id"
									@change="
										($event) => {
											$event.target.checked
												? item.plate_colors.push(color.id)
												: item.plate_colors.splice(colors.indexOf(color.id), 1);
										}
									"
								/>
								<label :for="`color-${index}`">{{ color.label }}</label>
							</div>
						</VControl>
					</VField>
				</div>
			</div>
		</template>
	</CreateFormWrapper>
</template>
