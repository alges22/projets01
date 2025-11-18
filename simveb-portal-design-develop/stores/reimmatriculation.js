import { defineStore } from 'pinia'
import {useApi} from "~/helpers/useApi";

const api = useApi()

const formDataHeaders = {
    ...api.defaults.headers,
    "Content-Type": "multipart/form-data",
};

export const useReimmatriculationStore = defineStore('reimmatriculation', {
    state: () => ({
        activeStep: 0,
        create: null,
        loading: false,
        data_plates: {
            plate_color_id: '',
            front_plate_shape_id: '',
            back_plate_shape_id: '',
        },
        update: false,
        parameter: "default",
        saving: false,
        type: null,
        number: null,
        suggestions: null,
        label: null,
        template: "",
        desired_number: null,
        vehicule_infos: null,
        owner: null,
        reimmatriculation_reason: null,
        with_immatriculation: null,
        attachments: [],
        base_infos: {
            vin: null,
            numero_douane: null
        },
        custom_reason: ""
    }),

    actions: {
        setActiveStep(index) {
            this.activeStep = index;
        },

        nextStep() {
            this.activeStep ++;
        },

        previousStep() {
            this.activeStep --;
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
                documents: this.attachments,
                reason_id: this.reimmatriculation_reason.id
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
                ...this.data_plates,
                npi: this.owner.npi,
                vin: this.vehicule_infos.vin,
                service_id: serviceId,
                desired_number: this.number,
                label: this.label,
                documents: this.attachments,
                reason_id: this.reimmatriculation_reason.id,
                with_immatriculation: this.with_immatriculation ? 1 : 0,
                custom_reason: "Custom Reason"
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

        resetReimmatriculation(){
            this.activeStep = 0
            this.create = null
            this.loading = true

            this.data_plates =  {
                plate_color_id: '',
                front_plate_shape_id: '',
                back_plate_shape_id: '',
            };

            this.update =  false;

            this.parameter = "default"

            this.saving = false
            this.type = null
            this.number = null
            this.suggestions = null
            this.label = null
            this.template = ""
            this.desired_number = null
            this.vehicule_infos = null
            this.owner =  null
            this.reimmatriculation_reason =  null
            this.with_immatriculation =  null

            this.attachments =  []

            this.base_infos = {
                vin: null,
                numero_douane: null
            }
        }
    },
})