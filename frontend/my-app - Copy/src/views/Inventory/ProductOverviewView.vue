<script setup>
import { ref, onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import api from '@/api/axios' // your axios instance
import logoFallback from '@/assets/sheems_logo.png'

const route = useRoute()
const router = useRouter()

const productId = route.params.id

const product = ref(null)
const loading = ref(true)
const error = ref('')

/*
|--------------------------------------------------------------------------
| LOAD PRODUCT FROM BACKEND
|--------------------------------------------------------------------------
| Uses:
|   GET /product-overview/{product}
| This route returns the Blade view normally, so we modify backend to return JSON.
| (If not done yet, I can update your controller — ask me.)
|--------------------------------------------------------------------------
*/
async function loadProduct() {
  try {
    const res = await api.get(`/product-overview/${productId}`, {
      headers: { Accept: 'application/json' },
    })

    product.value = res.data.product
  } catch (err) {
    console.error(err)
    error.value = 'Failed to load product details.'
  }

  loading.value = false
}

onMounted(() => {
  loadProduct()
})

/*
|--------------------------------------------------------------------------
| HELPERS
|--------------------------------------------------------------------------
*/
function formatPrice(n) {
  return Number(n || 0).toLocaleString(undefined, {
    minimumFractionDigits: 2,
  })
}

function imageUrl(path) {
  if (!path) return logoFallback
  return `http://localhost:8000/storage/${path}`
}
</script>

<template>
  <Layout>
    <div class="p-6">
      <div v-if="loading" class="text-center py-10 text-gray-500 text-lg">Loading...</div>

      <div v-if="error" class="text-red-600 font-semibold text-center mt-4">
        {{ error }}
      </div>

      <div v-if="product" class="bg-white rounded-xl shadow-md p-6 max-w-[1800px] mx-auto">
        <!-- HEADER -->
        <div class="flex justify-between items-center mb-6">
          <h1 class="text-2xl font-bold text-gray-800">
            {{ product.ProductName }}
          </h1>

          <div class="flex gap-3">
            <RouterLink
              :to="`/products/${product.ProductID}/edit`"
              class="bg-indigo-600 text-white px-4 py-2 rounded-lg hover:bg-indigo-700"
            >
              Edit Product
            </RouterLink>

            <RouterLink
              to="/products"
              class="bg-gray-300 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-400"
            >
              Back to List
            </RouterLink>
          </div>
        </div>

        <!-- TABS -->
        <nav class="mb-6">
          <ul class="flex border-b space-x-6 text-sm">
            <li class="border-b-2 border-blue-600 pb-2 font-semibold">Overview</li>

            <RouterLink :to="`/products/${product.ProductID}/transactions`">
              <li class="text-gray-500 hover:text-black pb-2 cursor-pointer">Transactions</li>
            </RouterLink>

            <li class="text-gray-500 hover:text-black pb-2 cursor-pointer">History</li>
          </ul>
        </nav>

        <!-- GRID -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
          <!-- LEFT SECTION -->
          <div class="lg:col-span-2 space-y-6">
            <!-- Item Info -->
            <section>
              <h2 class="text-lg font-bold mb-2">Item Information</h2>

              <div class="grid grid-cols-1 gap-y-2 text-sm text-gray-700">
                <div><strong>SKU:</strong> {{ product.SKU }}</div>
                <div><strong>Unit:</strong> {{ product.Unit }}</div>
                <div><strong>Weight:</strong> {{ product.Weight ?? 'N/A' }}</div>
                <div><strong>Brand:</strong> {{ product.Brand || 'N/A' }}</div>
                <div><strong>Description:</strong> {{ product.Description || 'N/A' }}</div>
              </div>
            </section>

            <!-- Purchase Info -->
            <section>
              <h2 class="text-lg font-bold mb-2">Purchase Information</h2>

              <div class="text-sm text-gray-700 space-y-1">
                <div><strong>Cost Price:</strong> ₱{{ formatPrice(product.CostPrice) }}</div>
                <div>
                  <strong>Supplier:</strong>
                  {{
                    product.suppliers?.[0]?.SupplierName
                      ? product.suppliers[0].SupplierName
                      : 'No supplier'
                  }}
                </div>
              </div>
            </section>

            <!-- Sales Info -->
            <section>
              <h2 class="text-lg font-bold mb-2">Sales Information</h2>

              <div class="text-sm text-gray-700 space-y-1">
                <div><strong>Selling Price:</strong> ₱{{ formatPrice(product.SellingPrice) }}</div>
                <div><strong>Description:</strong> {{ product.Description }}</div>
              </div>
            </section>
          </div>

          <!-- RIGHT SECTION -->
          <aside class="space-y-6">
            <img
              :src="imageUrl(product.Product_Image)"
              alt="Product Image"
              class="w-40 h-40 object-cover mx-auto rounded"
            />

            <div class="text-sm text-gray-700">
              <p><strong>Opening Stock:</strong> {{ product.OpeningStock }}</p>
            </div>

            <!-- Stock Cards -->
            <div class="bg-gray-100 p-4 rounded">
              <h3 class="text-sm font-bold mb-2">Accounting Stock</h3>
              <p><strong>Stock on Hand:</strong> {{ product.inventory?.QuantityOnHand ?? 0 }}</p>
            </div>

            <div class="bg-gray-100 p-4 rounded">
              <h3 class="text-sm font-bold mb-2">Physical Stock</h3>
              <p><strong>Stock on Hand:</strong> {{ product.inventory?.QuantityOnHand ?? 0 }}</p>
            </div>

            <div class="text-sm text-gray-700">
              <p><strong>Reorder Point:</strong> {{ product.ReorderLevel }}</p>
            </div>
          </aside>
        </div>

        <!-- CHART SECTION -->
        <section class="mt-8 bg-white border rounded p-4">
          <div class="flex justify-between items-start">
            <h2 class="text-sm font-bold mb-4">Sales Order Summary</h2>
            <button class="text-sm text-gray-600 hover:text-black flex items-center">
              This Month
              <svg class="w-4 h-4 ml-1" viewBox="0 0 20 20" fill="currentColor">
                <path
                  d="M5.23 7.21a.75.75 0 011.06.02L10 10.94l3.71-3.71a.75.75 0 011.06 1.06l-4.24 4.24a.75.75 0 01-1.06 0L5.23 8.27a.75.75 0 01.02-1.06z"
                />
              </svg>
            </button>
          </div>

          <div class="border h-60 rounded flex items-center justify-center text-gray-400">
            No data found.
          </div>
        </section>
      </div>
    </div>
  </Layout>
</template>
