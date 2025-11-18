<template>
	<div class="intro-x dropdown mr-auto sm:mr-6">
		<div
			v-tooltip
			class="dropdown-toggle cursor-pointer"
			role="button"
			aria-expanded="false"
			data-tw-toggle="dropdown"
			title="Mes accès / HUB"
		>
			<i class="text-2xl fa-solid fa-grid nav-icon"></i>
		</div>
		<div class="notification-content pt-2 dropdown-menu">
			<div class="notification-content__box dropdown-content">
				<div class="notification-content__title">Vos accès</div>
				<TextDivider width="w-1/6" class="mb-3" />

				<div class="mt-5 grid grid-cols-12 gap-5 sm:gap-3">
					<template v-for="(profile, index) in userProfiles" :key="index">
						<AppIcon
							v-tooltip
							:active="profile.id === online_profile.id"
							:label="profile.type.name"
							:add-class="'bg-' + profile.type.code"
							:disabled="profile.suspended || profile.space?.has_suspension"
							:border-color="'border-' + profile.type.code"
							:title="profile.institution ? profile.institution.name : null"
							@click="switchUser(profile)"
						>
							<img
								alt=""
								class="w-full h-full image-fit rounded-md"
								src="@/assets/images/simveb-avatar.webp"
							/>
						</AppIcon>
					</template>
					<template v-for="(profile, index) in otherProfiles" :key="index">
						<AppIcon
							v-tooltip
							:label="profile.type.name"
							:add-class="'bg-' + profile.type.code"
							:border-color="'border-' + profile.type.code"
							:active="profile.id === online_profile.id"
							:disabled="profile.suspended || profile.space?.has_suspension"
							:title="profile.institution ? profile.institution.name : null"
							@click="switchUser(profile)"
						>
							<i
								class="fa-solid text-3xl text-theme-9 text-white"
								:class="profileIcon(profile.type.code)"
							></i>
						</AppIcon>
					</template>
				</div>
			</div>
		</div>
	</div>
</template>

<script setup>
	import TextDivider from "@/components/TextDivider.vue";
	import AppIcon from "@/components/AppIcon.vue";
	import { useAuthStore } from "@/stores/auth.js";
	import { storeToRefs } from "pinia";
	import { computed } from "vue";
	import spaceConfig from "../../space-config.js";

	const authStore = useAuthStore();
	const { online_profile, profiles } = storeToRefs(authStore);

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
			case "clerk":
				return "fa-scale-balanced";
			case "distributor":
				return "fa-car";
			case "court":
				return "fa-gavel";
			default:
				return "fa-user";
		}
	};

	const switchUser = (profile) => {
		if (profile.suspended || profile.space?.has_suspension || profile.id === online_profile.value.id) {
			return;
		}
		authStore.switchProfile(profile.id, profile.type.code).then(() => {
			window.open(spaceConfig[profile.type.code], "_self");
		});
	};
</script>
