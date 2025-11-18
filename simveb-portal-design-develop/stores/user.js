import {defineStore} from 'pinia'
import spaceConfig from "~/space-config";
import {useApi} from "~/helpers/useApi";

const api = useApi()


export const useUserStore = defineStore('user', {
    state: () => ({
        user: null,
        invitations: [],
        profiles: [],
        online_profile: {},
        permissions: [],
        roles: []
    }),

    actions: {
        fetchUserData() {
            return new Promise((resolve, reject) => {
                api({
                    method: 'GET',
                    url: '/current-user'
                }).then((response) => response.data)
                    .then((response) => {
                    const online_profile = response.user.online_profile

                    this.setUser(response.user)
                    this.setInvitations(response.invitations)
                    this.setPermissions(response.permissions)
                    this.setProfiles(response.user.profiles)
                    this.setRoles(response.roles)
                    this.setOnlineProfile(online_profile)

                    resolve(response)


                    if (spaceConfig[online_profile.type.code] !== window.location.origin) {
                        if (import.meta.env.VITE_COOKIE_DOMAIN !== "localhost") {
                            window.open(spaceConfig[online_profile.type.code], "_self");
                        }
                    }
                }).catch((error) => {
                    resolve(error)
                })
            });
        },

        setUser(user) {
            this.user = user
        },

        setInvitations(invitations) {
            this.invitations = invitations
        },

        setProfiles(profiles) {
            this.profiles = profiles
        },

        setOnlineProfile(online_profile) {
            this.online_profile = online_profile
        },

        setPermissions(permissions) {
            this.permissions = permissions
        },

        setRoles(roles) {
            this.roles = roles
        }

    },
})