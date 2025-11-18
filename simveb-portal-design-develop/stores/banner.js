import { defineStore } from 'pinia'

export const useBannerStore = defineStore('banner', {
    state: () => ({ title: "", subTitle: "" }),

    actions: {
        setTitle(title) {
            this.title = title
        },

        setSubTitle(subtitle) {
            this.subTitle = subtitle
        },
    },
})