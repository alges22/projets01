<template>
    <div id="webview" ref="viewer" style="height: 100vh; width: 100%;"></div>
</template>

<script setup>
    import { onMounted, ref } from 'vue';
    let viewer = ref(null);

    const props = defineProps({
        path: {
            type: String,
            required: true
        },
        url: {
            type: String,
            required: true
        }
    });

    onMounted(() => {
        if (process.client) {
            import("@pdftron/webviewer").then(({ default: WebViewer }) => {
                WebViewer(
                    {
                        path: props.path,
                        initialDoc: props.url,
                        enableReadOnly: true,
                        disableAnnotations: true,
                        fullAPI: true,
                        ui: 'minimal',
                    },
                    viewer.value // Reference the viewer div using ref
                ).then(instance => {
                    instance.UI.disableElements(['header', 'toolsHeader', 'sidePanel']);
                });
            });
        }
    });
</script>

<style scoped></style>
