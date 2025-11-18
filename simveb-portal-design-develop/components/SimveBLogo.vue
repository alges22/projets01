<script setup>
    import confetti from 'canvas-confetti';

    const clickCount = ref(0);

    const showSantaHat = ref(false);

    const currentDate = new Date();
    const startDate = new Date(currentDate.getFullYear(), 11, 1);  // 1er décembre
    const endDate = new Date(currentDate.getFullYear() + 1, 0, 7); // 7 janvier

    if (currentDate >= startDate && currentDate <= endDate) {
        showSantaHat.value = true;
    }

    const continuousConfetti = () => {
        const duration = 3 * 1000;

        const end = Date.now() + duration;

        var colors = ['#1E3A8A', '#ffffff'];

        (function frame() {
            confetti({
                particleCount: 2,
                angle: 60,
                spread: 55,
                origin: { x: 0 },
                colors: colors
            });
            confetti({
                particleCount: 2,
                angle: 120,
                spread: 55,
                origin: { x: 1 },
                colors: colors
            });

            if (Date.now() < end) {
                requestAnimationFrame(frame);
            }
        })();
    }

    const handleClick = () => {
        clickCount.value += 1;

        if (clickCount.value === 10) {
            continuousConfetti()

            // Optionnel : réinitialiser le compteur après l'animation de confettis
            setTimeout(() => {
                clickCount.value = 0;
            }, 3000); // Réinitialiser après 3 secondes (temps de l'animation)
        }
    };

    const props = defineProps({
        theme: {
            type: String,
            default: "dark"
        },
    });
</script>

<template>
    <h4 @click="handleClick" :class="theme === 'dark' ? 'text-white' : 'text-blue-950'" class="relative font-bold text-7xl select-none">
        S I M V E
        <span class="santa-hat-container">
            <img v-if="showSantaHat" src="/santaHat.png"  class="santa-hat" alt="Santa Hat" /> B
        </span>
    </h4>
</template>

<style scoped lang="scss">
    .santa-hat-container {
        position: relative;
        display: inline-block;
    }

    .santa-hat {
        position: absolute;
        top: -20px;
        left: 10px;
        width: 70px;
        height: auto;
    }
</style>