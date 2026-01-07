<template>
  <Layout>
    <div class="p-6 bg-gray-50 min-h-screen">
      <!-- HEADER -->
      <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 gap-4">
        <h1 class="text-3xl font-extrabold text-gray-800">Product List</h1>

        <!-- CATEGORY FILTER + ADD BUTTON -->
        <div class="flex items-center gap-3">
          <select
            v-model="selectedCategory"
            @change="handleCategoryChange"
            class="border rounded-lg px-4 py-2 bg-white min-w-[200px]"
          >
            <option value="">All Categories</option>
            <option v-for="cat in categories" :key="cat.CategoryID" :value="String(cat.CategoryID)">
              {{ cat.CategoryName }}
            </option>
          </select>

          <RouterLink
            to="/products/create"
            class="flex items-center gap-2 bg-gradient-to-r from-blue-500 to-blue-600 text-white px-5 py-2 rounded-xl font-semibold shadow hover:from-blue-600 hover:to-blue-700 transition-all"
          >
            <i class="fa-solid fa-plus"></i>
            Add New Product
          </RouterLink>
        </div>
      </div>

      <!-- SUCCESS MESSAGE -->
      <div
        v-if="successMessage"
        class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg shadow"
      >
        {{ successMessage }}
      </div>

      <!-- PRODUCT TABLE -->
      <div class="overflow-x-auto bg-white rounded-2xl shadow-lg border border-gray-200">
        <table class="min-w-full divide-y divide-gray-200">
          <thead class="bg-gray-50">
            <tr>
              <th class="px-6 py-3">Product</th>
              <th class="px-6 py-3">Category</th>
              <th class="px-6 py-3">SKU</th>
              <th class="px-6 py-3">Price</th>
              <th class="px-6 py-3">Stock</th>
              <th class="px-6 py-3 text-right">Actions</th>
            </tr>
          </thead>

          <tbody class="bg-white divide-y divide-gray-200">
            <tr
              v-for="product in filteredProducts"
              :key="product.ProductID"
              class="hover:bg-gray-50 transition"
            >
              <!-- PRODUCT -->
              <td class="px-6 py-4">
                <div class="flex items-center gap-4">
                  <img
                    v-if="product.Product_Image"
                    :src="imagePath(product.Product_Image)"
                    class="h-12 w-12 rounded-full object-cover border"
                  />

                  <div
                    v-else
                    class="h-12 w-12 rounded-full bg-gray-200 flex items-center justify-center font-bold"
                  >
                    {{ product.ProductName.substring(0, 2).toUpperCase() }}
                  </div>

                  <div>
                    <RouterLink
                      :to="`/products/${product.ProductID}/transactions`"
                      class="text-blue-600 font-medium hover:underline"
                    >
                      {{ product.ProductName }}
                    </RouterLink>

                    <div class="text-gray-500 text-xs">SKU: {{ product.SKU }}</div>
                  </div>
                </div>
              </td>

              <td class="px-6 py-4">
                {{ product.category?.CategoryName || '—' }}
              </td>

              <td class="px-6 py-4">
                {{ product.SKU }}
              </td>

              <td class="px-6 py-4">₱{{ formatPrice(product.SellingPrice) }}</td>

              <td class="px-6 py-4">
                <span
                  :class="
                    (product.inventory?.QuantityOnHand ?? 0) < 5
                      ? 'text-red-600 font-bold'
                      : 'text-green-600'
                  "
                >
                  {{ product.inventory?.QuantityOnHand ?? 0 }}
                </span>
              </td>

              <!-- ACTIONS -->
              <td class="px-6 py-4 text-right">
                <div class="flex justify-end items-center gap-2">
                  <button @click="openRestockModal(product)" class="btn-primary bg-green-500">
                    <i class="fa-solid fa-boxes-stacked mr-1"></i>
                    Restock
                  </button>

                  <button
                    :ref="setMenuButtonRef(product.ProductID)"
                    @click="toggleMenu(product.ProductID)"
                    class="p-2 rounded-full hover:bg-gray-100"
                  >
                    <i class="fa-solid fa-ellipsis-vertical"></i>
                  </button>
                </div>
              </td>
            </tr>

            <tr v-if="filteredProducts.length === 0">
              <td colspan="6" class="px-6 py-6 text-center text-gray-500">
                No matching products found.
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- TELEPORTED DROPDOWN MENU -->
      <Teleport to="body">
        <div
          v-if="openMenuId && currentProduct"
          ref="dropdownRef"
          :style="dropdownStyle"
          class="w-44 bg-white border rounded-lg shadow-xl z-[9999]"
        >
          <RouterLink
            :to="`/products/${currentProduct.ProductID}`"
            class="dropdown-item"
            @click="closeMenu"
          >
            <i class="fa-solid fa-eye mr-2 text-blue-600"></i>
            View
          </RouterLink>

          <RouterLink
            :to="`/products/${currentProduct.ProductID}/edit`"
            class="dropdown-item"
            @click="closeMenu"
          >
            <i class="fa-solid fa-pen-to-square mr-2 text-yellow-600"></i>
            Edit
          </RouterLink>

          <button
            @click="handleDelete(currentProduct.ProductID)"
            class="dropdown-item text-red-600"
          >
            <i class="fa-solid fa-trash mr-2"></i>
            Delete
          </button>
        </div>
      </Teleport>

      <!-- RESTOCK MODAL -->
      <div
        v-if="modalVisible"
        class="fixed inset-0 bg-black/20 flex justify-center items-center z-[9999]"
      >
        <div class="bg-white p-6 rounded-xl w-96">
          <h2 class="text-xl font-bold mb-4">Restock {{ modalProduct?.ProductName }}</h2>

          <form @submit.prevent="submitRestock">
            <input
              v-model.number="restockQty"
              type="number"
              min="1"
              required
              class="w-full border rounded-lg px-3 py-2 mb-4"
            />

            <div class="flex justify-end gap-2">
              <button
                type="button"
                @click="closeRestockModal"
                class="px-4 py-2 bg-gray-200 rounded-lg"
              >
                Cancel
              </button>

              <button
                type="submit"
                class="px-4 py-2 bg-blue-600 text-white rounded-lg"
                :disabled="isRestocking"
              >
                {{ isRestocking ? 'Restocking...' : 'Restock' }}
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </Layout>
</template>

