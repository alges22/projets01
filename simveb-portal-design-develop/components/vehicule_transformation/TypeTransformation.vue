<script setup>
    import {useVehiculeTransformation} from "~/stores/vehiculeTransformation";
    import {ErrorMessage, Field, Form} from "vee-validate";
    import * as yup from "yup";
    import {FontAwesomeIcon} from "@fortawesome/vue-fontawesome";
    import Swal from "sweetalert2";
    import {useApi} from "~/helpers/useApi";
    import SelectInput from "~/components/form/SelectInput.vue";
    import simpleColorConverter from "simple-color-converter"

    const schema = yup.object({
        type_id: yup
            .string()
            .required("Veuillez sélectionner un type de transformation"),

        category_id: yup
            .string()
            .required("Veuillez sélectionner un type de transformation"),

        characteristic_id: yup
            .string()
            .required("Veuillez sélectionner la valeur de la caractéristique"),
    });

    const { $awn } = useNuxtApp()

    const api = useApi()

    const store = useVehiculeTransformation()

    const { create, transformations, loading, update, transformation } = storeToRefs(store);

    const selectedType = ref(null)
    const selectedTypeId = ref(null)
    const selectedPersonnalisation = ref(null)


    const getCategoryLabel = (id) => {
        for (const type of create.value.transformationTypes) {
            const characteristic = type.category_characteristics.find(c => c.id === id);
            if (characteristic) {
                return characteristic.label;
            }
        }

        return "Label not found";
    };

    const getVehicleCaracteristiqueLabel = (id) => {
        for (const type of create.value.transformationTypes) {
            for (const category of type.category_characteristics) {
                const vehicleCharacteristic = category.vehicle_characteristics.find(v => v.id === id);
                if (vehicleCharacteristic) {
                    return vehicleCharacteristic.value;
                }
            }
        }
        return "Label not found";
    };

    const groupByTransformationType = (array) => {
        return array.reduce((acc, item) => {
            (acc[item.transformationType] = acc[item.transformationType] || []).push(item);
            return acc;
        }, {});
    };

    const groupedTransformations = computed(() => groupByTransformationType(transformations.value));

    const onSubmit = (values) => {
        const newTransformation = {
            type_id: values.type_id,
            category_id: values.category_id,
            characteristic_id: values.characteristic_id,
            transformationType: selectedType.value.label,
        };

        // Check if the same category already exists in the transformations array
        const isDuplicate = transformations.value.some(
            (transformation) =>
                transformation.category_id === newTransformation.category_id
        );

        if (!isDuplicate) {
            if (update.value){
                // loading.value = true

                api({
                    method: 'POST',
                    url: `/client/add-characteristics/`,
                    data: {
                        transformation_id: transformation.value,
                        value_id: [newTransformation.characteristic_id]
                    }
                }).then((response) => response.data)
                  .then((response) => {
                      transformations.value.push({
                          transformation_id: response.id,
                          ...newTransformation
                      });
                  })
                  .catch((error) => {
                      $awn.alert(error.response.data.message)
                  })
            }else{
                transformations.value.push(newTransformation);
            }
        } else {
            $awn.warning("Cette catégorie à déja été ajoutée")
        }
    };

    const changeType = (typeId) => {
        selectedTypeId.value = typeId

        selectedType.value = create.value.transformationTypes.find(type => type.id === selectedTypeId.value)
    }

    const changePersonnalisaton = (personnalisation_id) => {
        const type = create.value.transformationTypes.find(type => type.id === selectedTypeId.value)

        selectedPersonnalisation.value = type.category_characteristics.find((c) => c.id === personnalisation_id)
    }

    const next = () => {
        store.nextStep()
    }

    const deleteTransformation = (characteristic_id, transformation_id) => {
        Swal.fire({
            title: "Supprimer une transformation",
            text: "Etes vous sûr de vouloir effectuer cette action?",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#172554",
            cancelButtonColor: "#EF4444",
            confirmButtonText: "Oui! Je confirme",
            cancelButtonText: "Non! J'annule",
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({
                    title: "Suppression en cours",
                    text: "Patientez pendant le traitement de votre requête",
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    },
                });

                if (update.value){
                    api({
                        method: 'DELETE',
                        url: `/client/characteristic/${transformation_id}`
                    }).then((response) => response.data)
                        .then((response) => {
                            transformations.value = transformations.value.filter(
                                (element) => element.characteristic_id !== characteristic_id
                            );
                        }).catch((error) => {
                            $awn.alert(error.response.data.message)
                        }).finally(() => {
                            Swal.close()
                        })
                }else{
                    transformations.value = transformations.value.filter(
                        (element) => element.characteristic_id !== characteristic_id
                    );

                    Swal.close()
                }
            }
        });
    }


    var color = new simpleColorConverter({
        string: 'pantone 358C',
        to: 'hex3'
    })

    console.log(color)
</script>

