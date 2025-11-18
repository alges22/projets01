<script setup>
    import {Modal, ModalBody, ModalHeader} from "@/global-components/modal/index.js";
    import {onMounted} from "vue";
    import {useOppositionStore} from "@/stores/opposition.js";
    import SelectInputGroup from "@/components/Form/SelectInputGroup.vue";
    import {storeToRefs} from "pinia";
    import FileInputGroup from "@/components/Form/FileInputGroup.vue";
    import BasicButton from "@/components/BasicButton.vue";

    const oppositionStore = useOppositionStore()
    const { create, loading, errors, reason_for_opposition } = storeToRefs(oppositionStore)

    const props = defineProps({
        updateModal: Boolean,
    });

    const emit = defineEmits(["close", "update"]);

    onMounted(() => {
        oppositionStore.loadCreate()
    })
</script>


<template>
    <Modal size="modal-lg" is-form :show="updateModal" @submit="$emit('update')" @hidden="$emit('close')">
        <ModalHeader>Modification de l'opposition</ModalHeader>
        <ModalBody>
            <div class="my-4">
                <SelectInputGroup
                    v-model="reason_for_opposition"
                    name="reason"
                    label="Motif de l'opposition"
                    add-class="w-full"
                    :disabled="loading"
                    :errors="errors.reason_for_opposition"
                    required
                    auto-complete="vin"
                    option-text="label"
                    option-value="id"
                    :options="create?.reasons"
                />
            </div>
            <div class="my-4">
                <FileInputGroup
                    :multiple="true"
                    v-model="opposition_file"
                    :disabled="loading"
                    :errors="errors.opposition_file"
                    add-class="w-full"
                    label="Sélectionnez les pièces jointes"
                    name="opposition_file"
                    required
                />
            </div>

            <BasicButton class="btn-primary w-full" type="submit" :loading="loading"> Modifier </BasicButton>
        </ModalBody>
    </Modal>
</template>