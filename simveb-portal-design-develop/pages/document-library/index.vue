<script setup>
    import { ref, onMounted } from 'vue';
    import { FontAwesomeIcon } from "@fortawesome/vue-fontawesome";
    import { useApi } from "~/helpers/useApi";
    import ImportFile from "~/components/document-library/ImportFile.vue";
    import dayjs from "dayjs";
    import ModalComponent from "~/components/ModalComponent.vue";

    const open = ref(false);
    const url = ref(false)

    const api = useApi();

    const file = ref(null)

    const files = ref([]);
    const loading = ref(true);

    onMounted(() => {
        loadFiles()
    });

    const loadFiles = () => {
        api({
            method: 'GET',
            url: '/simveb-file'
        }).then((response) => response.data)
            .then((response) => {
                files.value = response.data;
            })
            .finally(() => {
                loading.value = false;
            });
    }

    const editFile = (fileToUpdate) => {
        file.value = fileToUpdate;

        open.value = true;
    }

    const openFile = (link) => {
        url.value = link

        open.value = true;
    }
</script>

<template>
    <div class="p-8">
        <div class="flex mb-6 w-full">
            <div class="flex items-center">
                <h1 class="text-5xl font-bold">Documenthèque</h1>
            </div>
            <div class="ml-auto">
                <button class="btn-primary" @click="open = true">
                    <FontAwesomeIcon :icon="['fa', 'file-upload']"/>
                    Téléverser un document
                </button>
            </div>
        </div>

        <ImportFile :file="file" :open="open" @close="open = false" @saved="loadFiles" />

        <!--        <ModalComponent :open="open" @close="open = !open">-->
        <!--            <PdfViewer path="/dist" :url="url" />-->
        <!--        </ModalComponent>-->

        <div v-if="loading">
            <Loader/>
        </div>
        <div v-else class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-8">
            <div
                class="bg-white rounded-lg p-4 shadow-lg"
                v-for="(file, index) in files"
                :key="index"
            >
                <div>
                    <div class="px-4 flex justify-end">
<!--                        <button @click="openFile(file.url)">-->
                        <a :href="file.url" target="_blank">
                            <FontAwesomeIcon :icon="['fa', 'fa-eye']" class="px-2"/>
                        </a>
                        <button @click="editFile(file)">
                            <FontAwesomeIcon :icon="['fa', 'fa-pencil']" class="px-2"/>
                        </button>
                        <!--                        <a href="">-->
                        <!--                            <FontAwesomeIcon :icon="['fa', 'fa-trash']" class="px-2"/>-->
                        <!--                        </a>-->
                    </div>
                    <div class="p-4 my-2     bg-gray-200 rounded-lg h-40">
                        <!-- Dynamically assign a unique ID to each canvas -->
                    </div>
                    <div class="justify-center flex flex-col gap-y-1">
                        <h2 class="text-sm font-semibold">{{ file.path.name }}  {{ file.size ? `(${file.size})` : null }} </h2>
                        <p class="text-sm text-gray-500">{{ file.file_type?.description }}</p>
                        <p class="text-sm text-gray-500">Crée le : {{ dayjs(file.created_at).format('DD/MM/YYYY HH:mm') }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
