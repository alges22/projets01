<template>
	<div class="content">
		<div class="top-bar -mx-4 px-4 md:mx-0 md:px-0 md:align-baseline mt-4">
			<BackButton />

			<nav aria-label="breadcrumb" class="-intro-x mr-auto hidden sm:flex">
				<ol class="breadcrumb">
					<li class="breadcrumb-item">Paiement</li>
				</ol>
			</nav>
		</div>

		<div class="justify-center flex mt-8">
			<div class="dashboard-card md:w-2/3 w-full lg:w-1/2 mx-auto">
				<div class="p-5 text-center">
					<svg
						class="lucide lucide-check-circle w-24 h-24 text-success mx-auto mt-3"
						fill="none"
						height="32"
						stroke="currentColor"
						stroke-linecap="round"
						stroke-linejoin="round"
						stroke-width="2"
						viewBox="0 0 24 24"
						width="32"
						xmlns="http://www.w3.org/2000/svg"
					>
						<path d="M22 11.08V12a10 10 0 11-5.93-9.14"></path>
						<polyline points="22 4 12 14.01 9 11.01"></polyline>
					</svg>
					<div class="text-2xl mt-5 font-bold">Paiement réussi !</div>
					<div class="mt-2 text-base mb-4">
						Un email contenant les détails de votre commande a été envoyé à votre adresse email
						customer@gmail.com
					</div>

					<hr />

					<div class="px-5 pb-8 text-center mt-8">
						<button
							class="btn btn-outline-primary p-5 mx-2 text-base"
							type="button"
							@click="$router.push('/affiliate/dashboard')"
						>
							Voir la facture
						</button>
					</div>

					<div class="mt-2 text-base">
						<p>Une question ou une préoccupation ?</p>
						<p>Veuillez nous contacter au XXXXXXXXXXXX</p>
					</div>
				</div>
			</div>
		</div>
	</div>
</template>

<script setup>
	import BackButton from "@/components/BackButton.vue";
</script>
