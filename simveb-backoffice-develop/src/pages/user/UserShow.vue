<script setup lang="ts">
	import client from "/@src/composable/axiosClient";
	import UserDemands from "/@src/pages/user/tabs/UserDemands.vue";
	import UserActivity from "/@src/pages/user/tabs/UserActivity.vue";
	import UserVehicles from "/@src/pages/user/tabs/UserVehicles.vue";

	const tabs = ref([
		{ label: "Demandes", value: "demands", icon: "lightbulb" },
		{ label: "Logs des activités", value: "activity_log", icon: "lightbulb" },
		{ label: "Véhicules", value: "vehicles", icon: "lightbulb" },
	]);

	const demands = ref([]);
	const logs = ref([]);
	const vehicles = ref([]);

	const userData = ref(null);
	const activeProfile = ref(null);

	const activeTab = ref("demands");

	const toggle = (tab: string) => {
		activeTab.value = tab;
	};

	const changeProfile = (profileId) => {
		activeProfile.value = profileId;

		loadDemandes(profileId);

		getLogs(profileId);

		getVehicles(profileId);
	};

	const loadDemandes = (profileId) => {
		client({
			method: "GET",
			url: `/profile/demands/${profileId}`,
		})
			.then((response) => response.data)
			.then((response) => {
				demands.value = response;
			});
	};

	const getLogs = (profileId) => {
		const profile = userData.value.profiles.find((profile) => profile.id === profileId);

		logs.value = profile.latest_activity_logs;
	};

	const getVehicles = (profileId) => {
		const profile = userData.value.profiles.find((profile) => profile.id === profileId);

		vehicles.value = profile.vehicle_owner ? profile.vehicle_owner.vehicles : [];
	};

	onMounted(() => {
		client({
			method: "GET",
			url: "/user-details/8765432101",
		})
			.then((response) => response.data)
			.then((response) => {
				userData.value = response;
			});
	});
</script>

<template>
	<div v-if="userData" class="page-content-inner">
		<div class="bg-white rounded-lg mb-4 p-4 flex items-center justify-between space-x-4">
			<div class="text-primary-dark text-2xl font-bold">{{ userData.identity.fullName }}</div>
		</div>

		<div class="grid grid-cols-1 lg:grid-cols-2 xl:grid-cols-3 gap-4 tab-details-inner">
			<VCard class="side-card">
				<div class="card-head bg-primary-600">
					<h3 class="text-primary-dark">Informations de l'utilisateur</h3>
				</div>
				<div class="card-inner">
					<div class="columns">
						<div class="column">
							<span>NPI</span>
						</div>
						<div class="column is-flex is-justify-content-space-between">
							{{ userData.identity.npi }}
						</div>
					</div>
					<div class="columns">
						<div class="column">
							<span>Nom</span>
						</div>
						<div class="column">
							<span>{{ userData.identity.firstname }}</span>
						</div>
					</div>
					<div class="columns">
						<div class="column">
							<span>Prénoms</span>
						</div>
						<div class="column">
							<span>{{ userData.identity.lastname }}</span>
						</div>
					</div>

					<div class="columns">
						<div class="column">
							<span>Email</span>
						</div>
						<div class="column is-flex">
							<span>{{ userData.identity.email }}</span>
						</div>
					</div>
					<div class="columns">
						<div class="column">
							<span>Téléphone</span>
						</div>
						<div class="column">
							<span>{{ userData.identity.telephone }}</span>
						</div>
					</div>
				</div>
			</VCard>

			<VCard class="side-card">
				<div class="card-head bg-primary-600">
					<h3 class="text-primary-dark">Profils de l'utilisateur</h3>
				</div>

				<table class="table w-full mt-4">
					<thead>
						<tr>
							<th>Profil</th>
							<th>Institution</th>
							<th>#</th>
						</tr>
					</thead>
					<tbody>
						<tr
							v-for="profile in userData.profiles"
							:key="profile.id"
							class="cursor-pointer"
							:class="{ 'bg-gray-100': profile.id === activeProfile }"
							@click="changeProfile(profile.id)"
						>
							<td>{{ profile.type.name }}</td>
							<td>{{ profile.institution?.name }}</td>
							<td><i class="fa fa-arrow-alt-circle-right"></i></td>
						</tr>
					</tbody>
				</table>
			</VCard>

			<VCard class="side-card">
				<div class="tabs-inner">
					<div class="tabs">
						<ul>
							<li v-for="(tab, key) in tabs" :key="key" :class="[activeTab === tab.value && 'is-active']">
								<a
									:href="tab.to ?? '#'"
									@keydown.prevent.enter="() => toggle(tab.value)"
									@click.prevent="() => toggle(tab.value)"
								>
									<i :class="`fa-light fa-${tab.icon}`" />
									<span>
										{{ tab.label }}
									</span>
								</a>
							</li>
						</ul>
					</div>
				</div>

				<Transition name="fade-fast" mode="out-in">
					<UserDemands v-if="activeTab == 'demands'" :demands="demands" />

					<UserActivity v-else-if="activeTab == 'activity_log'" :logs="logs" />

					<UserVehicles v-else-if="activeTab == 'vehicles'" :vehicles="vehicles" />
				</Transition>
			</VCard>
		</div>
	</div>
</template>

<style scoped lang="scss"></style>
