<script setup lang="ts">
	import { useRegisterSW } from "virtual:pwa-register/vue";

	export interface VReloadPromptProps {
		appName: string;
	}

	const loading = ref(false);
	const props = defineProps<VReloadPromptProps>();

	const { offlineReady, needRefresh, updateServiceWorker } = useRegisterSW();

	const close = async () => {
		loading.value = false;
		offlineReady.value = false;
		needRefresh.value = false;
	};
	const update = async () => {
		loading.value = true;
		await updateServiceWorker();
		loading.value = false;
	};
</script>

<template>
	<Transition name="from-bottom">
		<VCard v-if="offlineReady || needRefresh" class="pwa-toast" role="alert" radius="smooth">
			<div class="pwa-message">
				<span v-if="offlineReady"> {{ props.appName }} est prêt à être utilisé hors ligne </span>
				<span v-else>
					Une nouvelle version de {{ props.appName }} est disponible, cliquez sur le bouton Recharger pour la
					mettre à jour.
				</span>
			</div>
			<VButtons align="right">
				<VButton
					v-if="needRefresh"
					color="primary"
					icon="fa fa-rotate"
					:loading="loading"
					@click="() => update()"
				>
					Recharger
				</VButton>
				<VButton icon="x" @click="close"> Fermer </VButton>
			</VButtons>
		</VCard>
	</Transition>
</template>

<style lang="scss">
	.pwa-toast {
		position: fixed;
		inset-inline-end: 0;
		bottom: 0;
		max-width: 350px;
		margin: 16px;
		padding: 12px;
		border: 1px solid #8885;
		border-radius: 4px;
		z-index: 10;
		text-align: inset-inline-start;
		box-shadow: 3px 4px 5px 0 #8885;
	}

	.pwa-message {
		padding: 0.5rem 1rem;
		margin-bottom: 1rem;
		font-size: 1.1rem;
	}
</style>
