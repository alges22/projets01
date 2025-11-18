<script setup>
	import {FontAwesomeIcon} from "@fortawesome/vue-fontawesome";
	import {useUserStore} from '~/stores/user.js'

	const showPanel = ref(false)
	const showAppBox = ref(false)

	const userStore = useUserStore()
    const { user, roles } = storeToRefs(userStore)

	const props = defineProps({
		theme: {
			type: String,
			default: "light"
		},
	});

    const getFirstName = (fullname) => {
        if (!fullname) return '';
        return fullname.split(' ')[0];
    };
</script>

<template>
	<AppBox :open="showAppBox" />

	<div :class="theme" class="toolbar flex flex-col sm:flex-row items-center sm:px-16">
		<RouterLink to="/">
			<img :src="theme === 'dark' ? '/logo_dark.png' : '/logo_light.png'" alt="AnaTT Services"
				 style="height: auto;">
		</RouterLink>

        <div v-if="user" class="sm:ms-auto flex flex-row py-4">
            <div class="flex flex-row px-4 notification-container">
                <!-- Notification Panel -->
                <div v-show="showPanel" id="notification-panel" class="notification-panel">
                    <h4 class="text-center text-blue-900 text-xl">Notifications et invitations</h4>

                    <div class="my-8">
                        <div class="text-black text-center">
                            Aucune notification
                        </div>
                    </div>
<!--                    <div class="notification-item">-->
<!--                        <div class="w-1/6">-->
<!--                            <div class="icon-container"></div>-->
<!--                        </div>-->
<!--                        <div class="w-3/5 flex flex-col">-->
<!--                            <span class="text-red-500">Alerte routière</span>-->

<!--                            <span-->
<!--                                class="text-gray-400 mt-2">Consultez votre compte pour les détails de l'infraction</span>-->
<!--                        </div>-->
<!--                        <div class="w-1/5 px-4"><span class="text-gray-500">14:33</span></div>-->
<!--                    </div>-->

<!--                    <div class="notification-item">-->
<!--                        <div class="w-1/6">-->
<!--                            <div class="icon-container"></div>-->
<!--                        </div>-->
<!--                        <div class="w-3/5 flex flex-col">-->
<!--                            <span>Demande de transformation</span>-->

