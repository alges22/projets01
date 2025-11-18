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
        { key: "reference", title: "Reférence du gage", sortable: false },
        { key: "institution", title: "Banque/Concessionnaire", sortable: false },
        { key: "created_at", title: "Date et heure", sortable: false },
        { key: "statut", title: "Statut", sortable: false },
    ];

    const options = ref({});

    const pledges = ref([]);
    const meta = ref({});
    const loading = ref(true);
    const { can } = userHasPermissions();

    const fetchPledgeLifts = (options) => {
        client({
            method: "GET",
            url: "/pledge-lift?" + formatURLSearchParams(options).toString(),
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
        fetchPledgeLifts(options)
    });

    watch(
        options,
        (newOptions) => {
            fetchPledgeLifts(newOptions);
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
            empty-text="Aucune demande de levée de gage trouvé"
            @update-datatable="(newOptions) => (options = newOptions)"
            search
        >
            <template #reference="{ item }">
                {{ item.pledge.reference }}
            </template>
            <template #institution="{ item }">
                {{ item.institution_emitted.acronym }} - {{ item.institution_emitted.name }}
            </template>
            <template #statut="{ item }">
                <StatusComponent :status="item.status" :status-text="item.status_label" />
            </template>
            <template #created_at="{ item }">
                {{ dayjs(item.created_at).format("DD/MM/YYYY") }}
            </template>
            <template #actions="{ item }">
                <RouterLink v-if="can('show-pledge')" :to="/pledge-lifts/ + item.id">Voir détails</RouterLink>
            </template>
        </DataTable>
    </div>
</template>

<style scoped lang="scss"></style>
