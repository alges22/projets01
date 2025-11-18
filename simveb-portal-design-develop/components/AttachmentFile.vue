<script setup>
    import {ref} from 'vue';
    import {FontAwesomeIcon} from "@fortawesome/vue-fontawesome";
    import ModalComponent from "~/components/ModalComponent.vue";

    const open = ref(false)

    const props = defineProps({
        typeId: Number,
        label: String,
        id: {
            type: String,
            default: 1514417414171,
        },
        url: {
            type: String,
            default: "http://localhost:8001/storage/immatriculation-standard/96Q4gZ50XED5wp0r.pdf",
        }
    });

    const useId = ref(false)

    const emit = defineEmits(['file-change']);

    const fileInputRef = ref(null);

    const handleFileChange = (event) => {
        const target = event.target;
        const files = target.files;
        if (files && files.length > 0) {
            const file = files[0];
            emit('file-change', {type_id: props.typeId, file});
        }
    };
</script>

<template>
    <ModalComponent :open="open" @close="open = !open">
        <PdfViewer path="/dist" :url="url" />
    </ModalComponent>

    <div class="file-input-container">
        <label :for="'attachment-' + typeId">{{ label }} <span class="text-red-500">*</span> </label>
        <input
            :disabled="useId"
            :id="'attachment-' + typeId"
            ref="fileInputRef"
            class="form-control"
            type="file"
            @change="handleFileChange"
        />

<!--        <div class="mt-4 flex">-->
<!--            <div class="ml-auto">-->
<!--                <button v-if="!useId" @click="useId = true" class="btn-sm btn-primary mx-1">Utiliser ceci</button>-->
<!--                <button class="btn-sm btn-primary" @click="open = !open"><FontAwesomeIcon :icon="['fa', 'fa-eye']" class="px-2"/></button>-->
<!--                <button v-if="useId" @click="useId = false" class="btn-sm btn-red mx-1"><FontAwesomeIcon :icon="['fa', 'fa-times']" class="px-2"/></button>-->
<!--            </div>-->
<!--        </div>-->
    </div>
</template>