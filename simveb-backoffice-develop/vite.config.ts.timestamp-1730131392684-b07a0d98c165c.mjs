// vite.config.ts
import { fileURLToPath, URL } from "node:url";
import { defineConfig } from "file:///opt/lampp/htdocs/simveb-backoffice/node_modules/vite/dist/node/index.js";
import Vue from "file:///opt/lampp/htdocs/simveb-backoffice/node_modules/@vitejs/plugin-vue/dist/index.mjs";
import VueRouter from "file:///opt/lampp/htdocs/simveb-backoffice/node_modules/unplugin-vue-router/dist/vite.mjs";
import { VueRouterAutoImports } from "file:///opt/lampp/htdocs/simveb-backoffice/node_modules/unplugin-vue-router/dist/index.mjs";
import Components from "file:///opt/lampp/htdocs/simveb-backoffice/node_modules/unplugin-vue-components/dist/vite.mjs";
import AutoImport from "file:///opt/lampp/htdocs/simveb-backoffice/node_modules/unplugin-auto-import/dist/vite.js";
import Unfonts from "file:///opt/lampp/htdocs/simveb-backoffice/node_modules/unplugin-fonts/dist/vite.mjs";
import { VitePluginRadar } from "file:///opt/lampp/htdocs/simveb-backoffice/node_modules/vite-plugin-radar/dist/index.js";
import ImageMin from "file:///opt/lampp/htdocs/simveb-backoffice/node_modules/vite-plugin-imagemin/dist/index.mjs";
import { VitePWA } from "file:///opt/lampp/htdocs/simveb-backoffice/node_modules/vite-plugin-pwa/dist/index.js";
import purgecss from "file:///opt/lampp/htdocs/simveb-backoffice/node_modules/rollup-plugin-purgecss/lib/rollup-plugin-purgecss.js";
var __vite_injected_original_import_meta_url = "file:///opt/lampp/htdocs/simveb-backoffice/vite.config.ts";
var MINIFY_IMAGES = process.env.MINIFY ? process.env.MINIFY === "true" : false;
var vite_config_default = defineConfig({
  // Project root directory (where index.html is located).
  root: process.cwd(),
  // Base public path when served in development or production.
  // You also need to add this base like `history: createWebHistory('my-subdirectory')`
  // in ./src/router.ts
  // base: '/my-subdirectory/',
  base: "/",
  // Directory to serve as plain static assets.
  publicDir: "public",
  // Adjust console output verbosity.
  logLevel: "info",
  // development server configuration
  server: {
    // Vite 4 defaults to 5173, but you can override it with the port option.
    port: 3e3
  },
  /**
   * By default, Vite will crawl your index.html to detect dependencies that
   * need to be pre-bundled. If build.rollupOptions.input is specified,
   * Vite will crawl those entry points instead.
   *
   * @see https://vitejs.dev/config/#optimizedeps-entries
   */
  optimizeDeps: {
    include: [
      "@ckeditor/ckeditor5-vue",
      "@ckeditor/ckeditor5-build-classic",
      "@mapbox/mapbox-gl-geocoder/dist/mapbox-gl-geocoder.min.js",
      "@vee-validate/zod",
      "@vueuse/core",
      "@vueuse/head",
      "@vueform/multiselect",
      "@vueform/slider",
      "axios",
      "billboard.js",
      "dayjs",
      "dropzone",
      "dragula",
      "defu",
      "filepond",
      "filepond-plugin-file-validate-size",
      "filepond-plugin-file-validate-type",
      "filepond-plugin-image-exif-orientation",
      "filepond-plugin-image-crop",
      "filepond-plugin-image-edit",
      "filepond-plugin-image-preview",
      "filepond-plugin-image-resize",
      "filepond-plugin-image-transform",
      "imask",
      "nprogress",
      "notyf",
      "mapbox-gl",
      "photoswipe/lightbox",
      "photoswipe",
      "plyr",
      "v-calendar",
      "vee-validate",
      "vue",
      "vue-scrollto",
      "vue3-apexcharts",
      "vue-tippy",
      "vue-i18n",
      "vue-router",
      "unplugin-vue-router/runtime",
      "simplebar",
      "simple-datatables",
      "tiny-slider/src/tiny-slider",
      "vue-accessible-color-picker",
      "zod",
      "@stefanprobst/remark-shiki",
      "rehype-external-links",
      "rehype-raw",
      "rehype-sanitize",
      "rehype-stringify",
      "rehype-slug",
      "rehype-autolink-headings",
      "remark-gfm",
      "remark-parse",
      "remark-rehype",
      "shiki",
      "unified",
      "workbox-window",
      "textarea-markdown-editor/dist/esm/bootstrap"
    ]
    // disabled: false,
  },
  // Will be passed to @rollup/plugin-alias as its entries option.
  resolve: {
    alias: [
      {
        find: "/@src/",
        replacement: `/src/`
      },
      {
        find: "@",
        replacement: fileURLToPath(new URL("./src", __vite_injected_original_import_meta_url))
      }
    ]
  },
  build: {
    minify: "terser",
    // Do not warn about large chunks
    // chunkSizeWarningLimit: Infinity,
    // Double the default size threshold for inlined assets
    // https://vitejs.dev/config/build-options.html#build-assetsinlinelimit
    assetsInlineLimit: 4096 * 2
    // commonjsOptions: { include: [] },
  },
  plugins: [
    /**
     * plugin-vue plugin inject vue library and allow sfc files to work (*.vue)
     *
     * @see https://github.com/vitejs/vite/tree/main/packages/plugin-vue
     */
    Vue({
      include: [/\.vue$/]
    }),
    /**
     * unplugin-vue-router plugin generate routes based on file system
     * allow to use typed routes and usage of defineLoader
     *
     * @see https://github.com/posva/unplugin-vue-router
     * @see https://github.com/vuejs/rfcs/blob/ad69da2aee9242ef88f036713db68f3ef274bb1b/active-rfcs/0000-router-use-loader.md
     */
    VueRouter({
      routesFolder: "src/pages",
      /**
       * Data Fetching is an experimental feature from vue & vue-router
       *
       * @see https://github.com/vuejs/rfcs/discussions/460
       * @see https://github.com/posva/unplugin-vue-router/tree/main/src/data-fetching
       */
      dataFetching: true
    }),
    /**
     * unplugin-auto-import allow to automaticaly import modules/components
     *
     * @see https://github.com/antfu/unplugin-auto-import
     */
    AutoImport({
      dts: true,
      imports: ["vue", "@vueuse/core", VueRouterAutoImports]
    }),
    /**
     * This is an internal vite plugin that load markdown files as vue components.
     *
     * @see /documentation
     * @see /vite-plugin-vuero-doc
     * @see /src/components/partials/documentation/DocumentationItem.vue
     * @see /src/composable/useMarkdownToc.ts
     */
    // VitePluginVueroDoc({
    //   pathPrefix: 'documentation',
    //   wrapperComponent: 'DocumentationItem',
    //   shiki: {
    //     theme: {
    //       light: 'min-light',
    //       dark: 'github-dark',
    //     },
    //   },
    //   sourceMeta: {
    //     enabled: true,
    //     editProtocol: 'vscode://vscode-remote/wsl+Ubuntu', // or 'vscode://file'
    //   },
    // }),
    /**
     * unplugin-vue-components plugin is responsible of autoloading components
     * documentation and md file are loaded for elements and components sections
     *
     * @see https://github.com/antfu/unplugin-vue-components
     */
    Components({
      dirs: ["documentation", "src/components", "src/layouts"],
      extensions: ["vue", "md"],
      dts: true,
      include: [/\.vue$/, /\.vue\?vue/, /\.md$/]
    }),
    /**
     * vite-plugin-fonts plugin inject webfonts from differents providers
     *
     * @see https://github.com/stafyniaksacha/vite-plugin-fonts
     */
    Unfonts({
      google: {
        families: [
          {
            name: "Fira Code",
            styles: "wght@400;600"
          },
          {
            name: "Montserrat",
            styles: "wght@500;600;700;800;900"
          },
          {
            name: "Roboto",
            styles: "wght@300;400;500;600;700"
          }
        ]
      }
    }),
    /**
     * vite-plugin-radar plugin inject snippets from analytics providers
     *
     * @see https://github.com/stafyniaksacha/vite-plugin-radar
     */
    !process.env.GTM_ID ? void 0 : VitePluginRadar({
      gtm: {
        id: process.env.GTM_ID
      }
    }),
    /**
     * vite-plugin-pwa generate manifest.json and register services worker to enable PWA
     *
     * @see https://github.com/antfu/vite-plugin-pwa
     */
    VitePWA({
      base: "/",
      includeAssets: ["favicon.svg", "favicon.ico", "robots.txt", "apple-touch-icon.png"],
      manifest: {
        name: "Vuero - A complete Vue 3 design system",
        short_name: "Vuero",
        start_url: "/?utm_source=pwa",
        display: "standalone",
        theme_color: "#ffffff",
        background_color: "#ffffff",
        icons: [
          {
            src: "pwa-192x192.png",
            sizes: "192x192",
            type: "image/png"
          },
          {
            src: "pwa-512x512.png",
            sizes: "512x512",
            type: "image/png"
          },
          {
            src: "pwa-512x512.png",
            sizes: "512x512",
            type: "image/png",
            purpose: "any maskable"
          }
        ]
      }
    }),
    /**
     * rollup-plugin-purgecss plugin is responsible of purging css rules
     * that are not used in the bundle
     *
     * @see https://github.com/FullHuman/purgecss/tree/main/packages/rollup-plugin-purgecss
     */
    purgecss({
      output: false,
      content: [`./src/**/*.vue`],
      variables: false,
      safelist: {
        standard: [
          /(autv|lnil|lnir|fas?)/,
          /-(leave|enter|appear)(|-(to|from|active))$/,
          /^(?!(|.*?:)cursor-move).+-move$/,
          /^router-link(|-exact)-active$/,
          /data-v-.*/
        ]
      },
      defaultExtractor(content) {
        const contentWithoutStyleBlocks = content.replace(/<style[^]+?<\/style>/gi, "");
        return contentWithoutStyleBlocks.match(/[A-Za-z0-9-_/:]*[A-Za-z0-9-_/]+/g) || [];
      }
    }),
    /**
     * vite-plugin-imagemin optimize all images sizes from public or asset folder
     *
     * @see https://github.com/anncwb/vite-plugin-imagemin
     */
    !MINIFY_IMAGES ? void 0 : ImageMin({
      gifsicle: {
        optimizationLevel: 7,
        interlaced: false
      },
      optipng: {
        optimizationLevel: 7
      },
      mozjpeg: {
        quality: 60
      },
      pngquant: {
        quality: [0.8, 0.9],
        speed: 4
      },
      svgo: {
        plugins: [
          {
            name: "removeViewBox",
            active: false
          },
          {
            name: "removeEmptyAttrs",
            active: false
          }
        ]
      }
    })
  ]
});
export {
  vite_config_default as default
};
//# sourceMappingURL=data:application/json;base64,ewogICJ2ZXJzaW9uIjogMywKICAic291cmNlcyI6IFsidml0ZS5jb25maWcudHMiXSwKICAic291cmNlc0NvbnRlbnQiOiBbImNvbnN0IF9fdml0ZV9pbmplY3RlZF9vcmlnaW5hbF9kaXJuYW1lID0gXCIvb3B0L2xhbXBwL2h0ZG9jcy9zaW12ZWItYmFja29mZmljZVwiO2NvbnN0IF9fdml0ZV9pbmplY3RlZF9vcmlnaW5hbF9maWxlbmFtZSA9IFwiL29wdC9sYW1wcC9odGRvY3Mvc2ltdmViLWJhY2tvZmZpY2Uvdml0ZS5jb25maWcudHNcIjtjb25zdCBfX3ZpdGVfaW5qZWN0ZWRfb3JpZ2luYWxfaW1wb3J0X21ldGFfdXJsID0gXCJmaWxlOi8vL29wdC9sYW1wcC9odGRvY3Mvc2ltdmViLWJhY2tvZmZpY2Uvdml0ZS5jb25maWcudHNcIjtpbXBvcnQgeyBmaWxlVVJMVG9QYXRoLCBVUkwgfSBmcm9tIFwibm9kZTp1cmxcIjtcbmltcG9ydCB7IGRlZmluZUNvbmZpZyB9IGZyb20gXCJ2aXRlXCI7XG5pbXBvcnQgVnVlIGZyb20gXCJAdml0ZWpzL3BsdWdpbi12dWVcIjtcbmltcG9ydCBWdWVSb3V0ZXIgZnJvbSBcInVucGx1Z2luLXZ1ZS1yb3V0ZXIvdml0ZVwiO1xuaW1wb3J0IHsgVnVlUm91dGVyQXV0b0ltcG9ydHMgfSBmcm9tIFwidW5wbHVnaW4tdnVlLXJvdXRlclwiO1xuaW1wb3J0IENvbXBvbmVudHMgZnJvbSBcInVucGx1Z2luLXZ1ZS1jb21wb25lbnRzL3ZpdGVcIjtcbmltcG9ydCBBdXRvSW1wb3J0IGZyb20gXCJ1bnBsdWdpbi1hdXRvLWltcG9ydC92aXRlXCI7XG5pbXBvcnQgVW5mb250cyBmcm9tIFwidW5wbHVnaW4tZm9udHMvdml0ZVwiO1xuaW1wb3J0IHsgVml0ZVBsdWdpblJhZGFyIH0gZnJvbSBcInZpdGUtcGx1Z2luLXJhZGFyXCI7XG5pbXBvcnQgSW1hZ2VNaW4gZnJvbSBcInZpdGUtcGx1Z2luLWltYWdlbWluXCI7XG5pbXBvcnQgeyBWaXRlUFdBIH0gZnJvbSBcInZpdGUtcGx1Z2luLXB3YVwiO1xuaW1wb3J0IHB1cmdlY3NzIGZyb20gXCJyb2xsdXAtcGx1Z2luLXB1cmdlY3NzXCI7XG5cbi8vIGxvY2FsIHZpdGUgcGx1Z2luXG4vLyBpbXBvcnQgeyBWaXRlUGx1Z2luVnVlcm9Eb2MgfSBmcm9tICcuL3ZpdGUtcGx1Z2luLXZ1ZXJvLWRvYydcblxuLy8gb3B0aW9ucyB2aWEgZW52IHZhcmlhYmxlc1xuY29uc3QgTUlOSUZZX0lNQUdFUyA9IHByb2Nlc3MuZW52Lk1JTklGWSA/IHByb2Nlc3MuZW52Lk1JTklGWSA9PT0gXCJ0cnVlXCIgOiBmYWxzZTtcblxuLyoqXG4gKiBUaGlzIGlzIHRoZSBtYWluIGNvbmZpZ3VyYXRpb24gZmlsZSBmb3Igdml0ZWpzXG4gKlxuICogQHNlZSBodHRwczovL3ZpdGVqcy5kZXYvY29uZmlnXG4gKi9cbmV4cG9ydCBkZWZhdWx0IGRlZmluZUNvbmZpZyh7XG5cdC8vIFByb2plY3Qgcm9vdCBkaXJlY3RvcnkgKHdoZXJlIGluZGV4Lmh0bWwgaXMgbG9jYXRlZCkuXG5cdHJvb3Q6IHByb2Nlc3MuY3dkKCksXG5cdC8vIEJhc2UgcHVibGljIHBhdGggd2hlbiBzZXJ2ZWQgaW4gZGV2ZWxvcG1lbnQgb3IgcHJvZHVjdGlvbi5cblx0Ly8gWW91IGFsc28gbmVlZCB0byBhZGQgdGhpcyBiYXNlIGxpa2UgYGhpc3Rvcnk6IGNyZWF0ZVdlYkhpc3RvcnkoJ215LXN1YmRpcmVjdG9yeScpYFxuXHQvLyBpbiAuL3NyYy9yb3V0ZXIudHNcblx0Ly8gYmFzZTogJy9teS1zdWJkaXJlY3RvcnkvJyxcblx0YmFzZTogXCIvXCIsXG5cdC8vIERpcmVjdG9yeSB0byBzZXJ2ZSBhcyBwbGFpbiBzdGF0aWMgYXNzZXRzLlxuXHRwdWJsaWNEaXI6IFwicHVibGljXCIsXG5cdC8vIEFkanVzdCBjb25zb2xlIG91dHB1dCB2ZXJib3NpdHkuXG5cdGxvZ0xldmVsOiBcImluZm9cIixcblx0Ly8gZGV2ZWxvcG1lbnQgc2VydmVyIGNvbmZpZ3VyYXRpb25cblx0c2VydmVyOiB7XG5cdFx0Ly8gVml0ZSA0IGRlZmF1bHRzIHRvIDUxNzMsIGJ1dCB5b3UgY2FuIG92ZXJyaWRlIGl0IHdpdGggdGhlIHBvcnQgb3B0aW9uLlxuXHRcdHBvcnQ6IDMwMDAsXG5cdH0sXG5cdC8qKlxuXHQgKiBCeSBkZWZhdWx0LCBWaXRlIHdpbGwgY3Jhd2wgeW91ciBpbmRleC5odG1sIHRvIGRldGVjdCBkZXBlbmRlbmNpZXMgdGhhdFxuXHQgKiBuZWVkIHRvIGJlIHByZS1idW5kbGVkLiBJZiBidWlsZC5yb2xsdXBPcHRpb25zLmlucHV0IGlzIHNwZWNpZmllZCxcblx0ICogVml0ZSB3aWxsIGNyYXdsIHRob3NlIGVudHJ5IHBvaW50cyBpbnN0ZWFkLlxuXHQgKlxuXHQgKiBAc2VlIGh0dHBzOi8vdml0ZWpzLmRldi9jb25maWcvI29wdGltaXplZGVwcy1lbnRyaWVzXG5cdCAqL1xuXHRvcHRpbWl6ZURlcHM6IHtcblx0XHRpbmNsdWRlOiBbXG5cdFx0XHRcIkBja2VkaXRvci9ja2VkaXRvcjUtdnVlXCIsXG5cdFx0XHRcIkBja2VkaXRvci9ja2VkaXRvcjUtYnVpbGQtY2xhc3NpY1wiLFxuXHRcdFx0XCJAbWFwYm94L21hcGJveC1nbC1nZW9jb2Rlci9kaXN0L21hcGJveC1nbC1nZW9jb2Rlci5taW4uanNcIixcblx0XHRcdFwiQHZlZS12YWxpZGF0ZS96b2RcIixcblx0XHRcdFwiQHZ1ZXVzZS9jb3JlXCIsXG5cdFx0XHRcIkB2dWV1c2UvaGVhZFwiLFxuXHRcdFx0XCJAdnVlZm9ybS9tdWx0aXNlbGVjdFwiLFxuXHRcdFx0XCJAdnVlZm9ybS9zbGlkZXJcIixcblx0XHRcdFwiYXhpb3NcIixcblx0XHRcdFwiYmlsbGJvYXJkLmpzXCIsXG5cdFx0XHRcImRheWpzXCIsXG5cdFx0XHRcImRyb3B6b25lXCIsXG5cdFx0XHRcImRyYWd1bGFcIixcblx0XHRcdFwiZGVmdVwiLFxuXHRcdFx0XCJmaWxlcG9uZFwiLFxuXHRcdFx0XCJmaWxlcG9uZC1wbHVnaW4tZmlsZS12YWxpZGF0ZS1zaXplXCIsXG5cdFx0XHRcImZpbGVwb25kLXBsdWdpbi1maWxlLXZhbGlkYXRlLXR5cGVcIixcblx0XHRcdFwiZmlsZXBvbmQtcGx1Z2luLWltYWdlLWV4aWYtb3JpZW50YXRpb25cIixcblx0XHRcdFwiZmlsZXBvbmQtcGx1Z2luLWltYWdlLWNyb3BcIixcblx0XHRcdFwiZmlsZXBvbmQtcGx1Z2luLWltYWdlLWVkaXRcIixcblx0XHRcdFwiZmlsZXBvbmQtcGx1Z2luLWltYWdlLXByZXZpZXdcIixcblx0XHRcdFwiZmlsZXBvbmQtcGx1Z2luLWltYWdlLXJlc2l6ZVwiLFxuXHRcdFx0XCJmaWxlcG9uZC1wbHVnaW4taW1hZ2UtdHJhbnNmb3JtXCIsXG5cdFx0XHRcImltYXNrXCIsXG5cdFx0XHRcIm5wcm9ncmVzc1wiLFxuXHRcdFx0XCJub3R5ZlwiLFxuXHRcdFx0XCJtYXBib3gtZ2xcIixcblx0XHRcdFwicGhvdG9zd2lwZS9saWdodGJveFwiLFxuXHRcdFx0XCJwaG90b3N3aXBlXCIsXG5cdFx0XHRcInBseXJcIixcblx0XHRcdFwidi1jYWxlbmRhclwiLFxuXHRcdFx0XCJ2ZWUtdmFsaWRhdGVcIixcblx0XHRcdFwidnVlXCIsXG5cdFx0XHRcInZ1ZS1zY3JvbGx0b1wiLFxuXHRcdFx0XCJ2dWUzLWFwZXhjaGFydHNcIixcblx0XHRcdFwidnVlLXRpcHB5XCIsXG5cdFx0XHRcInZ1ZS1pMThuXCIsXG5cdFx0XHRcInZ1ZS1yb3V0ZXJcIixcblx0XHRcdFwidW5wbHVnaW4tdnVlLXJvdXRlci9ydW50aW1lXCIsXG5cdFx0XHRcInNpbXBsZWJhclwiLFxuXHRcdFx0XCJzaW1wbGUtZGF0YXRhYmxlc1wiLFxuXHRcdFx0XCJ0aW55LXNsaWRlci9zcmMvdGlueS1zbGlkZXJcIixcblx0XHRcdFwidnVlLWFjY2Vzc2libGUtY29sb3ItcGlja2VyXCIsXG5cdFx0XHRcInpvZFwiLFxuXHRcdFx0XCJAc3RlZmFucHJvYnN0L3JlbWFyay1zaGlraVwiLFxuXHRcdFx0XCJyZWh5cGUtZXh0ZXJuYWwtbGlua3NcIixcblx0XHRcdFwicmVoeXBlLXJhd1wiLFxuXHRcdFx0XCJyZWh5cGUtc2FuaXRpemVcIixcblx0XHRcdFwicmVoeXBlLXN0cmluZ2lmeVwiLFxuXHRcdFx0XCJyZWh5cGUtc2x1Z1wiLFxuXHRcdFx0XCJyZWh5cGUtYXV0b2xpbmstaGVhZGluZ3NcIixcblx0XHRcdFwicmVtYXJrLWdmbVwiLFxuXHRcdFx0XCJyZW1hcmstcGFyc2VcIixcblx0XHRcdFwicmVtYXJrLXJlaHlwZVwiLFxuXHRcdFx0XCJzaGlraVwiLFxuXHRcdFx0XCJ1bmlmaWVkXCIsXG5cdFx0XHRcIndvcmtib3gtd2luZG93XCIsXG5cdFx0XHRcInRleHRhcmVhLW1hcmtkb3duLWVkaXRvci9kaXN0L2VzbS9ib290c3RyYXBcIixcblx0XHRdLFxuXHRcdC8vIGRpc2FibGVkOiBmYWxzZSxcblx0fSxcblx0Ly8gV2lsbCBiZSBwYXNzZWQgdG8gQHJvbGx1cC9wbHVnaW4tYWxpYXMgYXMgaXRzIGVudHJpZXMgb3B0aW9uLlxuXHRyZXNvbHZlOiB7XG5cdFx0YWxpYXM6IFtcblx0XHRcdHtcblx0XHRcdFx0ZmluZDogXCIvQHNyYy9cIixcblx0XHRcdFx0cmVwbGFjZW1lbnQ6IGAvc3JjL2AsXG5cdFx0XHR9LFxuXHRcdFx0e1xuXHRcdFx0XHRmaW5kOiBcIkBcIixcblx0XHRcdFx0cmVwbGFjZW1lbnQ6IGZpbGVVUkxUb1BhdGgobmV3IFVSTChcIi4vc3JjXCIsIGltcG9ydC5tZXRhLnVybCkpLFxuXHRcdFx0fSxcblx0XHRdLFxuXHR9LFxuXHRidWlsZDoge1xuXHRcdG1pbmlmeTogXCJ0ZXJzZXJcIixcblx0XHQvLyBEbyBub3Qgd2FybiBhYm91dCBsYXJnZSBjaHVua3Ncblx0XHQvLyBjaHVua1NpemVXYXJuaW5nTGltaXQ6IEluZmluaXR5LFxuXHRcdC8vIERvdWJsZSB0aGUgZGVmYXVsdCBzaXplIHRocmVzaG9sZCBmb3IgaW5saW5lZCBhc3NldHNcblx0XHQvLyBodHRwczovL3ZpdGVqcy5kZXYvY29uZmlnL2J1aWxkLW9wdGlvbnMuaHRtbCNidWlsZC1hc3NldHNpbmxpbmVsaW1pdFxuXHRcdGFzc2V0c0lubGluZUxpbWl0OiA0MDk2ICogMixcblx0XHQvLyBjb21tb25qc09wdGlvbnM6IHsgaW5jbHVkZTogW10gfSxcblx0fSxcblx0cGx1Z2luczogW1xuXHRcdC8qKlxuXHRcdCAqIHBsdWdpbi12dWUgcGx1Z2luIGluamVjdCB2dWUgbGlicmFyeSBhbmQgYWxsb3cgc2ZjIGZpbGVzIHRvIHdvcmsgKCoudnVlKVxuXHRcdCAqXG5cdFx0ICogQHNlZSBodHRwczovL2dpdGh1Yi5jb20vdml0ZWpzL3ZpdGUvdHJlZS9tYWluL3BhY2thZ2VzL3BsdWdpbi12dWVcblx0XHQgKi9cblx0XHRWdWUoe1xuXHRcdFx0aW5jbHVkZTogWy9cXC52dWUkL10sXG5cdFx0fSksXG5cblx0XHQvKipcblx0XHQgKiB1bnBsdWdpbi12dWUtcm91dGVyIHBsdWdpbiBnZW5lcmF0ZSByb3V0ZXMgYmFzZWQgb24gZmlsZSBzeXN0ZW1cblx0XHQgKiBhbGxvdyB0byB1c2UgdHlwZWQgcm91dGVzIGFuZCB1c2FnZSBvZiBkZWZpbmVMb2FkZXJcblx0XHQgKlxuXHRcdCAqIEBzZWUgaHR0cHM6Ly9naXRodWIuY29tL3Bvc3ZhL3VucGx1Z2luLXZ1ZS1yb3V0ZXJcblx0XHQgKiBAc2VlIGh0dHBzOi8vZ2l0aHViLmNvbS92dWVqcy9yZmNzL2Jsb2IvYWQ2OWRhMmFlZTkyNDJlZjg4ZjAzNjcxM2RiNjhmM2VmMjc0YmIxYi9hY3RpdmUtcmZjcy8wMDAwLXJvdXRlci11c2UtbG9hZGVyLm1kXG5cdFx0ICovXG5cdFx0VnVlUm91dGVyKHtcblx0XHRcdHJvdXRlc0ZvbGRlcjogXCJzcmMvcGFnZXNcIixcblxuXHRcdFx0LyoqXG5cdFx0XHQgKiBEYXRhIEZldGNoaW5nIGlzIGFuIGV4cGVyaW1lbnRhbCBmZWF0dXJlIGZyb20gdnVlICYgdnVlLXJvdXRlclxuXHRcdFx0ICpcblx0XHRcdCAqIEBzZWUgaHR0cHM6Ly9naXRodWIuY29tL3Z1ZWpzL3JmY3MvZGlzY3Vzc2lvbnMvNDYwXG5cdFx0XHQgKiBAc2VlIGh0dHBzOi8vZ2l0aHViLmNvbS9wb3N2YS91bnBsdWdpbi12dWUtcm91dGVyL3RyZWUvbWFpbi9zcmMvZGF0YS1mZXRjaGluZ1xuXHRcdFx0ICovXG5cdFx0XHRkYXRhRmV0Y2hpbmc6IHRydWUsXG5cdFx0fSksXG5cblx0XHQvKipcblx0XHQgKiB1bnBsdWdpbi1hdXRvLWltcG9ydCBhbGxvdyB0byBhdXRvbWF0aWNhbHkgaW1wb3J0IG1vZHVsZXMvY29tcG9uZW50c1xuXHRcdCAqXG5cdFx0ICogQHNlZSBodHRwczovL2dpdGh1Yi5jb20vYW50ZnUvdW5wbHVnaW4tYXV0by1pbXBvcnRcblx0XHQgKi9cblx0XHRBdXRvSW1wb3J0KHtcblx0XHRcdGR0czogdHJ1ZSxcblx0XHRcdGltcG9ydHM6IFtcInZ1ZVwiLCBcIkB2dWV1c2UvY29yZVwiLCBWdWVSb3V0ZXJBdXRvSW1wb3J0c10sXG5cdFx0fSksXG5cblx0XHQvKipcblx0XHQgKiBUaGlzIGlzIGFuIGludGVybmFsIHZpdGUgcGx1Z2luIHRoYXQgbG9hZCBtYXJrZG93biBmaWxlcyBhcyB2dWUgY29tcG9uZW50cy5cblx0XHQgKlxuXHRcdCAqIEBzZWUgL2RvY3VtZW50YXRpb25cblx0XHQgKiBAc2VlIC92aXRlLXBsdWdpbi12dWVyby1kb2Ncblx0XHQgKiBAc2VlIC9zcmMvY29tcG9uZW50cy9wYXJ0aWFscy9kb2N1bWVudGF0aW9uL0RvY3VtZW50YXRpb25JdGVtLnZ1ZVxuXHRcdCAqIEBzZWUgL3NyYy9jb21wb3NhYmxlL3VzZU1hcmtkb3duVG9jLnRzXG5cdFx0ICovXG5cdFx0Ly8gVml0ZVBsdWdpblZ1ZXJvRG9jKHtcblx0XHQvLyAgIHBhdGhQcmVmaXg6ICdkb2N1bWVudGF0aW9uJyxcblx0XHQvLyAgIHdyYXBwZXJDb21wb25lbnQ6ICdEb2N1bWVudGF0aW9uSXRlbScsXG5cdFx0Ly8gICBzaGlraToge1xuXHRcdC8vICAgICB0aGVtZToge1xuXHRcdC8vICAgICAgIGxpZ2h0OiAnbWluLWxpZ2h0Jyxcblx0XHQvLyAgICAgICBkYXJrOiAnZ2l0aHViLWRhcmsnLFxuXHRcdC8vICAgICB9LFxuXHRcdC8vICAgfSxcblx0XHQvLyAgIHNvdXJjZU1ldGE6IHtcblx0XHQvLyAgICAgZW5hYmxlZDogdHJ1ZSxcblx0XHQvLyAgICAgZWRpdFByb3RvY29sOiAndnNjb2RlOi8vdnNjb2RlLXJlbW90ZS93c2wrVWJ1bnR1JywgLy8gb3IgJ3ZzY29kZTovL2ZpbGUnXG5cdFx0Ly8gICB9LFxuXHRcdC8vIH0pLFxuXG5cdFx0LyoqXG5cdFx0ICogdW5wbHVnaW4tdnVlLWNvbXBvbmVudHMgcGx1Z2luIGlzIHJlc3BvbnNpYmxlIG9mIGF1dG9sb2FkaW5nIGNvbXBvbmVudHNcblx0XHQgKiBkb2N1bWVudGF0aW9uIGFuZCBtZCBmaWxlIGFyZSBsb2FkZWQgZm9yIGVsZW1lbnRzIGFuZCBjb21wb25lbnRzIHNlY3Rpb25zXG5cdFx0ICpcblx0XHQgKiBAc2VlIGh0dHBzOi8vZ2l0aHViLmNvbS9hbnRmdS91bnBsdWdpbi12dWUtY29tcG9uZW50c1xuXHRcdCAqL1xuXHRcdENvbXBvbmVudHMoe1xuXHRcdFx0ZGlyczogW1wiZG9jdW1lbnRhdGlvblwiLCBcInNyYy9jb21wb25lbnRzXCIsIFwic3JjL2xheW91dHNcIl0sXG5cdFx0XHRleHRlbnNpb25zOiBbXCJ2dWVcIiwgXCJtZFwiXSxcblx0XHRcdGR0czogdHJ1ZSxcblx0XHRcdGluY2x1ZGU6IFsvXFwudnVlJC8sIC9cXC52dWVcXD92dWUvLCAvXFwubWQkL10sXG5cdFx0fSksXG5cblx0XHQvKipcblx0XHQgKiB2aXRlLXBsdWdpbi1mb250cyBwbHVnaW4gaW5qZWN0IHdlYmZvbnRzIGZyb20gZGlmZmVyZW50cyBwcm92aWRlcnNcblx0XHQgKlxuXHRcdCAqIEBzZWUgaHR0cHM6Ly9naXRodWIuY29tL3N0YWZ5bmlha3NhY2hhL3ZpdGUtcGx1Z2luLWZvbnRzXG5cdFx0ICovXG5cdFx0VW5mb250cyh7XG5cdFx0XHRnb29nbGU6IHtcblx0XHRcdFx0ZmFtaWxpZXM6IFtcblx0XHRcdFx0XHR7XG5cdFx0XHRcdFx0XHRuYW1lOiBcIkZpcmEgQ29kZVwiLFxuXHRcdFx0XHRcdFx0c3R5bGVzOiBcIndnaHRANDAwOzYwMFwiLFxuXHRcdFx0XHRcdH0sXG5cdFx0XHRcdFx0e1xuXHRcdFx0XHRcdFx0bmFtZTogXCJNb250c2VycmF0XCIsXG5cdFx0XHRcdFx0XHRzdHlsZXM6IFwid2dodEA1MDA7NjAwOzcwMDs4MDA7OTAwXCIsXG5cdFx0XHRcdFx0fSxcblx0XHRcdFx0XHR7XG5cdFx0XHRcdFx0XHRuYW1lOiBcIlJvYm90b1wiLFxuXHRcdFx0XHRcdFx0c3R5bGVzOiBcIndnaHRAMzAwOzQwMDs1MDA7NjAwOzcwMFwiLFxuXHRcdFx0XHRcdH0sXG5cdFx0XHRcdF0sXG5cdFx0XHR9LFxuXHRcdH0pLFxuXG5cdFx0LyoqXG5cdFx0ICogdml0ZS1wbHVnaW4tcmFkYXIgcGx1Z2luIGluamVjdCBzbmlwcGV0cyBmcm9tIGFuYWx5dGljcyBwcm92aWRlcnNcblx0XHQgKlxuXHRcdCAqIEBzZWUgaHR0cHM6Ly9naXRodWIuY29tL3N0YWZ5bmlha3NhY2hhL3ZpdGUtcGx1Z2luLXJhZGFyXG5cdFx0ICovXG5cdFx0IXByb2Nlc3MuZW52LkdUTV9JRFxuXHRcdFx0PyB1bmRlZmluZWRcblx0XHRcdDogVml0ZVBsdWdpblJhZGFyKHtcblx0XHRcdFx0XHRndG06IHtcblx0XHRcdFx0XHRcdGlkOiBwcm9jZXNzLmVudi5HVE1fSUQsXG5cdFx0XHRcdFx0fSxcblx0XHRcdCAgfSksXG5cblx0XHQvKipcblx0XHQgKiB2aXRlLXBsdWdpbi1wd2EgZ2VuZXJhdGUgbWFuaWZlc3QuanNvbiBhbmQgcmVnaXN0ZXIgc2VydmljZXMgd29ya2VyIHRvIGVuYWJsZSBQV0Fcblx0XHQgKlxuXHRcdCAqIEBzZWUgaHR0cHM6Ly9naXRodWIuY29tL2FudGZ1L3ZpdGUtcGx1Z2luLXB3YVxuXHRcdCAqL1xuXHRcdFZpdGVQV0Eoe1xuXHRcdFx0YmFzZTogXCIvXCIsXG5cdFx0XHRpbmNsdWRlQXNzZXRzOiBbXCJmYXZpY29uLnN2Z1wiLCBcImZhdmljb24uaWNvXCIsIFwicm9ib3RzLnR4dFwiLCBcImFwcGxlLXRvdWNoLWljb24ucG5nXCJdLFxuXHRcdFx0bWFuaWZlc3Q6IHtcblx0XHRcdFx0bmFtZTogXCJWdWVybyAtIEEgY29tcGxldGUgVnVlIDMgZGVzaWduIHN5c3RlbVwiLFxuXHRcdFx0XHRzaG9ydF9uYW1lOiBcIlZ1ZXJvXCIsXG5cdFx0XHRcdHN0YXJ0X3VybDogXCIvP3V0bV9zb3VyY2U9cHdhXCIsXG5cdFx0XHRcdGRpc3BsYXk6IFwic3RhbmRhbG9uZVwiLFxuXHRcdFx0XHR0aGVtZV9jb2xvcjogXCIjZmZmZmZmXCIsXG5cdFx0XHRcdGJhY2tncm91bmRfY29sb3I6IFwiI2ZmZmZmZlwiLFxuXHRcdFx0XHRpY29uczogW1xuXHRcdFx0XHRcdHtcblx0XHRcdFx0XHRcdHNyYzogXCJwd2EtMTkyeDE5Mi5wbmdcIixcblx0XHRcdFx0XHRcdHNpemVzOiBcIjE5MngxOTJcIixcblx0XHRcdFx0XHRcdHR5cGU6IFwiaW1hZ2UvcG5nXCIsXG5cdFx0XHRcdFx0fSxcblx0XHRcdFx0XHR7XG5cdFx0XHRcdFx0XHRzcmM6IFwicHdhLTUxMng1MTIucG5nXCIsXG5cdFx0XHRcdFx0XHRzaXplczogXCI1MTJ4NTEyXCIsXG5cdFx0XHRcdFx0XHR0eXBlOiBcImltYWdlL3BuZ1wiLFxuXHRcdFx0XHRcdH0sXG5cdFx0XHRcdFx0e1xuXHRcdFx0XHRcdFx0c3JjOiBcInB3YS01MTJ4NTEyLnBuZ1wiLFxuXHRcdFx0XHRcdFx0c2l6ZXM6IFwiNTEyeDUxMlwiLFxuXHRcdFx0XHRcdFx0dHlwZTogXCJpbWFnZS9wbmdcIixcblx0XHRcdFx0XHRcdHB1cnBvc2U6IFwiYW55IG1hc2thYmxlXCIsXG5cdFx0XHRcdFx0fSxcblx0XHRcdFx0XSxcblx0XHRcdH0sXG5cdFx0fSksXG5cblx0XHQvKipcblx0XHQgKiByb2xsdXAtcGx1Z2luLXB1cmdlY3NzIHBsdWdpbiBpcyByZXNwb25zaWJsZSBvZiBwdXJnaW5nIGNzcyBydWxlc1xuXHRcdCAqIHRoYXQgYXJlIG5vdCB1c2VkIGluIHRoZSBidW5kbGVcblx0XHQgKlxuXHRcdCAqIEBzZWUgaHR0cHM6Ly9naXRodWIuY29tL0Z1bGxIdW1hbi9wdXJnZWNzcy90cmVlL21haW4vcGFja2FnZXMvcm9sbHVwLXBsdWdpbi1wdXJnZWNzc1xuXHRcdCAqL1xuXHRcdHB1cmdlY3NzKHtcblx0XHRcdG91dHB1dDogZmFsc2UsXG5cdFx0XHRjb250ZW50OiBbYC4vc3JjLyoqLyoudnVlYF0sXG5cdFx0XHR2YXJpYWJsZXM6IGZhbHNlLFxuXHRcdFx0c2FmZWxpc3Q6IHtcblx0XHRcdFx0c3RhbmRhcmQ6IFtcblx0XHRcdFx0XHQvKGF1dHZ8bG5pbHxsbmlyfGZhcz8pLyxcblx0XHRcdFx0XHQvLShsZWF2ZXxlbnRlcnxhcHBlYXIpKHwtKHRvfGZyb218YWN0aXZlKSkkLyxcblx0XHRcdFx0XHQvXig/ISh8Lio/OiljdXJzb3ItbW92ZSkuKy1tb3ZlJC8sXG5cdFx0XHRcdFx0L15yb3V0ZXItbGluayh8LWV4YWN0KS1hY3RpdmUkLyxcblx0XHRcdFx0XHQvZGF0YS12LS4qLyxcblx0XHRcdFx0XSxcblx0XHRcdH0sXG5cdFx0XHRkZWZhdWx0RXh0cmFjdG9yKGNvbnRlbnQpIHtcblx0XHRcdFx0Y29uc3QgY29udGVudFdpdGhvdXRTdHlsZUJsb2NrcyA9IGNvbnRlbnQucmVwbGFjZSgvPHN0eWxlW15dKz88XFwvc3R5bGU+L2dpLCBcIlwiKTtcblx0XHRcdFx0cmV0dXJuIGNvbnRlbnRXaXRob3V0U3R5bGVCbG9ja3MubWF0Y2goL1tBLVphLXowLTktXy86XSpbQS1aYS16MC05LV8vXSsvZykgfHwgW107XG5cdFx0XHR9LFxuXHRcdH0pLFxuXG5cdFx0LyoqXG5cdFx0ICogdml0ZS1wbHVnaW4taW1hZ2VtaW4gb3B0aW1pemUgYWxsIGltYWdlcyBzaXplcyBmcm9tIHB1YmxpYyBvciBhc3NldCBmb2xkZXJcblx0XHQgKlxuXHRcdCAqIEBzZWUgaHR0cHM6Ly9naXRodWIuY29tL2FubmN3Yi92aXRlLXBsdWdpbi1pbWFnZW1pblxuXHRcdCAqL1xuXHRcdCFNSU5JRllfSU1BR0VTXG5cdFx0XHQ/IHVuZGVmaW5lZFxuXHRcdFx0OiBJbWFnZU1pbih7XG5cdFx0XHRcdFx0Z2lmc2ljbGU6IHtcblx0XHRcdFx0XHRcdG9wdGltaXphdGlvbkxldmVsOiA3LFxuXHRcdFx0XHRcdFx0aW50ZXJsYWNlZDogZmFsc2UsXG5cdFx0XHRcdFx0fSxcblx0XHRcdFx0XHRvcHRpcG5nOiB7XG5cdFx0XHRcdFx0XHRvcHRpbWl6YXRpb25MZXZlbDogNyxcblx0XHRcdFx0XHR9LFxuXHRcdFx0XHRcdG1vempwZWc6IHtcblx0XHRcdFx0XHRcdHF1YWxpdHk6IDYwLFxuXHRcdFx0XHRcdH0sXG5cdFx0XHRcdFx0cG5ncXVhbnQ6IHtcblx0XHRcdFx0XHRcdHF1YWxpdHk6IFswLjgsIDAuOV0sXG5cdFx0XHRcdFx0XHRzcGVlZDogNCxcblx0XHRcdFx0XHR9LFxuXHRcdFx0XHRcdHN2Z286IHtcblx0XHRcdFx0XHRcdHBsdWdpbnM6IFtcblx0XHRcdFx0XHRcdFx0e1xuXHRcdFx0XHRcdFx0XHRcdG5hbWU6IFwicmVtb3ZlVmlld0JveFwiLFxuXHRcdFx0XHRcdFx0XHRcdGFjdGl2ZTogZmFsc2UsXG5cdFx0XHRcdFx0XHRcdH0sXG5cdFx0XHRcdFx0XHRcdHtcblx0XHRcdFx0XHRcdFx0XHRuYW1lOiBcInJlbW92ZUVtcHR5QXR0cnNcIixcblx0XHRcdFx0XHRcdFx0XHRhY3RpdmU6IGZhbHNlLFxuXHRcdFx0XHRcdFx0XHR9LFxuXHRcdFx0XHRcdFx0XSxcblx0XHRcdFx0XHR9LFxuXHRcdFx0ICB9KSxcblx0XSxcbn0pO1xuIl0sCiAgIm1hcHBpbmdzIjogIjtBQUEyUixTQUFTLGVBQWUsV0FBVztBQUM5VCxTQUFTLG9CQUFvQjtBQUM3QixPQUFPLFNBQVM7QUFDaEIsT0FBTyxlQUFlO0FBQ3RCLFNBQVMsNEJBQTRCO0FBQ3JDLE9BQU8sZ0JBQWdCO0FBQ3ZCLE9BQU8sZ0JBQWdCO0FBQ3ZCLE9BQU8sYUFBYTtBQUNwQixTQUFTLHVCQUF1QjtBQUNoQyxPQUFPLGNBQWM7QUFDckIsU0FBUyxlQUFlO0FBQ3hCLE9BQU8sY0FBYztBQVh5SixJQUFNLDJDQUEyQztBQWlCL04sSUFBTSxnQkFBZ0IsUUFBUSxJQUFJLFNBQVMsUUFBUSxJQUFJLFdBQVcsU0FBUztBQU8zRSxJQUFPLHNCQUFRLGFBQWE7QUFBQTtBQUFBLEVBRTNCLE1BQU0sUUFBUSxJQUFJO0FBQUE7QUFBQTtBQUFBO0FBQUE7QUFBQSxFQUtsQixNQUFNO0FBQUE7QUFBQSxFQUVOLFdBQVc7QUFBQTtBQUFBLEVBRVgsVUFBVTtBQUFBO0FBQUEsRUFFVixRQUFRO0FBQUE7QUFBQSxJQUVQLE1BQU07QUFBQSxFQUNQO0FBQUE7QUFBQTtBQUFBO0FBQUE7QUFBQTtBQUFBO0FBQUE7QUFBQSxFQVFBLGNBQWM7QUFBQSxJQUNiLFNBQVM7QUFBQSxNQUNSO0FBQUEsTUFDQTtBQUFBLE1BQ0E7QUFBQSxNQUNBO0FBQUEsTUFDQTtBQUFBLE1BQ0E7QUFBQSxNQUNBO0FBQUEsTUFDQTtBQUFBLE1BQ0E7QUFBQSxNQUNBO0FBQUEsTUFDQTtBQUFBLE1BQ0E7QUFBQSxNQUNBO0FBQUEsTUFDQTtBQUFBLE1BQ0E7QUFBQSxNQUNBO0FBQUEsTUFDQTtBQUFBLE1BQ0E7QUFBQSxNQUNBO0FBQUEsTUFDQTtBQUFBLE1BQ0E7QUFBQSxNQUNBO0FBQUEsTUFDQTtBQUFBLE1BQ0E7QUFBQSxNQUNBO0FBQUEsTUFDQTtBQUFBLE1BQ0E7QUFBQSxNQUNBO0FBQUEsTUFDQTtBQUFBLE1BQ0E7QUFBQSxNQUNBO0FBQUEsTUFDQTtBQUFBLE1BQ0E7QUFBQSxNQUNBO0FBQUEsTUFDQTtBQUFBLE1BQ0E7QUFBQSxNQUNBO0FBQUEsTUFDQTtBQUFBLE1BQ0E7QUFBQSxNQUNBO0FBQUEsTUFDQTtBQUFBLE1BQ0E7QUFBQSxNQUNBO0FBQUEsTUFDQTtBQUFBLE1BQ0E7QUFBQSxNQUNBO0FBQUEsTUFDQTtBQUFBLE1BQ0E7QUFBQSxNQUNBO0FBQUEsTUFDQTtBQUFBLE1BQ0E7QUFBQSxNQUNBO0FBQUEsTUFDQTtBQUFBLE1BQ0E7QUFBQSxNQUNBO0FBQUEsTUFDQTtBQUFBLE1BQ0E7QUFBQSxNQUNBO0FBQUEsSUFDRDtBQUFBO0FBQUEsRUFFRDtBQUFBO0FBQUEsRUFFQSxTQUFTO0FBQUEsSUFDUixPQUFPO0FBQUEsTUFDTjtBQUFBLFFBQ0MsTUFBTTtBQUFBLFFBQ04sYUFBYTtBQUFBLE1BQ2Q7QUFBQSxNQUNBO0FBQUEsUUFDQyxNQUFNO0FBQUEsUUFDTixhQUFhLGNBQWMsSUFBSSxJQUFJLFNBQVMsd0NBQWUsQ0FBQztBQUFBLE1BQzdEO0FBQUEsSUFDRDtBQUFBLEVBQ0Q7QUFBQSxFQUNBLE9BQU87QUFBQSxJQUNOLFFBQVE7QUFBQTtBQUFBO0FBQUE7QUFBQTtBQUFBLElBS1IsbUJBQW1CLE9BQU87QUFBQTtBQUFBLEVBRTNCO0FBQUEsRUFDQSxTQUFTO0FBQUE7QUFBQTtBQUFBO0FBQUE7QUFBQTtBQUFBLElBTVIsSUFBSTtBQUFBLE1BQ0gsU0FBUyxDQUFDLFFBQVE7QUFBQSxJQUNuQixDQUFDO0FBQUE7QUFBQTtBQUFBO0FBQUE7QUFBQTtBQUFBO0FBQUE7QUFBQSxJQVNELFVBQVU7QUFBQSxNQUNULGNBQWM7QUFBQTtBQUFBO0FBQUE7QUFBQTtBQUFBO0FBQUE7QUFBQSxNQVFkLGNBQWM7QUFBQSxJQUNmLENBQUM7QUFBQTtBQUFBO0FBQUE7QUFBQTtBQUFBO0FBQUEsSUFPRCxXQUFXO0FBQUEsTUFDVixLQUFLO0FBQUEsTUFDTCxTQUFTLENBQUMsT0FBTyxnQkFBZ0Isb0JBQW9CO0FBQUEsSUFDdEQsQ0FBQztBQUFBO0FBQUE7QUFBQTtBQUFBO0FBQUE7QUFBQTtBQUFBO0FBQUE7QUFBQTtBQUFBO0FBQUE7QUFBQTtBQUFBO0FBQUE7QUFBQTtBQUFBO0FBQUE7QUFBQTtBQUFBO0FBQUE7QUFBQTtBQUFBO0FBQUE7QUFBQTtBQUFBO0FBQUE7QUFBQTtBQUFBO0FBQUEsSUErQkQsV0FBVztBQUFBLE1BQ1YsTUFBTSxDQUFDLGlCQUFpQixrQkFBa0IsYUFBYTtBQUFBLE1BQ3ZELFlBQVksQ0FBQyxPQUFPLElBQUk7QUFBQSxNQUN4QixLQUFLO0FBQUEsTUFDTCxTQUFTLENBQUMsVUFBVSxjQUFjLE9BQU87QUFBQSxJQUMxQyxDQUFDO0FBQUE7QUFBQTtBQUFBO0FBQUE7QUFBQTtBQUFBLElBT0QsUUFBUTtBQUFBLE1BQ1AsUUFBUTtBQUFBLFFBQ1AsVUFBVTtBQUFBLFVBQ1Q7QUFBQSxZQUNDLE1BQU07QUFBQSxZQUNOLFFBQVE7QUFBQSxVQUNUO0FBQUEsVUFDQTtBQUFBLFlBQ0MsTUFBTTtBQUFBLFlBQ04sUUFBUTtBQUFBLFVBQ1Q7QUFBQSxVQUNBO0FBQUEsWUFDQyxNQUFNO0FBQUEsWUFDTixRQUFRO0FBQUEsVUFDVDtBQUFBLFFBQ0Q7QUFBQSxNQUNEO0FBQUEsSUFDRCxDQUFDO0FBQUE7QUFBQTtBQUFBO0FBQUE7QUFBQTtBQUFBLElBT0QsQ0FBQyxRQUFRLElBQUksU0FDVixTQUNBLGdCQUFnQjtBQUFBLE1BQ2hCLEtBQUs7QUFBQSxRQUNKLElBQUksUUFBUSxJQUFJO0FBQUEsTUFDakI7QUFBQSxJQUNBLENBQUM7QUFBQTtBQUFBO0FBQUE7QUFBQTtBQUFBO0FBQUEsSUFPSixRQUFRO0FBQUEsTUFDUCxNQUFNO0FBQUEsTUFDTixlQUFlLENBQUMsZUFBZSxlQUFlLGNBQWMsc0JBQXNCO0FBQUEsTUFDbEYsVUFBVTtBQUFBLFFBQ1QsTUFBTTtBQUFBLFFBQ04sWUFBWTtBQUFBLFFBQ1osV0FBVztBQUFBLFFBQ1gsU0FBUztBQUFBLFFBQ1QsYUFBYTtBQUFBLFFBQ2Isa0JBQWtCO0FBQUEsUUFDbEIsT0FBTztBQUFBLFVBQ047QUFBQSxZQUNDLEtBQUs7QUFBQSxZQUNMLE9BQU87QUFBQSxZQUNQLE1BQU07QUFBQSxVQUNQO0FBQUEsVUFDQTtBQUFBLFlBQ0MsS0FBSztBQUFBLFlBQ0wsT0FBTztBQUFBLFlBQ1AsTUFBTTtBQUFBLFVBQ1A7QUFBQSxVQUNBO0FBQUEsWUFDQyxLQUFLO0FBQUEsWUFDTCxPQUFPO0FBQUEsWUFDUCxNQUFNO0FBQUEsWUFDTixTQUFTO0FBQUEsVUFDVjtBQUFBLFFBQ0Q7QUFBQSxNQUNEO0FBQUEsSUFDRCxDQUFDO0FBQUE7QUFBQTtBQUFBO0FBQUE7QUFBQTtBQUFBO0FBQUEsSUFRRCxTQUFTO0FBQUEsTUFDUixRQUFRO0FBQUEsTUFDUixTQUFTLENBQUMsZ0JBQWdCO0FBQUEsTUFDMUIsV0FBVztBQUFBLE1BQ1gsVUFBVTtBQUFBLFFBQ1QsVUFBVTtBQUFBLFVBQ1Q7QUFBQSxVQUNBO0FBQUEsVUFDQTtBQUFBLFVBQ0E7QUFBQSxVQUNBO0FBQUEsUUFDRDtBQUFBLE1BQ0Q7QUFBQSxNQUNBLGlCQUFpQixTQUFTO0FBQ3pCLGNBQU0sNEJBQTRCLFFBQVEsUUFBUSwwQkFBMEIsRUFBRTtBQUM5RSxlQUFPLDBCQUEwQixNQUFNLGtDQUFrQyxLQUFLLENBQUM7QUFBQSxNQUNoRjtBQUFBLElBQ0QsQ0FBQztBQUFBO0FBQUE7QUFBQTtBQUFBO0FBQUE7QUFBQSxJQU9ELENBQUMsZ0JBQ0UsU0FDQSxTQUFTO0FBQUEsTUFDVCxVQUFVO0FBQUEsUUFDVCxtQkFBbUI7QUFBQSxRQUNuQixZQUFZO0FBQUEsTUFDYjtBQUFBLE1BQ0EsU0FBUztBQUFBLFFBQ1IsbUJBQW1CO0FBQUEsTUFDcEI7QUFBQSxNQUNBLFNBQVM7QUFBQSxRQUNSLFNBQVM7QUFBQSxNQUNWO0FBQUEsTUFDQSxVQUFVO0FBQUEsUUFDVCxTQUFTLENBQUMsS0FBSyxHQUFHO0FBQUEsUUFDbEIsT0FBTztBQUFBLE1BQ1I7QUFBQSxNQUNBLE1BQU07QUFBQSxRQUNMLFNBQVM7QUFBQSxVQUNSO0FBQUEsWUFDQyxNQUFNO0FBQUEsWUFDTixRQUFRO0FBQUEsVUFDVDtBQUFBLFVBQ0E7QUFBQSxZQUNDLE1BQU07QUFBQSxZQUNOLFFBQVE7QUFBQSxVQUNUO0FBQUEsUUFDRDtBQUFBLE1BQ0Q7QUFBQSxJQUNBLENBQUM7QUFBQSxFQUNMO0FBQ0QsQ0FBQzsiLAogICJuYW1lcyI6IFtdCn0K
