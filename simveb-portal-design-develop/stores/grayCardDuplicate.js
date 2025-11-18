import { defineStore } from 'pinia'
import {useApi} from "~/helpers/useApi";

const api = useApi()

export const useGrayCardDuplicateStore = defineStore('grayCardDuplicate', {
    state: () => ({
        activeStep: 0,
        loading: false,
        saving: false,
        car: null,
    }),

    actions: {
        nextStep() {
            this.activeStep++
        },

        previousStep() {
            this.activeStep--
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

        addToCart(serviceId, npi, data) {
            const { vin, is_lost, is_spoiled } = data

            this.saving = true

            return new Promise((resolve, reject) => {
                api({
                    method: "POST",
                    url: `/client/add-demand-to-cart`,
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

    },
})