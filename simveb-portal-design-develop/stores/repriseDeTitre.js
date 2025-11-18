import { defineStore } from 'pinia'
import {useApi} from "~/helpers/useApi";

const api = useApi()

const formDataHeaders = {
    ...api.defaults.headers,
    "Content-Type": "multipart/form-data",
};

export const useRepriseDeTitreStore = defineStore('repriseDeTitreStore', {
    state: () => ({
        activeStep: 0,
        vin: null,
        loading: false,
        vehicule_infos: null,
        create: null,
        attachments: [],
        title_deposit: null
    }),

    actions: {
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

        storeDemande(serviceId, npi) {
            this.saving = true

            const data = {
                npi: npi,
                vin: this.vin,
                service_id: serviceId,
                documents: this.attachments,
                deposit_id: this.vehicule_infos.title_deposits[0].id
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

        addToCart(serviceId, npi) {
            this.saving = true

            const data = {
                npi: npi,
                vin: this.vin,
                service_id: serviceId,
                documents: this.attachments,
                deposit_id: this.vehicule_infos.title_deposits[0].id
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

        resetRepriseDeTitre(){
            this.activeStep = 0
            this.vin = 0
            this.loading = true
            this.vehicule_infos = null
            this.create = null
            this.attachments =  []
            this.title_deposit =  null
        }

    },
})