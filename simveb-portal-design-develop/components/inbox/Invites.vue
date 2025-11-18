<script setup>
    import {useUserStore} from "~/stores/user";
    import {useApi} from "~/helpers/useApi";

    const userStore = useUserStore()
    const api = useApi()

    const validatingStates = ref({});
    const rejectingStates = ref({});

    const { $awn } = useNuxtApp()

    const validateInvitation = (id) => {
        validatingStates.value[id] = true;

        api({
            method: 'PUT',
            url: `/invitations/${id}/validate`
        }).then((response) => {
            if(response.status === 200){
                $awn.success('Vous avez approuvé cette invitation')

                window.location.reload()
            }
        }).finally(() => {
            validatingStates.value[id] = false;
        })
    }

    const denyInvitation = (id) => {
        rejectingStates.value[id] = true;

        api({
            method: 'PUT',
            url: `/invitations/${id}/deny`
        }).then((response) => {
            if(response.status === 200){
                $awn.success('Vous avez rejeté cette invitation')

                userStore.invitations = userStore.invitations.filter(invitation => invitation.id !== id);
            }
        }).finally(() => {
            rejectingStates.value[id] = false;
        })
    }
</script>

<template>
    <div v-if="userStore.invitations.length === 0">
        <span>Aucune invitation en attente</span>
    </div>
    <div v-else class="notification-item" v-for="invitation in userStore.invitations">
        <div>
            <div class="icon-container"></div>
        </div>
        <div class="flex me-8 flex-col ms-4">
            <span class="text-red-500">
                Invitation à rejoindre

                <template v-if="invitation.profile_type.code === 'company'">
                    une entreprise
                </template>
                <template v-else>
                    un service de l'anatt
                </template>
            </span>
            <span class="text-gray-400 mt-2">
                L'entreprise {{ invitation.space.institution?.name }} vous invite à rejoindre son staff
            </span>
        </div>
        <div class="ms-auto items-center flex" v-if="!invitation.accepted && !invitation.denied">
            <button @click="validateInvitation(invitation.id)" class="bg-green-500 text-white p-2 rounded mx-2" :disabled="validatingStates[invitation.id]">
                <span v-if="validatingStates[invitation.id]">...</span>
                <span v-else>Accepter</span>
            </button>
            <button @click="denyInvitation(invitation.id)" class="bg-red-500 text-white p-2 rounded" :disabled="rejectingStates[invitation.id]">
                <span v-if="rejectingStates[invitation.id]">...</span>
                <span v-else>Refuser</span>
            </button>
        </div>
    </div>
</template>

<style scoped lang="scss">
    .notification-item {
        line-height: 1.2;
        display: flex;
        flex-direction: row;
        border-bottom: 0.5px solid #ece8e8;
        width: 100%;
        padding-bottom: 10px;
        padding-top: 10px;
    }

    .icon-container {
        width: 50px;
        height: 50px;
        background-color: #f3f0f0;
        border-radius: 50%;
    }
</style>