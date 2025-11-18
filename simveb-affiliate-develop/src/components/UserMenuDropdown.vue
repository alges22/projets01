<template>
	<div class="intro-x dropdown mr-auto sm:mr-6">
		<div class="dropdown-toggle overflow-hidden" role="button" aria-expanded="false" data-tw-toggle="dropdown">
			<div class="flex flex-col lg:flex-row items-center p-5">
				<div v-if="user.photo" class="w-12 h-12 image-fit lg:mr-1">
					<img alt="Photo de l'utilisateur" class="rounded-full" :src="user.photo" />
				</div>
				<AvatarComponent
					v-else
					:lastname="user.lastname"
					:firstname="user.firstname"
					size="lg"
					text-size="xl"
					class="lg:mr-1"
					:background-color="theme === 'vivid' ? 'white' : 'gray-500'"
					:text-color="theme === 'vivid' ? 'primary' : 'white'"
				/>
				<div class="lg:ml-2 lg:mr-auto text-center lg:text-left mt-3 lg:mt-0 hidden lg:block">
					<span class="font-bold text-base">{{ user.lastname }}</span>
					<div class="text-xs mt-0.5 text-end w-24 lg:w-full">{{ online_profile.type.name }}</div>
				</div>
				<div class="side-menu__sub-icon transform ms-3 hidden lg:block">
					<i class="fa-light fa-chevron-down"></i>
				</div>
			</div>
		</div>
		<div class="dropdown-menu w-56">
			<ul class="dropdown-content bg-white">
				<li class="p-2">
					<div class="font-medium">{{ user.lastname }} {{ user.firstname }}</div>
					<div class="text-xs dark:text-slate-500">NPI : {{ user.npi }}</div>
					<div class="text-xs dark:text-slate-500">Profile {{ online_profile.type.name }}</div>
					<div v-if="online_profile.institution" class="text-xs dark:text-slate-500">
						Institution : {{ online_profile.institution.name }}
					</div>
					<div class="text-xs dark:text-slate-500">
						{{ roles.map((role) => capitalizeFirstLetter(role.label)).join(", ") }}
					</div>
				</li>
				<li>
					<hr class="dropdown-divider border-dark/[0.08]" />
				</li>
				<li class="text-blue-600">
					<a href="" class="dropdown-item hover:bg-dark/5 font-bold">
						<i class="w-4 h-4 mr-2 fa-light fa-user"></i> Profile
					</a>
				</li>
				<li class="text-blue-600">
					<button class="dropdown-item hover:bg-dark/5 font-bold" @click="logout">
						<i class="w-4 h-4 mr-2 fa-light fa-toggle-off"></i> Se déconnecter
					</button>
				</li>
				<li v-if="can('invite-space-staff')" class="text-blue-600">
					<button class="dropdown-item hover:bg-dark/5 font-bold" @click="modalIsOpen = true">
						<i class="w-4 h-4 mr-2 fa-light fa-user-plus"></i> Inviter un membre
					</button>
				</li>
			</ul>
		</div>
	</div>

	<InvitationForm
		v-if="can('invite-space-staff')"
		:open="modalIsOpen"
		@close="modalIsOpen = false"
		@submit="modalIsOpen = false"
	/>
</template>

<script setup>
	import { useAuthStore } from "@/stores/auth.js";
	import { storeToRefs } from "pinia";
	import AvatarComponent from "@/components/AvatarComponent.vue";
	import Alert from "@/components/notification/alert.js";
	import InvitationForm from "@/views/Staff/InvitationForm.vue";
	import { ref } from "vue";
	import { userHasPermissions } from "@/helpers/permissions.js";
	import { capitalizeFirstLetter } from "../helpers/utils.js";

	const authStore = useAuthStore();
	const { user, online_profile, theme, roles } = storeToRefs(authStore);
	const { can } = userHasPermissions();
	const modalIsOpen = ref(false);

	const logout = () => {
		authStore.logout().then(() => {
			Alert.success("Au revoir, vous avez été déconnecté.");
			window.location.href = "/login";
		});
	};
</script>
