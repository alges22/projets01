<template>
	<VModal
		is="form"
		:open="open"
		actions="center"
		title="Exportation des demandes"
		size="medium"
		@submit.prevent="submit"
		@close="close"
	>
		<template #content>
			<div>
				<p class="text-sm leading-5 text-gray-500">Choisir le format d'exportation</p>
				<fieldset class="mt-4">
					<div class="space-y-4 sm:flex sm:items-center sm:space-y-0 sm:space-x-10">
						<div class="flex items-center">
							<input id="pdf" v-model="format" name="format" type="radio" value="pdf" class="h-4 w-4" />
							<label for="pdf" class="ml-3 block text-md font-medium text-success-700"> PDF </label>
						</div>
						<div class="flex items-center">
							<input
								id="excel"
								v-model="format"
								name="format"
								type="radio"
								value="excel"
								class="h-4 w-4"
							/>
							<label for="excel" class="ml-3 block text-md font-medium text-danger-700"> Excel </label>
						</div>
					</div>
				</fieldset>
			</div>
		</template>
		<template #action>
			<VButton type="submit" color="primary" raised :loading="formLoading"> Confirmer </VButton>
		</template>
	</VModal>
</template>
<script setup lang="ts">
	import { useNotyf } from "/src/composable/useNotyf";
	import client from "/@src/composable/axiosClient";

	defineProps<{
		open: boolean;
	}>();

	const emit = defineEmits(["close"]);
	const notyf = useNotyf();
	const formLoading = ref(false);
	const format = ref("excel");

	const submit = async () => {
		formLoading.value = true;
		client({
			method: "GET",
			url: `/exports/${format.value}/demands`,
			responseType: "blob",
			data: {},
		})
			.then((response) => {
				const href = URL.createObjectURL(response.data);
				const link = document.createElement("a");
				link.href = href;
				link.setAttribute("download", `Demandes.${format.value === "pdf" ? "pdf" : "xlsx"}`);
				document.body.appendChild(link);
				link.click();

				document.body.removeChild(link);
				URL.revokeObjectURL(href);

				notyf.success("Exportation réussie");
				emit("close");
			})
			.finally(() => {
				formLoading.value = false;
			});
	};

	const close = () => {
		formLoading.value = false;
		format.value = "excel";
		emit("close");
	};
</script>
