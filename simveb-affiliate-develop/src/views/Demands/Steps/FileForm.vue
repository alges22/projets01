<template>
	<div class="intro-y flex items-center mt-4">
		<div class="mr-auto">
			<h2 class="text-lg font-semibold">
				{{ update ? "Mettre à jour les pièces suivantes" : "Ajouter les pièces suivantes" }}
			</h2>
			<span v-if="update" class="text-xs text-danger-600"> Laissez vide si vous ne souhaitez pas modifier </span>
		</div>
	</div>

	<form class="grid grid-cols-12 gap-6 mt-5" @submit.prevent="submit">
		<div class="intro-y col-span-12">
			<div class="intro-y box p-5">
				<div class="sm:grid grid-cols-2 gap-8 mt-4">
					<div v-for="(document, index) in demand.documents" :key="index">
						<FileInputGroup
							v-model="document.file"
							:name="`document-${index}`"
							:label="document.name"
							:required="!update"
							:disabled="loading"
							accept="application/pdf,image/*"
							:errors="errors.documents || []"
						>
							<p class="mt-1 text-xs leading-4 text-gray-600">
								Format pris en compte: png, jpg - Taille : 500 Ko
							</p>
						</FileInputGroup>
					</div>
				</div>
			</div>

			<div class="flex align-center justify-end mt-5">
				<button class="btn btn-outline-primary w-36 mr-4 border-2" type="button" @click="$emit('prev')">
					Retour
				</button>
				<BasicButton :loading="loading" class="btn btn-primary w-36" type="submit">Suivant</BasicButton>
			</div>
		</div>
	</form>
</template>

<script setup>
	import { useDemandStore } from "@/stores/demand.js";
	import { storeToRefs } from "pinia";
	import { onMounted } from "vue";
	import FileInputGroup from "@/components/Form/FileInputGroup.vue";
	import BasicButton from "@/components/BasicButton.vue";

	const demandStore = useDemandStore();
	const { demand, formLoading: loading, formData, errors, update } = storeToRefs(demandStore);
	const emit = defineEmits(["next", "prev"]);

	onMounted(() => {
		demand.value.documents = formData.value.required_documents.map((document) => ({
			type_id: document.id,
			file: null,
			name: document.description,
		}));
	});

	const submit = () => {
		demand.value.documents = demand.value.documents.filter((document) => document.file);
		emit("next");
	};
</script>