<script setup>
import Layout from '@/components/Layout.vue'
import api from '@/api/axios'
import { ref, onMounted, computed, watch, nextTick, onBeforeUnmount } from 'vue'
import { useRoute, useRouter } from 'vue-router'

/* ROUTER */
const route = useRoute()
const router = useRouter()

/* STATE */
const products = ref([])
const categories = ref([])
const selectedCategory = ref('')
const successMessage = ref('')

/* RESTOCK */
const modalVisible = ref(false)
const modalProduct = ref(null)
const restockQty = ref(1)
const isRestocking = ref(false)

/* SEARCH + MENU */
const searchQuery = ref('')
const openMenuId = ref(null)

/* DROPDOWN */
const dropdownRef = ref(null)
const dropdownStyle = ref({ position: 'fixed', top: '0px', left: '0px' })

const menuButtonRefs = new Map()
const setMenuButtonRef = (id) => (el) => {
  if (el) menuButtonRefs.set(id, el)
  else menuButtonRefs.delete(id)
}

/* HELPERS */
const imagePath = (path) => `http://127.0.0.1:8000/storage/${path}`

const formatPrice = (value) =>
  Number(value || 0).toLocaleString(undefined, {
    minimumFractionDigits: 2,
  })

/* LOAD DATA */
async function loadProducts() {
  const res = await api.get('/api/product-list')
  products.value = res.data
}

async function loadCategories() {
  const res = await api.get('/api/categories')
  categories.value = res.data.categories
}

/* INIT */
onMounted(() => {
  loadProducts()
  loadCategories()

  if (route.query.category) {
    selectedCategory.value = String(route.query.category)
  }
})

