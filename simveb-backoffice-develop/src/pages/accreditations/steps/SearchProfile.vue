<template>
	<div>
		<div class="form-fieldset">
			<div class="fieldset-heading">
				<h4>Veuillez recherchez le profile que vous souhaitez accréditer.</h4>
			</div>

			<div class="flex">
				<div class="column is-6">
					<VField>
						<VLabel required>Entrer le NPI du profile</VLabel>
						<VControl fullwidth>
							<VInput v-model="row.npi" name="npi" required />
						</VControl>
					</VField>
				</div>
				<VButton
					class="mb-3 mt-auto"
					color="primary"
					raised
					tabindex="0"
					type="button"
					:loading="formLoading"
					@click="handleSearch"
				>
					Rechercher
				</VButton>
			</div>
			<hr />
			<div class="mt-3">
				<div class="p-4">
					<div v-if="identity" class="fieldset-heading">
						<h4>Nom de l'utilisateur : {{ identity.fullName }}</h4>
					</div>
					<form v-if="formData?.profiles" @submit.prevent="submit">
						<div class="fieldset-heading">
							<h4>Cliquez pour sélectionner le type de profil à associer</h4>
						</div>
						<div class="column is-12">
							<VField v-if="formData.profiles.length > 0">
								<VControl>
									<div class="radio-pills grid grid-cols-1 lg:grid-cols-2 gap-1">
										<div
											v-for="(profile, index) in formData.profiles"
											:key="index"
											class="radio-pill my-2"
										>
											<input
												v-model="selectedProfile"
												type="radio"
												name="profile_type_id"
												:value="profile"
											/>
											<div class="radio-pill-inner mx-5 !w-full">
												<span class="mx-2 text-center text-base">
													{{ profile.type.name }}
													<template v-if="profile.institution">
														- {{ profile.institution.name }}
													</template>
												</span>
											</div>
										</div>
									</div>
								</VControl>
							</VField>
							<div v-else>
								<p class="has-text-centered">Aucun profile trouvé</p>
							</div>
						</div>

						<div class="column is-12 flex justify-end">
							<VButton
								v-if="formData?.profiles?.length > 0"
								color="primary"
								raised
								tabindex="0"
								:loading="formLoading"
								type="submit"
							>
								Suivant
								<i class="ms-2 fa-light fa-arrow-right"></i>
							</VButton>
						</div>
					</form>
				</div>
			</div>
		</div>
	</div>
</template>

<script setup>
	import { storeToRefs } from "pinia";
	import { useCrudStore } from "/@src/stores/modules/crud";

	const crudStore = useCrudStore();
	const { formLoading, formData, row } = storeToRefs(crudStore);
	const emit = defineEmits(["next"]);
	const identity = ref(null);
	const selectedProfile = ref(null);

	const handleSearch = () => {
		formLoading.value = true;
		crudStore
			.makeRequest("GET", null, "/accreditations/user/search?npi=" + row.value.npi)
			.then((response) => {
				formData.value.profiles = response.profiles;
				identity.value = response.identity;
				formLoading.value = false;
			})
			.finally(() => {
				formLoading.value = false;
			});
	};

	const submit = () => {
		row.value.profile_id = selectedProfile.value.id;
		row.value.receiver_id = selectedProfile.value.id;
		formLoading.value = true;
		crudStore
			.makeRequest("GET", null, `/accreditations/create?profile_id=${row.value.profile_id}`)
			.then((response) => {
				formData.value.roles = response.roles;
				formData.value.modules = response.modules;
				formLoading.value = false;
				emit("next");
			})
			.finally(() => {
				formLoading.value = false;
			});
	};
</script>

<style lang="scss" scoped></style>
