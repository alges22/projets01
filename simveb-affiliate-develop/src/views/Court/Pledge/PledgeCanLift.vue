<script setup>
    import DataTable from "@/components/DataTable.vue";
    import {onMounted, ref, watch} from "vue";
    import client from "@/assets/js/axios/client.js";
    import StatusComponent from "@/components/StatusComponent.vue";
    import dayjs from "dayjs";
    import { userHasPermissions } from "@/helpers/permissions.js";
    import {formatURLSearchParams} from "@/helpers/utils.js";

    const headers = [
        { key: "key", title: "#", sortable: false },
        { key: "reference", title: "Ref", sortable: false },
        { key: "institution", title: "Banque/Concessionnaire", sortable: false },
        { key: "owner_name", title: "Nom du propriétaire", sortable: false },
        { key: "vin", title: "Vin", sortable: false },
        { key: "created_at", title: "Date et heure", sortable: false },
        { key: "statut", title: "Statut", sortable: false },
    ];

    const pledges = ref([]);
    const meta = ref({});
    const loading = ref(true);
    const { can } = userHasPermissions();

    const options = ref({});

    const fetchPledges = (options) => {
        client({
            method: "GET",
            url: "/pledge/liftable/list?" + formatURLSearchParams(options).toString(),
        })
            .then((response) => response.data)
            .then((response) => {
                pledges.value = response.data;

                meta.value = {
                    current_page: response.current_page,
                    total: response.total,
                    per_page: response.per_page,
                    from: response.from,
                    to: response.to,
                    links: response.links,
                };

                loading.value = false;
            });
    }

    onMounted(() => {
        fetchPledges(options)
    });

    watch(
        options,
        (newOptions) => {
            fetchPledges(newOptions);
        },
        { deep: true, immediate: true }
    );
</script>

<template>
    <div class="grid grid-cols-12 gap-6 mt-5 dashboard-card">
        <DataTable
            :headers="headers"
            :items="pledges"
            :loading="loading"
            :meta="meta"
            empty-text="Aucun gage trouvé"
            search
            @update-datatable="(newOptions) => (options = newOptions)"
        >
            <template #vin="{ item }">
                {{ item.vehicle.vin }}
            </template>
            <template #institution="{ item }">
                {{ item.institution_emitted.acronym }} - {{ item.institution_emitted.name }}
            </template>
            <template #owner_name="{ item }">
                {{ item.vehicle_owner.identity.fullName }}
            </template>
            <template #statut="{ item }">
                <StatusComponent :status="item.status" :status-text="item.status_label" />
            </template>
            <template #created_at="{ item }">
                {{ dayjs(item.created_at).format("DD/MM/YYYY") }}
            </template>
            <template #actions="{ item }">
                <RouterLink v-if="can('show-pledge')" :to="`/pledge/lift/${item.id}`">Lever le gage</RouterLink>
            </template>
        </DataTable>
    </div>
</template>

<style scoped lang="scss"></style>
