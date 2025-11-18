<script lang="ts" setup>
	import type { SideblockTheme } from "/@src/components/navigation/desktop/Sideblock.vue";
	import { usePanels } from "/@src/stores/panels";
	import { useViewWrapper } from "/@src/stores/viewWrapper";
	import { userHasPermissions } from "/@src/utils/permission";

	const props = withDefaults(
		defineProps<{
			theme?: SideblockTheme;
			defaultSideblock?: string;
			closeOnChange?: boolean;
			openOnMounted?: boolean;
			nowrap?: boolean;
		}>(),
		{
			defaultSideblock: "dashboard",
			theme: "default",
			openOnMounted: true,
		},
	);

	const viewWrapper = useViewWrapper();
	const panels = usePanels();
	const route = useRoute();
	const openSideblockLinks = ref("");
	const isMobileSideblockOpen = ref(false);
	const isDesktopSideblockOpen = ref(props.openOnMounted);
	const activeMobileSubsidebar = ref(props.defaultSideblock);

	const { hasOnePermissions, can } = userHasPermissions();

	/**
	 * watchPostEffect callback will be executed each time dependent reactive values has changed
	 */
	watchPostEffect(() => {
		viewWrapper.setPushedBlock(isDesktopSideblockOpen.value ?? false);
	});
	onMounted(() => {
		viewWrapper.setPushed(false);
	});
	watch(
		() => route.fullPath,
		() => {
			isMobileSideblockOpen.value = false;

			if (props.closeOnChange && isDesktopSideblockOpen.value) {
				isDesktopSideblockOpen.value = false;
			}
		},
	);
</script>

