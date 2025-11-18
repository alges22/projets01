<template>
	<div class="page-content-inner">
		<VLoader :active="loading" size="large">
			<CreateFormWrapper :col="12" @submit="submit">
				<template #form-head-inner>
					<div class="left">
						<h3>{{ update ? "Modification" : "Création" }}</h3>
					</div>
					<div class="right">
						<div class="buttons">
							<VButton
								dark-outlined
								icon="fa-light fa-arrow-left rem-100"
								light
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
					<div class="form-fieldset">
						<div class="fieldset-heading">
							<h4>Personnalisation</h4>
						</div>
						<div class="columns is-multiline">
							<div class="column is-4">
								<VField>
									<VLabel required>Nom</VLabel>
									<VControl :errors="errors.name" fullwidth>
										<VInput
											v-model="institution.name"
											name="name"
											placeholder="Nom"
											required
											type="text"
										/>
									</VControl>
								</VField>
							</div>
							<div class="column is-4">
								<VField>
									<VLabel>Acronym</VLabel>
									<VControl :errors="errors.acronym || []" fullwidth>
										<VInput
											v-model="institution.acronym"
											name="acronym"
											placeholder="Acronym"
											type="text"
										/>
									</VControl>
								</VField>
							</div>
							<div class="column is-4">
								<VField>
									<VLabel>Email</VLabel>
									<VControl :errors="errors.email" fullwidth>
										<VInput
											v-model="institution.email"
											name="email"
											placeholder="Email"
											type="email"
										/>
									</VControl>
								</VField>
							</div>
							<div class="column is-4">
								<VField>
									<VLabel>Icône représentatif</VLabel>
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
							</div>
							<div class="column is-4">
								<VField>
									<VLabel>IFU</VLabel>
									<VControl :errors="errors.ifu" fullwidth>
										<VInput v-model="institution.ifu" name="ifu" placeholder="IFU" type="text" />
									</VControl>
								</VField>
							</div>
							<div class="column is-4">
								<VField>
									<VLabel>Téléphone</VLabel>
									<VControl :errors="errors.telephone" fullwidth>
										<VInput
											v-model="institution.telephone"
											name="telephone"
											placeholder="Téléphone"
											type="text"
										/>
									</VControl>
								</VField>
							</div>
						</div>
					</div>

					<div class="is-divider" />

					<div class="form-fieldset">
						<div class="fieldset-heading">
							<h4>Adresse</h4>
						</div>

						<div class="columns is-multiline">
							<div class="column is-6">
								<VField>
									<VLabel required>Commune</VLabel>
									<VControl :errors="errors.town_id" fullwidth>
										<v-select
											v-model="institution.town_id"
											:options="formData.towns"
											:reduce="(item) => item.id"
											label="name"
											placeholder="Sélectionnez la commune"
										></v-select>
									</VControl>
								</VField>
							</div>
							<div class="column is-6">
								<VField>
									<VLabel>Arrondissement</VLabel>
									<VControl :errors="errors.district_id" fullwidth>
										<v-select
											v-model="institution.district_id"
											:options="formData.districts"
											:reduce="(item) => item.id"
											label="name"
											placeholder="Sélectionnez l'arrondissement"
										></v-select>
									</VControl>
								</VField>
							</div>
							<div class="column is-6">
								<VField>
									<VLabel>Quartier ou Village</VLabel>
									<VControl :errors="errors.village_id" fullwidth>
										<v-select
											v-model="institution.village_id"
											:options="formData.villages"
											:reduce="(item) => item.id"
											label="name"
											placeholder="Sélectionnez le quartier ou village"
										></v-select>
									</VControl>
								</VField>
							</div>
							<div class="column is-6">
								<VField>
									<VLabel required>Type d'institution financière</VLabel>
									<VControl :errors="errors.type_id" fullwidth>
										<v-select
											v-model="institution.type_id"
											:options="formData.types"
											:reduce="(item) => item.id"
											label="description"
											placeholder="Sélectionnez type d'institution financière"
										></v-select>
									</VControl>
								</VField>
							</div>
							<div class="column is-6">
								<VField>
									<VLabel>Adresse</VLabel>
									<VControl :errors="errors.address" fullwidth>
										<VInput
											v-model="institution.address"
											name="address"
											placeholder="Addresse"
											type="text"
										/>
									</VControl>
								</VField>
							</div>
							<div class="column is-6">
								<VField>
									<VLabel>Frontières</VLabel>
									<VControl :errors="errors.border_id" fullwidth>
										<v-select
											v-model="institution.border_id"
											:options="formData.borders"
											:reduce="(item) => item.id"
											label="name"
											placeholder="Sélectionnez la frontière"
										></v-select>
									</VControl>
								</VField>
							</div>
						</div>
					</div>
				</template>
			</CreateFormWrapper>
		</VLoader>
	</div>
</template>

<script setup lang="ts">
	import { useNotyf } from "/@src/composable/useNotyf";
	import { useCrudStore } from "/@src/stores/modules/crud";
	import { storeToRefs } from "pinia";
	import { returnPreviousPage } from "/@src/utils/helpers";

	const props = defineProps({
		id: {
			type: String,
			default: null,
		},
	});

	const router = useRouter();
	const notyf = useNotyf();
	const crudStore = useCrudStore();
	const update = ref(false);
	const { url, formLoading, errors, row: institution, loading, formData } = storeToRefs(crudStore);

	onBeforeMount(() => {
		loading.value = true;
		url.value = "/institutions";
	});

	const handleChange = (event: Event) => {
		const files = event.target?.files;
		// console.log(files);
		if (files.length > 0) {
			institution.value.logo = files[0];
		}
	};

	const submit = () => {
		if (update.value) {
			crudStore.updateWithFile(institution.value.id, institution.value).then(() => {
				notyf.success("L'institution a bien été modifié");
				router.push({ name: "institutions" });
			});
		} else {
			crudStore.createWithFile(institution.value).then(() => {
				notyf.success("L'institution a bien été crée");
				router.push({ name: "institutions" });
			});
		}
	};

	onMounted(async () => {
		await crudStore.loadCreateData().then(() => {});
		if (props.id) {
			update.value = true;
			await crudStore.fetchRow(props.id).then((res) => {});
		} else {
			institution.value = {};
		}
	});
</script>

<style scoped lang="scss"></style>
