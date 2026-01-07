<template>
  <Layout title="Categories">
    <div class="p-10">
      <!-- HEADER -->
      <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-semibold">Categories</h1>

        <RouterLink
          to="/categories/create"
          class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition"
        >
          + Add New Category
        </RouterLink>
      </div>

      <!-- TABLE WRAPPER -->
      <div class="bg-white rounded-xl shadow p-6">
        <table class="min-w-full divide-y divide-gray-200">
          <thead class="bg-gray-50">
            <tr>
              <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">
                Category Name
              </th>
              <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">
                Products Count
              </th>
              <th class="px-6 py-3 text-right text-xs font-semibold text-gray-500 uppercase">
                Actions
              </th>
            </tr>
          </thead>

          <tbody class="bg-white divide-y divide-gray-200">
            <tr v-for="cat in categories" :key="cat.CategoryID" class="hover:bg-gray-50 transition">
              <td class="px-6 py-4 text-gray-700 font-medium">
                {{ cat.CategoryName }}
              </td>

              <td class="px-6 py-4">
                <span class="bg-gray-100 px-3 py-1 rounded-full text-gray-700 text-sm font-medium">
                  {{ cat.products_count ?? 0 }}
                </span>
              </td>

              <!-- 3 DOT ACTIONS -->
              <td class="px-6 py-4 text-right">
                <button
                  :ref="setMenuButtonRef(cat.CategoryID)"
                  @click="toggleMenu(cat.CategoryID)"
                  class="p-2 rounded-full hover:bg-gray-100"
                >
                  <i class="fa-solid fa-ellipsis-vertical"></i>
                </button>
              </td>
            </tr>

            <tr v-if="categories.length === 0">
              <td colspan="3" class="px-6 py-4 text-center text-gray-500">No categories found.</td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- TELEPORTED DROPDOWN -->
    <Teleport to="body">
      <div
        v-if="openMenuId && currentCategory"
        ref="dropdownRef"
        :style="dropdownStyle"
        class="w-40 bg-white border rounded-lg shadow-xl z-[9999]"
      >
        <RouterLink
          :to="`/categories/${currentCategory.CategoryID}/edit`"
          class="dropdown-item"
          @click="closeMenu"
        >
          <i class="fa-solid fa-pen-to-square mr-2 text-blue-600"></i>
          Edit
        </RouterLink>

        <button
          @click="handleDelete(currentCategory.CategoryID)"
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
import Layout from '@/components/Layout.vue'
import api from '@/api/axios'
import { ref, onMounted, computed, nextTick, onBeforeUnmount } from 'vue'

/* STATE */
const categories = ref([])

/* MENU STATE */
const openMenuId = ref(null)
const dropdownRef = ref(null)
const dropdownStyle = ref({ position: 'fixed', top: '0px', left: '0px' })

const menuButtonRefs = new Map()
const setMenuButtonRef = (id) => (el) => {
  if (el) menuButtonRefs.set(id, el)
  else menuButtonRefs.delete(id)
}

/* LOAD CATEGORIES */
onMounted(async () => {
  try {
    const res = await api.get('/api/categories')
    categories.value = res.data.categories
  } catch (err) {
    console.error('Failed to load categories', err)
  }
})

/* CURRENT CATEGORY */
const currentCategory = computed(() =>
  categories.value.find((c) => c.CategoryID === openMenuId.value),
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

/* DELETE CATEGORY */
async function handleDelete(id) {
  closeMenu()

  if (!confirm('Are you sure you want to delete this category?')) return

  try {
    await api.delete(`/api/categories/${id}`, {
      withCredentials: true,
    })

    categories.value = categories.value.filter((c) => c.CategoryID !== id)
  } catch (err) {
    console.error('Delete failed:', err)
    alert('Could not delete category.')
  }
}
</script>

<style scoped>
.dropdown-item {
  @apply flex items-center px-4 py-2 text-sm hover:bg-gray-100 w-full text-left;
}
</style>
