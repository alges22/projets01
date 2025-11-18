<template>
	<div class="grid grid-cols-12 gap-6 mt-5">
		<div class="intro-y col-span-12">
			<div class="intro-y box p-5">
				<div class="sm:grid gap-8 mb-8">
					<div>
						<label class="form-label" for="crud-form-1">Sélectionner la banque concernée</label>
						<select aria-label="Default select example" class="form-select">
							<option>BOA Benin</option>
							<option value="Inch'Allah">Future Spider-Bank</option>
						</select>
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
					@click="$router.push('/parc/dashboard/declaration/VerifInfoVehicule')"
				>
					Suivant
				</button>
			</div>
		</div>
	</div>
</template>

<script setup></script>
