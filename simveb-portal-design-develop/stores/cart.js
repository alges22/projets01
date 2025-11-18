import {defineStore} from "pinia";
import {useApi} from "~/helpers/useApi";

const api = useApi()

export const useCartStore = defineStore('cart', {
    state: () => ({
        loading: false
    }),

    actions: {
        validateCart(url){
            this.loading = true

            return new Promise((resolve, reject) => {
                api({
                    method: "POST",
                    url: url
                })
                    .then((response) => response.data)
                    .then((response) => {
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

        submitOrder (reference, id){
            return new Promise((resolve, reject) => {
                api({
                    method: "POST",
                    url: `/client/submit-order`,
                    data: {
                        "payment_reference" : reference.toString(),
                        "order_id" : id
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
                        this.loading = false
                    })
            });
        }
    },
})