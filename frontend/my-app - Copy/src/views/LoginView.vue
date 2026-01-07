<template>
  <div class="landing-wrapper h-screen font-sans antialiased text-gray-800">
    <!-- NAVBAR -->
    <header class="w-full bg-white/80 backdrop-blur-sm border-b border-gray-200 sticky top-0 z-50">
      <div class="w-full px-10 flex items-center justify-between h-16">
        <div class="flex items-center gap-3">
          <img :src="sheemsLogo" class="w-30 h-20 rounded-full" />
          <span class="font-semibold text-lg text-orange-600">Sheems Steel</span>
        </div>
      </div>
    </header>

    <!-- HERO -->
    <section class="hero relative overflow-hidden w-full">
      <!-- LEFT SHAPE -->
      <div class="hero-shape"></div>

      <!-- CONTENT -->
      <div class="relative z-10 py-16 md:py-24">
        <div class="w-full px-10">
          <div class="grid grid-cols-1 lg:grid-cols-2 gap-16 items-start w-full">
            <!-- LEFT TEXT -->
            <!-- LEFT TEXT -->
            <div class="w-full flex flex-col justify-center min-h-[60vh]">
              <h1 class="text-4xl font-extrabold text-gray-900">
                Sheems Steel Construction Company
              </h1>

              <p class="text-sm text-gray-600 mt-1">IMS / SMS — Inventory & Sales Management</p>

              <h2 class="text-3xl font-semibold text-gray-900 mt-6">
                Designed to streamline Sheem Steel Company operations
              </h2>

              <p class="mt-4 text-gray-700">
                Real-time inventory, clean dashboards, and automated reporting.
              </p>
            </div>

            <!-- RIGHT LOGIN -->
            <div id="login-form" class="flex justify-center lg:justify-end w-full">
              <div class="bg-white border border-gray-200 shadow-lg rounded-lg p-8 w-full max-w-md">
                <h4 class="text-lg font-semibold text-gray-900 text-center">
                  Login to your account
                </h4>

                <!-- ERROR -->
                <div
                  v-if="error"
                  class="mt-4 bg-red-100 text-red-700 px-3 py-2 rounded text-sm text-center"
                >
                  {{ error }}
                </div>

                <form @submit.prevent="submitLogin" class="mt-6 space-y-4">
                  <div>
                    <label class="text-sm font-medium text-gray-800">Email</label>
                    <input
                      v-model="email"
                      type="email"
                      required
                      class="mt-1 block w-full rounded-md border border-gray-300 py-2 px-3"
                    />
                  </div>

                  <div>
                    <label class="text-sm font-medium text-gray-800">Password</label>
                    <div class="relative">
                      <input
                        :type="showPassword ? 'text' : 'password'"
                        v-model="password"
                        class="mt-1 block w-full rounded-md border border-gray-300 py-2 px-3 pr-10"
                      />

                      <button
                        type="button"
                        @click="showPassword = !showPassword"
                        class="absolute inset-y-0 right-3 flex items-center text-gray-500"
                      >
                        <i :class="showPassword ? 'fa-solid fa-eye-slash' : 'fa-solid fa-eye'"></i>
                      </button>
                    </div>
                  </div>

                  <div class="flex justify-between text-sm text-gray-700">
                    <label class="flex items-center gap-2">
                      <input
                        type="checkbox"
                        v-model="remember"
                        class="text-orange-600 border-gray-300 rounded"
                      />
                      Remember me
                    </label>

                    <a class="text-orange-600 hover:underline">Forgot?</a>
                  </div>

                  <button
                    type="submit"
                    class="w-full py-2 bg-orange-500 hover:bg-orange-600 text-white rounded-md flex justify-center items-center gap-2"
                    :disabled="loading"
                  >
                    <i v-if="loading" class="fa-solid fa-spinner fa-spin"></i>
                    <span>{{ loading ? 'Logging in...' : 'Login' }}</span>
                  </button>
                </form>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- FOOTER -->
    <footer class="bg-white border-t border-gray-200 py-8">
      <div class="w-full px-10 flex flex-col md:flex-row justify-between text-sm text-gray-700">
        <div>© {{ year }} Sheems Steel Construction Company</div>
        <div class="flex gap-4">
          <span>support@sheems.example</span>
          <span>+63 912 345 6789</span>
        </div>
      </div>
    </footer>
  </div>
</template>

<script setup lang="ts">
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth'

import sheemsLogo from '@/assets/sheemslogo.png'

const auth = useAuthStore()
const router = useRouter()

const year = new Date().getFullYear()
const email = ref('')
const password = ref('')
const remember = ref(false)
const showPassword = ref(false)
const loading = ref(false)
const error = ref('')

async function submitLogin() {
  error.value = ''
  loading.value = true

  try {
    await auth.login({
      email: email.value,
      password: password.value,
      remember: remember.value,
    })
    router.push('/home')
  } catch (err: any) {
    error.value = err?.response?.data?.message || 'Login failed.'
  } finally {
    loading.value = false
  }
}
</script>

<style scoped>
/* HERO BASE */
.hero {
  min-height: calc(100vh - 64px);
  background: white;
}

/* LEFT ORANGE SHAPE */
.hero-shape {
  position: absolute;
  top: 0;
  left: 0;
  width: 55%;
  height: 100%;
  background: linear-gradient(180deg, #ffffff 0%, #ffe8d3 55%, #f97316 100%);

  border-top-right-radius: 220px;
  border-bottom-right-radius: 220px;
}

/* MOBILE: STACKED */
@media (max-width: 1024px) {
  .hero-shape {
    width: 100%;
    border-radius: 0;
  }
}

.landing-wrapper {
  overflow-x: hidden;
}
</style>
