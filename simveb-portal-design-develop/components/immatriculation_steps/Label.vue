<script lang="ts" setup>
	import {FontAwesomeIcon} from "@fortawesome/vue-fontawesome";
	import * as yup from "yup";
	import {ErrorMessage, Field, Form} from "vee-validate";
	import {useApi} from "~/helpers/useApi";
	import {useImmatriculationStore} from "~/stores/immatriculation";

	const api = useApi();

	const { $awn } = useNuxtApp()

    const props = defineProps<{
        type: string;
    }>();

    const store = props.type === "reimmatriculation" ? useReimmatriculationStore() :  useImmatriculationStore()

	let { saving, label } = storeToRefs(store)

	const route = useRoute();

	const default_values = ref({
		label: label,
	})

	const schema = yup.object({
		label: yup
			.string()
			.max(8, 'Le texte ne peux pas contenir plus de 8 caractères')
			.required("Veuillez renseigner le label que vous voulez"),
	});

	const onSubmit = (values:any) => {
		api({
			method: 'POST',
			url: '/client/check-label',
			data: values
		}).then((response) => (response.data))
			.then((response) => {
				let available = response.available

				if (!available) $awn.alert(response.message)
				else {
					$awn.success(response.message)

					label.value = values.label

					store.nextStep()
				}
			})
	}
</script>

<template>
	<Form :validation-schema="schema" @submit="onSubmit" :initial-values="default_values" class="card p-16 mt-4">
		<div class="w-1/2 mx-auto">
			<div class="text-left">
				<Field name="label" id="label" type="text" class="form-control" placeholder="EX: XX-304-YY" />
				<ErrorMessage name="label" class="form-invalid-lg" />
			</div>

			<div class="text-left text-gray-400 text-xl p-6">
				<font-awesome-icon :icon="['fas', 'circle-info']"/>
				Le label peut être alphanumérique mais ne peux pas dépasser 8 caractères y compris les espaces
			</div>
		</div>

		<div class="mt-16 flex-row flex">
			<button class="text-blue font-bold text-2xl" @click="store.previousStep()">
				<font-awesome-icon class="mx-4" :icon="['fas', 'arrow-left']" />
				Précédent
			</button>

			<div class="ms-auto">
<!--				<button class="btn-outline-blue mx-4" type="button" @click="store.storeDemande(id)" :disabled="saving">-->
<!--					<span class="font-bold text-2xl">Enregistrer</span>-->
<!--				</button>-->

				<button class="btn-blue mx-4" :disabled="saving">
					<span class="font-bold text-2xl">Suivant</span>
				</button>
			</div>
		</div>
	</Form>
</template>

<style scoped>

</style>