<template>
	<div class="sidebar-layout">
		<div class="app-overlay"></div>

		<!-- Mobile navigation -->
		<MobileNavbar :is-open="isMobileSideblockOpen" @toggle="isMobileSideblockOpen = !isMobileSideblockOpen">
			<template #brand>
				<RouterLink class="navbar-item is-brand" to="/">
					<AnimatedLogo height="38px" width="38px" />
				</RouterLink>

				<div class="brand-end">
					<NotificationsMobileDropdown />
					<UserProfileDropdown />
				</div>
			</template>
		</MobileNavbar>

		<!-- Mobile sidebar links -->
		<MobileSidebar :is-open="isMobileSideblockOpen" @toggle="isMobileSideblockOpen = !isMobileSideblockOpen">
			<template #links>
				<li>
					<a
						:class="[activeMobileSubsidebar === 'dashboard' && 'is-active']"
						aria-label="Display dashboard content"
						role="button"
						tabindex="0"
						@click="activeMobileSubsidebar = 'dashboard'"
						@keydown.space.prevent="activeMobileSubsidebar = 'dashboard'"
					>
						<i aria-hidden="true" class="fa-light fa-activity"></i>
					</a>
				</li>
			</template>

			<template #bottom-links>
				<li>
					<a
						aria-label="Display search panel"
						role="button"
						tabindex="0"
						@click="panels.setActive('search')"
						@keydown.space.prevent="panels.setActive('search')"
					>
						<i aria-hidden="true" class="fa-light fa-search"></i>
					</a>
				</li>
				<li>
					<a aria-label="View settings" href="#">
						<i aria-hidden="true" class="fa-light fa-settings"></i>
					</a>
				</li>
			</template>
		</MobileSidebar>

		<!-- Mobile subsidebar links -->
		<Transition name="slide-x">
			<DashboardsMobileSubsidebar v-if="isMobileSideblockOpen && activeMobileSubsidebar === 'dashboard'" />
		</Transition>

		<Transition name="slide-x">
			<Sideblock v-if="isDesktopSideblockOpen" :theme="props.theme">
				<template #header>
					<RouterLink class="mt-6" to="/">
						<img alt="logo" src="/images/logos/logo/logo_anatt.png" />
					</RouterLink>
				</template>
				<template #links>
					<li>
						<RouterLink class="single-link" to="/admin">
							<span class="icon">
								<i class="fa-light fa-home"></i>
							</span>
							Dashboard
						</RouterLink>
					</li>

					<VCollapseLinks v-model:open="openSideblockLinks" collapse-id="four-wheels">
						<template #header>
							<div class="icon">
								<i class="fa-light fa-truck"></i>
							</div>
							Véhicules 4 roues
							<i aria-hidden="true" class="rtl-hidden fa-light fa-chevron-right" />
							<i aria-hidden="true" class="ltr-hidden fa-light fa-chevron-left" />
						</template>
						<RouterLink
							:to="{
								name: 'immatriculation_demands',
								params: {
									wheels: 4,
								},
							}"
							class="is-submenu"
						>
							<span>Immatriculation</span>
						</RouterLink>
						<RouterLink
							v-if="can('browse-plate-duplicate')"
							:to="{ name: 'plate_duplicates' }"
							class="is-submenu"
						>
							Duplicata de plaques
						</RouterLink>
						<RouterLink
							v-if="can('browse-card-duplicate')"
							:to="{ name: 'gray_card_duplicates' }"
							class="is-submenu"
						>
							Duplicata de cartes grise
						</RouterLink>
					</VCollapseLinks>

					<VCollapseLinks v-model:open="openSideblockLinks" collapse-id="ray-card">
						<template #header>
							<div class="icon">
								<i aria-hidden="true" class="fa-light fa-credit-card sidebar-svg"></i>
							</div>
							Carte grise
							<i aria-hidden="true" class="rtl-hidden fa-light fa-chevron-right" />
							<i aria-hidden="true" class="ltr-hidden fa-light fa-chevron-left" />
						</template>
						<RouterLink
							:to="{
								name: 'gray_card_demands',
							}"
							class="is-submenu"
						>
							Demande de carte grise
						</RouterLink>
					</VCollapseLinks>

					<VCollapseLinks v-model:open="openSideblockLinks" collapse-id="immatriculation-plate">
						<template #header>
							<div class="icon">
								<img alt="plate-icon" src="/@src/assets/custom/images/license-plate.png" />
							</div>
							Plaques
							<i aria-hidden="true" class="rtl-hidden fa-light fa-chevron-right" />
							<i aria-hidden="true" class="ltr-hidden fa-light fa-chevron-left" />
						</template>
						<RouterLink
							class="is-submenu"
							v-if="can('browse-plate')"
							:to="{ name: 'plate_list' }"
							tabindex="0"
							role="button"
						>
							Liste des plaques
						</RouterLink>
						<RouterLink
							class="is-submenu"
							v-if="can('browse-plate-order')"
							:to="{ name: 'plate_order_list' }"
							tabindex="0"
							role="button"
						>
							Commandes
						</RouterLink>
					</VCollapseLinks>

					<VCollapseLinks v-model:open="openSideblockLinks" collapse-id="affiliate">
						<template #header>
							<div class="icon">
								<i
									aria-hidden="true"
									class="iconify sidebar-svg"
									data-icon="mdi:partnership-outline"
								></i>
							</div>
							Affilié
							<i aria-hidden="true" class="rtl-hidden fa-light fa-chevron-right" />
							<i aria-hidden="true" class="ltr-hidden fa-light fa-chevron-left" />
						</template>
						<RouterLink
							v-if="can('browse-affiliate-registration-request')"
							class="is-submenu"
							:to="{
								name: 'affiliate_registration_requests',
							}"
						>
							Demandes d'enregistrement d'affilié
						</RouterLink>
						<RouterLink
							v-if="can('browse-affiliate')"
							class="is-submenu"
							:to="{
								name: 'affiliates',
							}"
						>
							Liste des affiliés
						</RouterLink>
					</VCollapseLinks>
				</template>

				<template #bottom-links>
					<UserProfileDropdown up />

					<template v-if="hasOnePermissions(['access-config'])" class="is-hidden-touch">
						<RouterLink
							id="open-settings"
							v-tooltip.primary.top.right="'Configurations'"
							data-content="Configurations"
							to="/admin/configs"
						>
							<i aria-hidden="true" class="fa-light fa-settings sidebar-svg"></i>
						</RouterLink>
					</template>
				</template>
			</Sideblock>
		</Transition>

		<VViewWrapper full>
			<VPageContentWrapper>
				<template v-if="props.nowrap">
					<slot></slot>
				</template>
				<VPageContent v-else class="is-relative">
					<div class="page-title has-text-centered">
						<!-- Sidebar Trigger -->
						<div
							class="vuero-hamburger nav-trigger push-resize"
							tabindex="0"
							role="button"
							@keydown.space.prevent="isDesktopSideblockOpen = !isDesktopSideblockOpen"
							@click="isDesktopSideblockOpen = !isDesktopSideblockOpen"
						>
							<span class="menu-toggle has-chevron">
								<span :class="[isDesktopSideblockOpen && 'active']" class="icon-box-toggle">
									<span class="rotate">
										<i aria-hidden="true" class="icon-line-top"></i>
										<i aria-hidden="true" class="icon-line-center"></i>
										<i aria-hidden="true" class="icon-line-bottom"></i>
									</span>
								</span>
							</span>
						</div>

						<div class="title-wrap">
							<h1 class="title is-4">{{ viewWrapper.pageTitle }}</h1>
						</div>

						<Toolbar class="desktop-toolbar">
							<ToolbarNotification />

							<a
								class="toolbar-link right-panel-trigger"
								aria-label="View activity panel"
								tabindex="0"
								role="button"
								@keydown.space.prevent="panels.setActive('activity')"
								@click="panels.setActive('activity')"
							>
								<i aria-hidden="true" class="fa-light fa-grid"></i>
							</a>
						</Toolbar>
					</div>

					<router-view v-slot="{ Component }">
						<transition mode="out-in" name="scale">
							<component :is="Component" />
						</transition>
					</router-view>
				</VPageContent>
			</VPageContentWrapper>
		</VViewWrapper>
	</div>
</template>
