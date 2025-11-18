<template>
    <div>
        <Form action="">
            <div class="w-full mt-3">
                <label for="numeroEnregistrement" class="font-xs mb-2">Nom de l'acheteur</label>
                <input type="text" class="form-control" placeholder="Nom de l'acheteur">
            </div>
            <div class="w-full mt-3">
                <label for="numeroIFU" class="font-xs mb-2">Prénom de l'acheteur</label>
                <input type="text" class="form-control" placeholder="Prénom de l'acheteur">
            </div>
            <div class="w-full mt-3">
                <label for="nom" class="font-xs mb-2">Numéro passport de l'acheteur</label>
                <input type="text" class="form-control" placeholder="Numéro passport de l'acheteur">
            </div>
            <div class="w-full mt-3">
                <label for="telephone" class="font-xs mb-2">Téléphone de l'acheteur</label>
                <input type="text" class="form-control" placeholder="Téléphone de l'acheteur">
            </div>
        </Form>
    </div>
</template>

<script setup>
    import * as yup from "yup";
    import {ErrorMessage, Field, Form} from "vee-validate";
    const schemaBeninoisPersonnePhysique = yup.object({
		npi: yup
			.number()
			.required("Veuillez renseigner un npi valide"),
	});
</script>

<style lang="css" scoped>

</style>