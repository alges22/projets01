import { defineStore } from 'pinia'
import {useApi} from "~/helpers/useApi";

const api = useApi()

export const usePlateDuplicate = defineStore('plateDuplicate', {
    state: () => ({
        create: null,
        activeStep: 0,
        loading: false,
        saving: false,
        car: null,
        front_plate: null,
        back_plate: null
    }),

    actions: {
        nextStep() {
            this.activeStep++
        },

        previousStep() {
            this.activeStep--
        },

        fetchPlates(id) {
            this.loading = true;

            return new Promise((resolve, reject) => {
                api({
                    method: "GET",
                    url: `/vehicles/${id}/plates`,
                })
                    .then((response) => {
                        this.front_plate = response.data.front_plate;
                        this.back_plate = response.data.back_plate;
                        
                        resolve(response.data);
                    })
                    .catch((error) => {
                        reject(error.response.data.message);
                    })
                    .finally(() => {
                        this.loading = false;
                    });
            });
        },

        storeDemande(serviceId, npi, data) {
            const { vin, is_lost, is_spoiled } = data

            this.saving = true

            return new Promise((resolve, reject) => {
                api({
                    method: "POST",
                    url: `/client/demands`,
                    data: {
                        service_id: serviceId,
                        npi: npi,
                        vin: vin,
                        is_lost: is_lost,
                        is_spoiled: is_spoiled
                    }
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

        addToCart(serviceId, npi, values) {

            this.saving = true

            return new Promise((resolve, reject) => {
                api({
                    method: "POST",
                    url: `/client/add-demand-to-cart`,
                    data: {
                        service_id: serviceId,
                        npi: npi,
                        ...values
                    }
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