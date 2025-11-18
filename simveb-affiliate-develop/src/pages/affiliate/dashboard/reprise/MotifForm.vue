<template>
	<div class="grid grid-cols-12 gap-6 mt-5">
		<div class="intro-y col-span-12">
			<div class="intro-y box p-5">
				<div class="sm:grid gap-8 mb-8">
					<div>
						<label class="form-label" for="crud-form-1">Sélectionner le motif</label>
						<select aria-label="Default select example" class="form-select">
							<option>Transfert intra-familial</option>
							<option>Changement de propriétaire</option>
							<option>Changement d'adresse</option>
							<option>Fin de vocation/Leasing</option>
							<option>Conversion de titres</option>
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
					@click="$router.push('/affiliate/dashboard/reprise/PlateCaracteristicForm')"
				>
					Suivant
				</button>
			</div>
		</div>
	</div>
</template>

<script setup></script>
