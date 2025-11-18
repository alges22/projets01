<template>
	<SideBar>
		<li>
			<MenuLink :active="$route.path === '/interpol'" icon="gauge-max" label="Tableau de bord" to="/interpol" />
		</li>
		<li>
			<MenuLink
				:active="$route.path.startsWith('/interpol/application')"
				icon="suitcase"
				label="Dossiers"
				to="interpol/application"
			/>
		</li>
		<li>
			<MenuLink
				:active="$route.path.startsWith('/interpol/application')"
				icon="book"
				label="Certificats de gages"
				to="bank/pledge"
			/>
		</li>
	</SideBar>

	<div class="content">
		<div class="top-bar -mx-4 px-4 md:mx-0 md:px-0 md:align-baseline mt-5">
			<nav aria-label="breadcrumb" class="-intro-x mr-auto hidden sm:flex">
				<BackButton />

				<ol class="breadcrumb">
					<li class="breadcrumb-item">Emettre un gage</li>
				</ol>
			</nav>
		</div>

		<div class="grid grid-cols-12 gap-6 mt-10">
			<div class="intro-y col-span-12">
				<div class="intro-y box p-5">
					<div class="sm:grid grid-cols-2 gap-8 mb-8">
						<div>
							<label class="form-label" for="crud-form-1">Entrer le VIN du véhicule</label>
							<input
								id="crud-form-1"
								class="form-control w-full"
								placeholder="Input text"
								type="text"
								value="VN1324718265422NVV  "
							/>
						</div>

						<div>
							<label class="form-label" for="crud-form-1">Entrer le numéro d'immatriculation</label>
							<input
								id="crud-form-1"
								class="form-control w-full"
								placeholder="Input text"
								type="text"
								value="XX00"
							/>
						</div>
					</div>
				</div>

				<div class="text-right mt-5">
					<button class="btn btn-outline-primary w-36 mr-4 border-2" type="button" @click="$router.go(-1)">
						Annuler
					</button>
					<button
						class="btn btn-primary w-36"
						type="button"
						@click="$router.push('/bank/pledge/VehicleInfo')"
					>
						Vérifier
					</button>
				</div>
			</div>
		</div>
	</div>
</template>

<script setup>
	import SideBar from "@/layouts/SideBar.vue";
	import MenuLink from "@/components/MenuLink.vue";
	import BackButton from "@/components/BackButton.vue";
</script>
