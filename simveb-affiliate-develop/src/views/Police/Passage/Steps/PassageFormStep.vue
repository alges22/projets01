<template>
	<div class="grid grid-cols-12 gap-6 mt-5">
		<form class="intro-y col-span-12" @submit.prevent="submit">
			<div class="intro-y box p-5">
				<div class="sm:grid grid-cols-2 gap-8 mb-8">
					<div>
						<TextInputGroup
							v-model="passage.vehicle_owner_lastname"
							name="vehicle_owner_lastname"
							required
							label="Nom du propriétaire"
							:errors="errors.vehicle_owner_lastname"
							add-class="w-full"
						/>
					</div>

					<div>
						<TextInputGroup
							v-model="passage.vehicle_owner_firstname"
							name="vehicle_owner_firstname"
							required
							label="Prénom du propriétaire"
							:errors="errors.vehicle_owner_firstname"
							add-class="w-full"
						/>
					</div>
				</div>

				<div class="sm:grid grid-cols-2 gap-8 mt-4">
					<div>
						<TextInputGroup
							v-model="passage.driver_lastname"
							name="driver_lastname"
							required
							label="Nom du conducteur"
							:errors="errors.driver_lastname"
							add-class="w-full"
						/>
					</div>

					<div>
						<TextInputGroup
							v-model="passage.driver_firstname"
							name="driver_firstname"
							required
							label="Prénom du conducteur"
							:errors="errors.driver_firstname"
							add-class="w-full"
						/>
					</div>
				</div>

				<div class="sm:grid grid-cols-2 gap-8 mt-4">
					<div>
						<TextInputGroup
							v-model="passage.driving_license_number"
							name="driving_license_number"
							required
							label="Numéro de permis de conduire"
							:errors="errors.driving_license_number"
							add-class="w-full"
						/>
					</div>

					<div>
						<FileInputGroup
							v-model="passage.driving_license_file"
							name="driving_license_file"
							required
							label="Photo de permis de conduire"
							accept="image/*"
							:errors="errors.driving_license_file"
							add-class="w-full"
						/>
					</div>
				</div>

				<div class="sm:grid grid-cols-2 gap-8 mt-4">
					<div>
						<SelectInputGroup
							v-model="passage.passage_type"
							:options="[
								{ id: 'in', name: 'Entrée' },
								{ id: 'out', name: 'Sortie' },
							]"
							label="Type de passage"
							name="passage_type"
							:errors="errors.passage_type"
							:multiple="false"
						/>
					</div>

					<div>
						<TextInputGroup
							v-model="passage.total_passengers_on_board"
							name="total_passengers_on_board"
							required
							type="number"
							label="Nombre de personnes à bord"
							:errors="errors.total_passengers_on_board"
							add-class="w-full"
						/>
					</div>
				</div>
			</div>

			<div class="text-right mt-5">
				<button class="btn btn-outline-primary w-36 mr-4 border-2" type="reset" @click="$emit('prev')">
					Annuler
				</button>
				<BasicButton :loading="formLoading" class="btn-primary w-36" type="submit"> Enregistrer </BasicButton>
			</div>
		</form>
	</div>
</template>
<script setup>
	import { useVehiclePassageStore } from "@/stores/passage.js";
	import { storeToRefs } from "pinia";
	import TextInputGroup from "@/components/Form/TextInputGroup.vue";
	import BasicButton from "@/components/BasicButton.vue";
	import FileInputGroup from "@/components/Form/FileInputGroup.vue";
	import SelectInputGroup from "@/components/Form/SelectInputGroup.vue";

	const emit = defineEmits(["next", "prev"]);

	const passageStore = useVehiclePassageStore();
	const { passage, formLoading, errors } = storeToRefs(passageStore);

	const submit = async () => {
		await passageStore.registerPassage(passage.value).then(() => {
			emit("next");
		});
	};
</script>
