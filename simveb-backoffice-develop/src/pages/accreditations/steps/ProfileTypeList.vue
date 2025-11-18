<template>
	<div>
		<div class="form-fieldset">
			<div class="fieldset-heading">
				<h4>Sélectionner des profiles dans la liste</h4>
			</div>
			<div class="columns is-multiline">
				<div class="column is-full">
					<VField>
						<VControl fullwidth>
							<VInput
								v-model="query"
								placeholder="Tapez pour rechercher un profile"
								name="searchedProfile"
							/>
						</VControl>
					</VField>
				</div>
			</div>

			<div v-if="formDataLoaded" class="justify-center">
				<div class="card-content">
					<TransitionGroup name="list" tag="div" appear>
						<div v-for="(profile, index) in filteredProfiles" :key="index" class="mb-2 flex">
							<VField fullwidth horizontal>
								<VAnimatedCheckbox
									v-model="row.profile_types"
									:value="profile.id"
									color="primary"
									class="mr-5"
									name="profile_types"
								/>
								<VLabel class="!text-lg !font-bold text-dark">{{ profile.name }}</VLabel>
							</VField>
						</div>
					</TransitionGroup>
				</div>
			</div>

			<div class="column is-12 flex justify-end">
				<VButton
					v-if="row.profile_types?.length > 0"
					color="primary"
					raised
					tabindex="0"
					type="button"
					:loading="formLoading"
					@click="$emit('next')"
				>
					Suivant
					<i class="ms-2 fa-light fa-arrow-right"></i>
				</VButton>
			</div>
		</div>
	</div>
</template>

<script setup>
	import { storeToRefs } from "pinia";
	import { ref } from "vue";
	import { useCrudStore } from "/@src/stores/modules/crud";

	defineEmits(["next"]);

	const crudStore = useCrudStore();
	const { formData, formLoading, row, formDataLoaded } = storeToRefs(crudStore);

	const query = ref(null);

	const filteredProfiles = computed(() => {
		if (!formData.value?.profile_types) return [];
		if (!query.value) return formData.value.profile_types;
		return formData.value.profile_types.filter((profile) => {
			return profile.name.toLowerCase().includes(query.value.toLowerCase());
		});
	});
</script>

<style lang="css" scoped></style>
