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
					<div class="mb-8">
						<div>
							<label class="form-label">Dossier de gage</label>
							<label
								class="flex rounded-lg border bg-[#F4F6F9] px-4 py-2 items-center cursor-pointer focus-within:outline-none focus-within:ring-2 focus-within:ring-indigo-600 focus-within:ring-offset-2 hover:text-indigo-500"
								for="file-upload"
							>
								<i class="text-[#6D7580] me-8 fa-solid text-2xl fa-cloud-arrow-up"></i>
								<div class="flex text-sm leading-5 text-dark">
									<div class="relative rounded-md font-semibold">
										<span>Sélectionner le fichier</span>
									</div>
								</div>
							</label>
							<input id="file-upload" class="sr-only" name="file-upload" type="file" />
							<p class="mt-1 text-xs leading-4 text-gray-600">
								Format pris en compte: png, jpg - Taille : 500 Ko
							</p>
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
						@click="$router.push('/bank/pledge/ApplicationSuccess')"
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
