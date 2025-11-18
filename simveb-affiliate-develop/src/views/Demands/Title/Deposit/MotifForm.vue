<template>
	<div class="intro-y flex items-center mt-4">
		<h2 class="text-lg font-semibold mr-auto">{{ update ? "Changer le motif" : "Sélectionner le motif" }}</h2>
	</div>

	<div class="grid grid-cols-12 gap-6 mt-5">
		<form class="intro-y col-span-12" @submit.prevent="submit">
			<div class="intro-y box p-5">
				<SelectInputGroup
					v-model="demand.title_reason_id"
					:options="formData.title_reasons"
					name="title_reason_id"
					label="Motif"
					required
					option-value="id"
					option-text="label"
					:errors="errors.title_reason_id || []"
				/>
			</div>

			<div class="flex align-center justify-end mt-5">
				<button class="btn btn-outline-primary w-36 mr-4 border-2" type="button" @click="$emit('prev')">
					Retour
				</button>
				<BasicButton class="btn-primary w-36" type="submit" :loading="loading"> Suivant</BasicButton>
			</div>
		</form>
	</div>
</template>

<script setup>
	import { useDemandStore } from "@/stores/demand.js";
	import { storeToRefs } from "pinia";
	import BasicButton from "@/components/BasicButton.vue";
	import SelectInputGroup from "@/components/Form/SelectInputGroup.vue";

	const emit = defineEmits(["next", "prev"]);

	const demandStore = useDemandStore();
	const { demand, loading, errors, formData, update } = storeToRefs(demandStore);

	const submit = () => {
		if (!update.value) {
			demandStore.fetchVehicleInfo(demand.value.vin, demand.value.customs_ref).then(() => {});
		}
		emit("next");
	};
</script>
