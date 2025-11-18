<script setup>
    import {Modal, ModalBody, ModalHeader} from "@/global-components/modal/index.js";
    import {onMounted} from "vue";
    import SelectInputGroup from "@/components/Form/SelectInputGroup.vue";
    import {storeToRefs} from "pinia";
    import FileInputGroup from "@/components/Form/FileInputGroup.vue";
    import BasicButton from "@/components/BasicButton.vue";
    import {usePledgeStore} from "@/stores/pledge.js";
    import {userHasPermissions} from "@/helpers/permissions.js";

    const pledgeStore = usePledgeStore()
    const { create, loading, errors, files } = storeToRefs(pledgeStore)

    const props = defineProps({
        updateModal: Boolean,
    });

    const { can } = userHasPermissions();

    const emit = defineEmits(["close", "update"]);

    onMounted(() => {
        pledgeStore.loadCreate()
    })
</script>


<template>
    <Modal size="modal-lg" is-form :show="updateModal" @submit="$emit('update')" @hidden="$emit('close')">
        <ModalHeader>Modification de gage </ModalHeader>
        <ModalBody>
            <div v-if="can('store-pledge-by-distributor')">
                <SelectInputGroup
                    v-model="financial_institution"
                    name="financial_institution"
                    label="Banque"
                    add-class="w-full"
                    :disabled="loading"
                    :errors="errors.financial_institution"
                    required
                    auto-complete="vin"
                    option-text="acronym"
                    option-value="id"
                    :options="create.financial_institutions"
                />
            </div>

            <div class="my-4">
                <FileInputGroup
                    :multiple="true"
                    v-model="files"
                    :disabled="loading"
                    :errors="errors.files"
                    add-class="w-full"
                    label="Sélectionnez les pièces jointes"
                    name="files"
                    required
                />
            </div>

            <BasicButton class="btn-primary w-full" type="submit" :loading="loading"> Modifier </BasicButton>
        </ModalBody>
    </Modal>
</template>