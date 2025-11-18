<template>
	<VModal
		is="form"
		:open="open"
		actions="right"
		title="Importations de plaques"
		@close="$emit('close')"
		@submit.prevent="importPlates"
	>
		<template #content>
			<div class="modal-form">
				<VField grouped>
					<VControl :errors="errors.file || []">
						<div class="file has-name">
							<label class="file-label" for="file-import">
								<input
									id="file-import"
									class="file-input"
									type="file"
									name="file"
									@change="handleFileUpload"
								/>
								<span class="file-cta">
									<span class="file-icon">
										<i class="fas fa-cloud-upload-alt"></i>
									</span>
									<span class="file-label"> Choisissiez un fichier… </span>
								</span>
								<span class="file-name light-text">
									{{ fileName || "Aucun fichier" }}
								</span>
							</label>
						</div>
					</VControl>
				</VField>
			</div>
		</template>
		<template #action>
			<a href="#" class="download-link" @click.prevent="handleDownload"> Télécharger le format </a>
			<VButton :loading="formLoading" :disabled="formLoading || !file" color="primary" raised type="submit">
				Confirmer
			</VButton>
		</template>
	</VModal>
</template>

<script setup lang="ts">
	import { useCrudStore } from "/@src/stores/modules/crud";
	import { storeToRefs } from "pinia";
	import { Notyf } from "notyf";
	import { defineProps, defineEmits, ref, onMounted } from "vue";

	const emit = defineEmits(["submit", "close"]);
	defineProps({
		open: {
			type: Boolean,
			required: true,
		},
	});

	const handleDownload = () => {
		fetch("/imports/format_import_plate.xlsx")
			.then((response) => response.blob())
			.then((blob) => {
				// Use file-saver to save the file
				const url = window.URL.createObjectURL(blob);
				const a = document.createElement("a");
				a.style.display = "none";
				a.href = url;
				a.setAttribute("download", "import.xlsx");
				document.body.appendChild(a);
				a.click();
				window.URL.revokeObjectURL(url);
				document.body.removeChild(a);
			})
			.catch((error) => console.error("Download error:", error));
	};

	const crudStore = useCrudStore();
	const { formLoading, url, errors } = storeToRefs(crudStore);
	const fileName = ref("");
	const file = ref(null);
	const notyf = new Notyf();
	// const importFileUrl = new URL("/imports/format_import_plate.xlsx", import.meta.url).href;
	const importFileUrl = `/imports/format_import_plate.xlsx`;

	onMounted(() => {
		console.log(importFileUrl);
	});

	const handleFileUpload = (event: Event) => {
		file.value = event.target.files[0];
		fileName.value = event.target.files[0].name;
	};

	const importPlates = async () => {
		url.value = `plates`;
		await crudStore.createWithFile({ file: file.value }).then(() => {
			notyf.success("Plaque ajoutée avec succès");
			emit("submit");
		});
	};
</script>

<style scoped lang="scss"></style>
