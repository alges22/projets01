<template>
	<div>
		<div class="form-fieldset">
			<div class="fieldset-heading">
				<h4>Sélectionner les permissions à assigner à l'utilisateur</h4>
			</div>
			<div class="columns is-multiline">
				<div class="column is-full">
					<VField>
						<VControl fullwidth>
							<VInput
								v-model="query"
								placeholder="Tapez pour rechercher un module"
								name="searchedModule"
							/>
						</VControl>
					</VField>
				</div>
			</div>

			<div class="justify-center my-8 max-h-screen overflow-y-scroll">
				<VCollapse :items="filteredModules" with-chevron>
					<template #collapse-item-summary="{ item }">{{ item.name }}</template>
					<template #collapse-item-content="{ item }">
						<div v-for="permission in item.permissions" :key="permission.id" class="columns">
							<div class="column is-6">
								<div class="is-flex is-flex-wrap-wrap is-align-content-center">
									<VAnimatedCheckbox
										v-model="row.permissions"
										:value="permission.id"
										color="primary"
									/>
									<span class="m-auto">{{ permission.label }}</span>
								</div>
							</div>
						</div>
					</template>
				</VCollapse>
			</div>

			<div class="column is-12 flex justify-end">
				<VButton
					color="primary"
					raised
					tabindex="0"
					type="button"
					:loading="formLoading"
					@click="$emit('next')"
				>
					Enregistrer
					<i class="ms-2 fa-light fa-arrow-right"></i>
				</VButton>
			</div>
		</div>
	</div>
</template>

<script setup>
	import { storeToRefs } from "pinia";
	import { useCrudStore } from "/@src/stores/modules/crud";
	import { ref } from "vue";

	defineEmits(["next"]);

	const crudStore = useCrudStore();
	const { formData, formLoading, row } = storeToRefs(crudStore);

	const query = ref(null);

	const filteredModules = computed(() => {
		if (!formData.value?.modules) return [];
		if (!query.value) return formData.value.modules;
		return formData.value.modules.filter((module) => {
			return module.name.toLowerCase().includes(query.value.toLowerCase());
		});
	});
</script>

<style lang="css" scoped></style>
