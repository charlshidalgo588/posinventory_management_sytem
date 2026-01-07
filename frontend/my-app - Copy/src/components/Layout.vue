<template>
  <div class="flex w-full h-screen overflow-hidden">
    <!-- SIDEBAR -->
    <aside class="fixed top-0 left-0 z-40 w-64 h-screen bg-orange-600 text-white shadow-xl">
      <div class="flex justify-center py-6">
        <router-link to="/home">
          <img :src="logo" class="w-44 object-contain" />
        </router-link>
      </div>

      <div class="px-4 space-y-1">
        <NavLink to="/home" icon="fa-solid fa-house" label="Home" />

        <SidebarDropdown
          label="Inventory"
          icon="fa-solid fa-boxes-stacked"
          :open="inventoryOpen"
          @toggle="inventoryOpen = !inventoryOpen"
        >
          <NavLink to="/products" menu-item label="Product List" icon="fa-solid fa-list" />
          <NavLink to="/products/create" menu-item label="Add Item" icon="fa-solid fa-plus" />
          <NavLink to="/categories" menu-item label="Category" icon="fa-solid fa-layer-group" />
        </SidebarDropdown>

        <SidebarDropdown
          label="Sales"
          icon="fa-solid fa-cart-shopping"
          :open="salesOpen"
          @toggle="salesOpen = !salesOpen"
        >
          <NavLink to="/point-of-sale" menu-item label="POS" icon="fa-solid fa-cash-register" />
          <NavLink
            to="/sales-transaction"
            menu-item
            label="Transaction"
            icon="fa-solid fa-receipt"
          />
        </SidebarDropdown>

        <NavLink to="/suppliers" label="Suppliers" icon="fa-solid fa-truck-field" />
        <NavLink to="/reports" label="Reports" icon="fa-solid fa-chart-line" />
        <NavLink to="/inventory-logs" label="Inventory Logs" icon="fa-solid fa-folder-open" />
      </div>
    </aside>

    <!-- MAIN COLUMN -->
    <div class="flex-1 ml-64 flex flex-col h-screen bg-gray-50">
      <!-- TOP BAR -->
      <header
        class="sticky top-0 bg-white shadow-md border-b px-10 py-4 z-20 flex justify-between items-center"
      >
        <div>
          <p class="text-gray-700 font-semibold">
            Hello,
            <span class="text-orange-600 font-bold">{{ user.name }}</span
            >!
          </p>
          <p class="text-gray-700 text-sm">
            Welcome to
            <span class="text-orange-600 font-semibold"> Sheem Steel Construction Company </span>
            POS/Inventory System.
          </p>
        </div>

        <!-- SEARCH -->
        <div class="relative w-full max-w-lg mx-6">
          <i class="fa-solid fa-search absolute left-3 top-3 text-gray-400"></i>
          <input
            v-model="search"
            @keyup.enter="goToProducts"
            class="w-full border rounded-lg py-2 pl-10 pr-4 shadow-sm text-sm"
            placeholder="Search Products..."
          />
        </div>

        <!-- RIGHT ICONS -->
        <div class="flex items-center space-x-4">
          <router-link to="/products/create">
            <button class="p-2 border rounded-full hover:bg-gray-100">
              <i class="fa-solid fa-plus"></i>
            </button>
          </router-link>

          <div class="relative">
            <button @click="profileOpen = !profileOpen">
              <img
                :src="profileIcon"
                class="w-10 h-10 rounded-full border shadow hover:scale-105"
              />
            </button>

            <div
              v-if="profileOpen"
              class="absolute right-0 mt-2 w-48 bg-white rounded shadow border z-30"
            >
              <router-link to="/settings" class="block px-4 py-2 hover:bg-gray-100 text-sm">
                <i class="fa-solid fa-gear mr-2"></i>Settings
              </router-link>

              <button
                @click="showLogoutModal = true"
                class="w-full text-left px-4 py-2 text-red-600 hover:bg-gray-100 text-sm"
              >
                <i class="fa-solid fa-right-from-bracket mr-2"></i>Sign Out
              </button>
            </div>
          </div>
        </div>
      </header>

      <!-- CONTENT -->
      <main class="flex-1 overflow-y-auto p-4 md:p-6">
        <div class="max-w-[1800px] mx-auto space-y-8">
          <slot />
        </div>
      </main>
    </div>
  </div>

  <!-- LOGOUT CONFIRMATION MODAL -->
  <transition name="fade">
    <div
      v-if="showLogoutModal"
      class="fixed inset-0 z-[9999] flex items-center justify-center bg-black/50 backdrop-blur-sm"
    >
      <div class="bg-white rounded-xl shadow-xl w-[420px] p-6">
        <h2 class="text-lg font-semibold text-gray-800 mb-4">Are you sure you want to log out?</h2>

        <p class="text-sm text-gray-600 mb-6">
          You will need to log in again to access the system.
        </p>

        <div class="flex justify-end gap-3">
          <button
            @click="showLogoutModal = false"
            class="px-4 py-2 rounded-lg border text-gray-700 hover:bg-gray-100"
          >
            Cancel
          </button>

          <button
            @click="confirmLogout"
            class="px-4 py-2 rounded-lg bg-red-600 text-white hover:bg-red-700"
          >
            Log Out
          </button>
        </div>
      </div>
    </div>
  </transition>
</template>

<script setup>
import { ref, watch, onMounted } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import api from '@/api/axios'

import NavLink from './NavLink.vue'
import SidebarDropdown from './SidebarDropdown.vue'
import logo from '@/assets/sheems_logo.png'
import profileIcon from '@/assets/profile-icon.png'

/* ROUTER */
const router = useRouter()
const route = useRoute()

/* STATE */
const search = ref('')
const inventoryOpen = ref(false)
const salesOpen = ref(false)
const profileOpen = ref(false)
const showLogoutModal = ref(false)
const user = ref({ name: 'User' })

/* FETCH AUTH USER */
onMounted(async () => {
  try {
    const res = await api.get('/api/user')
    user.value = res.data
  } catch {
    router.push('/login')
  }
})

/* SEARCH */
const goToProducts = () => {
  if (!search.value.trim()) return

  router.push({
    path: '/products',
    query: { search: search.value },
  })
}

watch(search, (val) => {
  if (!route.path.startsWith('/products')) return

  router.replace({
    query: {
      ...route.query,
      search: val || undefined,
    },
  })
})

/* LOGOUT CONFIRMATION */
const confirmLogout = async () => {
  try {
    await api.post('/api/logout')
  } catch (err) {
    console.error('Logout error:', err)
  } finally {
    showLogoutModal.value = false
    profileOpen.value = false
    router.push('/login')
  }
}
</script>

<style scoped>
.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.25s ease;
}
.fade-enter-from,
.fade-leave-to {
  opacity: 0;
}
</style>
