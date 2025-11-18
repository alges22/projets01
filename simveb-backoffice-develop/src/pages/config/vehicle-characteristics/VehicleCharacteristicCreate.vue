<script setup lang="ts">
	import { storeToRefs } from "pinia";
	import { returnPreviousPage } from "/@src/utils/helpers";
	import { useCrudStore } from "/@src/stores/modules/crud";
	import { useNotyf } from "/@src/composable/useNotyf";
	import { ref, watch } from "vue";

	const route = useRoute();
	const router = useRouter();
	const notyf = useNotyf();
	const crudStore = useCrudStore();
	const { url, formLoading, errors } = storeToRefs(crudStore);

	const itemId = route.params.id;
	const item = ref({
		value: null,
		min_value: null,
		max_value: null,
		cost: null,
	});
	const categoryId = ref(null);

	const categories = ref([]);
	const selectedType = ref(null);

	onBeforeMount(() => {
		url.value = "/vehicle-characteristics";
	});

	const mountSelectedType = () => {
		Object.entries(categories.value).forEach(([key, value]) => {
			if (value.id == categoryId.value) {
				selectedType.value = value.type;
			}
		});
	};

	onMounted(() => {
		if (itemId) {
			crudStore.fetchRow(itemId).then((res) => {
				item.value = {
					value: res.value,
					min_value: res.min_value,
					max_value: res.max_value,
					cost: res.cost,
				};
				categoryId.value = res.category_id;
				mountSelectedType();
			});
		}
		crudStore.loadCreateData().then((res) => {
			categories.value = res.categories;
			mountSelectedType();
		});
	});

	const submit = () => {
		item.value.category_id = categoryId.value;
		if (selectedType.value == null) {
			item.value.value = null;
			item.value.min_value = null;
			item.value.max_value = null;
		} else if (selectedType.value == "string") {
			item.value.min_value = null;
			item.value.max_value = null;
		} else {
			item.value.value = null;
		}

		if (itemId) {
			crudStore.updateRow(itemId, item.value).then(() => {
				notyf.success("Modification effectuée avec succès!");
				router.push({ name: "vehicle_characteristics" });
			});
		} else {
			crudStore.createRow(item.value).then(() => {
				notyf.success("Enregistrement effectué avec succès!");
				router.push({ name: "vehicle_characteristics" });
			});
		}
	};

	watch(categoryId, (newVal) => {
		if (newVal == null) {
			selectedType.value = null;
		} else {
			Object.entries(categories.value).forEach(([key, value]) => {
				if (value.id == newVal) {
					selectedType.value = value.type;
				}
			});
		}
	});
</script>

<template>
	<CreateFormWrapper :col="10" @submit="submit">
		<template #form-head-inner>
			<div class="left">
				<h3>{{ itemId ? "Modification" : "Création" }}</h3>
			</div>
			<div class="right">
				<div class="buttons">
					<VButton
						icon="fa-light fa-arrow-left rem-100"
						light
						type="reset"
						dark-outlined
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
			<div class="columns is-centered pt-2 is-multiline">
				<div class="column is-8">
					<VField label="Catégorie de caractéristique" horizontal>
						<VControl fullwidth>
							<v-select
								v-model="categoryId"
								:options="categories"
								label="label"
								name="id"
								:reduce="(item) => item.id"
							></v-select>
						</VControl>
					</VField>
				</div>
				<div v-if="selectedType == 'string'" class="column is-8">
					<VField label="Valeur" horizontal>
						<VControl fullwidth :errors="errors.value || []">
							<VInput v-model="item.value" name="value" placeholder="Valeur" required />
						</VControl>
					</VField>
				</div>
				<div v-else-if="selectedType == 'interval'" class="column is-8">
					<VField label="Minimum" horizontal>
						<VControl fullwidth :errors="errors.min_value || []">
							<VInput
								v-model="item.min_value"
								name="min_value"
								placeholder="Minimum"
								type="number"
								min="1"
								required
							/>
						</VControl>
					</VField>
					<VField label="Maximum" horizontal>
						<VControl fullwidth :errors="errors.max_value || []">
							<VInput
								v-model="item.max_value"
								name="max_value"
								placeholder="Maximum"
								type="number"
								min="1"
								required
							/>
						</VControl>
					</VField>
				</div>
				<div class="column is-8">
					<VField label="Coût" horizontal>
						<VControl fullwidth :errors="errors.cost || []">
							<VInput v-model="item.cost" name="cost" placeholder="Coût en CFA" required />
						</VControl>
					</VField>
				</div>
			</div>
		</template>
	</CreateFormWrapper>
</template>
