<template>
	<div class="intro-y flex items-center justify-center mt-4">
		<h2 class="text-lg font-semibold text-primary">Choisissez le type d'acheteur</h2>
	</div>

	<TabGroup class="mt-8">
		<TabList class="nav-link-tabs">
			<Tab class="w-full py-2 nav-link tab-link" tag="button" @change="userType = 'moral'"> Personne moral </Tab>
			<Tab class="w-full py-2 nav-link tab-link" tag="button" @change="userType = 'physique'">
				Personne physique
			</Tab>
		</TabList>
		<TabPanels class="">
			<TabPanel class="leading-relaxed p-5 pt-0 ps-0">
				<form class="grid grid-cols-12 gap-6" @submit.prevent="submit()">
					<div class="intro-y col-span-12">
						<div class="intro-y box p-5">
							<div class="sm:grid grid-cols-2 gap-8 mb-8">
								<div>
									<label
										class="has-[:checked]:border-primary flex items-center justify-start rounded-lg bg-white border p-4 cursor-pointer"
									>
										<span class="form-check ml-4">
											<input
												v-model="isBeninese"
												class="form-check-input"
												name="car-choice"
												type="radio"
												:value="true"
											/>
										</span>
										<span class="mx-4"> Béninois </span>
									</label>
								</div>
								<div>
									<label
										class="has-[:checked]:border-primary flex items-center justify-start rounded-lg bg-white border p-4 cursor-pointer"
									>
										<span class="form-check ml-4">
											<input
												v-model="isBeninese"
												class="form-check-input"
												name="car-choice"
												type="radio"
												:value="false"
											/>
										</span>
										<span class="mx-4"> Non Béninois </span>
									</label>
								</div>
							</div>

							<div class="sm:grid grid-cols-2 gap-8 mb-8">
								<div>
									<label class="form-label" for="crud-form-1">Numéro d'enregistrement </label>
									<input
										id="crud-form-1"
										type="text"
										class="form-control"
										placeholder="Numéro d'enregistrement "
									/>
								</div>
								<div>
									<TextInputGroup
										v-model="demand.new_owner_npi"
										name="new_owner_ifu"
										:disabled="loading"
										required
										label="Numéro IFU de l'acheteur"
										pattern="[0-9]{13}"
										:maxlength="13"
										:errors="errors.new_owner_npi || []"
									/>
								</div>
							</div>

							<div class="sm:grid grid-cols-2 gap-8 mb-8">
								<div>
									<label class="form-label" for="crud-form-1">Nom </label>
									<input id="crud-form-1" type="text" class="form-control" placeholder="" />
								</div>
								<div>
									<label class="form-label" for="crud-form-1">Téléphone</label>
									<input id="crud-form-1" type="text" class="form-control" placeholder="Téléphone" />
								</div>
							</div>
						</div>
					</div>
					<div class="col-span-12 text-right mt-5">
						<button class="btn btn-outline-primary w-36 mr-4 border-2" type="reset" @click="$emit('prev')">
							Annuler
						</button>
						<BasicButton class="btn-primary w-36" :loading="loading" type="submit">Suivant</BasicButton>
					</div>
				</form>
			</TabPanel>

			<TabPanel class="leading-relaxed p-5 pt-0 ps-0">
				<form class="grid grid-cols-12 gap-6" @submit.prevent="submit()">
					<div class="intro-y col-span-12">
						<div class="intro-y box p-5">
							<div class="sm:grid grid-cols-2 gap-8 mb-8">
								<div>
									<label
										class="has-[:checked]:border-primary flex items-center justify-start rounded-lg bg-white border p-4 cursor-pointer"
									>
										<span class="form-check ml-4">
											<input
												v-model="isBeninese"
												class="form-check-input"
												name="user-choice"
												type="radio"
												:value="true"
											/>
										</span>
										<span class="mx-4"> Béninois </span>
									</label>
								</div>
								<div>
									<label
										class="has-[:checked]:border-primary flex items-center justify-start rounded-lg bg-white border p-4 cursor-pointer"
									>
										<span class="form-check ml-4">
											<input
												v-model="isBeninese"
												class="form-check-input"
												name="user-choice"
												type="radio"
												:value="false"
											/>
										</span>
										<span class="mx-4"> Non Béninois </span>
									</label>
								</div>
							</div>

							<template v-if="!isBeninese">
								<div class="sm:grid grid-cols-2 gap-8 mb-8">
									<div>
										<label class="form-label" for="crud-form-1">Nom de l'acheteur </label>
										<input
											id="crud-form-1"
											type="text"
											class="form-control"
											placeholder="Nom de l'acheteur"
										/>
									</div>
									<div>
										<label class="form-label" for="crud-form-1">Prénoms de l'acheteur</label>
										<input
											id="crud-form-1"
											type="text"
											class="form-control"
											placeholder="Prénoms de l'acheteur"
										/>
									</div>
								</div>

								<div class="sm:grid grid-cols-2 gap-8 mb-8">
									<div>
										<label class="form-label" for="crud-form-1"
											>Numéro Passport de l'acheteur
										</label>
										<input
											id="crud-form-1"
											type="text"
											class="form-control"
											placeholder="Numéro Passport de l'acheteur"
										/>
									</div>
									<div>
										<label class="form-label" for="crud-form-1">Téléphone de l'acheteur</label>
										<input
											id="crud-form-1"
											type="text"
											class="form-control"
											placeholder="Téléphone de l'acheteur"
										/>
									</div>
								</div>
							</template>

							<template v-else>
								<div>
									<TextInputGroup
										v-model="demand.new_owner_npi"
										label="Numéro d’Identification Personnelle (NPI)"
										name="new_owner_npi"
										:disabled="loading"
										required
										placeholder="NPI de l'acheteur"
										pattern="[0-9]{10}"
										:maxlength="13"
										:errors="errors.new_owner_npi || []"
									/>
								</div>
							</template>
						</div>
					</div>
					<div class="col-span-12 text-right mt-5">
						<button class="btn btn-outline-primary w-36 mr-4 border-2" type="reset" @click="$emit('prev')">
							Annuler
						</button>
						<BasicButton class="btn-primary w-36" :loading="loading" type="submit">Suivant</BasicButton>
					</div>
				</form>
			</TabPanel>
		</TabPanels>
	</TabGroup>

	<Modal :show="modalIsOpen" size="modal-lg" @hidden="$emit('next')">
		<ModalBody>
			<h4 class="text-lg text-center font-bold mb-8 text-primary">
				Souhaitez-vous effectuer la mutation de nom ?
			</h4>
			<div class="flex flex-col justify-between mx-4">
				<div class="relative mb-2">
					<input
						id="custom-checkbox-1"
						class="text-primary focus:ring-violet-300 peer w-6 h-6 absolute top-5 left-4"
						name="custom-checkbox"
						type="radio"
						:value="true"
					/>
					<label
						class="w-full h-[68px] cursor-pointer text-gray-400 flex flex-row justify-between items-center border rounded-lg p-4 peer-focus:outline-none peer-focus:ring peer-checked:border-primary peer-checked:text-primary hover:bg-blue-100"
						for="custom-checkbox-1"
					>
						<span class="text-lg ml-10 mr-4">Oui, je procèderai à la mutation de nom</span>
					</label>
				</div>
				<div class="relative mt-2">
					<input
						id="custom-checkbox-2"
						class="text-primary focus:ring-violet-300 peer w-6 h-6 absolute top-5 left-4"
						name="custom-checkbox"
						type="radio"
						:value="false"
					/>
					<label
						class="w-full h-[68px] cursor-pointer text-gray-400 flex flex-row justify-between items-center border rounded-lg p-4 peer-focus:outline-none peer-focus:ring peer-checked:border-primary peer-checked:text-primary hover:bg-blue-100"
						for="custom-checkbox-2"
					>
						<span class="text-lg ml-10 mr-4">Non, l'acheteur effectuera la mutation</span>
					</label>
				</div>
			</div>
		</ModalBody>
		<ModalFooter class="">
			<div class="flex mb-2">
				<button class="btn btn-primary w-full" type="button" @click="goNext">Suivant</button>
			</div>
		</ModalFooter>
	</Modal>
</template>

<script setup>
	import { onUnmounted, ref } from "vue";
	import { Tab, TabGroup, TabList, TabPanel, TabPanels } from "@/global-components/tab/index.js";
	import { Modal, ModalBody, ModalFooter } from "@/global-components/modal/index.js";
	import { useDemandStore } from "@/stores/demand.js";
	import { storeToRefs } from "pinia";
	import BasicButton from "@/components/BasicButton.vue";
	import TextInputGroup from "@/components/Form/TextInputGroup.vue";

	const demandStore = useDemandStore();
	const { demand, loading, errors, buyer_info } = storeToRefs(demandStore);

	const userType = ref("physique");
	const isBeninese = ref(true);
	const modalIsOpen = ref(false);

	onUnmounted(() => {
		modalIsOpen.value = false;
	});

	const goNext = () => {
		modalIsOpen.value = false;
	};

	const submit = () => {
		demandStore.fetchOwnerInfo(demand.value.new_owner_npi, userType.value === "moral").then((res) => {
			buyer_info.value = res;
			modalIsOpen.value = true;
		});
	};

	const emit = defineEmits(["next", "prev"]);
</script>
