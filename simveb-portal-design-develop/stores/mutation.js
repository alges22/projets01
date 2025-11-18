import { defineStore } from 'pinia'
import {useApi} from "~/helpers/useApi";

const api = useApi()

const formDataHeaders = {
    ...api.defaults.headers,
    "Content-Type": "multipart/form-data",
};

const userStore = useUserStore()

const { user } = storeToRefs(userStore)

export const useMutationStore = defineStore('mutation', {
    state: () => ({
        loading: false,
        activeStep: 0,
        create: null,
        saving: false,
        saleDeclaration: null,
        attachments: [],
        buyer: null
    }),

    actions: {
        resetMutation(){
            this.activeStep = 0
            this.create = null
            this.attachments = null
            this.saleDeclaration = null
        },

        nextStep() {
            this.activeStep++
        },

        previousStep() {
            this.activeStep--
        },

        loadCreate(serviceId){
            this.loading = true

            return new Promise((resolve, reject) => {
                api({
                    method: "GET",
                    url: `/client/demands/create/${serviceId}`
                })
                    .then((response) => response.data)
                    .then((response) => {
                        this.create = response;
                        resolve(response);
                    })
                    .catch((error) => {
                        reject(error.response.data.message);

                        throw showError({
                            statusCode: 404,
                            statusMessage: 'Page Not Found'
                        })
                    })
                    .finally(() => {
                        this.loading = false
                    })
            });
        },

        storeDemande(serviceId) {
            this.saving = true

            const data = {
                npi: user.value.identity.npi,
                sale_declaration_reference: this.saleDeclaration.reference,
                vin: this.saleDeclaration.sold_vehicle.vin,
                service_id: serviceId,
                documents: this.attachments
            };

            return new Promise((resolve, reject) => {
                api({
                    method: "POST",
                    url: `/client/demands`,
                    headers: formDataHeaders,
                    data: data
                })
                    .then((response) => response.data)
                    .then((response) => {
                        resolve(response);
                    })
                    .catch((error) => {
                        reject(error.response.data.message);
                    })
                    .finally(() => {
                        this.saving = false
                    })
            });
        },

        addToCart(serviceId) {
            this.saving = true

            const data = {
                npi: this.saleDeclaration.buyer.npi,
                sale_declaration_reference: this.saleDeclaration.reference,
                vin: this.saleDeclaration.sold_vehicle.vin,
                service_id: serviceId,
                documents: this.attachments
            };

            return new Promise((resolve, reject) => {
                api({
                    method: "POST",
                    url: `/client/add-demand-to-cart`,
                    data: data,
                    headers: formDataHeaders,
                })
                    .then((response) => response.data)
                    .then((response) => {
                        resolve(response);
                    })
                    .catch((error) => {
                        reject(error.response.data.message);
                    })
                    .finally(() => {
                        this.saving = false
                    })
            });
        },
    },
})