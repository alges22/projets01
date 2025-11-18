<template>
	<div class="content">
		<div class="top-bar -mx-4 px-4 md:mx-0 md:px-0 md:align-baseline mt-4">
			<nav aria-label="breadcrumb" class="-intro-x mr-auto hidden sm:flex">
				<ol class="breadcrumb">
					<li class="breadcrumb-item">Paramètre</li>
				</ol>
			</nav>

			<NotificationBox />

			<AppsBox />

			<UserMenuDropdown />
		</div>

		<div class="intro-y flex flex-col sm:flex-row items-center mt-8">
			<div class="w-full sm:w-auto flex mt-4 sm:mt-0 ml-auto">
				<button class="btn btn-outline-primary shadow-md mr-2" @click="modalIsOpen = true">
					Ajouter un membre
				</button>
			</div>
		</div>

		<div class="grid grid-cols-12 gap-6 mt-5 dashboard-card">
			<div class="intro-y mt-2 flex items-center col-span-12">
				<h2 class="mr-auto text-lg font-bold">Team</h2>
				<div class="w-full sm:w-auto flex mt-4 sm:mt-0 ml-auto">
					<button class="border-0 mr-2">
						<i class="w-4 h-4 fa-light fa-ellipsis text-2xl"></i>
					</button>
				</div>
			</div>

			<DataTable
				:headers="headers"
				:items="data"
				empty-text="Aucun membre trouvé"
				header-class="uppercase text-start"
				search
			>
				<template #member="{ item }">
					<div class="flex items-center">
						<div class="w-24 h-24 lg:w-12 lg:h-12 image-fit lg:mr-1">
							<img alt="Profile" class="rounded-full" src="@/assets/images/preview-14.jpg" />
						</div>
						<div class="ml-3">
							<a class="font-medium dark:text-gray-900" href="">{{ item.member }}</a>
						</div>
					</div>
				</template>
				<template #role="{ item }">
					<div class="font-bold text-gray-600">{{ item.role }}</div>
				</template>
			</DataTable>
		</div>

		<Modal :show="modalIsOpen" @hidden="modalIsOpen = false">
			<ModalBody>
				<div class="flex flex-col justify-between mx-4">
					<div>
						<label class="form-label" for="crud-form-1">Entrer l'adresse email</label>
						<input
							id="crud-form-1"
							class="form-control w-full"
							placeholder="Input text"
							type="text"
							value="mongmail.com"
						/>
					</div>
					<div class="mt-4">
						<label class="form-label" for="crud-form-1">Définissez le rôle</label>
						<select aria-label="Default select example" class="form-select">
							<option>Administrateur</option>
							<option value="">Secrétaire Particulière</option>
						</select>
					</div>
				</div>
			</ModalBody>
			<ModalFooter class="">
				<div class="flex mb-2">
					<button class="btn btn-primary w-full" type="button" @click="openSuccessModal">Inviter</button>
				</div>
			</ModalFooter>
		</Modal>

		<Modal :show="successModalIsOpen" backdrop="static" @hidden="successModalIsOpen = false">
			<ModalBody class="p-0">
				<div class="p-5 text-center font-bold">
					<svg
						class="lucide lucide-check-circle w-16 h-16 text-success mx-auto mt-3"
						fill="none"
						height="24"
						stroke="currentColor"
						stroke-linecap="round"
						stroke-linejoin="round"
						stroke-width="2"
						viewBox="0 0 24 24"
						width="24"
						xmlns="http://www.w3.org/2000/svg"
					>
						<path d="M22 11.08V12a10 10 0 11-5.93-9.14"></path>
						<polyline points="22 4 12 14.01 9 11.01"></polyline>
					</svg>
					<div class="text-2xl mt-5">Invitation envoyée avec succès !</div>
				</div>
				<div class="px-5 pb-8 text-center mt-4">
					<button class="btn btn-primary w-full mx-2" type="button" @click="closeModal">
						Ok, j’ai compris !
					</button>
				</div>
			</ModalBody>
		</Modal>
	</div>
</template>

<script setup>
	import DataTable from "@/components/DataTable.vue";
	import { ref } from "vue";
	import { Modal, ModalBody, ModalFooter } from "@/global-components/modal/index.js";
	import { useRouter } from "vue-router";
	import AppsBox from "@/components/AppsBox.vue";
	import UserMenuDropdown from "@/components/UserMenuDropdown.vue";
	import NotificationBox from "@/components/NotificationBox.vue";

	const router = useRouter();

	const headers = [
		{ key: "member", title: "Membre", sortable: false },
		{ key: "role", title: "Rôle", sortable: false },
	];
	const modalIsOpen = ref(false);
	const successModalIsOpen = ref(false);

	const data = ref([
		{ member: "Brice Kakpo", role: "Administrateur" },
		{ member: "Kouagou Otiniel", role: "A. Commercial" },
		{ member: "Anoura Bossa", role: "Service client" },
		{ member: "Kpingla Jean", role: "Service client" },
		{ member: "Benjamin Franklin", role: "A. Commercial" },
		{ member: "Janette Doe", role: "A. Commercial" },
		{ member: "Kokou Brice", role: "A. Commercial" },
	]);

	const openSuccessModal = () => {
		modalIsOpen.value = false;
		successModalIsOpen.value = true;
	};

	const closeModal = () => {
		modalIsOpen.value = false;
		successModalIsOpen.value = false;
		router.push("/parc/dashboard");
	};
</script>

<style scoped></style>
