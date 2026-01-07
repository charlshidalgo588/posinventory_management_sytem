<template>
  <Layout title="Suppliers">
    <div class="p-10">
      <div class="bg-white rounded-2xl shadow-lg p-6">
        <!-- HEADER -->
        <div
          class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 gap-4"
        >
          <h1 class="text-3xl font-extrabold text-gray-800">Suppliers</h1>

          <RouterLink
            to="/suppliers/create"
            class="bg-gradient-to-r from-blue-500 to-blue-600 text-white px-6 py-2 rounded-xl font-semibold shadow hover:from-blue-600 hover:to-blue-700 transition-all"
          >
            Add New Supplier
          </RouterLink>
        </div>

        <!-- SUCCESS MESSAGE -->
        <div
          v-if="successMessage"
          class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg mb-4 animate-fade-in"
        >
          {{ successMessage }}
        </div>

        <!-- ERROR MESSAGE -->
        <div
          v-if="errorMessage"
          class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg mb-4 animate-fade-in"
        >
          {{ errorMessage }}
        </div>

        <!-- TABLE -->
        <div class="overflow-x-auto rounded-lg border border-gray-200">
          <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
              <tr>
                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">
                  Supplier Name
                </th>
                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">
                  Contact Number
                </th>
                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">
                  Email
                </th>
                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">
                  Address
                </th>
                <th class="px-6 py-3 text-right text-xs font-semibold text-gray-500 uppercase">
                  Actions
                </th>
              </tr>
            </thead>

            <tbody class="bg-white divide-y divide-gray-200">
              <tr
                v-for="supplier in suppliers"
                :key="supplier.SupplierID"
                class="hover:bg-gray-50 transition"
              >
                <td class="px-6 py-4 font-medium text-gray-900">
                  {{ supplier.SupplierName }}
                </td>
                <td class="px-6 py-4 text-gray-600">{{ supplier.ContactNumber }}</td>
                <td class="px-6 py-4 text-gray-600">{{ supplier.Email }}</td>
                <td class="px-6 py-4 text-gray-600">{{ supplier.Address }}</td>

                <!-- 3 DOT ACTIONS -->
                <td class="px-6 py-4 text-right">
                  <button
                    :ref="setMenuButtonRef(supplier.SupplierID)"
                    @click="toggleMenu(supplier.SupplierID)"
                    class="p-2 rounded-full hover:bg-gray-100"
                  >
                    <i class="fa-solid fa-ellipsis-vertical"></i>
                  </button>
                </td>
              </tr>

              <tr v-if="suppliers.length === 0">
                <td colspan="5" class="px-6 py-4 text-center text-gray-500">
                  No suppliers found.
                  <RouterLink to="/suppliers/create" class="text-blue-500 hover:underline">
                    Add one now
                  </RouterLink>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <!-- TELEPORTED DROPDOWN -->
    <Teleport to="body">
      <div
        v-if="openMenuId && currentSupplier"
        ref="dropdownRef"
        :style="dropdownStyle"
        class="w-40 bg-white border rounded-lg shadow-xl z-[9999]"
      >
        <RouterLink
          :to="`/suppliers/${currentSupplier.SupplierID}/edit`"
          class="dropdown-item"
          @click="closeMenu"
        >
          <i class="fa-solid fa-pen-to-square mr-2 text-blue-600"></i>
          Edit
        </RouterLink>

        <button
          @click="handleDelete(currentSupplier.SupplierID)"
          class="dropdown-item text-red-600"
        >
          <i class="fa-solid fa-trash mr-2"></i>
          Delete
        </button>
      </div>
    </Teleport>
  </Layout>
</template>

<script setup>
import { ref, onMounted, computed, nextTick, onBeforeUnmount } from 'vue'
import Layout from '@/components/Layout.vue'
import api from '@/api/axios'

/* STATE */
const suppliers = ref([])
const successMessage = ref('')
const errorMessage = ref('')

/* MENU STATE */
const openMenuId = ref(null)
const dropdownRef = ref(null)
const dropdownStyle = ref({ position: 'fixed', top: '0px', left: '0px' })

const menuButtonRefs = new Map()
const setMenuButtonRef = (id) => (el) => {
  if (el) menuButtonRefs.set(id, el)
  else menuButtonRefs.delete(id)
}

/* LOAD SUPPLIERS */
async function loadSuppliers() {
  try {
    const res = await api.get('/api/suppliers')
    suppliers.value = res.data.suppliers
  } catch (error) {
    console.error(error)
    errorMessage.value = 'Failed to load suppliers.'
  }
}

onMounted(loadSuppliers)

/* CURRENT SUPPLIER */
const currentSupplier = computed(() =>
  suppliers.value.find((s) => s.SupplierID === openMenuId.value),
)

/* MENU LOGIC */
function toggleMenu(id) {
  if (openMenuId.value === id) {
    closeMenu()
    return
  }

  openMenuId.value = id

  nextTick(() => {
    const btn = menuButtonRefs.get(id)
    if (!btn) return

    const rect = btn.getBoundingClientRect()
    dropdownStyle.value = {
      position: 'fixed',
      top: `${rect.bottom + 8}px`,
      left: `${rect.right - 160}px`,
    }
  })
}

function closeMenu() {
  openMenuId.value = null
}

/* CLICK OUTSIDE */
function onClickOutside(e) {
  if (!openMenuId.value) return

  const dropdownEl = dropdownRef.value
  const btnEl = menuButtonRefs.get(openMenuId.value)

  if (dropdownEl && !dropdownEl.contains(e.target) && btnEl && !btnEl.contains(e.target)) {
    closeMenu()
  }
}

/* ESC CLOSE */
function onKeydown(e) {
  if (e.key === 'Escape') closeMenu()
}

document.addEventListener('click', onClickOutside)
document.addEventListener('keydown', onKeydown)

onBeforeUnmount(() => {
  document.removeEventListener('click', onClickOutside)
  document.removeEventListener('keydown', onKeydown)
})

/* DELETE */
async function handleDelete(id) {
  closeMenu()

  if (!confirm('Are you sure you want to delete this supplier?')) return

  try {
    await api.delete(`/api/suppliers/${id}`, {
      withCredentials: true,
    })

    successMessage.value = 'Supplier deleted successfully!'
    errorMessage.value = ''
    loadSuppliers()
  } catch (error) {
    console.error(error)
    errorMessage.value = 'Failed to delete supplier.'
  }
}
</script>

<style scoped>
@keyframes fade-in {
  from {
    opacity: 0;
    transform: translateY(-5px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

.animate-fade-in {
  animation: fade-in 0.3s ease-out;
}

.dropdown-item {
  @apply flex items-center px-4 py-2 text-sm hover:bg-gray-100 w-full text-left;
}
</style>
