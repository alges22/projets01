<script lang="ts" setup>
    import { useImmatriculationStore } from '~/stores/immatriculation'
    import {FontAwesomeIcon} from "@fortawesome/vue-fontawesome";
    import * as yup from "yup";
    import {ErrorMessage, Form, Field} from "vee-validate";

    const props = defineProps<{
        type: string;
    }>();

    const { $awn } = useNuxtApp()

    const store = props.type === "reimmatriculation" ? useReimmatriculationStore() :  useImmatriculationStore()

    let { create, loading, data_plates, saving, update, vehicule_infos } = storeToRefs(store)

	const route = useRoute();
	const id = route.params.id

    const schema = yup.object({
        front_plate_shape_id: yup
            .string()
            .nullable(),

        back_plate_shape_id: yup
            .string()
            .required("Veuillez renseigner la forme de la plaque arrière"),

        plate_color_id: yup
            .string()
            .required("Veuillez renseigner la couleur de la plaque"),
    });


    const next = () => {
        if (update){
            store.nextStep()
        }else{
            store.storeDemande(id)
                .then(() => {
                    store.nextStep()
                })
                .catch((error) => {
                    $awn.alert(error)
                })
        }
    }
</script>

<template>
    <div class="card p-4 md:p-16 mt-4 text-center">
        <h4 class="text-blue font-bold text-5xl mb-4">Plaque</h4>

        <Form :validation-schema="schema" @submit="next" :initial-values="data_plates">
            <template v-if="loading">
                <Loader />
            </template>
            <template v-else>
                <div ref="form" class="grid grid-cols-1 sm:grid-cols-2 gap-8 text-left w-4/5 mx-auto mt-8">
                    <div v-if="vehicule_infos.is_car">
                        <label for="immatriculation_front_plate">Forme plaque avant</label>
                        <Field as="select" class="form-control" v-model="data_plates.front_plate_shape_id" id="immatriculation_front_plate" name="front_plate_shape_id">
                            <option value="">Choisissez une forme</option>
                            <option v-for="shape in create?.plate_shapes" :value="shape.id"> {{ shape.name }} </option>
                        </Field>
                        <ErrorMessage name="front_plate_shape_id" class="form-invalid" />
                    </div>
                    <div>
                        <label for="immatriculation_back_plate" v-if="vehicule_infos.is_car">Forme plaque arrière</label>
                        <label for="immatriculation_back_plate" v-else>Forme de la plaque</label>
                        <Field as="select" class="form-control" v-model="data_plates.back_plate_shape_id" id="immatriculation_back_plate" name="back_plate_shape_id">
                            <option value="">Choisissez une forme</option>
                            <option v-for="shape in create?.plate_shapes" :value="shape.id"> {{ shape.name }} </option>
                        </Field>
                        <ErrorMessage name="back_plate_shape_id" class="form-invalid" />
                    </div>
                    <div>
                        <label for="immatriculation_type">Type de numéro d'immatriculation</label>
                        <select disabled class="form-control" id="immatriculation_type">
                            <option>Normale</option>
                        </select>
                    </div>
                    <div>
                        <label for="immatriculation_plate_color">Couleur de la plaque</label>
                        <Field as="select" class="form-control" v-model="data_plates.plate_color_id" id="immatriculation_plate_color" name="plate_color_id">
                            <option value="">Couleur de la plaque</option>
                            <option v-for="color in create?.plate_colors" :value="color.id"> {{ color.label }} </option>
                        </Field>
                        <ErrorMessage name="plate_color_id" class="form-invalid" />
                    </div>
                </div>
            </template>

            <div class="mt-16 flex-row flex">
                <button class="text-blue font-bold text-2xl" @click="store.previousStep()">
                    <font-awesome-icon class="mx-4" :icon="['fas', 'arrow-left']" />
                    Précédent
                </button>

                <div class="ms-auto">
<!--                    <button class="btn-outline-blue mx-4" @click="store.storeDemande(id)" :disabled="saving">-->
<!--                        <span class="font-bold text-2xl">Enregistrer</span>-->
<!--                    </button>-->

                    <button class="btn-blue mx-4" :disabled="saving">
                        <span class="font-bold text-2xl">Suivant</span>
                    </button>
                </div>
            </div>
        </Form>
    </div>

</template>

<style scoped>

</style>