<template>
	<VModal
		is="form"
		:open="open"
		actions="center"
		title="Vérification de la demande"
		size="medium"
		@submit.prevent="submit"
		@close="close"
	>
		<template #content>
			<div>
				<label class="text-base font-medium text-gray-900">Poursuivre la demande ?</label>
				<p class="text-sm leading-5 text-gray-500">Vous confirmez que les informations sont correctes.</p>
				<fieldset class="mt-4">
					<legend class="sr-only">Accepter ou rejeter</legend>
					<div class="space-y-4 sm:flex sm:items-center sm:space-y-0 sm:space-x-10">
						<div class="flex items-center">
							<input
								id="approved"
								v-model="approved"
								name="verify"
								type="radio"
								:value="true"
								class="h-4 w-4 border-success-300 text-success-600 focus:ring-indigo-500"
							/>
							<label for="approved" class="ml-3 block text-md font-medium text-success-700">
								Oui, j'accepte
							</label>
						</div>
						<div class="flex items-center">
							<input
								id="rejected"
								v-model="approved"
								name="verify"
								type="radio"
								:value="false"
								class="h-4 w-4 border-danger-300 text-danger-600 focus:ring-indigo-500"
							/>
							<label for="rejected" class="ml-3 block text-md font-medium text-danger-700">
								Non, je refuse
							</label>
						</div>
					</div>
				</fieldset>

				<div v-if="!approved" class="mt-4">
					<VField>
						<VLabel required>Raison du rejet</VLabel>
						<VControl fullwidth :errors="errors.reason">
							<VTextarea v-model="reason" placeholder="Raison du rejet"></VTextarea>
						</VControl>
					</VField>
				</div>
			</div>
		</template>
		<template #action>
			<VButton type="submit" color="primary" raised :loading="formLoading"> Confirmer </VButton>
		</template>
	</VModal>
</template>
<script setup lang="ts">
	import { useDemandStore } from "/src/stores/modules/demand";
	import { storeToRefs } from "pinia";
	import { useNotyf } from "/src/composable/useNotyf";

	defineProps<{
		open: boolean;
	}>();

	const emit = defineEmits(["close"]);
	const notyf = useNotyf();
	const demandStore = useDemandStore();
	const { demand, formLoading, errors } = storeToRefs(demandStore);

	const approved = ref(true);
	const reason = ref("");

	const submit = async () => {
		await demandStore
			.verifyDemand(demand.value.active_treatment.id, approved.value, reason.value)
			.then(async () => {
				notyf.success(approved ? "Demande vérifié avec succès !" : "Demande suspendu avec succès !");
				await demandStore.fetchDemand(demand.value.id);
				emit("close");
			});
	};

	const close = () => {
		reason.value = "";
		emit("close");
	};
</script>
