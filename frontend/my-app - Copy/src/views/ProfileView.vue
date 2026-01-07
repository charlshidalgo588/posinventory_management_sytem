<template>
  <div class="flex h-screen w-screen bg-gray-100 overflow-hidden">
    <!-- Sidebar -->
    <aside class="w-64 bg-green-700 text-white flex flex-col items-center py-6 space-y-6">
      <img src="@/assets/logo.png" alt="CSU Logo" class="w-24 mb-2" />
      <h2 class="text-center text-base font-semibold leading-tight">
        Caraga State University<br />
        Library Management System
      </h2>

      <nav class="mt-8 w-full px-4">
        <ul class="space-y-2">
          <li>
            <router-link
              to="/home"
              class="flex items-center gap-3 px-4 py-2 rounded-md hover:bg-green-600 transition"
              active-class="bg-green-800"
            >
              <el-icon><House /></el-icon> Home
            </router-link>
          </li>
          <li>
            <router-link
              to="/books"
              class="flex items-center gap-3 px-4 py-2 rounded-md hover:bg-green-600 transition"
              active-class="bg-green-800"
            >
              <el-icon><Collection /></el-icon> Books
            </router-link>
          </li>
          <li>
            <router-link
              to="/borrow"
              class="flex items-center gap-3 px-4 py-2 rounded-md hover:bg-green-600 transition"
              active-class="bg-green-800"
            >
              <el-icon><Document /></el-icon> Borrowings
            </router-link>
          </li>
          <li>
            <router-link
              to="/history"
              class="flex items-center gap-3 px-4 py-2 rounded-md hover:bg-green-600 transition"
              active-class="bg-green-800"
            >
              <el-icon><Notebook /></el-icon> History
            </router-link>
          </li>
        </ul>
      </nav>
    </aside>

    <!-- Main Content -->
    <div class="flex-1 flex flex-col bg-white">
      <!-- Header -->
      <header class="relative h-[220px] flex items-center justify-between p-12 overflow-hidden">
        <img
          src="@/assets/backg-top.jpg"
          alt="CSU Library"
          class="absolute inset-0 w-full h-full object-cover object-center"
        />
        <div class="absolute inset-0 bg-gradient-to-r from-[#07D007]/90 to-[#286028]/90 z-[5]" />

        <div class="relative z-10 flex justify-between items-center w-full">
          <!-- Search -->
          <div class="flex items-center bg-white rounded-full px-4 py-2 w-2/3 shadow-sm">
            <el-icon class="text-gray-400"><Search /></el-icon>
            <input
              v-model="searchQuery"
              type="text"
              placeholder="Search for books..."
              class="ml-2 w-full focus:outline-none text-gray-700"
            />
          </div>

          <!-- Profile Section -->
          <div class="flex items-center space-x-3 text-white">
            <div class="flex items-center space-x-2">
              <img
                :src="avatarUrl"
                alt="User"
                class="w-10 h-10 rounded-full border-2 border-white object-cover"
              />
              <span class="font-medium">
                {{ user?.name ?? 'User' }}
              </span>
            </div>
          </div>
        </div>
      </header>

      <!-- Profile Content -->
      <main class="flex-1 overflow-y-auto p-10 bg-gray-50">
        <div class="max-w-5xl mx-auto bg-white shadow-lg rounded-lg p-10">
          <div class="flex items-center mb-10">
            <img
              :src="avatarUrl"
              alt="Profile Picture"
              class="w-36 h-36 rounded-full border-4 border-green-600 object-cover"
            />

            <div class="ml-8">
              <h1 class="text-3xl font-semibold">
                {{ user?.name ?? 'Loading...' }}
              </h1>
              <p class="text-gray-500">
                {{ user?.email ?? '' }}
              </p>

              <p v-if="loading" class="text-sm text-gray-400 mt-2">Loading profile…</p>
              <p v-if="error" class="text-sm text-red-600 mt-2">{{ error }}</p>
            </div>

            <!-- Logout Button -->
            <button
              @click="showLogoutModal = true"
              class="ml-auto bg-green-600 hover:bg-green-700 text-white font-semibold px-6 py-2 rounded-md shadow"
            >
              Log out
            </button>
          </div>

          <div class="grid grid-cols-2 gap-6">
            <!-- Contact Info -->
            <div class="bg-gray-100 rounded-lg p-6 shadow-sm">
              <h2 class="text-xl font-semibold mb-4">Contact Information</h2>

              <!-- NOTE: your User model only has name/email/password,
                   so these are placeholders until you add columns -->
              <ul class="space-y-3 text-gray-700">
                <li>
                  <span class="font-semibold">Student ID:</span>
                  <span class="ml-2 text-gray-500">Not set</span>
                </li>

                <li>
                  <span class="font-semibold">Email:</span>
                  <span class="ml-2">{{ user?.email ?? '' }}</span>
                </li>

                <li>
                  <span class="font-semibold">Phone:</span>
                  <span class="ml-2 text-gray-500">Not set</span>
                </li>

                <li>
                  <span class="font-semibold">Address:</span>
                  <span class="ml-2 text-gray-500">Not set</span>
                </li>
              </ul>
            </div>

            <!-- Account Settings -->
            <div class="bg-gray-100 rounded-lg p-6 shadow-sm">
              <h2 class="text-xl font-semibold mb-6">Account Settings</h2>

              <button
                class="w-full bg-green-600 hover:bg-green-700 text-white py-3 rounded-lg font-semibold text-lg mb-4 transition"
              >
                Change Password
              </button>

              <button
                class="w-full bg-green-600 hover:bg-green-700 text-white py-3 rounded-lg font-semibold text-lg transition"
              >
                Notification Preferences
              </button>
            </div>
          </div>
        </div>
      </main>
    </div>
  </div>

  <!-- Logout Confirmation Modal -->
  <transition name="fade-scale">
    <div
      v-if="showLogoutModal"
      class="fixed inset-0 z-[9999] flex items-center justify-center bg-black bg-opacity-50 backdrop-blur-[6px]"
    >
      <div class="bg-white rounded-md shadow-2xl w-[420px] p-0 overflow-hidden text-center">
        <div class="pt-6 pb-4">
          <div
            class="mx-auto bg-yellow-100 w-16 h-16 flex items-center justify-center rounded-full"
          >
            <svg
              xmlns="http://www.w3.org/2000/svg"
              class="w-10 h-10 text-yellow-500"
              fill="none"
              viewBox="0 0 24 24"
              stroke="currentColor"
              stroke-width="2"
            >
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                d="M12 9v4m0 4h.01M12 2a10 10 0 100 20 10 10 0 000-20z"
              />
            </svg>
          </div>
        </div>

        <h2 class="text-gray-800 text-lg font-semibold mb-6">Are you sure you want to log out?</h2>

        <div class="flex w-full">
          <button
            @click="showLogoutModal = false"
            class="flex-1 bg-red-600 hover:bg-red-700 text-white py-3 text-lg font-semibold uppercase transition"
          >
            Cancel
          </button>
          <button
            @click="handleLogout"
            class="flex-1 bg-green-600 hover:bg-green-700 text-white py-3 text-lg font-semibold uppercase transition"
          >
            Logout
          </button>
        </div>
      </div>
    </div>
  </transition>
