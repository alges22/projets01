<script setup>
	import { onMounted, ref } from "vue";
	import SelectInputGroup from "@/components/Form/SelectInputGroup.vue";
	import client from "@/assets/js/axios/client";
	import TextInputGroup from "@/components/Form/TextInputGroup.vue";
	import DataTable from "@/components/DataTable.vue";
	import FileInputGroup from "@/components/Form/FileInputGroup.vue";
	import TextDivider from "@/components/TextDivider.vue";
	import Alert from "@/components/notification/alert.js";
	import { Modal, ModalBody, ModalFooter } from "@/global-components/modal/index.js";
	import BasicButton from "@/components/BasicButton.vue";
	import LoaderSpinner from "@/components/LoaderSpinner.vue";

	import * as XLSX from "xlsx";
	import { useRouter } from "vue-router";

	const router = useRouter();

	const loadingOfficial = ref(false);
	const loadingVehicle = ref(false);

	const submitting = ref(false);

	const modalIsOpen = ref(true);

	const importData = ref({
		modal: false,
		file: null,
		type: "vehicles",
		saving: false,
	});

	const type = ref("");

	const formDataHeaders = {
		...client.defaults.headers,
		"Content-Type": "multipart/form-data",
	};

	const create = ref({
		institutions: [],
	});

	const auctionDeclaration = ref({
		institution_id: null,
		report: null,
	});

	const vehicules = ref([]);
	const officiels = ref([]);

	const vehicule = ref({
		vin: null,
		npi: null,
		prix: null,
	});

	const officiel = ref({
		npi: null,
		fonction: null,
	});

	// const vehicule = ref({
	//     vin: "12345678",
	//     npi: "4321098760",
	//     prix: 800000
	// })
	//
	// const officiel = ref({
	//     npi: "3210987650",
	//     fonction: "Greffier"
	// })

	const headers = [
		{ key: "vin", title: "VIN du véhicule", sortable: false },
		{ key: "npi", title: "Acheteur", sortable: true },
		{ key: "prix", title: "Prix de vente du véhicule", sortable: false },
	];

	const headersOfficiels = [
		{ key: "npi", title: "NPI", sortable: false },
		{ key: "nom", title: "Nom", sortable: true },
		{ key: "prenoms", title: "Prénoms", sortable: false },
		{ key: "email", title: "Prénoms", sortable: false },
		{ key: "fonction", title: "Fonction", sortable: false },
	];

	onMounted(() => {
		client({
			method: "GET",
			url: "/auction-sale-declarations/create",
		})
			.then((response) => response.data)
			.then((response) => {
				create.value = response;
			});
	});

	const importFile = () => {
		const file = importData.value.file;
		const reader = new FileReader();

		reader.onload = (e) => {
			const data = new Uint8Array(e.target.result);
			const workbook = XLSX.read(data, { type: "array" });
			const sheetName = workbook.SheetNames[0];
			const worksheet = workbook.Sheets[sheetName];
			const lines = XLSX.utils.sheet_to_json(worksheet, { header: 1 });

			const isVehiclesImport = importData.value.type === "vehicles";
			const expectedColumnCount = isVehiclesImport ? 3 : 2;

			// Validate all lines before processing
			const validLines = lines.filter((line) => line.length === expectedColumnCount);

			if (validLines.length !== lines.length) {
				// Display an error or handle invalid lines here
				Alert.error("Certaines lines ne contiennent pas le nombre de colonnes attendues");

				return;
			}

			importData.value.saving = true;

			const npis = validLines.map((line) => line[isVehiclesImport ? 1 : 0]);

			const response = getIdentities(npis);

			response
				.then((response) => response.data)
				.then((response) => {
					//response : if some keys are null throw error
					const hasNullValues = Object.keys(response).some((key) => response[key] === null);

					if (hasNullValues) {
						Alert.error("Le fichier contient des données invalides ");
						return;
					}

					lines.map((line) => {
						if (importData.value.type === "vehicles") {
							vehicules.value.push({
								vin: line[0],
								npi: line[1],
								prix: line[2],
								identity: response[line[1]],
							});
						} else {
							officiels.value.push({
								npi: line[0],
								fonction: line[1],
								identity: response[line[0]],
							});
						}
					});

					importData.value.modal = false;
				})
				.finally(() => {
					importData.value.saving = false;
				});
		};

		reader.readAsArrayBuffer(file);
	};

	const addVehicule = async () => {
		const found = vehicules.value.find((element) => element.vin === vehicule.value.vin);

		if (found) {
			Alert.error("Ce véhicule à déjà été ajouté");
			return;
		}

		loadingVehicle.value = true;
		await getIdentity(vehicule.value.npi)
			.then((response) => response.data)
			.then((response) => {
				vehicules.value.push({
					...vehicule.value,
					identity: response,
				});

				vehicule.value = {
					vin: null,
					npi: null,
					prix: null,
				};
			})
			.finally(() => {
				loadingVehicle.value = false;
			});
	};

	const getIdentity = (npi) => {
		return client({
			method: "GET",
			url: `/get-identity/${npi}`,
		});
	};

	const getIdentities = (npis) => {
		return client({
			method: "GET",
			url: `/get-identities/[${npis}]`,
		});
	};

	const addOfficiel = async () => {
		const found = officiels.value.find((element) => element.npi === officiel.value.npi);

		if (found) {
			Alert.error("Cet officiel à déjà été ajouté");
			return;
		}

		loadingOfficial.value = true;
		await getIdentity(officiel.value.npi)
			.then((response) => response.data)
			.then((response) => {
				officiels.value.push({
					...officiel.value,
					identity: response,
				});

				officiel.value = {
					npi: null,
					fonction: null,
				};
			})
			.finally(() => {
				loadingOfficial.value = false;
			});
	};

	const save = () => {
		submitting.value = true;

		client({
			method: "POST",
			url: "/auction-sale-declarations",
			data: {
				...auctionDeclaration.value,
				officials: officiels.value.map((officiel) => {
					return {
						npi: officiel.npi,
						title: officiel.fonction,
					};
				}),
				vehicles: vehicules.value.map((vehicule) => {
					return {
						vehicle_vin: vehicule.vin,
						price: vehicule.prix,
						buyer_npi: vehicule.npi,
					};
				}),
			},
			headers: formDataHeaders,
		})
			.then((response) => {
				Alert.success("Déclaration de vente aux enchères enregistrée avec succès");

				router.push("/auctionner/declarations");
			})
			.finally(() => {
				submitting.value = false;
			});
	};
