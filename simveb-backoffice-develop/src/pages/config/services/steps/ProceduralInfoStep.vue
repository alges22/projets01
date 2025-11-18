<script setup lang="ts">
	import { storeToRefs } from "pinia";
	import { useCrudStore } from "/@src/stores/modules/crud";
	import CKE from "@ckeditor/ckeditor5-vue";
	import ClassicEditor from "@ckeditor/ckeditor5-build-classic";

	defineEmits(["next", "prev"]);

	const crudStore = useCrudStore();
	const { formLoading, errors, row: service, loading, formData } = storeToRefs(crudStore);
	const CKEditor = CKE.component;
	const editorConfig = {
		fontFamily: {
			options: ['"Montserrat", sans-serif', '"Roboto", sans-serif'],
		},
	};
</script>

<template>
	<div class="form-fieldset">
		<div class="fieldset-heading">
			<h4>Informations procédurales</h4>
			<p>Informations sur les procédures à suivre pour l'obtention du service</p>
		</div>

		<div class="columns is-multiline">
			<div class="column is-6">
				<VField label="Durée">
					<VControl fullwidth :errors="errors.duration || []">
						<VInput v-model="service.duration" name="duration" required />
					</VControl>
				</VField>
			</div>
			<div class="column is-6">
				<VField label="Qui peut faire la demande ?">
					<VControl fullwidth :errors="errors.who_can_apply || []">
						<VInput v-model="service.who_can_apply" name="who_can_apply" required />
					</VControl>
				</VField>
			</div>
			<div class="column is-6">
				<VField label="Lien">
					<VControl fullwidth :errors="errors.link || []">
						<VInput v-model="service.link" name="link" placeholder="Lien" required />
					</VControl>
				</VField>
			</div>
			<div class="column is-6">
				<VField label="Service associé">
					<VControl fullwidth :errors="errors.target_organization_id || []">
						<v-select
							v-model="service.target_organization_id"
							:options="formData?.organizations"
							label="name"
							:reduce="(item) => item.id"
						></v-select>
					</VControl>
				</VField>
			</div>
			<div v-if="!loading" class="column is-12">
				<VField label="Procédures">
					<VControl fullwidth :errors="errors.procedures || []">
						<CKEditor
							v-model="service.procedures"
							:editor="ClassicEditor"
							:config="editorConfig"
							height="600px"
						></CKEditor>
					</VControl>
				</VField>
			</div>
			<div v-if="!loading" class="column is-12">
				<VField label="Extrait">
					<VControl fullwidth :errors="errors.extract || []">
						<CKEditor
							v-model="service.extract"
							:editor="ClassicEditor"
							:config="editorConfig"
							height="300px"
						></CKEditor>
					</VControl>
				</VField>
			</div>
			<div class="column is-12">
				<VCheckbox v-model="service.is_child" label="Ce service est un service enfant" color="primary" />
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
