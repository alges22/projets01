<script lang="ts" setup>
    import {FontAwesomeIcon} from "@fortawesome/vue-fontawesome";
    import { useDepotDeTitreStore } from '~/stores/depotDeTitre'
    import {storeToRefs} from "pinia";

    const store = useDepotDeTitreStore()

    let { create, attachments, loading } = storeToRefs(store)


    const next = () => {
        // Check if all required documents are provided
        const requiredDocumentsIds = create?.value.required_documents.map(piece => piece.id);
        const attachedDocumentsIds = attachments.value.map(item => item.type_id);
        const allRequiredDocumentsProvided = requiredDocumentsIds.every(id => attachedDocumentsIds.includes(id));

        if (!allRequiredDocumentsProvided) {
            // If not all required documents are provided, show an alert or handle it accordingly
            alert("Please provide all required documents.");
            return; // Prevent proceeding to the next step
        }

        store.nextStep()
    }

    // Function to handle file input change
    const handleFileChange = (event, typeId) => {
        const target = event.target;
        const files = target.files;
        if (files && files.length > 0) {
            // Assuming only one file is selected, you can modify it if multiple files are allowed
            const file = files[0];
            // Check if a file with the same type_id already exists
            const existingFileIndex = attachments.value.findIndex(item => item.type_id === typeId);
            if (existingFileIndex !== -1) {
                // If a file with the same type_id exists, replace it with the new file
                attachments.value[existingFileIndex] = { type_id: typeId, file };
            } else {
                // If no file with the same type_id exists, add the new file to the array
                attachments.value.push({ type_id: typeId, file });
            }
        }
    }
</script>

<template>
    <div class="card p-4 md:p-16 mt-4 text-center">
        <h4 class="text-blue font-bold text-5xl">Pièces jointes</h4>

        <h4 class="text-primary font-bold text-md mt-4">Veuillez télécharger les fichiers suivants</h4>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-8 text-left w-4/5 mx-auto mt-8">
            <div v-for="piece in create?.required_documents">
                <label :for="piece.id">{{ piece.description }}</label>
                <input type="file" class="form-control" :id="piece.id" @change="event => handleFileChange(event, piece.id)">
            </div>
        </div>

        <div class="mt-16 flex-row flex">
            <button class="text-blue font-bold text-2xl" @click="store.previousStep()">
                <font-awesome-icon class="mx-4" :icon="['fas', 'arrow-left']" />
                Précédent
            </button>

            <div class="ms-auto">
                <!--                <button class="btn-outline-blue mx-4" @click="store.storeDemande(id)" :disabled="saving">-->
                <!--                    <span class="font-bold text-2xl">Enregistrer</span>-->
                <!--                </button>-->

                <button class="btn-blue mx-4" :disabled="loading" @click="next">
                    <span class="font-bold text-2xl">Suivant</span>
                </button>
            </div>
        </div>
    </div>

</template>

<style scoped>

</style>