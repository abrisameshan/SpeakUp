<template>
    <main class="welcome">
        <img
            src="/images/1.png"
            alt="Logo pertama"
            class="logo"
        />

        <img
            v-if="showSecondLogo"
            src="/images/2.png"
            alt="Logo kedua"
            class="logo second-logo"
        />
    </main>
</template>

<script setup>
import { ref, onMounted, onUnmounted } from 'vue'
import { useRouter } from 'vue-router'

const router = useRouter()
const showSecondLogo = ref(false)

let secondLogoTimer
let redirectTimer

onMounted(() => {
    secondLogoTimer = setTimeout(() => {
        showSecondLogo.value = true
    }, 2000)

    redirectTimer = setTimeout(() => {
        router.push('/dashboard')
    }, 3000)
})

onUnmounted(() => {
    clearTimeout(secondLogoTimer)
    clearTimeout(redirectTimer)
})
</script>

<style scoped>
.welcome {
    min-height: 100vh;
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
    gap: 20px;
}

.logo {
    width: 120px;
    height: auto;
    object-fit: contain;
}

.second-logo {
    animation: appear 0.6s ease;
}

@keyframes appear {
    from {
        opacity: 0;
        transform: translateY(15px);
    }

    to {
        opacity: 1;
        transform: translateY(0);
    }
}
</style>
