<script setup lang="ts">
	import { userHasPermissions } from "/@src/utils/permission";

	const route = useRoute();
	let nbWheels = ref(route.params.wheels);
	const { can } = userHasPermissions();

	// const crudStore = useCrudStore()
	// const { rows, row, meta, loading, url, formLoading } = storeToRefs(crudStore)
	const options = ref({});

	// const urlPath = '/immatriculation-demands'

	// onBeforeMount(() => {
	//   url.value = urlPath
	// })

	// const getItems = async (metadata) => {
	//   url.value = urlPath
	//   await crudStore.fetchRows(metadata)
	// }

	const headers = [
		{ title: "Référence", key: "reference", sortable: true },
		{ title: "Nom du demandeur", key: "full_name", sortable: true },
		{ title: "IFU", key: "ifu" },
		// { title: 'NPI', key: 'npi' },
		// { title: 'email', key: 'email' },
		{ title: "Type de véhicule", key: "vehicle_type_id" }, //neuf ou occasion
		{ title: "Type de vitre", key: "glass_type_id" },
		{ title: "Couleur de plaque ", key: "plate_color_id" },
		// { title: 'Date', key: 'date' },
	];

	watch(route, async (newRoute, oldRoute) => {
		nbWheels.value = newRoute.params.wheels;
	});

	// watch(
	//   options,
	//   (newOptions) => {
	//     getItems(newOptions)
	//   },
	//   { deep: true, immediate: true }
	// )

	// onUnmounted(() => {
	//   crudStore.reset()
	// })

	const loading = false;

	const rows = [
		{
			id: "555468489494",
			reference: "IM-8894F8REA",
			full_name: "Todd Hood",
			ifu: "00114892203",
			npi: "984894840054",
			email: "todd@todd.com",
			vehicle_type_id: "Neuf",
			glass_type_id: "Normale",
			plate_color_id: "Blanche",
			date: "17-07-2023 à 11h03",
		},
		{
			id: "555468489002",
			reference: "IM-JRE24835",
			full_name: "Allison Randolph",
			ifu: "23774892203",
			npi: "023894840054",
			email: "alli@gmail.com",
			vehicle_type_id: "Occasion",
			glass_type_id: "Teintée",
			plate_color_id: "Blanche",
			date: "06-07-2023 à 15h50",
		},
		{
			id: "225468489494",
			reference: "IM-OPL7654CF",
			full_name: "Elisabeth Mcmillan",
			ifu: "32614892203",
			npi: "894810568662",
			email: "millan.el@gmail.com",
			vehicle_type_id: "Normale",
			glass_type_id: "Normale",
			plate_color_id: "Blanche",
			date: "20-07-2023 à 17h20",
		},
	];

	const meta = {
		current_page: 1,
		total: 3,
		per_page: 10,
		from: 1,
		to: 3,
		links: null,
	};

	//TODO: add permission helper and middleware to use
</script>

<template>
	<div class="page-content-inner">
		<div class="">
			<span class="is-size-5">Véhicule à {{ nbWheels }} roues</span>
		</div>
		<DataTable
			:headers="headers"
			:items="rows"
			:loading="loading"
			:meta="meta"
			empty-text="Aucune demande trouvée"
			search
			@update-datatable="(newOptions) => (options = { ...options, ...newOptions })"
		>
			<template #plate_color_id="{ item }">
				<VTag color="" :label="item.plate_color_id" curved />
			</template>
			<template #actions="{ item }">
				<div class="block">
					<VIconButton
						v-if="can('show-reimmatriculation-demand')"
						class="has-text-primary"
						icon="eye"
						:to="{
							name: 're_immatriculation_demands_show',
							params: { id: item.id, wheel: nbWheels },
						}"
					/>
				</div>
			</template>
		</DataTable>
	</div>
</template>
