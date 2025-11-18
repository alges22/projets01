<script setup lang="ts">
	import { storeToRefs } from "pinia";
	import { returnPreviousPage } from "/@src/utils/helpers";
	import { useCrudStore } from "/@src/stores/modules/crud";
	import { useNotyf } from "/@src/composable/useNotyf";

	const route = useRoute();
	const router = useRouter();
	const notyf = useNotyf();
	const crudStore = useCrudStore();
	const { url, formLoading, errors } = storeToRefs(crudStore);

	const itemId = route.params.id;
	const item = ref({
		code: null,
		name: null,
		cost: null,
		description: null,
	});

	onBeforeMount(() => {
		url.value = "/anatt-service-types";
	});

	onMounted(() => {
		if (itemId) {
			crudStore.fetchRow(itemId).then((res) => {
				item.value = {
					code: res.code,
					name: res.name,
					cost: res.cost,
					description: res.description,
				};
			});
		}
	});

	const submit = () => {
		if (itemId) {
			crudStore.updateRow(itemId, item.value).then(() => {
				notyf.success("Modification effectuée avec succès!");
				router.push({ name: "anatt_service_types" });
			});
		} else {
			crudStore.createRow(item.value).then(() => {
				notyf.success("Enregistrement effectué avec succès!");
				router.push({ name: "anatt_service_types" });
			});
		}
	};
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
					<VField label="Code" horizontal>
						<VControl fullwidth :errors="errors.code || []">
							<VInput v-model="item.code" name="code" placeholder="Code" required />
						</VControl>
					</VField>
					<VField label="Nom" horizontal>
						<VControl fullwidth :errors="errors.name || []">
							<VInput v-model="item.name" name="name" placeholder="Nom" required />
						</VControl>
					</VField>
					<VField label="Coût" horizontal>
						<VControl fullwidth :errors="errors.cost || []">
							<VInput v-model="item.cost" name="cost" placeholder="Coût" required />
						</VControl>
					</VField>
					<VField label="Description" horizontal>
						<VControl fullwidth :errors="errors.description || []">
							<VTextarea v-model="item.description" name="description" placeholder="Description" required>
							</VTextarea>
						</VControl>
					</VField>
				</div>
			</div>
		</template>
	</CreateFormWrapper>
</template>
