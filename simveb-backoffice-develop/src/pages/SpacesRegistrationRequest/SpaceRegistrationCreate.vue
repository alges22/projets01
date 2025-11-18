<script setup lang="ts">
	import { returnPreviousPage } from "/@src/utils/helpers";
	import { useNotyf } from "/@src/composable/useNotyf";
	import { useCrudStore } from "/@src/stores/modules/crud";
	import { storeToRefs } from "pinia";

	const router = useRouter();
	const notyf = useNotyf();
	const crudStore = useCrudStore();
	const { url, formLoading, row, errors, loading, formData } = storeToRefs(crudStore);

	onMounted(() => {
		crudStore.loadCreateData();
		row.value = {};
	});

	onBeforeMount(() => {
		loading.value = true;
		url.value = "/space-registration-requests";
	});

	const submit = () => {
		crudStore.createRow(row.value).then(() => {
			notyf.success("Enregistrement effectué avec succès!");

			router.push({ name: "spaces_registration_request_list" });
		});
	};
</script>

<template>
	<div class="page-content-inner">
		<VLoader :active="loading" size="large">
			<!-- content ... --->
			<CreateFormWrapper :col="12" @submit="submit">
				<template #form-head-inner>
					<div class="left">
						<h3>Enregistrement d'espace</h3>
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
							<VButton :loading="formLoading" color="primary" raised tabindex="0" type="submit">
								Enregistrer
							</VButton>
						</div>
					</div>
				</template>
				<template #form-body>
					<div class="columns is-multiline">
						<div class="column is-4">
							<VField>
								<VLabel required>Type d'espace</VLabel>
								<VControl fullwidth :errors="errors.profile_type_id || []">
									<v-select
										v-model="row.profile_type_id"
										:options="formData.profile_types"
										:reduce="(item) => item.id"
										label="name"
									></v-select>
								</VControl>
							</VField>
						</div>
						<div class="column is-4">
							<VField>
								<VLabel required>Institution</VLabel>
								<VControl fullwidth :errors="errors.institution_id || []">
									<v-select
										v-model="row.institution_id"
										:options="formData.institutions"
										:reduce="(item) => item.id"
										label="name"
									></v-select>
								</VControl>
							</VField>
						</div>

						<div class="column is-4">
							<VField>
								<VLabel required>NPI de l'administrateur</VLabel>
								<VControl fullwidth :errors="errors.name || []">
									<VInput
										v-model="row.first_member_npi"
										name="first_member_npi"
										placeholder="NPI de l'administrateur"
										required
										type="text"
									/>
								</VControl>
							</VField>
						</div>
					</div>
				</template>
			</CreateFormWrapper>
		</VLoader>
	</div>
</template>

<style scoped lang="scss"></style>