<!--                            <span-->
<!--                                class="text-gray-400 mt-2">Consultez votre compte pour les détails de l'infraction</span>-->
<!--                        </div>-->
<!--                        <div class="w-1/5 px-4"><span class="text-gray-500">14:33</span></div>-->
<!--                    </div>-->

                    <div class="p-4 text-center">
                        <RouterLink
                            class="bg-white hover:bg-blue-900 hover:text-white text-blue-900 font-semibold py-2 px-4 border border-blue-900 rounded shadow"
                            to="/inbox">
                            Invitations ({{ user.pending_invitations_count }})
                        </RouterLink>
                    </div>
                </div>

                <RouterLink class="cursor-pointer mx-5" to="/cart">
                    <font-awesome-icon :icon="['far', 'cart-shopping-fast']" class="notification_icon" />
                    <span v-if="user.online_profile.cart_demands > 0" class="absolute rounded-full py-1 px-1 text-xs font-medium content-[''] leading-none grid place-items-center top-[10%] right-[45%] translate-x-2/4 -translate-y-2/4 bg-red-500 text-white min-w-[24px] min-h-[24px]">{{ user.online_profile.cart_demands }}</span>
                </RouterLink>

                <a class="cursor-pointer" @click="showPanel = !showPanel">
                    <font-awesome-icon :icon="['fal', 'bell']" class="notification_icon" />
                    <span v-if="user.pending_invitations_count > 0" class="absolute rounded-full py-1 px-1 text-xs font-medium content-[''] leading-none grid place-items-center top-[10%] right-[10%] translate-x-2/4 -translate-y-2/4 bg-red-500 text-white min-w-[24px] min-h-[24px]">{{ user.pending_invitations_count }}</span>
                </a>
            </div>
            <div class="flex items-center px-2">
                <div class="dots cursor-pointer" @click="showAppBox = !showAppBox">
                    <div class="dots-item"></div>
                    <div class="dots-item"></div>
                    <div class="dots-item"></div>
                    <div class="dots-item"></div>
                    <div class="dots-item"></div>
                    <div class="dots-item"></div>
                    <div class="dots-item"></div>
                    <div class="dots-item"></div>
                    <div class="dots-item"></div>
                </div>
            </div>
            <div class="flex items-center justify-center">
                <div class="relative inline-block text-left dropdown">
                    <div class="infos flex flex-row sm:px-4">
                        <div class="flex items-center">
                            <img alt="avatar" src="/avatar.png" style="width: 50px;">
                            <div class="flex flex-col ps-2">
                            <span class="font-bold text-sm sm:text-md">{{ getFirstName(userStore.user?.identity.firstname) }} </span>
                            <span class="text-sm">Roles : {{ roles.map(role => role.label).join(', ') }}</span>
                            <span class="text-sm">NPI : {{ userStore.user?.identity.npi }}</span>
                            </div>
                            <span class="rounded-md shadow-sm">
                    <button
                        aria-controls="headlessui-menu-items-117"
                        aria-expanded="true" aria-haspopup="true"
                        type="button">
                        <svg class="w-5 h-5 ml-2 -mr-1" fill="currentColor" viewBox="0 0 20 20">
                            <path
                                clip-rule="evenodd"
                                d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                fill-rule="evenodd">
                            </path>
                        </svg>
                    </button>
                </span>
                        </div>
                    </div>
                    <div class="opacity-0 invisible dropdown-menu transition-all duration-300 transform origin-top-right -translate-y-2 scale-95">
                        <div
                            id="headlessui-menu-items-117"
                            aria-labelledby="headlessui-menu-button-1"
                            class="absolute right-0 w-56 mt-2 origin-top-right bg-white border border-gray-200 divide-y divide-gray-100 rounded-md shadow-lg outline-none"
                            role="menu">
                            <div class="py-1">
                                <!--								<a class="text-gray-700 flex justify-between w-full px-4 py-2 text-sm leading-5 text-left"-->
                                <!--								   href="javascript:void(0)"-->
                                <!--								   role="menuitem"-->
                                <!--								   tabindex="0">Account settings</a>-->
                                <!--								<a class="text-gray-700 flex justify-between w-full px-4 py-2 text-sm leading-5 text-left"-->
                                <!--								   href="javascript:void(0)"-->
                                <!--								   role="menuitem"-->
                                <!--								   tabindex="1">Support</a>-->
                                <!--								<span aria-disabled="true"-->
                                <!--									  class="flex justify-between w-full px-4 py-2 text-sm leading-5 text-left text-gray-700 cursor-not-allowed opacity-50"-->
                                <!--									  role="menuitem"-->
                                <!--									  tabindex="-1">New feature (soon)</span>-->
                                <RouterLink to="/profile" class="text-gray-700 flex justify-between w-full px-4 py-2 text-sm leading-5 text-left"
                                   role="menuitem"
                                   tabindex="2">Mon profil</RouterLink>
                            </div>
                            <div class="py-1">
                                <a class="text-red-700 flex justify-between w-full px-4 py-2 text-sm leading-5 text-left"
                                   href="/auth/logout"
                                   role="menuitem"
                                   tabindex="3">
                                    Déconnexion
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div v-else class="sm:ms-auto flex flex-row py-4">
            <RouterLink to="/auth/login" class="btn-account">Se connecter</RouterLink>
            <RouterLink to="/auth/register" class="btn-account mx-2">Inscription</RouterLink>
        </div>
	</div>
</template>

<style scoped>
.toolbar img {
	width: 250px;
	height: auto;
}

.notification_icon {
	width: 30px;
	height: 30px;
	align-items: center;
	display: flex;
}

.dots {
	display: grid;
	grid-template-columns: repeat(3, 1fr);
	gap: 4px;
	height: 30px;
}

.dots-item {
	text-align: center;
	padding: 4px;
	border-radius: 2px;
	cursor: pointer;
	transition: background-color 0.3s ease;
}

.dark .dots-item {
	background-color: #ffffff;
	color: #002766;
}

.light .dots-item {
	background-color: #002766;
	color: #ffffff;
}

.notification-container {
	position: relative;
	align-items: center;
}

.notification-panel {
	position: absolute;
	top: 180%;
	right: 0;
	background-color: #ffffff;
	border: 5px solid #E8EBEE;
	border-radius: 20px;
	padding: 20px;
	box-shadow: 0px 2px 4px rgba(0, 0, 0, 0.1);
	z-index: 999;
	width: 450px;
}

.notification-item {
	line-height: 1.2;
	display: flex;
	flex-direction: row;
	border-bottom: 0.5px solid #ece8e8;
	width: 100%;
	padding-bottom: 10px;
	padding-top: 10px;
}


.notification-item:last-child {
	border-bottom: none;
}

.icon-container {
	width: 50px;
	height: 50px;
	background-color: #f3f0f0;
	border-radius: 50%;
}

.appbox-panel {
	position: absolute;
	top: 10%;
	right: 10%;
	background-color: #ffffff;
	border: 5px solid #E8EBEE;
	border-radius: 20px;
	padding: 20px;
	box-shadow: 0px 2px 4px rgba(0, 0, 0, 0.1);
	z-index: 999;
	width: 450px;
}

/* Styles spécifiques au thème */
.dark {
	background-color: #002766;
	color: white;
}

.light {
	background-color: white;
	color: #002766;
}

.dropdown:focus-within .dropdown-menu {
	opacity: 1;
	transform: translate(0) scale(1);
	visibility: visible;
}

</style>