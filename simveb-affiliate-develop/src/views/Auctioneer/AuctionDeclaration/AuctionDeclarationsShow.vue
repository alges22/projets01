<script setup>
    import {useRoute} from "vue-router"
    import {onMounted, ref} from "vue"
    import client from "@/assets/js/axios/client.js";
    import dayjs from "dayjs";
    import DataTable from "@/components/DataTable.vue";
    import TextInputGroup from "@/components/Form/TextInputGroup.vue";
    import BasicButton from "@/components/BasicButton.vue";
    import Alert from "@/components/notification/alert.js";
    import {Modal, ModalBody, ModalFooter} from "@/global-components/modal/index.js";
    import SelectInputGroup from "@/components/Form/SelectInputGroup.vue";
    import FileInputGroup from "@/components/Form/FileInputGroup.vue";

    const route = useRoute()
    const id = route.params.id

    const loading = ref(true)
    const loadingOfficial = ref(false)
    const loadingVehicle = ref(false)

    const auctionDeclaration = ref(null)

    const create = ref({
        institutions: []
    })

    const vehicule = ref({
        id: null,
        vin: null,
        npi: null,
        prix: null,
        update: false
    })

    const officiel = ref({
        npi: null,
        fonction: null,
        update: false
    })

    const infos = ref({
        institution_id: null,
        report: null
    })

    const modalIsOpen = ref(false);
    const saving = ref(false);

    const headers = [
        {key: "vehicle", title: "VIN du véhicule", sortable: false},
        {key: "buyer_npi", title: "Acheteur", sortable: true},
        {key: "price", title: "Prix de vente du véhicule", sortable: false},
    ];

    const headersOfficiels = [
        {key: "npi", title: "NPI", sortable: false},
        {key: "full_name", title: "Nom complet", sortable: true},
        {key: "title", title: "Fontion", sortable: true}
    ];

    const formDataHeaders = {
        ...client.defaults.headers,
        "Content-Type": "multipart/form-data",
    };

    const submit = () => {
        saving.value = true

        client({
            url: `/auction-sale-declarations/${auctionDeclaration.value.id}`,
            method: 'POST',
            data: {
                ...infos.value,
                _method: 'PUT'
            },
            headers: formDataHeaders
        }).then((response) => response.data)
            .then((response) => {
                loadAuctionDeclaration()
            })
            .finally(() => {
                saving.value = false
            })
    };

    const handleUpdateInfos = () => {
        modalIsOpen.value = true
    }

    const handleUpdateVehicule = vehicle => {
        vehicule.value = {
            id: vehicle.id,
            npi: vehicle.buyer_npi,
            prix: vehicle.price,
            vin: vehicle.vehicle.vin,
            update: true
        }
    }

    const addVehicule = async () => {
        if (!vehicule.value.update){
            const found = auctionDeclaration.value.saled_vehicles.find((element) => element.vehicle.vin === vehicule.value.vin)

            if (found) {
                Alert.error("Ce véhicule à déjà été ajouté");
                return
            }
        }

        const data = {
            vehicle_vin: vehicule.value.vin,
            buyer_npi: vehicule.value.npi,
            price: vehicule.value.prix
        }

        loadingVehicle.value = true

        const response = vehicule.value.update ? client({
            url: `/auction-sale-vehicles/${vehicule.value.id}`,
            method: 'PUT',
            data: data
        }) : client({
            url: `/auction-sale-declarations/${auctionDeclaration.value.id}/add-vehicle`,
            method: 'PUT',
            data: data
        })


        response
            .then((response) => response.data)
            .then((response) => {
                vehicule.value = {
                    vin: null,
                    npi: null,
                    prix: null,
                    update: false
                }

                loading.value = true

                loadAuctionDeclaration()
            })
            .finally(() => {
                loadingVehicle.value = false
            })
    }

    const addOfficiel = async () => {
        const found = auctionDeclaration.value.official_identities.find((element) => element.npi === officiel.value.npi)

        if (found) {
            Alert.error("Cet officiel à déjà été ajouté");
            return
        }

        loadingOfficial.value = true
        client({
            url: `/auction-sale-declarations/${auctionDeclaration.value.id}/add-official`,
            method: 'PUT',
            data: {
                npi: officiel.value.npi,
                title: officiel.value.fonction
            }
        })
            .then((response) => response.data)
            .then((response) => {
                officiel.value = {
                    npi: null,
                    fonction: null
                }

                loading.value = true

                loadAuctionDeclaration()
            })
            .finally(() => {
                loadingOfficial.value = false
            })
    }

    const generateCertificat = () => {
        client({
            method: 'GET',
            url: `/auction-sale-declarations/${id}/generate-certificate`,
            responseType: "blob",
        }).then((response) => response.data)
            .then((response) => {
                const href = URL.createObjectURL(response);
                const link = document.createElement("a");
                link.href = href;
                link.setAttribute(
                    "download",
                    `${id}.pdf`
                );
                document.body.appendChild(link);
                link.click();

                document.body.removeChild(link);
                URL.revokeObjectURL(href);
            })
    }

    const loadAuctionDeclaration = () => {
        client({
            method: 'GET',
            url: `/auction-sale-declarations/${id}`
        }).then((response) => response.data)
            .then((response) => {
                auctionDeclaration.value = response;

                infos.value = {
                    institution_id: response.institution_id,
                    report: null
                }

                loading.value = false
            })
    }

    const deleteVehicle = (id) => {
        client({
            method: 'PUT',
            url: `/auction-sale-declarations/${auctionDeclaration.value.id}/remove-vehicle`,
            data: {
                "vehicle_id": id
            }
        }).then((response) => response.data)
            .then((response) => {
                loading.value = true

                loadAuctionDeclaration()
            })
    }

    const deleteOfficial = (npi) => {
        client({
            method: 'PUT',
            url: `/auction-sale-declarations/${auctionDeclaration.value.id}/remove-official`,
            data: {
                npi: npi
            }
        }).then((response) => response.data)
            .then((response) => {
                loading.value = true

                loadAuctionDeclaration()
            })
    }

    onMounted(() => {
        loadAuctionDeclaration()

        client({
            method: 'GET',
            url: '/auction-sale-declarations/create'
        }).then((response) => response.data)
            .then((response) => {
                create.value = response
            })
    })
