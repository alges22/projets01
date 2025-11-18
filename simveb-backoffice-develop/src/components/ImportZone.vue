<script lang="ts" setup>
	const doublon = ref(false);
	const filename = ref("Sélectionnez un fichier");

	function updateFile($event) {
		filename.value = $event.target.value.split("\\").pop();
	}

	function uploadDocument() {
		// var fd = new FormData();
		// var files = $('#file')[0].files;
		//
		// fd.append('document', files[0]);
		// fd.append('modele', 'commune');
	}
</script>

<template>
	<div class="card p-3 mt-4">
		<div class="is-flex is-flex-direction-row">
			<VControl>
				<div class="file has-name">
					<label class="file-label">
						<input
							class="file-input"
							id="document"
							name="documnet"
							type="file"
							@change="updateFile($event)"
						/>
						<span class="file-cta">
							<span class="file-icon">
								<i class="fas fa-cloud-upload-alt"></i>
							</span>
							<span class="file-label"> Choose a file… </span>
						</span>
						<span class="file-name light-text"> {{ filename }} </span>
					</label>
				</div>
			</VControl>

			<VButtons class="ml-2">
				<VButton color="primary" @click="uploadDocument"> Importer </VButton>
			</VButtons>
		</div>
		<VControl raw subcontrol>
			<VCheckbox v-model="doublon" label="Autoriser la mise à jour lorsqu'un doublon est rencontré" />
		</VControl>
	</div>
</template>

<style lang="scss" scoped></style>
