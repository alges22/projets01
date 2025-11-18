<template>
	<div>
		<div class="form-fieldset">
			<div class="fieldset-heading">
				<h4>Sélectionner les rôles à accréditer dans la liste</h4>
			</div>
			<div class="columns is-multiline">
				<div class="column is-full">
					<VField>
						<VControl fullwidth>
							<VInput v-model="query" placeholder="Tapez pour rechercher un role" name="searchedRole" />
						</VControl>
					</VField>
				</div>
			</div>

			<div class="justify-center">
				<div class="card-content">
					<TransitionGroup name="list" tag="div" appear>
						<div v-for="(role, index) in filteredRoles" :key="index" class="mb-2 flex">
							<VField fullwidth horizontal>
								<VAnimatedCheckbox
									v-model="row.roles"
									:value="role.id"
									color="primary"
									class="mr-5"
									name="roles"
								/>
								<VLabel class="!text-lg !font-bold text-dark">{{ role.label }}</VLabel>
							</VField>
						</div>
					</TransitionGroup>
				</div>
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
					Suivant
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

	const filteredRoles = computed(() => {
		if (!formData.value?.roles) return [];
		if (!query.value) return formData.value.roles;
		return formData.value.roles.filter((role) => {
			return role.label.toLowerCase().includes(query.value.toLowerCase());
		});
	});
</script>

<style lang="css" scoped></style>
