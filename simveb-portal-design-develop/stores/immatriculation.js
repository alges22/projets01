import { defineStore } from 'pinia'
import {useApi} from "~/helpers/useApi";

const api = useApi()

const formDataHeaders = {
    ...api.defaults.headers,
    "Content-Type": "multipart/form-data",
};

export const useImmatriculationStore = defineStore('immatriculation', {
    state: () => ({
        activeStep: 0,
        update: false,
        number: null,
        suggestions: null,
        label: null,
        template: "",
        base_infos: {},
        owner: null,
        vehicule_infos: null,
        create: null,
        loading: true,
        saving: false,
        data_plates: {
            plate_color_id: '',
            front_plate_shape_id: '',
            back_plate_shape_id: '',
        },
        attachments: []
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

        getOwner(npi){
            this.loading = true

            return new Promise((resolve, reject) => {
                api({
                    method: "GET",
                    url: `/get-identity/${npi}`
                })
                    .then((response) => response.data)
                    .then((response) => {
                        this.owner = response;
                        resolve(response);
                    })
                    .catch((error) => {
                        reject(error.response.data.message);
                    })
                    .finally(() => {
                        this.loading = false
                    })
            });
        },

        storeDemande(serviceId) {
            this.saving = true

            const data = {
                ...this.data_plates,
                npi: this.owner.npi,
                vin: this.vehicule_infos.vin,
                service_id: serviceId,
                desired_number: this.number,
                label: this.label,
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

        updateDemande(demandId) {
            this.saving = true

            const data = {
                ...this.data_plates,
                label: this.label,
                documents: this.attachments,
                desired_number: this.number,
                _method: 'PUT'
            };

            return new Promise((resolve, reject) => {
                api({
                    method: "POST",
                    url: `/client/demands/${demandId}`,
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
                ...this.data_plates,
                npi: this.owner.npi,
                vin: this.vehicule_infos.vin,
                service_id: serviceId,
                desired_number: this.number,
                label: this.label,
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

        resetImmatriculation(){
            this.activeStep = 0
            this.base_infos = {}
            this.owner =  null
            this.vehicule_infos = null
            this.create = null
            this.loading = true
            this.saving = false
            this.suggestions = null
            this.number = null
            this.label = null
            this.template = ""
            this.update = false

            this.data_plates = {
                plate_color_id: '',
                    front_plate_shape_id: '',
                    back_plate_shape_id: '',
            }
        }
    },
})