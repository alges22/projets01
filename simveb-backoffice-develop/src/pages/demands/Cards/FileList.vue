<template>
	<div class="list-widget">
		<div class="inner-list-wrapper is-active">
			<div class="inner-list">
				<div v-if="files.length > 0">
					<VBlock v-for="(file, index) in files" :key="index" center lighter class="inner-list-item">
						<template #icon>
							<img class="image-icon" src="/@src/assets/icons/doc.svg" alt="" />
						</template>
						<template #action>
							<VIconButton color="success" light circle icon="download" @click="downloadFile(file)" />
						</template>

						<a target="_blank" :href="file.url">{{ file.name }}</a>
						<span>{{ file.type }}</span>
					</VBlock>
				</div>
				<div v-else>Aucun fichier uploadé</div>
			</div>
		</div>
	</div>
</template>

<script setup lang="ts">
	import client from "/@src/composable/axiosClient";

	defineProps<{
		files?: any[];
	}>();

	const downloadFile = (file) => {
		client({
			method: "GET",
			url: `/download/${file.id}`,
			responseType: "blob",
			data: {},
		}).then((response) => {
			const href = URL.createObjectURL(response.data);
			const link = document.createElement("a");
			link.href = href;
			link.setAttribute("download", `${file.name ?? "file"}`);
			link.setAttribute("target", "_blank");
			document.body.appendChild(link);
			link.click();

			document.body.removeChild(link);
			URL.revokeObjectURL(href);
		});
	};
</script>

<style lang="scss">
	@import "/@src/scss/abstracts/all";

	.list-widget {
		flex: 1;
		display: inline-block;
		width: 100%;
		padding: 15px;
		background-color: var(--white);
		transition: all 0.3s; // transition-all test

		&:not(:last-child) {
			margin-bottom: 1.5rem;
		}

		&.is-straight {
			@include vuero-s-card;
		}

		.inner-list {
			padding: 10px 0;

			.inner-list-item {
				+ .inner-list-item {
					margin-top: 24px;
				}
			}
		}
	}

	.is-dark {
		.list-widget {
			@include vuero-card--dark;
		}
	}

	.image-icon {
		height: 40px;
		width: 40px;
	}
</style>
