<script setup lang="ts">
	import { storeToRefs } from "pinia";
	import { useCrudStore } from "/@src/stores/modules/crud";
	import { ColorPicker } from "vue-accessible-color-picker";

	defineProps({
		serviceId: {
			type: String,
			default: null,
		},
	});

	defineEmits(["next"]);

	const crudStore = useCrudStore();
	const update = ref(false);
	const { url, formLoading, errors, row: service, formData } = storeToRefs(crudStore);

	onBeforeMount(() => {
		url.value = "/services";
	});

	const handleChange = (event: Event) => {
		const files = event.target?.files;
		//console.log(files);
		if (files.length > 0) {
			service.value.image = files[0];
		}
	};

	const updateColor = (e) => {
		service.value.color = e.cssColor;
	};
</script>
<template>
	<!--Fieldset-->
	<div class="form-fieldset">
		<div class="fieldset-heading">
			<h4>Personnalisation</h4>
			<p>Personnaliser l'affichage du service</p>
		</div>

		<div class="columns is-multiline">
			<div class="column is-6">
				<VField>
					<VLabel required>Code</VLabel>
					<VControl fullwidth :errors="errors.code || []">
						<VInput v-model="service.code" name="code" required />
					</VControl>
				</VField>
			</div>
			<div v-if="!update" class="column is-6">
				<VField>
					<VLabel required>Type de service</VLabel>
					<VControl fullwidth :errors="errors.type_id || []">
						<v-select
							v-model="service.type_id"
							:options="formData?.types"
							label="name"
							:reduce="(item) => item.id"
						></v-select>
					</VControl>
				</VField>
			</div>
			<div class="column is-6">
				<VField required label="Nom">
					<VControl fullwidth :errors="errors.name || []">
						<VInput v-model="service.name" name="name" required />
					</VControl>
				</VField>
			</div>
			<div class="column is-6">
				<VField label="Coût">
					<VControl fullwidth :errors="errors.cost || []">
						<VInput v-model="service.cost" name="cost" required />
					</VControl>
				</VField>
			</div>
			<div class="column is-12">
				<VField>
					<VLabel>Description</VLabel>
					<VControl :errors="errors.description || []">
						<VTextarea
							v-model="service.description"
							name="description"
							required
							class="textarea"
							rows="4"
							autocomplete="off"
							autocapitalize="off"
							spellcheck="true"
						/>
					</VControl>
				</VField>
			</div>
			<div class="column is-12">
				<VField grouped label="Icône représentatif">
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
			<div class="column is-12">
				<div>Couleur du service</div>
				<VField>
					<VControl fullwidth :errors="errors.color || []">
						<ColorPicker
							default-format="hex"
							hue-channel="hide"
							:visible-formats="['hex']"
							:color="service.color || '#000000'"
							@color-change="updateColor"
						/>
					</VControl>
				</VField>
			</div>

			<div class="column is-12 flex justify-end">
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
