<script setup lang="ts">
	import { storeToRefs } from "pinia";
	import { useCrudStore } from "/@src/stores/modules/crud";

	defineEmits(["next", "prev"]);

	const crudStore = useCrudStore();
	const { formData, row: service, formLoading } = storeToRefs(crudStore);
</script>

<template>
	<div class="form-fieldset">
		<div class="fieldset-heading m-4 is-size-4">
			<h4>Peut faire l'objet d'une demande</h4>
		</div>
		<div>
			<div class="mb-2" style="display: flex; justify-content: start; align-items: center">
				<VCheckbox
					id="can_be_demanded"
					v-model="service.can_be_demanded"
					color="primary"
					label="Ce service peut faire l'objet d'une demande"
				/>
			</div>
		</div>

		<div class="fieldset-heading m-4 is-size-4">
			<h4>Status du service</h4>
		</div>
		<div>
			<VCheckbox id="is_active" v-model="service.is_active" color="primary" label="Ce service est actif" />
		</div>

		<div class="fieldset-heading m-4 is-size-4">
			<h4>Extra services</h4>
		</div>
		<div>
			<VCheckbox
				id="has_extra"
				v-model="service.has_extra"
				color="primary"
				label="Ce service a des extra services"
			/>
		</div>

		<div v-if="service.has_extra" class="grid grid-cols-2 gap-4">
			<div v-for="(extra_service, index) in formData.services" :key="index" class="mb-2 column">
				<VCheckbox
					:id="`extra-service-${index}`"
					:label="extra_service.name"
					:value="extra_service.id"
					color="primary"
					@update:model-value="
						(val) => {
							val
								? service.extra_services.push(extra_service.id)
								: service.extra_services.splice(service.extra_services.indexOf(extra_service.id), 1);
						}
					"
				/>
			</div>
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
			<VButton color="success" raised :loading="formLoading" tabindex="0" type="button" @click="$emit('next')">
				Enregistrer
				<i class="ms-2 fa-light fa-check"></i>
			</VButton>
		</div>
	</div>
</template>

<style lang="scss">
	.form-fieldset {
		width: 60%;
		max-width: 100% !important;
	}
</style>
