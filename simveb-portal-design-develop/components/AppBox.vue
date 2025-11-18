<script setup>
	import { FontAwesomeIcon } from "@fortawesome/vue-fontawesome";
	import AppIcon from "~/components/AppIcon.vue";
	import { useUserStore } from "~/stores/user";
	import spaceConfig from "~/space-config";

	defineProps({
        open: Boolean,
    });

    const { $awn } = useNuxtApp()

    const userStore = useUserStore()
    const authStore = useAuthStore()

    const { online_profile, profiles } = storeToRefs(userStore);

    const userProfiles = computed(() => {
        return profiles.value.filter((profile) => profile.type.code === "user");
    });

    const otherProfiles = computed(() => {
        return profiles.value.filter((profile) => profile.type.code !== "user");
    });

    const profileIcon = (profileCode) => {
        switch (profileCode) {
            case "affiliate":
                return "fa-users";
            case "interpol":
                return "fa-user-shield";
            case "police":
                return "fa-user-police-tie";
            case "bank":
                return "fa-bank";
            case "anatt":
                return "fa-users-gear";
            case "central_garage":
                return "fa-star";
            case "approved":
                return "fa-user-check";
            case "gma":
                return "fa-users-rays";
            case "gmd":
                return "fa-building-columns";
            case "court":
                return "fa-gavel";
            case "distributor":
                return "fa-car";
            case "clerk":
                return "fa-scale-balanced";
            default:
                return "fa-user";
        }
    };

    const switchUser = async (profile) => {
        // if (profile.type.code !== "user" || profile.type.code !== "company") {
        //     window.open(PORTAL_URL, "_blank");
        //     return;
        // }

        try {
            await authStore.switchProfile(profile.id)
                .then((response) => {
	                const profileCookie = useCookie('profile', {
		                path: '/',
		                domain: import.meta.env.VITE_COOKIE_DOMAIN
	                });
	                profileCookie.value = profile.type.code
                    window.open(spaceConfig[profile.type.code], "_self");
                })

        } catch (error) {
            $awn.alert(error.response.data.error.message)
        }
    };
</script>

<template>
    <div class="appbox-panel" v-show="open">
        <h4 class="text-center text-blue-900 text-xl mb-8 font-bold">Vos accès</h4>

        <div class="mt-5 grid grid-cols-12 gap-5 sm:gap-3">
            <template v-for="profile in userProfiles" :key="profile.id">
                <AppIcon
	                title="Utilisateur"
                    @click="switchUser(profile)"
                    :active="profile.id === online_profile.id"
                    :label="profile.type.name"
                    :disabled="profile.suspended || profile.space?.has_suspension"
                >
                    <img
                        alt=""
                        class="w-full h-full image-fit rounded-md"
                        src="/avatar.png"
                    />
                </AppIcon>
            </template>
            <template v-for="profile in otherProfiles" :key="profile.id">
<!--                :label="profile.type.name"-->

                <!--                    :label="profile.type.name + (profile.institution && profile.institution.name ? ' - ' + profile.institution.name : '') "-->
                <AppIcon
                    :title="profile.type.name + (profile.institution && profile.institution.name ? ' - ' + profile.institution.name : '') "
                    :label="(profile.type.name + (profile.institution && profile.institution.name ? ' - ' + profile.institution.name : '')).length > 40 ? (profile.type.name + (profile.institution && profile.institution.name ? ' - ' + profile.institution.name : '')).slice(0, 37) + '...' : profile.type.name + (profile.institution && profile.institution.name ? ' - ' + profile.institution.name : '')"

                    :addClass="'bg-' + profile.type.code"
                    :border-color="'border-' + profile.type.code"
                    @click="switchUser(profile)"
                    :active="profile.id === online_profile.id"
                    :disabled="profile.suspended || profile.space?.has_suspension"
                >
                    <font-awesome-icon
                        class="text-3xl text-theme-9 text-white"
                        :icon="['fas', profileIcon(profile.type.code)]"
                    />
                </AppIcon>
            </template>
        </div>
    </div>
</template>

<style lang="scss">
.bg-affiliate {
    background-color: #2845dc;
}

.border-affiliate {
    border-color: #1b2f98;
}

.bg-interpol {
    background-color: #2583b3;
}

.border-interpol {
    border-color: #176085;
}

.bg-police {
    background-color: #4343a6;
}

.border-police {
    border-color: #1b1b71;
}

.bg-anatt {
    background-color: #a9a15b;
}

.border-anatt {
    border-color: #1b1b71;
}

.bg-bank {
    background-color: #4e1ca5;
}

.border-bank {
    border-color: gray;
}

.bg-approved {
    background-color: #1c9378;
}

.border-approved {
    border-color: #106b57;
}

.bg-user {
    background-color: #bfd340;
}

.border-user {
    border-color: #a2b626;
}

.bg-central_garage {
    background-color: #aee32f;
}

.border-central_garage {
    border-color: #1b1b71;
}

.bg-auctioneer {
    background-color: #aee32f;
}

.border-auctioneer {
    border-color: #1b1b71;
}

.bg-gma {
    background-color: #e3a12f;
}

.border-gma {
    border-color: #1b1b71;
}

.bg-gmd {
    background-color: #a6438c;
}

.border-gmd {
    border-color: #1b1b71;
}

.bg-clerk {
    background-color: #3793ae;
}

.border-clerk {
    border-color: #1b1b71;
}

.bg-distributor {
    background-color: #bfd340;
}

.border-distributor {
    border-color: #a2b626;
}

.bg-court {
    background-color: #a64343;
}

.border-court {
    border-color: #1b1b71;
}
</style>