<template>
    <div class="card w-full p-16 text-center mt-2">
        <div v-if="loading" class="flex justify-center">
            <Loader />
        </div>

        <div v-else>
            <div class="w-2/3 mx-auto">
                <span class="text-2xl text-blue-900 font-bold"> Veuillez choisisr le type de transformation</span>
                <Form :validation-schema="schema" @submit="onSubmit">
                    <div class="grid grid-cols-2 gap-x-8 text-left mt-8">
                        <Field
                            v-model="selectedTypeId"
                            class="hidden"
                            name="type_id"
                            type="text"
                        />
                        <div v-for="type in create.transformationTypes" :key="type.id" @click="changeType(type.id)"
                             class="shadow rounded p-8 border-2 flex cursor-pointer w-full">
                            <span>
                                {{ type.label }}
                            </span>

                            <input
                                :checked="selectedTypeId && selectedTypeId === type.id"
                                class="ms-auto rounded-full border-2 border-gray-400 appearance-none checked:border-8 h-6 w-6"
                                type="radio"
                            />
                        </div>
                        <ErrorMessage class="form-invalid" name="type_id"/>
                    </div>

                    <div class="grid gap-8 text-left mt-4">
                        <div>
                            <SelectInput
                                label="Personnalisation"
                                name="category_id"
                                label-field="label"
                                :options="selectedType?.category_characteristics"
                                placeholder="Selectionnez la personnalisation"
                                @update:modelValue="changePersonnalisaton"
                            />
                        </div>

                        <div v-if="selectedPersonnalisation?.code === 'pantone_color' ">
<!--                            <PantonSelectInput-->
<!--                                label="Caractéristique"-->
<!--                                name="characteristic_id"-->
<!--                                :options="selectedPersonnalisation?.vehicle_characteristics"-->
<!--                                placeholder="Selectionnez la caractéristique"-->
<!--                            />-->

                            <div class="grid grid-cols-3 gap-8 text-left mt-8">
                                <Field
                                    class="hidden"
                                    name="characteristic_id"
                                    type="text"
                                />
                                <div v-for="type in selectedPersonnalisation?.vehicle_characteristics" :key="type.id"
                                     class="shadow rounded p-8 border-2 flex cursor-pointer w-full">
                                        <span>
                                            {{ type.value }}
                                        </span>

                                    <input
                                        class="ms-auto rounded-full border-2 border-gray-400 appearance-none checked:border-8 h-6 w-6"
                                        type="radio"
                                    />
                                </div>
                                <ErrorMessage class="form-invalid" name="characteristic_id" />
                            </div>
                        </div>
                        <div v-else>
                            <SelectInput
                                label="Caractéristique"
                                name="characteristic_id"
                                :options="selectedPersonnalisation?.vehicle_characteristics"
                                placeholder="Selectionnez la caractéristique"
                            />
                        </div>
                    </div>

                    <button class="btn btn-primary mt-4" type="submit">Ajouter</button>
                </Form>
            </div>

            <div class="mt-12">
                <h2 class="text-xl font-bold">Transformations Effectuées</h2>
                <div v-for="(group, transformationType) in groupedTransformations" :key="transformationType" class="mt-4">
                    <details class="group" open>
                        <summary
                            class="flex cursor-pointer list-none items-center justify-between py-4 text-lg font-medium text-secondary-900 group-open:text-primary-500">
                            {{ transformationType }}
                            <div>
                                <svg class="block h-5 w-5 group-open:hidden" fill="none" stroke="currentColor"
                                     stroke-width="1.5"
                                     viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M12 4.5v15m7.5-7.5h-15" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                                <svg class="hidden h-5 w-5 group-open:block" fill="none" stroke="currentColor"
                                     stroke-width="1.5"
                                     viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M19.5 12h-15" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            </div>
                        </summary>
                        <table>
                            <thead>
                            <tr>
                                <td>#</td>
                                <td>Catégorie</td>
                                <td>Caractéristiques</td>
                                <td>Actions</td>
                            </tr>
                            </thead>
                            <tbody>
                            <tr v-for="(transformation, key) in group" :key="transformation.characteristic_id">
                                <td>{{ key + 1 }}</td>
                                <td>{{ getCategoryLabel(transformation.category_id) }}</td>
                                <td>{{ getVehicleCaracteristiqueLabel(transformation.characteristic_id) }}</td>
                                <td>
                                    <button @click="deleteTransformation(transformation.characteristic_id, transformation.transformation_id)" class="btn btn-red btn-sm">
                                        <FontAwesomeIcon :icon="['fast', 'minus']" />
                                    </button>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </details>
                </div>
            </div>
        </div>

        <div class="text-left flex">
            <button class="btn btn-blue" @click="store.previousStep()">Précedent</button>
            <button class="btn btn-blue ms-auto" @click="next">Suivant</button>
        </div>
    </div>
</template>

<style scoped>
    table{
        @apply w-full
    }

    thead {
        @apply font-bold bg-gray-100 text-blue-900 text-sm
    }

    tr > td,
    tr > th {
        padding: 20px;
        text-align: left;
    }
</style>