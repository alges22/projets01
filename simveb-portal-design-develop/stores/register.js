import { defineStore } from 'pinia'

export const useRegisterStore = defineStore('register', {
    state: () => ({ activeStep: 0, npi: "", ifu: "", person_type: "", email: "", user_data : {}, company_data : {} }),

    actions: {
        nextStep() {
            this.activeStep++
        },

        previousStep() {
            this.activeStep--
        },

        updateInfos(values){
            this.npi = values.npi
            this.person_type = values.person_type
            this.email = values.email
            this.ifu = values.ifu
        },

        setUserData (userData){
            this.user_data = userData
        },

        setCompanyData (companyData){
            this.company_data = companyData
        },


        reset(){
            this.activeStep = 0;
            this.npi = "";
            this.person_type = "";
            this.email = "";
            this.user_data = {}
        }
    },
})