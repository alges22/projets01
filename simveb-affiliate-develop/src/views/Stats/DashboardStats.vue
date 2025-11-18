<template>
	<template v-if="loading">
		<div class="col-span-12 dashboard-card mt-4 shadow-md pointer-events-none">
			<div class="intro-y flex items-center">
				<h2 class="text-lg font-bold truncate mr-5">Statistiques</h2>
			</div>
			<div class="grid grid-cols-12 gap-6 mt-4">
				<div v-for="n in 3" :key="n" class="col-span-12 sm:col-span-6 p-4 xl:col-span-4 intro-y">
					<div class="animate-pulse">
						<div class="w-full h-24 bg-gray-300 rounded"></div>
					</div>
				</div>
			</div>
		</div>
	</template>

	<template v-else>
		<PoliceStats v-if="online_profile.type.code === 'police'" :stats="stats" />
		<InterpolStats v-else-if="online_profile.type.code === 'interpol'" :stats="stats" />
		<AuctioneerStats v-else-if="online_profile.type.code === 'auctioneer'" :stats="stats" />
		<CentralGarageStats v-else-if="online_profile.type.code === 'central_garage'" :stats="stats" />
		<ApprovedStats v-else-if="online_profile.type.code === 'approved'" :stats="stats" />
		<GMAStats v-else-if="online_profile.type.code === 'gma'" :stats="stats" />
		<GMDStats v-else-if="online_profile.type.code === 'gmd'" :stats="stats" />
		<BanqueStats v-else-if="online_profile.type.code === 'bank'" :stats="stats" />
		<CourtStats v-else-if="online_profile.type.code === 'court'" :stats="stats" />
		<DistributorStats v-else-if="online_profile.type.code === 'distributor'" :stats="stats" />
		<AffiliateStats v-else :stats="stats" />
	</template>
</template>

<script setup>
	import { onMounted, ref } from "vue";
	import { storeToRefs } from "pinia";
	import { getDashboardStats } from "@/services.js";
	import { useAuthStore } from "@/stores/auth.js";
	import AffiliateStats from "@/views/Stats/AffiliateStats.vue";
	import DistributorStats from "@/views/Stats/DistributorStats.vue";
	import CentralGarageStats from "@/views/Stats/CentralGarageStats.vue";
	import ApprovedStats from "@/views/Stats/ApprovedStats.vue";
	import AuctioneerStats from "@/views/Stats/AuctioneerStats.vue";
	import CourtStats from "@/views/Stats/CourtStats.vue";
	import BanqueStats from "@/views/Stats/BanqueStats.vue";
	import InterpolStats from "@/views/Stats/InterpolStats.vue";
	import GMAStats from "@/views/Stats/GMAStats.vue";
	import GMDStats from "@/views/Stats/GMDStats.vue";
	import PoliceStats from "@/views/Stats/PoliceStats.vue";

	const authStore = useAuthStore();
	const { online_profile } = storeToRefs(authStore);
	const loading = ref(true);

	const stats = ref({});

	onMounted(() => {
		loading.value = true;
		getDashboardStats()
			.then((data) => {
				stats.value = data;
			})
			.finally(() => {
				loading.value = false;
			});
	});
</script>
