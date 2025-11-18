<template>
	<div class="intro-y flex items-center mt-4">
		<h2 class="text-lg font-semibold mr-auto">Personnaliser votre plaque</h2>
	</div>

	<div class="grid grid-cols-12 gap-6 mt-5">
		<form class="intro-y col-span-12" @submit.prevent="submit">
			<div class="intro-y box p-5">
				<div class="mb-8">
					<div>
						<TextInputGroup
							v-model="demand.label"
							name="label"
							label="Entrer le label voulu"
							:errors="errors.label || []"
							required
							:disabled="loading"
							:minlength="4"
							:maxlength="8"
							pattern="^[a-zA-Z0-9]{8}$"
						/>
					</div>
				</div>
			</div>

			<div class="flex align-center justify-end mt-5">
				<button class="btn btn-outline-primary w-36 mr-4 border-2" type="button" @click="$emit('prev')">
					Retour
				</button>
				<BasicButton :loading="loading" class="btn-primary w-36" type="submit"> Valider</BasicButton>
			</div>
		</form>
	</div>
</template>

<script setup>
	import { onMounted, ref } from "vue";
	import { useDemandStore } from "@/stores/demand.js";
	import { storeToRefs } from "pinia";
	import BasicButton from "@/components/BasicButton.vue";
	import TextInputGroup from "@/components/Form/TextInputGroup.vue";
	import client from "@/assets/js/axios/client.js";
	import Alert from "@/components/notification/alert.js";

	const emit = defineEmits(["next", "prev"]);

	const demandStore = useDemandStore();
	const { demand } = storeToRefs(demandStore);
	const loading = ref(false);
	const errors = ref({});

	const submit = () => {
		loading.value = true;
		errors.value = {};
		client({
			method: "POST",
			url: "client/check-label",
			data: { label: demand.value.label },
		})
			.then((response) => {
				Alert.success(response.data.message);
				emit("next");
			})
			.catch((error) => {
				errors.value = error.response.data.errors || [];
			})
			.finally(() => {
				loading.value = false;
			});
	};

	onMounted(() => {});
</script>

<style scoped></style>