</script>

<template>
    <Modal :show="modalIsOpen" is-form @hidden="modalIsOpen = false" @submit="submit">
        <ModalBody>
            <div class="flex flex-col justify-between mx-4">
                <span class="text-xl font-bold text-center mb-4">Modifier les informations</span>

                <div class="mt-4">
                    <SelectInputGroup required :options="create.institutions" v-model="infos.institution_id" name="institutions" label="Institutions" />
                </div>
                <div class="mt-4">
                    <FileInputGroup name="report" label="Rapport" v-model="infos.report" />
                </div>
            </div>
        </ModalBody>
        <ModalFooter class="">
            <div class="flex align-center justify-end mb-2">
                <BasicButton class="btn-primary w-full" type="submit" :loading="saving"> Suivant </BasicButton>
            </div>
        </ModalFooter>
    </Modal>

    <div class="flex flex-row">
        <button class="btn btn-primary mt-8 ml-auto" @click="generateCertificat">Générer le certificat</button>
    </div>
    <div v-if="!loading" class="bg-white mt-2 p-2 xl:p-4 rounded-md">
        <div class="bg-blue-100 p-4">
            <span class="font-bold text-lg text-blue-500 rounded-md">Informations de la déclaration</span>

            <button @click="handleUpdateInfos" class="btn btn-warning ml-4"><i class="fa fa-edit"></i></button>
        </div>
        <table class="table mt-5">
            <tr v-if="auctionDeclaration?.institution">
                <th>Instituion</th>
                <th> {{ auctionDeclaration?.institution?.name }}</th>
            </tr>
            <tr>
                <td>Rapport</td>
                <th>
                    {{ auctionDeclaration.report_path.name }}
                    <a :href="auctionDeclaration.report" class="btn btn-primary ml-4" target="_blank"><i
                        class="fa fa-download"></i></a>
                </th>
            </tr>
        </table>

        <div class="bg-blue-100 p-4 mt-8">
            <span class="font-bold text-lg text-blue-500 rounded-md">Véhicules demandés</span>
        </div>

        <div class="grid grid-cols-12 mt-8 gap-3">
            <div class="col-span-3">
                <TextInputGroup v-model="vehicule.vin" label="VIN du véhicule" name="vin"/>
            </div>

            <div class="col-span-3">
                <TextInputGroup v-model="vehicule.npi" label="NPI de l'acheteur" name="npi"/>
            </div>

            <div class="col-span-3">
                <TextInputGroup v-model="vehicule.prix" label="Prix de vente du véhicule" name="prix"/>
            </div>

            <div class="flex items-end">
                <button :disabled="loadingVehicle" class="btn btn-primary" @click="addVehicule">
                    <span v-if="vehicule.update">Modifier</span>
                    <span v-else>Ajouter</span>
                </button>
            </div>
        </div>

        <DataTable
            :create-button="false"
            :headers="headers"
            :items="auctionDeclaration.saled_vehicles"
            :loading="loading"
            empty-text="Aucune donnée trouvé"
            header-class="uppercase text-start"
        >
            <template #vehicle="{ item }">
                {{ item.vehicle.vin }}
            </template>
            <template #actions="{ item }">
                <button class="btn btn-warning" @click="handleUpdateVehicule(item)"><i class="fa fa-edit"></i></button>
                <button class="btn btn-danger mx-1" @click="deleteVehicle(item.vehicle.id)"><i class="fa fa-minus"></i></button>
            </template>
        </DataTable>

        <div class="bg-blue-100 p-4 mt-8">
            <span class="font-bold text-lg text-blue-500 rounded-md">Officiels présents</span>
        </div>
        <div class="grid grid-cols-12 mt-8 gap-3">
            <div class="col-span-4">
                <TextInputGroup name="npi" v-model="officiel.npi" label="NPI de l'officiel" />
            </div>

            <div class="col-span-4">
                <TextInputGroup name="fonction" v-model="officiel.fonction" label="Fonction de l'officiel" />
            </div>

            <div class="flex items-end">
                <button class="btn btn-primary" @click="addOfficiel" :disabled="loadingOfficial">Ajouter</button>
            </div>
        </div>
        <DataTable
            :create-button="false"
            :headers="headersOfficiels"
            :items="auctionDeclaration.official_identities"
            :loading="loading"
            empty-text="Aucune donnée trouvé"
            header-class="uppercase text-start"
        >
            <template #actions="{ item }">
<!--                <button class="btn btn-warning"><i class="fa fa-edit"></i></button>-->
                <button class="btn btn-danger mx-1" @click="deleteOfficial(item.npi)"><i class="fa fa-minus"></i></button>
            </template>
        </DataTable>
    </div>
</template>