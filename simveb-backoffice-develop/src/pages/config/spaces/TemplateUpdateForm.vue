<template>
	<VModal
		is="form"
		:open="open"
		actions="right"
		size="big"
		title="Choisir un template"
		@submit.prevent="submit"
		@close="$emit('close')"
	>
		<template #content>
			<div class="modal-form">
				<div class="grid grid-cols-1 lg:grid-cols-2 gap-4 tab-details-inner">
					<label class="flex-col items-center justify-center">
						<span
							class="has-[:checked]:border-primary-dark has-[:checked]:border-4 hover:border-2 has-[:checked]:bg-blue-50 hover:bg-blue-50 flex rounded-lg h-56 cursor-pointer"
						>
							<span class="w-full">
								<img
									class="w-full h-full object-cover rounded-md"
									src="@/assets/templates/focus.webp"
									alt="vehicle"
								/>
							</span>
							<span class="hidden form-check ml-auto mr-4">
								<input
									v-model="form.template"
									class="form-check-input"
									name="template-choice"
									type="radio"
									value="focus"
								/>
							</span>
						</span>
						<span class="flex items-center justify-center mt-2 text-primary-dark text-xl">FOCUS</span>
					</label>

					<label class="flex-col items-center justify-center">
						<span
							class="has-[:checked]:border-primary-dark has-[:checked]:border-4 hover:border-2 has-[:checked]:bg-blue-50 hover:bg-blue-50 flex rounded-lg h-56 cursor-pointer"
						>
							<span class="w-full">
								<img
									class="w-full h-full object-cover rounded-md"
									src="@/assets/templates/vivid.webp"
									alt="vehicle"
								/>
							</span>
							<span class="hidden form-check ml-auto mr-4">
								<input
									v-model="form.template"
									class="form-check-input"
									name="template-choice"
									type="radio"
									value="vivid"
								/>
							</span>
						</span>
						<span class="flex items-center justify-center mt-2 text-primary-dark text-xl">VIVID</span>
					</label>

					<label class="flex-col items-center justify-center">
						<span
							class="has-[:checked]:border-primary-dark has-[:checked]:border-4 hover:border-2 has-[:checked]:bg-blue-50 hover:bg-blue-50 flex rounded-lg h-56 cursor-pointer"
						>
							<span class="w-full">
								<img
									class="w-full h-full object-cover rounded-md"
									src="@/assets/templates/serenity.webp"
									alt="vehicle"
								/>
							</span>
							<span class="hidden form-check ml-auto mr-4">
								<input
									v-model="form.template"
									class="form-check-input"
									name="template-choice"
									type="radio"
									value="default"
								/>
							</span>
						</span>
						<span class="flex items-center justify-center mt-2 text-primary-dark text-xl">SERENITY</span>
					</label>
				</div>
			</div>
		</template>
		<template #action>
			<VButton :loading="formLoading" :disabled="formLoading" color="primary" raised type="submit">
				Confirmer
			</VButton>
		</template>
	</VModal>
</template>

<script lang="ts" setup>
	import { useCrudStore } from "/@src/stores/modules/crud";
	import { storeToRefs } from "pinia";
	import { Notyf } from "notyf";

	const emit = defineEmits(["submit", "close"]);
	const props = defineProps({
		space: {
			type: Object,
			required: true,
		},
		open: {
			type: Boolean,
			required: true,
		},
	});

	const crudStore = useCrudStore();
	const { formLoading, url } = storeToRefs(crudStore);
	const notyf = new Notyf();
	const form = ref({
		template: "default",
	});

	const submit = async () => {
		url.value = "spaces";
		await crudStore
			.updateRow(props.space.id, {
				...form.value,
			})
			.then(() => {
				notyf.success("Mise à jour effectuée");
				url.value = "spaces";
				crudStore.fetchRow(props.space.id);
				emit("submit");
			});
	};

	watch(
		() => props.open,
		(newVal) => {
			if (newVal) {
				form.value = {
					template: props.space.template,
				};
			}
		}
	);
</script>

<style lang="scss"></style>
