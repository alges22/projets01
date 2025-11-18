<template>
	<div class="flex items-center justify-center border-4 border-black rounded-lg px-2 bg-white w-96">
		<!-- Letters and numbers -->
		<div class="flex items-center space-x-2 text-5xl font-bold">
			<span class="text-black">AE</span>
			<span class="text-black">AE</span>
			<span class="text-black">8888</span>
		</div>

		<!-- Space between letters/numbers and the right-side graphics -->
		<div class="flex-1"></div>

		<!-- Right-side graphics -->
		<div class="flex items-center space-x-2">
			<!-- Rectangle with text -->
			<div
				class="flex flex-col items-center justify-center bg-orange-400 text-white text-xs p-1 border border-black"
			>
				<span>xx</span>
			</div>
			<!-- Country flag graphic -->
			<div class="flex flex-col items-center justify-center">
				<div class="flex items-center">
					<BeninFlagCardIcon />
				</div>
				<!-- Country code -->
				<div class="text-lg font-bold text-black">RB</div>
			</div>
		</div>
	</div>
	<div class="border-4 border-black rounded-lg p-2 bg-white w-56 h-32 mx-auto">
		<div class="flex flex-col h-full justify-between">
			<!-- Partie supérieure -->
			<div class="flex justify-between items-center h-16">
				<!-- Lettres -->
				<div class="flex items-center text-4xl font-bold">
					<span class="text-black">CN</span>
				</div>
				<!-- Espace -->
				<div
					class="flex flex-col items-center justify-center bg-orange-400 w-8 text-white text-xs p-1 border border-black"
				>
					<span>xx</span>
				</div>
				<!-- Drapeau et code du pays -->
				<div class="flex flex-col items-center justify-center">
					<div class="flex items-center">
						<BeninFlagCardIcon />
					</div>
					<!-- Country code -->
					<div class="text-xl font-bold text-black">RB</div>
				</div>
			</div>
			<!-- Partie inférieure -->
			<div class="flex justify-center text-5xl font-bold h-16">
				<span class="text-black me-2" style="letter-spacing: 0.5rem">AA</span>
				<span class="text-black" style="letter-spacing: 0.5rem">0001</span>
			</div>
		</div>
	</div>
</template>
<script setup>
	import BeninFlagCardIcon from "@/components/immatriculation/BeninFlagCardIcon.vue";
	/*
	[
    {
        "id": "9c54e0d4-dfa9-4a3a-a3e1-60d76b7f20eb",
        "path": {
            "path": "storage/printed_plate_images/gtBLALpkmJzh3Fgq.gif",
            "name": "БЕНІН_НОМЕР_1.gif",
            "type": "image"
        },
        "model_type": "App\\Models\\Treatment\\PrintOrder",
        "model_id": "9c52b16c-d8bf-46ca-b576-c1a62f6001f5",
        "type": "image",
        "file_type_id": null,
        "created_at": "2024-06-20T11:40:32.000000Z",
        "updated_at": "2024-06-20T11:40:32.000000Z",
        "url": "http://localhost:8000/storage/printed_plate_images/gtBLALpkmJzh3Fgq.gif"
    },
    {
        "id": "9c54e0d4-e0c7-49ae-b3e9-7d7e6868ad81",
        "path": {
            "path": "storage/printed_plate_images/wPREp47jG4qbxqAV.png",
            "name": "image.png",
            "type": "image"
        },
        "model_type": "App\\Models\\Treatment\\PrintOrder",
        "model_id": "9c52b16c-d8bf-46ca-b576-c1a62f6001f5",
        "type": "image",
        "file_type_id": null,
        "created_at": "2024-06-20T11:40:32.000000Z",
        "updated_at": "2024-06-20T11:40:32.000000Z",
        "url": "http://localhost:8000/storage/printed_plate_images/wPREp47jG4qbxqAV.png"
    }
]
	 */
</script>
