<script setup lang="ts">
	import { storeToRefs } from "pinia";
	import { useCrudStore } from "/@src/stores/modules/crud";

	defineEmits(["next", "prev"]);

	const crudStore = useCrudStore();
	const { url, formLoading, errors, row: service, formData } = storeToRefs(crudStore);
	const hasChildService = ref(false);

	onBeforeMount(() => {
		url.value = "/services";
	});

	onMounted(() => {
		if (service?.value.children != undefined) {
			hasChildService.value = service?.value.children.length > 0;
		} else {
			hasChildService.value = false;
		}
	});
</script>

<template>
	<div class="form-fieldset">
		<div class="fieldset-heading">
			<h4>Informations relationnelles</h4>
		</div>

		<div class="columns is-multiline">
			<div class="column is-12">
				<div class="relative flex items-start">
					<div class="flex h-5 items-center">
						<input
							id="has_child"
							:checked="hasChildService"
							aria-describedby="has-child-description"
							name="has-child"
							type="checkbox"
							class="h-5 w-5 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500"
							@change="($event) => (hasChildService = $event.target.checked)"
						/>
					</div>
					<div class="ml-3 text-xl">
						<label for="has_child" class="font-medium text-gray-700">Service enfants</label>
					</div>
				</div>

				<div v-if="hasChildService" class="column is-12">
					<div class="grid grid-cols-2 gap-4">
						<div v-for="(child, index) in formData?.services" :key="index" class="mb-2">
							<div class="relative flex items-start">
								<div class="flex h-5 items-center">
									<input
										:id="`child-service-${index}`"
										aria-describedby="comments-description"
										name="child_service"
										type="checkbox"
										:checked="service.children.includes(child.id)"
										class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500"
										@change="
											($event) => {
												$event.target.checked
													? service.children.push(child.id)
													: service.children.splice(service.children.indexOf(child.id), 1);
											}
										"
									/>
								</div>
								<div class="ml-3 text-lg">
									<label :for="`child-service-${index}`" class="font-medium text-gray-700">
										{{ child.name }}
									</label>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>

			<div class="column is-12">
				<VField label="Catégorie de véhicule">
					<VControl fullwidth :errors="errors.vehicle_category_id || []">
						<div class="radio-pills">
							<div v-for="(category, index) in formData.categories" :key="index" class="radio-pill">
								<input
									v-model="service.category_id"
									type="radio"
									name="category_type_id"
									:value="category.id"
								/>
								<div class="radio-pill-inner">
									<span>{{ category.label }}</span>
								</div>
							</div>
						</div>
					</VControl>
				</VField>
			</div>
			<div class="column is-12">
				<VField label="Documents">
					<div v-if="formData?.documents" class="column is-12">
						<div class="grid grid-cols-2 gap-4">
							<div v-for="(document, index) in formData?.documents" :key="index" class="mb-2 column">
								<input
									:id="`document-${index}`"
									class="focus:ring-primary-500 h-4 w-4 text-primary-600 border-primary-300 me-2"
									type="checkbox"
									:checked="formData?.documents.includes(document.id)"
									:value="document.id"
									@change="
										($event) => {
											$event.target.checked
												? service.documents.push(document.id)
												: service.documents.splice(service.documents.indexOf(document.id), 1);
										}
									"
								/>
								<label :for="`document-${index}`">{{ document.description }}</label>
							</div>
						</div>
					</div>
				</VField>
			</div>

			<div class="column is-12 flex justify-between">
				<VButton
					color="primary"
					raised
					:loading="formLoading"
					tabindex="0"
					type="button"
					icon="fa-light fa-arrow-left"
					@click="$emit('prev')"
				>
					Précedent
				</VButton>
				<VButton
					color="success"
					raised
					:loading="formLoading"
					tabindex="0"
					type="button"
					@click="$emit('next')"
				>
					Suivant
					<i class="ms-2 fa-light fa-arrow-right"></i>
				</VButton>
			</div>
		</div>
	</div>
</template>

<style lang="scss">
	.form-fieldset {
		width: 60%;
		max-width: 100% !important;
	}
</style>
