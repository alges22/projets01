<template>
	<div class="intro-y flex items-center mt-4">
		<h2 class="text-lg font-semibold mr-auto">
			{{
				update
					? "Mettre à jour les caractéristiques de la plaque"
					: "Sélectionner les caractéristiques de la plaque"
			}}
		</h2>
	</div>

	<div class="grid grid-cols-12 gap-6 mt-5">
		<form class="intro-y col-span-12" @submit.prevent="submit">
			<div class="intro-y box p-5">
				<div class="sm:grid grid-cols-2 gap-8 mb-8">
					<SelectInputGroup
						v-if="demand.is_car"
						v-model="demand.front_plate_shape_id"
						:options="formData.plate_shapes"
						name="front_plate_shape_id"
						label="Forme de plaque avant"
						required
						option-value="id"
						option-text="name"
						:errors="errors.front_plate_shape_id || []"
					/>

					<SelectInputGroup
						v-model="demand.back_plate_shape_id"
						:options="formData.plate_shapes"
						name="back_plate_shape_id"
						label="Forme de plaque arrière"
						required
						option-value="id"
						option-text="name"
						:errors="errors.back_plate_shape_id || []"
					/>

					<SelectInputGroup
						v-model="demand.plate_color_id"
						:options="formData.plate_colors"
						name="plate_color_id"
						label="Couleur de plaque"
						placeholder="Sélectionner une couleur"
						required
						option-value="id"
						option-text="label"
						:errors="errors.plate_color_id || []"
					>
						<template #custom="{ selected, option }">
							<div :style="{ backgroundColor: option.color_code }" class="color-box mr-5"></div>
							<span :class="[selected ? 'font-semibold' : 'font-normal', 'block truncate']">
								{{ option.label }}
							</span>
						</template>
					</SelectInputGroup>
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
	import { onMounted } from "vue";
	import { useDemandStore } from "@/stores/demand.js";
	import { storeToRefs } from "pinia";
	import SelectInputGroup from "@/components/Form/SelectInputGroup.vue";
	import BasicButton from "@/components/BasicButton.vue";
	import Alert from "@/components/notification/alert.js";

	const emit = defineEmits(["next", "prev"]);

	const demandStore = useDemandStore();
	const { demand, formLoading: loading, errors, formData, update } = storeToRefs(demandStore);

	const submit = async () => {
		if (
			(!demand.value.is_car && (!demand.value.back_plate_shape_id || !demand.value.plate_color_id)) ||
			(demand.value.is_car &&
				(!demand.value.front_plate_shape_id ||
					!demand.value.back_plate_shape_id ||
					!demand.value.plate_color_id))
		) {
			Alert.error("Veuillez remplir tous les champs");
			return;
		}
		if (!update.value)
			await demandStore.createDemand(demand.value).then(() => {
				emit("next");
			});
		else emit("next");
	};

	onMounted(() => {});
</script>

<style scoped>
	.color-box {
		width: 30px;
		height: 30px;
		border-radius: 80%;
	}
</style>
