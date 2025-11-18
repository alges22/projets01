import { defineStore } from "pinia";
import { useApi } from "~/helpers/useApi";

const api = useApi();

interface UserPayloadInterface {
    username: string;
    password: string;
    grant_type: string;
    client_id: string;
    client_secret: string;
}

const setTokenCookie = (tokenValue: string) => {
    const token = useCookie('token', {
        path: '/',
        domain: import.meta.env.VITE_COOKIE_DOMAIN
    });

    token.value = tokenValue
}

const removeTokenCookie = () => {
    const token = useCookie('token', {
        path: '/',
        domain: import.meta.env.VITE_COOKIE_DOMAIN
    });

    token.value = null
}


export const useAuthStore = defineStore('auth', {
    state: () => ({
        authenticated: false,
        loading: false,
    }),
    actions: {
        async authenticateUser(data: UserPayloadInterface) {
            this.loading = true;

            return new Promise<void>((resolve, reject) => {
                api({
                    method: "POST",
                    url: '/login/',
                    data: data
                }).then((response) => {
                    if (response.status === 200) {
                        if (response.data) {
                            const token = useCookie('token'); // useCookie new hook in nuxt 3
                            setTokenCookie(response?.data?.access_token)
                            this.authenticated = true; // set authenticated  state value to true
                            resolve(); // resolve the promise
                        }
                    }
                }).catch((error) => {
                    reject(error);
                }).finally(() => {
                    this.loading = false;
                });
            });
        },

        async switchProfile(id : string){
            return new Promise<void>((resolve, reject) => {
                api({
                    method: "PUT",
                    url: '/change-space',
                    data: {
                        profile_id : id
                    }
                }).then((response) => {
                    if (response.status === 200) {
                        resolve(response.data);
                    }
                }).catch((error) => {
                    reject(error);
                })
            });
        },

        logUserOut() {
            this.authenticated = false;

            removeTokenCookie()

            window.location.href = '/'
        },
    },
});
