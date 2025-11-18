<template>
	<VModal
		is="form"
		:open="open"
		actions="right"
		title="Validation de la commande de plaques"
		@close="$emit('close')"
		@submit.prevent="validatePlateOrder"
	>
		<template #content>
			<div class="modal-form">
				<VField grouped>
					<VControl>
						<label for="validationFile">Veuillez joindre le fichier de la liste des plaques livrées</label>
						<div class="file has-name">
							<label class="file-label">
								<input
									class="file-input"
									id="validationFile"
									name="file"
									type="file"
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
			<VButton :href="importFileUrl" color="info" raised target="_blank"> Télécharger le format</VButton>
			<VButton :disabled="loading || !file" :loading="loading" color="primary" raised type="submit">
				Confirmer
			</VButton>
		</template>
	</VModal>
</template>

<script lang="ts" setup>
	import { Notyf } from "notyf";
	import client from "/@src/composable/axiosClient";

	const formDataHeaders = {
		...client.defaults.headers,
		"Content-Type": "multipart/form-data",
	};

	const emit = defineEmits(["submit", "close"]);
	const props = defineProps({
		order: {
			type: Object,
			required: true,
		},
		open: {
			type: Boolean,
			required: true,
		},
	});

	const loading = ref(false);
	const importFileUrl = ref("");
	const fileName = ref("");
	const file = ref(null);
	const notyf = new Notyf();

	const handleFileUpload = (event: Event) => {
		file.value = event.target.files[0];
		fileName.value = event.target.files[0].name;
	};

	const validatePlateOrder = () => {
		client({
			method: "POST",
			url: "plate-orders/confirm",
			data: {
				file: file.value,
				plate_order_id: props.order.id,
			},
			headers: formDataHeaders,
		})
			.then((response) => response.data)
			.then((response) => {
				notyf.success(response.message);
				emit("submit");
			})
			.finally(() => {
				loading.value = true;
			});
	};

	onMounted(() => {
		client({
			method: "GET",
			url: "plate-orders/confirmation-file-format",
		})
			.then((response) => response.data)
			.then((response) => {
				importFileUrl.value = response;
			});
	});
</script>

<style lang="scss" scoped></style>
