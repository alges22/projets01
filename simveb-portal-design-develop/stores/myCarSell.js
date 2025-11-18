import { useApi } from "~/helpers/useApi";
import { defineStore } from 'pinia'
import { useUserStore } from './user';

const Car = {
    id: '',
    vehicle_model: '',
    vin: '',
    number_of_seats: 0,
    empty_weight: '',
    charged_weight: '',
    vehicle_model: '',
    first_circulation_year: '',
    origin_country_id: '',
    origin_country: '',
    vehicle_type_id: '',
    vehicle_type: '',
}

const api = useApi()
export const useSellStore = defineStore('sellStore', {

    state() {
        return {
            activeStep: 0,
            create: null,
            attachments: [],
            loading: false,
            choixPersonne: 'personne physique',
            nationalite: 'beninois',
            car: null,
            nipBuyer: "",

            service_id: null,

            buyer: {
                lastname: '',
                firstname: '',
                social_reason: '',
                seat: '',
                email: '',
                telephone: '',
                address: '',
                bfu: '',
                gender: '',
                ifu: '',
                origin_country: '',
                birth_date: '',
                birth_place: '',
            },

            personneMoraleBeninois: {
                numeroDenregistrement: '',
                numeroIFU: '',
                nom: '',
                telephone: '',
            },

            mutation: false
        }
    },
    actions: {
        nextStep() {
            this.activeStep++
        },

        previousStep() {
            this.activeStep--
        },

        setCar(value) {
            this.car = value
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

        getBuyerInformation() {
            this.loadPerson = true;
            this.buttonLoading = true;

            if (this.choixPersonne === 'personne morale') {
                // entreprise
                if (this.nationalite === 'beninois') {
                    // entreprise béninoise
                    return new Promise((resolve, reject) => {
                        api({
                            method: 'get',
                            url: "/get-company/" + this.personneMoraleBeninois.numeroIFU
                        })
                            .then((response) => response.data)
                            .then((response) => resolve(response))
                            .catch((error) => reject(error.response.data.message))
                    })
                } else {
                    //entreprise étrangère
                }
            } else {
                // particulier
                if (this.nationalite === 'beninois') {
                    // particulier béninois
                    return new Promise((resolve, reject) => {
                        api({
                            method: "get",
                            url: 'get-identity/' + this.nipBuyer,
                        })
                            .then((response) => response.data)
                            .then((response) => {
                                resolve(response)
                            })
                            .catch((error) => {
                                reject(error.response.data.message)
                            })
                    });
                } else {
                    //particulier étranger
                }
            }
        },

        setDataToSend() {
            let data = null
            const userStore = useUserStore()

            if (this.choixPersonne === 'personne morale') {
                // entreprise
                if (this.nationalite === 'beninois') {
                    // entreprise béninoise
                    data = {
                        'vin': this.car.vin,
                        'new_owner_ifu': this.personneMoraleBeninois.numeroIFU,
                        'npi': userStore.user.identity.npi,
                        'service_id': this.service_id
                    }
                } else {
                    //entreprise étrangère
                }
            } else {
                // particulier
                if (this.nationalite === 'beninois') {
                    // particulier béninois
                    data = {
                        'vin': this.car.vin,
                        'new_owner_npi': this.nipBuyer,
                        'npi': userStore.user.identity.npi,
                        'service_id': this.service_id
                    }
                } else {
                    //particulier étranger
                }
            }

            return data
        },

        saveDemande() {
            this.loading = true

            const data = this.setDataToSend()
            return new Promise((resolve, reject) => {
                api({
                    method: "post",
                    url: '/client/demands',
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
                        this.loading = false
                    })
            })
        },

        addToCart() {
            this.loading = true

            const data = this.setDataToSend()
            return new Promise((resolve, reject) => {
                api({
                    method: "post",
                    'url': '/client/add-demand-to-cart',
                    'data': {
                        ...data,
                        mutation: this.mutation
                    }
                })
                    .then((response) => response.data)
                    .then((response) => resolve(response))
                    .catch((error) => {
                        reject(error.response.data.message)
                    })
                    .finally(() => {
                        this.loading = false
                    })
            })
        },

        resetSaleDeclaration(){
            this.activeStep = 0
            this.service_id =  null;
            this.car =  null
        }
    }
})