</template>

<script setup lang="ts">
import { computed, onMounted, ref } from 'vue'
import { useRouter } from 'vue-router'
import { House, Collection, Document, Notebook, Search } from '@element-plus/icons-vue'
import api from '@/api/axios'

const router = useRouter()

const showLogoutModal = ref(false)
const searchQuery = ref('')

const user = ref<{ name: string; email: string } | null>(null)
const loading = ref(false)
const error = ref('')

// ✅ avatar is placeholder for now (since your User model has no avatar column)
const avatarUrl = computed(() => {
  // if later you add user.avatar, return your backend storage url here
  return new URL('@/assets/justin.png', import.meta.url).href
})

async function loadUser() {
  loading.value = true
  error.value = ''
  try {
    const res = await api.get('/api/user')
    user.value = res.data
  } catch (e: any) {
    user.value = null
    // common case: not logged in / session expired
    error.value = 'Not authenticated. Please login again.'
    router.push('/login')
  } finally {
    loading.value = false
  }
}

const handleLogout = async () => {
  try {
    await api.post('/api/logout')
  } catch (e) {
    // even if backend fails, still redirect
  } finally {
    showLogoutModal.value = false
    router.push('/login')
  }
}

onMounted(loadUser)
</script>

<style scoped>
aside {
  background: linear-gradient(180deg, #07d007, #286028);
}
.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.3s ease;
}
.fade-enter-from,
.fade-leave-to {
  opacity: 0;
}
</style>