</script>

<template>
	<Modal :show="modalIsOpen" backdrop="static" is-form @hidden="modalIsOpen = false">
		<ModalBody>
			<div class="flex flex-col justify-between mx-4">
				<span class="text-xl font-bold text-center mb-4"></span>
				<div class="grid grid-cols-2 gap-6 rounded-2xl">
					<label
						class="has-[:checked]:border-success has-[:checked]:bg-green-50 hover:bg-green-50 flex items-center justify-center rounded-lg h-12 border-2 cursor-pointer"
						for="seller_type_moral"
					>
						<span class="form-check ml-auto ms-4">
							<input
								id="seller_type_moral"
								v-model="type"
								class="form-check-input"
								name="seller-type"
								type="radio"
								value="etat"
								@change="modalIsOpen = false"
							/>
						</span>
						<span class="w-full mx-4"> Véhicules de l'état </span>
					</label>
					<label
						class="has-[:checked]:border-success has-[:checked]:bg-green-50 hover:bg-green-50 flex items-center justify-center rounded-lg h-12 border-2 cursor-pointer"
						for="seller_type_physique"
					>
						<span class="form-check ml-auto ms-4">
							<input
								id="seller_type_physique"
								v-model="type"
								class="form-check-input"
								name="seller-type"
								type="radio"
								value="autres"
								@change="modalIsOpen = false"
							/>
						</span>
						<span class="w-full mx-4"> Autres véhicules </span>
					</label>
				</div>
			</div>
		</ModalBody>
	</Modal>

	<Modal :show="importData.modal" is-form @hidden="importData.modal = false" @submit="importFile">
		<ModalBody>
			<div>
				<FileInputGroup v-model="importData.file" label="Fichier à importer" name="fichier" required />
			</div>
		</ModalBody>
		<ModalFooter class="">
			<div class="flex align-center justify-end mb-2">
				<BasicButton class="btn-primary w-full" type="submit" :loading="importData.saving">
					Importer
				</BasicButton>
			</div>
		</ModalFooter>
	</Modal>

	<LoaderSpinner v-if="type === ''" type="block" class="mt-4" />
	<div v-else class="mt-8 bg-white p-2 xl:p-4 rounded-md">
		<div v-if="type === 'etat'">
			<SelectInputGroup
				v-model="auctionDeclaration.institution_id"
				:options="create.institutions"
				name="institutions"
				label="Institutions"
			/>

			<TextDivider class="mt-4" />
		</div>

		<FileInputGroup v-model="auctionDeclaration.report" name="report" label="Rapport" />
	</div>

	<form class="bg-white p-2 xl:p-4 rounded-md mb-4 mt-4" @submit.prevent="addVehicule">
		<div class="bg-blue-100 p-4">
			<span class="font-bold text-lg text-blue-500 rounded-md">Véhicules vendus</span>
		</div>

		<div class="grid grid-cols-12 mt-8 gap-3">
			<div class="col-span-3">
				<TextInputGroup v-model="vehicule.vin" required name="vin" label="VIN du véhicule" />
			</div>

			<div class="col-span-3">
				<TextInputGroup v-model="vehicule.npi" required name="npi" label="NPI de l'acheteur" />
			</div>

			<div class="col-span-3">
				<TextInputGroup v-model="vehicule.prix" required name="prix" label="Prix de vente du véhicule" />
			</div>

			<div class="flex items-end">
				<button class="btn btn-primary" type="submit" :disabled="loadingVehicle">Ajouter</button>
				<BasicButton
					type="button"
					class="btn-success mx-1 text-white"
					@click="
						importData.modal = true;
						importData.type = 'vehicles';
					"
				>
					<i class="fa-light fa-cloud-arrow-down mr-2"></i> Importer
				</BasicButton>
			</div>
		</div>
	</form>

	<DataTable
		:headers="headers"
		:items="vehicules"
		:create-button="false"
		empty-text="Aucune donnée trouvé"
		header-class="uppercase text-start"
	>
		<template #prix="{ item }">
			{{ item.prix.toLocaleString() }}
		</template>
		<template #npi="{ item }">
			{{ item.npi }} ( {{ item.identity.lastname }} {{ item.identity.firstname }} )
		</template>
		<template #actions>
			<button class="btn btn-danger"><i class="fa-light fa-minus" /></button>
		</template>
	</DataTable>

	<form class="bg-white p-2 xl:p-4 rounded-md mb-4 mt-8" @submit.prevent="addOfficiel">
		<div class="bg-blue-100 p-4">
			<span class="font-bold text-lg text-blue-500 rounded-md">Officiels présents</span>
		</div>
		<div class="grid grid-cols-12 mt-8 gap-3">
			<div class="col-span-4">
				<TextInputGroup v-model="officiel.npi" required name="npi" label="NPI de l'officiel" />
			</div>

			<div class="col-span-4">
				<TextInputGroup v-model="officiel.fonction" required name="fonction" label="Fonction de l'officiel" />
			</div>

			<div class="flex items-end">
				<button class="btn btn-primary" :disabled="loadingOfficial">Ajouter</button>
				<BasicButton
					class="btn-success mx-1 text-white"
					@click="
						importData.modal = true;
						importData.type = 'officials';
					"
				>
					<i class="fa-light fa-cloud-arrow-down mr-2"></i> Importer
				</BasicButton>
			</div>
		</div>
	</form>

	<DataTable
		:headers="headersOfficiels"
		:items="officiels"
		:create-button="false"
		empty-text="Aucune donnée trouvé"
		header-class="uppercase text-start"
	>
		<template #nom="{ item }">
			{{ item.identity.lastname }}
		</template>
		<template #prenoms="{ item }">
			{{ item.identity.firstname }}
		</template>
		<template #email="{ item }">
			{{ item.identity.email }}
		</template>
		<template #actions>
			<button class="btn btn-danger"><i class="fa-light fa-minus" /></button>
		</template>
	</DataTable>

	<BasicButton class="btn-primary" type="submit" :loading="submitting" @click="save">
		Soumettre la demande
	</BasicButton>
</template>
