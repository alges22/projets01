import {defineStore} from "pinia";
import client from "@/assets/js/axios/client.js";


export const usePledgeStore = defineStore("pledge", {
    state: () => ({
        vehicle_info: null,
        owner_info: null,
        loading: false,
        errors: {},
        vin: null,
        custom_ref: null,
        files: [],
        financial_institution: null,
        create: {
            financial_institution: []
        }
    }),

    actions: {
        loadCreate(){
            this.loading = true;
            return new Promise((resolve, reject) => {
                client({
                    method: "GET",
                    url: `/pledge/create`,
                })
                    .then((response) => {
                        this.create = response.data;
                        resolve(response.data);
                    })
                    .catch((error) => {
                        this.errors = error.response.data.errors;
                        reject(error.response.data.message);
                    })
                    .finally(() => {
                        this.loading = false;
                    });
            });
        },

        getRecapInfos() {
            this.loading = true;
            return new Promise((resolve, reject) => {
                client({
                    method: "GET",
                    url: `/pledge/vehicle/owner?vin=${this.vin}&customs_ref=${this.custom_ref}`,
                })
                    .then((response) => {
                        this.vehicle_info = response.data.vehicle;
                        this.owner_info = response.data.owner;
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

        savePledge(authorization_id){
            this.loading = true;
            return new Promise((resolve, reject) => {
                client({
                    method: "POST",
                    url: `/pledge`,
                    data : {
                        vin: this.vin,
                        authorization_id: authorization_id,
                        pledge_file: this.files,
                        financial_institution: this.financial_institution
                    },
                    headers: {
                        ...client.defaults.headers,
                        "Content-Type": "multipart/form-data",
                    },
                })
                    .then((response) => {
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

        updatePledge(id){
            this.loading = true;
            return new Promise((resolve, reject) => {
                client({
                    method: "POST",
                    url: `/pledge/${id}`,
                    data : {
                        pledge_file: this.files,
                        _method: 'PUT'
                    },
                    headers: {
                        ...client.defaults.headers,
                        "Content-Type": "multipart/form-data",
                    },
                })
                    .then((response) => {
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

        updatePledgeLift(id){
            this.loading = true;
            return new Promise((resolve, reject) => {
                client({
                    method: "POST",
                    url: `/pledge-lift/${id}`,
                    data : {
                        pledge_file: this.files,
                        _method: 'PUT'
                    },
                    headers: {
                        ...client.defaults.headers,
                        "Content-Type": "multipart/form-data",
                    },
                })
                    .then((response) => {
                        resolve(response.data);
                    })
                    .catch((error) => {
                        reject(error.response.data.message);
                    })
                    .finally(() => {
                        this.loading = false;
                    });
            });
        }
    }
});