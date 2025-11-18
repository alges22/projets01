import {defineStore} from "pinia";
import {useApi} from "~/helpers/useApi";

const api = useApi();
export const useServiceStore = defineStore('service', {
    state: () => {
        return {
            services: [],
            service: null,
            loading: true,
            links: [
                {
                    code: 'IMMATRICULATION_STANDARD',
                    link: 'services/immatriculation-number/'
                },
                {
                    code: 'IMMATRICULATION_PRESTIGE_NUMBER',
                    link: 'services/immatriculation-number-prestige/'
                },
                {
                    code: 'IMMATRICULATION_PRESTIGE_LABEL',
                    link: 'services/immatriculation-prestige-label/'
                },
                {
                    code: 'IMMATRICULATION_PRESTIGE_NUMBER_LABEL',
                    link: 'services/immatriculation-prestige-numero-label/'
                },
                {
                    code: 'MUTATION',
                    link: '/services/mutation/'
                },
                {
                    code: 'SALE_DECLARATION',
                    link: '/services/sales-declaration/'
                },
                {
                    code: 'TITLE_DEPOSIT',
                    link: '/services/depot-de-titre/'
                },
                {
                    code: 'TITLE_RECOVERY',
                    link: '/services/reprise-de-titre/'
                },
                {
                    code: 'RE_IMMATRICULATION',
                    link: '/services/reimmatriculation/'
                },
                {
                    code: 'VEHICLE_TRANSFORMATION',
                    link: '/services/vehicule-transformation/'
                },
                {
                    code: 'TINTED_WINDOW_AUTHORIZATION',
                    link: '/services/tinted-windows/'
                },
                {
                    code: 'GRAY_CARD_DUPLICATE',
                    link: '/services/gray-card-duplicate/'
                },
                {
                    code: 'PLATE_DUPLICATE',
                    link: '/services/plate-duplicate/'
                },
                {
                    code: 'GLASS_ENGRAVING',
                    link: '/services/plate-engraving/'
                }
            ]
        }
    },
    actions: {
        getServices(code = null) {
            this.loading = true;

            return new Promise((resolve, reject) => {
                if (code !== ""){
                    api({
                        method: 'GET',
                        url: `/client/services/${code}`
                    }).then((response) => response.data)
                        .then((response) => {
                            this.service = response
                            this.services = response.children

                            this.loading = false;
                        })
                }else{
                    api({
                        method: "GET",
                        url: '/client/services',
                    })
                        .then((response) => response.data)
                        .then((response) => {
                            this.services = response
                            this.loading = false;
                        })
                        .catch((error) => {
                            reject(error.response.data.message)
                        })
                }

            })
        },

        getLinksOfService(service) {
            const {code, id} = service

            const _link = this.links.find((element) => element.code === code)

            if (_link)
                return _link.link + id

            return ''
        },

        getEditLink({code, id}) {
            const _link = this.links.find((element) => element.code === code)

            if (_link)
                return _link.link + 'edit/' + id
            return ''
        },
    }
})