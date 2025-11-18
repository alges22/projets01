<script setup>
    import {useUserStore} from "~/stores/user";
    import Invites from "~/components/inbox/Invites.vue";
    import Notifications from "~/components/inbox/Notifications.vue";

    definePageMeta({
        layout: "without-navbar-banner"
    })

    useHead({
        title: 'SIMVEB - Boite de reception',
    })


    const userStore = useUserStore()
    const { user } = storeToRefs(userStore)
    const selectedTab = ref(0)

    const tabs = [
        {
            label: 'Notifications',
            component: Notifications
        },
        {
            label: `Invitations (${user.value.pending_invitations_count})`,
            component: Invites
        },
    ]
</script>

<template>
    <div class="card p-8">
        <div class="w-2/3 mx-auto">
            <div class="flex flex-row mb-16">
                <h4 class="text-2xl text-blue-600 font-bold">Boite de reception</h4>

                <div class="ms-auto items-center flex">
                    <button
                        @click="selectedTab = index"
                        :class="{ 'btn-switch': true, 'active': index === selectedTab }"
                        class="btn-switch" v-for="(tab, index) in tabs">
                        {{ tab.label }}
                    </button>
                </div>
            </div>

            <component :is="tabs[selectedTab].component" />
        </div>
    </div>
</template>

<style scoped lang="scss">
    .btn-switch{
        padding: 10px;
        color: #D9D9D9;
        font-weight: bold;
        margin-left: 10px;
        margin-right: 10px;
        border: 1px #D9D9D9 solid;
        border-radius: 5px;
    }

    .btn-switch.active{
        background-color: #1470EB;
        color: white;
        border: white;
    }
</style>