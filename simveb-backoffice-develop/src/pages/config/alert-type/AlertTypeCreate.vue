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
		name: null,
		description: null,
		code: null,
	});

	onBeforeMount(() => {
		url.value = "/alert-types";
	});

	onMounted(() => {
		if (itemId) {
			crudStore.fetchRow(itemId).then((res) => {
				item.value = {
					name: res.name,
					description: res.description,
					code: res.code,
				};
			});
		}
	});

	const handleChange = (event: Event) => {
		const files = event.target?.files;

		if (files.length > 0) {
			item.value.image = files[0];
		}
	};

	const submit = () => {
		if (itemId) {
			crudStore.updateWithFile(itemId, item.value).then(() => {
				notyf.success("Modification effectuée avec succès!");
				router.push({ name: "alert_types" });
			});
		} else {
			crudStore.createWithFile(item.value).then(() => {
				notyf.success("Enregistrement effectué avec succès!");
				router.push({ name: "alert_types" });
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
							<VInput v-model="item.name" name="nom" placeholder="Nom" required />
						</VControl>
					</VField>

					<VField label="Icône représentatif" horizontal>
						<VControl :errors="errors.image || []">
							<div class="file">
								<label class="file-label">
									<input
										class="file-input"
										type="file"
										name="image"
										accept="image/*"
										@change="handleChange($event)"
									/>
									<span class="file-cta">
										<span class="file-icon">
											<i class="fa-light fa-cloud-upload-alt" />
										</span>
										<span class="file-label"> Choisir un fichier… </span>
									</span>
								</label>
							</div>
						</VControl>
					</VField>

					<VField label="Description" horizontal>
						<VControl fullwidth :errors="errors.description || []">
							<VInput v-model="item.description" name="description" placeholder="Description" />
						</VControl>
					</VField>
				</div>
			</div>
		</template>
	</CreateFormWrapper>
</template>

<style scoped lang="scss"></style>
