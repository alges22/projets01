import { defineStore } from 'pinia'

export const useLoginStore = defineStore('login', {
    state: () => ({ npi: "" }),

    actions: {
        setNpi(npi){
            this.npi = npi
        }
    },
})