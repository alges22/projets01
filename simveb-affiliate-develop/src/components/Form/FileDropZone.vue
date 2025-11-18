<template>
	<label v-if="label" :class="required ? 'required' : ''" :for="name" class="form-label">
		{{ label }}
	</label>
	<div
		ref="dropZoneRef"
		:class="isOverDropZone ? 'border-indigo-600 border-2 bg-gray-100' : 'border-dashed border-gray-900/25 bg-white'"
		class="mt-2 flex-col justify-center rounded-lg border border-dashed border-gray-900/25 px-6 py-10"
	>
		<div class="text-center">
			<i aria-hidden="true" class="mx-auto h-12 w-12 text-gray-300 fa text-4xl fa-cloud-upload-alt"></i>
			<div class="mt-4 flex text-sm leading-6 text-gray-600">
				<label
					class="relative cursor-pointer rounded-md bg-white font-semibold text-indigo-600 focus-within:outline-none focus-within:ring-2 focus-within:ring-indigo-600 focus-within:ring-offset-2 hover:text-indigo-500"
					for="file-upload"
				>
					<button type="button" @click="openFileBrowser">Télécharger un fichier</button>
					<input
						:id="name"
						class="sr-only"
						type="file"
						:name="name"
						:required="required"
						:class="{ 'is-invalid': errors.length > 0 }"
						:disabled="disabled"
						:accept="accept"
						:multiple="multiple"
						@change="handleChange($event)"
					/>
				</label>
				<p class="pl-1">ou glisser-déposer</p>
			</div>
			<slot></slot>
		</div>

		<template v-if="uploadedFiles.length > 0">
			<div
				v-for="(file, index) in uploadedFiles"
				:key="index"
				class="mt-4 flex items-center justify-between bg-gray-100 rounded-lg p-2"
			>
				<div class="flex items-center">
					<div class="ml-4">
						<p class="text-sm font-semibold text-gray-800">{{ file.name }}</p>
						<p class="text-xs text-gray-600">{{ file.formattedSize }}</p>
					</div>
				</div>
				<div class="flex items-center">
					<button
						type="button"
						class="text-red-600 hover:text-red-800 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2"
						@click="removeFile(index)"
					>
						<i class="fas fa-trash-alt"></i>
					</button>
				</div>
			</div>
		</template>
	</div>
	<div v-if="errors.length > 0" class="invalid-feedback">
		<template v-for="error in errors">
			{{ error }}
		</template>
	</div>
</template>

<script setup>
	import { useDropZone } from "@vueuse/core";
	import { ref } from "vue";

	const props = defineProps({
		modelValue: {},
		label: {
			type: String,
			required: false,
		},
		name: {
			type: String,
			required: true,
		},
		errors: {
			type: Array,
			required: false,
			default: () => [],
		},
		required: {
			type: Boolean,
			required: false,
			default: false,
		},
		disabled: {
			type: Boolean,
			required: false,
			default: false,
		},
		class: {
			type: String,
			required: false,
		},
		image: {
			type: Array,
			required: false,
		},
		accept: {
			type: String,
			required: false,
		},
		multiple: {
			type: Boolean,
			required: false,
			default: true,
		},
		help: {
			type: String,
			required: false,
			default: "Sélectionner un fichier",
		},
	});
	const emit = defineEmits(["update:modelValue"]);

	const dropZoneRef = ref();
	const uploadedFiles = ref([]);

	function onDrop(files) {
		files.forEach((file) => {
			const reader = new FileReader();
			reader.onload = (e) => {
				file.preview = e.target.result;
			};
			reader.readAsDataURL(file);
			file.formattedSize = formatSize(file.size);
		});
		props.multiple ? uploadedFiles.value.push(...files) : (uploadedFiles.value = files);
		emit("update:modelValue", props.multiple ? uploadedFiles.value : uploadedFiles.value[0]);
	}

	const handleChange = (event) => {
		const files = event.target.files;
		if (files.length > 0) {
			onDrop(files);
		}
	};

	const removeFile = (index) => {
		uploadedFiles.value.splice(index, 1);
		emit("update:modelValue", uploadedFiles.value);
	};

	const { isOverDropZone } = useDropZone(dropZoneRef, onDrop);

	const formatSize = (size) => {
		const units = ["B", "KB", "MB", "GB", "TB"];
		let unitIndex = 0;
		while (size > 1024) {
			size /= 1024;
			unitIndex++;
		}
		return `${size.toFixed(2)} ${units[unitIndex]}`;
	};

	const openFileBrowser = () => {
		const input = document.createElement("input");
		input.type = "file";
		input.accept = props.accept;
		input.multiple = props.multiple;
		input.click();
		input.onchange = (e) => {
			let files = [];
			for (let i = 0; i < e.target.files.length; i++) {
				files.push(e.target.files[i]);
			}
			onDrop(files);
		};
	};
</script>

<style scoped></style>