/* SEARCH SYNC */
watch(
  () => route.query.search,
  (val) => {
    searchQuery.value = (val || '').toLowerCase()
  },
  { immediate: true },
)

/* CATEGORY URL SYNC */
watch(selectedCategory, (newCategory) => {
  router.replace({
    query: {
      ...route.query,
      category: newCategory || undefined,
    },
  })
})

/* FILTER */
const filteredProducts = computed(() => {
  return products.value.filter((p) => {
    const matchesSearch =
      !searchQuery.value ||
      [p.ProductName, p.SKU, p.category?.CategoryName]
        .join(' ')
        .toLowerCase()
        .includes(searchQuery.value)

    const matchesCategory =
      !selectedCategory.value || String(p.CategoryID) === selectedCategory.value

    return matchesSearch && matchesCategory
  })
})

/* CURRENT PRODUCT */
const currentProduct = computed(() =>
  filteredProducts.value.find((p) => p.ProductID === openMenuId.value),
)

/* MENU */
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
      left: `${rect.right - 176}px`,
    }
  })
}

function closeMenu() {
  openMenuId.value = null
}

function handleCategoryChange() {
  closeMenu()
}

/* CLOSE MENU WHEN CLICK OUTSIDE (keeps your Teleport UX correct) */
function onClickOutside(e) {
  if (!openMenuId.value) return

  const dropdownEl = dropdownRef.value
  const btnEl = menuButtonRefs.get(openMenuId.value)

  const clickedDropdown = dropdownEl && dropdownEl.contains(e.target)
  const clickedButton = btnEl && btnEl.contains(e.target)

  if (!clickedDropdown && !clickedButton) {
    closeMenu()
  }
}

/* ESC CLOSE */
function onKeydown(e) {
  if (e.key === 'Escape') {
    closeMenu()
    closeRestockModal()
  }
}

document.addEventListener('click', onClickOutside)
document.addEventListener('keydown', onKeydown)

onBeforeUnmount(() => {
  document.removeEventListener('click', onClickOutside)
  document.removeEventListener('keydown', onKeydown)
})

/* RESTOCK (✅ FIXED: backend + refresh list) */
function openRestockModal(product) {
  modalProduct.value = product
  restockQty.value = 1
  modalVisible.value = true
}

function closeRestockModal() {
  modalVisible.value = false
  modalProduct.value = null
  restockQty.value = 1
}

async function submitRestock() {
  if (!modalProduct.value) return
  if (!restockQty.value || Number(restockQty.value) < 1) return
  if (isRestocking.value) return

  isRestocking.value = true

  try {
    // Your API route is protected by auth:sanctum -> send credentials
    await api.post(
      `/api/products/${modalProduct.value.ProductID}/restock`,
      { quantity: Number(restockQty.value) },
      { withCredentials: true },
    )

    successMessage.value = 'Product restocked successfully!'
    closeRestockModal()

    // IMPORTANT: reload list so UI reflects DB
    await loadProducts()

    setTimeout(() => (successMessage.value = ''), 2500)
  } catch (err) {
    console.error(err)
    alert(err?.response?.data?.message || 'Failed to restock product')
  } finally {
    isRestocking.value = false
  }
}

/* DELETE (keep your existing logic if you already have it elsewhere) */
async function handleDelete(productId) {
  closeMenu()

  if (!productId) return

  const confirmed = confirm('Are you sure you want to delete this product?')
  if (!confirmed) return

  try {
    await api.delete(`/api/products/${productId}`, {
      withCredentials: true,
    })

    successMessage.value = 'Product deleted successfully!'

    await loadProducts()

    setTimeout(() => (successMessage.value = ''), 2500)
  } catch (err) {
    console.error(err)

    alert(err?.response?.data?.message || `Delete failed (status ${err?.response?.status})`)
  }
}
</script>

<style scoped>
.btn-primary {
  @apply text-white px-3 py-1 rounded-lg shadow hover:opacity-80;
}
.dropdown-item {
  @apply flex items-center px-4 py-2 text-sm hover:bg-gray-100 w-full text-left;
}
</style>
