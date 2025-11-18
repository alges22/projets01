import AWN from 'awesome-notifications'

export default defineNuxtPlugin((nuxtApp) => {
    const vuwAWNOptions = {
        position: 'bottom-right',
        durations: { alert: 10000}
    }


    return {
        provide: {
            awn: new AWN(vuwAWNOptions)
        },
    }
})