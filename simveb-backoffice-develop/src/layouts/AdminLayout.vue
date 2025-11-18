<script setup lang="ts">
	import type { SidebarTheme } from "/@src/components/navigation/desktop/Sidebar.vue";
	import { useViewWrapper } from "/@src/stores/viewWrapper";
	import { userHasPermissions } from "/@src/utils/permission";
	import DesktopSidebarItems from "/@src/layouts/DesktopSidebarItems.vue";
	import Navbar from "/@src/components/navigation/desktop/Navbar.vue";
	import SearchBox from "/@src/layouts/SearchBox.vue";
	import AppsBox from "/@src/components/AppsBox.vue";

	const props = withDefaults(
		defineProps<{
			theme?: SidebarTheme;
			defaultSidebar?: string;
			closeOnChange?: boolean;
			openOnMounted?: boolean;
			nowrap?: boolean;
		}>(),
		{
			defaultSidebar: "dashboard",
			theme: "default",
		}
	);
	const { hasOnePermissions, can } = userHasPermissions();
	const viewWrapper = useViewWrapper();
	const route = useRoute();
	const isMobileSidebarOpen = ref(false);
	const isDesktopSidebarOpen = ref(props.openOnMounted);
	const activeMobileSubsidebar = ref(props.defaultSidebar);

	function switchSidebar(id: string) {
		if (id === activeMobileSubsidebar.value) {
			isDesktopSidebarOpen.value = !isDesktopSidebarOpen.value;
		} else {
			isDesktopSidebarOpen.value = true;
			activeMobileSubsidebar.value = id;
		}
	}

	/**
	 * watchPostEffect callback will be executed each time dependent reactive values has changed
	 */
	watchPostEffect(() => {
		viewWrapper.setPushed(isDesktopSidebarOpen.value ?? false);
	});
	watch(
		() => route.fullPath,
		() => {
			isMobileSidebarOpen.value = false;

			if (props.closeOnChange && isDesktopSidebarOpen.value) {
				isDesktopSidebarOpen.value = false;
			}
		}
	);
</script>

<template>
	<div class="navbar-layout">
		<div class="app-overlay"></div>

		<!-- Mobile navigation -->
		<MobileNavbar :is-open="isMobileSidebarOpen" @toggle="isMobileSidebarOpen = !isMobileSidebarOpen">
			<template #brand>
				<RouterLink to="/admin" class="navbar-item is-brand">
					<img alt="logo" src="/images/logos/logo/logo_anatt.png" />
				</RouterLink>

				<div class="brand-end">
					<NotificationsMobileDropdown />
					<UserProfileDropdown />
				</div>
			</template>
		</MobileNavbar>

		<!--		<MobileSidebar :is-open="isMobileSidebarOpen" @toggle="isMobileSidebarOpen = !isMobileSidebarOpen">
		</MobileSidebar>-->

		<!-- Mobile subsidebar links -->
		<!--		<Transition name="slide-x">
			<DashboardsMobileSubsidebar v-if="isMobileSidebarOpen && activeMobileSubsidebar === 'dashboard'" />
			<PlateMobileSubsidebar
				v-else-if="isMobileSidebarOpen && activeMobileSubsidebar === 'immatriculation-plate'"
			/>
			<UsersMobileSubsidebar v-else-if="isMobileSidebarOpen && activeMobileSubsidebar === 'users'" />
			<FourWheelsMobileSubsidebar v-else-if="isMobileSidebarOpen && activeMobileSubsidebar === 'four-wheels'" />
			<AffiliateMobileSubsidebar v-else-if="isMobileSidebarOpen && activeMobileSubsidebar === 'affiliate'" />
		</Transition>-->

		<Sidebar :theme="props.theme" :is-open="isDesktopSidebarOpen">
			<template #links="{ key }">
				<DesktopSidebarItems :s-key="key" />
			</template>
		</Sidebar>

		<Navbar>
			<!-- Custom navbar toolbar -->
			<template #toolbar>
				<Toolbar class="desktop-toolbar">
					<ToolbarNotification />

					<AppsBox />
				</Toolbar>

				<UserProfileDropdown />
			</template>
			<template #links>
				<div class="centered-links">
					<RouterLink
						v-if="can('browse-im-demand')"
						:to="{ name: 'taches_list' }"
						class="centered-link centered-link-toggle"
						active-class="bg-blue-200"
					>
						<span>Mes tâches</span>
					</RouterLink>
					<RouterLink
						v-if="can('browse-im-demand')"
						:to="{ name: 'traitements_list' }"
						class="centered-link centered-link-toggle"
						active-class="bg-blue-200"
					>
						<span>Mes traitements</span>
					</RouterLink>
				</div>
			</template>

			<template #title>
				<SearchBox />
			</template>
		</Navbar>

		<VViewWrapper top-nav>
			<VPageContentWrapper>
				<template v-if="props.nowrap">
					<slot></slot>
				</template>
				<VPageContent v-else class="is-relative">
					<div class="is-navbar-lg">
						<!--						<div v-if="$route.name !== 'admin_home'" class="page-title has-text-centered">
							<VIconButton
								icon="arrow-left"
								class="mr-2"
								circle
								outlined
								color="primary"
								@click="returnPreviousPage($router)"
							/>

							<div class="title-wrap">
								<h1 class="title is-4">{{ viewWrapper.pageTitle }}</h1>
							</div>
						</div>-->

						<router-view v-slot="{ Component }">
							<transition name="scale" mode="out-in">
								<component :is="Component" />
							</transition>
						</router-view>
					</div>
				</VPageContent>
			</VPageContentWrapper>
		</VViewWrapper>
	</div>
</template>
