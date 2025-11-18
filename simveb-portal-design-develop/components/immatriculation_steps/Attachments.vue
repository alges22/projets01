<script setup>
    import { FontAwesomeIcon } from "@fortawesome/vue-fontawesome";
    import { useImmatriculationStore } from "~/stores/immatriculation";

    const props = defineProps({
        type: String,
    });

    const route = useRoute();
    const id = route.params.id;

    const { $awn } = useNuxtApp();

    const store = props.type === "reimmatriculation" ? useReimmatriculationStore() : useImmatriculationStore();

    let { create, attachments, loading, update } = storeToRefs(store);

    const next = () => {
        const requiredDocumentsIds = create?.value.required_documents.map((piece) => piece.id);
        const attachedDocumentsIds = attachments.value.map((item) => item.type_id);
        const allRequiredDocumentsProvided = requiredDocumentsIds.every((id) => attachedDocumentsIds.includes(id));

        if (!allRequiredDocumentsProvided) {
            $awn.alert("Veuillez joindre toutes les pièces");
            return;
        }

        if (update.value) {
            store
                .updateDemande(id)
                .then(() => {
                    $awn.success("Demande modifiée avec succès");
                    navigateTo("/file-status");
                })
                .catch((error) => {
                    $awn.alert(error);
                });
        } else {
            store.nextStep();
        }
    };

    const handleFileChange = ({ type_id, file }) => {
        const existingFileIndex = attachments.value.findIndex((item) => item.type_id === type_id);
        if (existingFileIndex !== -1) {
            attachments.value[existingFileIndex] = { type_id: type_id, file };
        } else {
            attachments.value.push({ type_id: type_id, file });
        }
    };
</script>

<template>
    <div class="card p-4 md:p-16 mt-4 text-center">
        <h4 class="text-blue font-bold text-5xl">Pièces jointes</h4>

        <h4 class="text-primary font-bold text-md mt-4">Veuillez télécharger les fichiers suivants</h4>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-8 text-left w-4/5 mx-auto mt-8">
            <AttachmentFile
                v-for="piece in create?.required_documents"
                :key="piece.id"
                :typeId="piece.id"
                :label="piece.description"
                @file-change="handleFileChange"
            />
        </div>

        <div class="mt-16 flex-row flex">
            <button class="text-blue font-bold text-2xl" @click="store.previousStep()">
                <font-awesome-icon :icon="['fas', 'arrow-left']" class="mx-4"/>
                Précédent
            </button>

            <div class="ms-auto">
                <!--                <button class="btn-outline-blue mx-4" @click="store.storeDemande(id)" :disabled="saving">-->
                <!--                    <span class="font-bold text-2xl">Enregistrer</span>-->
                <!--                </button>-->

                <button :disabled="loading" class="btn-blue mx-4" @click="next">
                    <span class="font-bold text-2xl">
                        <template v-if="update">
                            Modifier la demande
                        </template>
                        <template v-else>
                            Suivant
                        </template>
                    </span>
                </button>
            </div>
        </div>
    </div>

</template>

<style scoped>

</style>