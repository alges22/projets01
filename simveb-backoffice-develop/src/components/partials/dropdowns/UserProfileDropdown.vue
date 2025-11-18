<script setup lang="ts">
	import { useUserSession } from "/@src/stores/userSession";
	import { storeToRefs } from "pinia";
	import { useNotyf } from "/@src/composable/useNotyf";

	const userSession = useUserSession();
	const { user, online_profile } = storeToRefs(userSession);

	const isLogoutLoading = ref(false);
	const notyf = useNotyf();

	const logout = () => {
		if (!isLogoutLoading.value) {
			isLogoutLoading.value = true;

			userSession.logout().then(() => {
				isLogoutLoading.value = false;

				notyf.dismissAll();
				notyf.success("Vous êtes déconnecté!");
				window.location.href = "/login";
			});
		}
	};
</script>

<template>
	<VDropdown v-if="user" right spaced class="user-dropdown profile-dropdown">
		<template #button="{ toggle }">
			<a
				role="button"
				tabindex="0"
				class="is-trigger dropdown-trigger user-info-head"
				aria-haspopup="true"
				@keydown.space.prevent="toggle"
				@click="toggle"
			>
				<VAvatar v-if="user.photo" :picture="user.photo" />
				<VAvatar v-else size="medium" :initials="user.lastname.charAt(0) + user.firstname.charAt(1)" />
				<div class="meta">
					<span>{{ user.lastname }} {{ user.firstname }}</span>
					<span class="user-info">N° {{ user.npi }}</span>
					<span class="user-info">Profile {{ online_profile.type.name }}</span>
				</div>
			</a>
		</template>

		<template #content>
			<div class="dropdown-head">
				<VAvatar v-if="user.photo" :picture="user.photo" />
				<VAvatar v-else size="small" :initials="user.lastname.charAt(0) + user.firstname.charAt(1)" />

				<div class="meta">
					<span>{{ user.fullName }}</span>
					<span>Profile {{ online_profile.type.name }}</span>
					<span>N° {{ user.npi }}</span>
				</div>
			</div>

			<hr class="dropdown-divider" />

			<div class="dropdown-item is-button">
				<VButton
					:loading="isLogoutLoading"
					class="logout-button"
					icon="user-lock"
					color="primary"
					role="menuitem"
					raised
					fullwidth
					@click.prevent="logout"
				>
					Déconnexion
				</VButton>
			</div>
		</template>
	</VDropdown>
</template>

<style lang="scss">
	.user-info-head {
		display: flex;
		align-items: center;
		padding: 10px 16px;

		.meta {
			margin-inline-start: 12px;
			font-family: var(--font);

			span {
				display: block;

				&:first-child {
					//font-size: 1.1rem;
					font-weight: 500;
					color: var(--dark-text);
					line-height: 1.2;
				}

				&:nth-child(2),
				&:nth-child(3) {
					color: var(--light-text);
					//font-size: 0.9rem;
				}
			}
		}
	}
</style>
