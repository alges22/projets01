<script setup lang="ts">
	import {FontAwesomeIcon} from "@fortawesome/vue-fontawesome";
	import {useImmatriculationStore} from "~/stores/immatriculation";
	import * as yup from "yup";
	import {ErrorMessage, Field, Form} from "vee-validate";
	import {useApi} from "~/helpers/useApi";

    const props = defineProps<{
        type: string;
    }>();

	const api = useApi();
	const { $awn } = useNuxtApp()

	const store = props.type === "reimmatriculation" ? useReimmatriculationStore() :  useImmatriculationStore()

	let { saving, create, suggestions, number, template } = storeToRefs(store)

	const route = useRoute();
	const id = route.params.id

	const onSubmit = () => {
		api({
			method: 'POST',
			url: '/client/check-number',
			data: {
				number: number.value
			}
		}).then((response) => response.data)
			.then((response) => {
				if(response.available === false){
					$awn.alert(response.message)
				}else{
                    store.nextStep()
				}
			})
	}

	const updateSuggestions = ($event: any) => {
		let template = $event.target.value

		api({
			method: 'POST',
			url: '/client/suggest-numbers',
			data: {
				template : template
			}
		}).then((response) => response.data)
			.then((response) => {
				suggestions.value = response
			})
	}

	const schema = yup.object({
		numero: yup
			.string()
			.required("Veuillez renseigner le numéro voulu"),

		template: yup
			.string()
	});
</script>

<template>
	<Form :validation-schema="schema" @submit="onSubmit" class="card p-16 mt-4">
		<div class="w-1/2 mx-auto">
			<div class="w-full p-4">
				<label for="">Sélectionnez un template</label>
				<Field name="template" as="select" class="form-control" @change="updateSuggestions" v-model="template">
					<option value="">Sélectionnez un template</option>
					<option v-for="template in create?.number_templates" :value="template.template"> {{ template.template }} </option>
				</Field>
				<ErrorMessage name="template" class="form-invalid" />
			</div>

			<div class="p-4 grid grid-cols-6 gap-4 max-h-60 overflow-x-auto">
				<button
					type="button"
					@click="number = suggestion"
					:class="{'form-control': true, 'active': suggestion === number}"
					v-for="suggestion in suggestions"
					:key="suggestion"
				>
					{{ suggestion }}
				</button>
			</div>

			<div class="text-center">
				<h4>ou</h4>
			</div>

			<div class="w-full p-4">
				<label for="">Entrez le numéro voulu</label>
				<Field name="numero" v-model="number" class="form-control" placeholder="EX: X00X"/>
				<ErrorMessage name="numero" class="form-invalid" />
			</div>
		</div>

		<div class="mt-16 flex-row flex">
			<button class="text-blue font-bold text-2xl" @click="store.previousStep()">
				<font-awesome-icon class="mx-4" :icon="['fas', 'arrow-left']" />
				Précédent
			</button>

			<div class="ms-auto">
<!--				<button class="btn-outline-blue mx-4" @click="store.storeDemande(id)" :disabled="saving">-->
<!--					<span class="font-bold text-2xl">Enregistrer</span>-->
<!--				</button>-->

				<button class="btn-blue mx-4" :disabled="saving">
					<span class="font-bold text-2xl">Suivant</span>
				</button>
			</div>
		</div>
	</Form>
</template>

<style scoped lang="scss">

</style>