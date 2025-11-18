import {defineStore} from "pinia";
import {useApi} from "~/helpers/useApi";

const api = useApi()

const formDataHeaders = {
    ...api.defaults.headers,
    "Content-Type": "multipart/form-data",
};

export const useVehiculeTransformation = defineStore('vehiculeTransformation', {
    state: () => ({
        activeStep: 0,
        transformations: [],
        create: null,
        car: null,
        attachments: [],
        loading: true,
        update: false,
        vehicule_infos: null,
        transformation: null
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

        updateDemande(demandId) {
            this.saving = true

            const data = {
                ...this.attachments,
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

        addToCart(serviceId, npi) {
            this.saving = true

            const data = {
                vin: this.car.vin,
                npi: npi,
                service_id: serviceId,
                documents: this.attachments,
                value_id : this.transformations.map((transformation) => {
                    return transformation.characteristic_id
                })
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

        resetVehicleTransformation(){
            this.activeStep = 0
            this.car = null
            this.loading = true
            this.transformations = []
            this.create = null
            this.attachments =  []
        }
    }
})