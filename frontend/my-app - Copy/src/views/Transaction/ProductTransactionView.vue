<script setup>
import { ref, computed, onMounted, watch } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import axios from 'axios'
import Layout from '@/components/Layout.vue'

/*
|--------------------------------------------------------------------------
| ROUTER
|--------------------------------------------------------------------------
*/
const route = useRoute()
const router = useRouter()

/*
|--------------------------------------------------------------------------
| API INSTANCE
|--------------------------------------------------------------------------
*/
const api = axios.create({
  baseURL: 'http://localhost:8000/api',
  withCredentials: true,
  xsrfCookieName: 'XSRF-TOKEN',
  xsrfHeaderName: 'X-XSRF-TOKEN',
})

api.interceptors.request.use((config) => {
  const match = document.cookie.match(/(^| )XSRF-TOKEN=([^;]+)/)
  if (match) {
    config.headers['X-XSRF-TOKEN'] = decodeURIComponent(match[2])
  }
  return config
})

/*
|--------------------------------------------------------------------------
| STATE
|--------------------------------------------------------------------------
*/
const products = ref([])
const product = ref({})
const transactions = ref([])
const history = ref([])

const summary = ref({
  total_sales: 0,
  total_returns: 0,
  units_sold: 0,
  units_returned: 0,
})

const productSearch = ref('')
const transactionSearch = ref('')
const activeTab = ref('transactions')
const sortBy = ref('date_desc')

const showModal = ref(false)
const modalData = ref(null)

/*
|--------------------------------------------------------------------------
| FETCHERS
|--------------------------------------------------------------------------
*/
const fetchProducts = async () => {
  const { data } = await api.get('/products')
  products.value = data
}

const fetchProduct = async () => {
  const { data } = await api.get(`/products/${route.params.id}`)
  product.value = data
}

const fetchTransactions = async () => {
  const { data } = await api.get(`/products/${route.params.id}/transactions`, {
    params: {
      search: transactionSearch.value,
      sort: sortBy.value,
    },
  })

  transactions.value = data.transactions.data
}

const fetchSummary = async () => {
  const { data } = await api.get(`/products/${route.params.id}/summary`)
  summary.value = data
}

const fetchHistory = async () => {
  const { data } = await api.get(`/products/${route.params.id}/history`)
  history.value = data.data
}

/*
|--------------------------------------------------------------------------
| COMPUTED
|--------------------------------------------------------------------------
*/
const filteredProducts = computed(() => {
  if (!productSearch.value.trim()) return products.value

  const term = productSearch.value.toLowerCase()

  return products.value.filter((p) => {
    const name = (p.ProductName ?? '').toLowerCase()
    const sku = (p.SKU ?? '').toLowerCase()
    return name.includes(term) || sku.includes(term)
  })
})

/*
|--------------------------------------------------------------------------
| WATCHERS
|--------------------------------------------------------------------------
*/
watch([sortBy, transactionSearch], fetchTransactions)

watch(
  () => route.params.id,
  async () => {
    await fetchProduct()
    await fetchTransactions()
    await fetchSummary()
    await fetchHistory()
  },
)

/*
|--------------------------------------------------------------------------
| MODAL
|--------------------------------------------------------------------------
*/
function openModal(t) {
  modalData.value = t
  showModal.value = true
}

function closeModal() {
  showModal.value = false
}

/*
|--------------------------------------------------------------------------
| INIT
|--------------------------------------------------------------------------
*/
onMounted(async () => {
  await fetchProducts()
  await fetchProduct()
  await fetchTransactions()
  await fetchSummary()
  await fetchHistory()
})
</script>

<template>
  <Layout>
    <div class="flex h-screen bg-gray-100">
      <!-- SIDEBAR -->
      <aside class="w-64 bg-white shadow h-screen p-6">
        <div class="mb-6 flex items-center justify-between">
          <h2 class="text-lg font-semibold">All Items</h2>
          <RouterLink to="/products/create" class="bg-blue-600 text-white px-4 py-2 rounded">
            + New
          </RouterLink>
        </div>

        <input
          v-model="productSearch"
          placeholder="Search products..."
          class="w-full px-3 py-2 border rounded mb-4"
        />

        <ul class="space-y-3 overflow-y-auto">
          <li
            v-for="item in filteredProducts"
            :key="item.ProductID"
            @click="router.push(`/products/${item.ProductID}/transactions`)"
            class="p-3 rounded hover:bg-gray-50 cursor-pointer flex justify-between"
          >
            <div>
              <p class="font-medium text-sm">{{ item.ProductName }}</p>
              <span class="text-xs text-gray-500">SKU: {{ item.SKU }}</span>
              <div class="mt-1 text-xs text-gray-600">Stock: {{ item.Stock }}</div>
            </div>
            <span class="font-semibold">₱{{ item.SellingPrice }}</span>
          </li>
        </ul>
      </aside>

      <!-- RIGHT PANEL -->
      <div class="flex-1 flex flex-col">
        <!-- HEADER -->
        <header class="h-16 bg-white border-b flex items-center justify-between px-6 shadow-sm">
          <div>
            <h1 class="text-lg font-semibold">
              {{ product.ProductName || 'Product Transactions' }}
            </h1>
            <p class="text-xs text-gray-500">Inventory & Sales Transactions</p>
          </div>

          <div class="flex gap-3">
            <RouterLink to="/products" class="px-3 py-2 border rounded text-sm">
              Back to Products
            </RouterLink>
            <RouterLink
              to="/products/create"
              class="px-4 py-2 bg-blue-600 text-white rounded text-sm"
            >
              + Add Product
            </RouterLink>
          </div>
        </header>

        <!-- MAIN -->
        <main class="flex-1 p-6 space-y-8 bg-white overflow-y-auto">
          <input
            v-model="transactionSearch"
            placeholder="Search transactions..."
            class="w-full px-3 py-2 border rounded"
          />

          <!-- SUMMARY -->
          <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div class="p-4 border rounded">
              <p class="text-sm text-gray-500">Total Sales</p>
              <p class="text-2xl font-semibold">₱{{ summary.total_sales }}</p>
            </div>
            <div class="p-4 border rounded">
              <p class="text-sm text-gray-500">Total Returns</p>
              <p class="text-2xl font-semibold">₱{{ summary.total_returns }}</p>
            </div>
            <div class="p-4 border rounded">
              <p class="text-sm text-gray-500">Units Sold</p>
              <p class="text-2xl font-semibold">{{ summary.units_sold }}</p>
            </div>
            <div class="p-4 border rounded">
              <p class="text-sm text-gray-500">Units Returned</p>
              <p class="text-2xl font-semibold">{{ summary.units_returned }}</p>
            </div>
          </div>

          <!-- TABS -->
          <ul class="flex border-b gap-6 text-sm">
            <li
              class="cursor-pointer pb-2"
              :class="activeTab === 'transactions' && 'border-b-2 border-blue-600'"
              @click="activeTab = 'transactions'"
            >
              Transactions
            </li>
            <li
              class="cursor-pointer pb-2"
              :class="activeTab === 'history' && 'border-b-2 border-blue-600'"
              @click="activeTab = 'history'"
            >
              History
            </li>
          </ul>

          <!-- TRANSACTIONS -->
          <div v-if="activeTab === 'transactions'">
            <select v-model="sortBy" class="border px-3 py-1 mb-4">
              <option value="date_desc">Date (Newest)</option>
              <option value="date_asc">Date (Oldest)</option>
              <option value="amount_desc">Amount (High)</option>
              <option value="amount_asc">Amount (Low)</option>
            </select>

            <table class="min-w-full border text-sm">
              <thead class="bg-gray-50">
                <tr>
                  <th class="px-4 py-2">Date</th>
                  <th>Type</th>
                  <th>Qty</th>
                  <th>Unit Price</th>
                  <th>Total</th>
                  <th>Ref</th>
                </tr>
              </thead>
              <tbody>
                <tr
                  v-for="t in transactions"
                  :key="t.id"
                  @click="openModal(t)"
                  class="hover:bg-gray-50 cursor-pointer"
                >
                  <td class="px-4 py-2">{{ t.date }}</td>
                  <td>{{ t.type }}</td>
                  <td>{{ Math.abs(t.quantity) }}</td>
                  <td>₱{{ t.unit_price }}</td>
                  <td>₱{{ t.total }}</td>
                  <td>{{ t.reference }}</td>
                </tr>
              </tbody>
            </table>
          </div>

          <!-- HISTORY -->
          <div v-if="activeTab === 'history'" class="space-y-2">
            <div v-for="h in history" :key="h.event_date" class="p-3 border rounded">
              <p class="text-sm font-medium">{{ h.description }}</p>
              <p class="text-xs text-gray-500">{{ h.event_date }}</p>
            </div>
          </div>
        </main>
      </div>

      <!-- MODAL -->
      <div
        v-if="showModal"
        class="fixed inset-0 bg-black/40 flex items-center justify-center"
        @click.self="closeModal"
      >
        <div class="bg-white p-6 rounded w-96">
          <h2 class="font-semibold mb-2">Transaction Details</h2>
          <p><strong>Date:</strong> {{ modalData.date }}</p>
          <p><strong>Type:</strong> {{ modalData.type }}</p>
          <p><strong>Qty:</strong> {{ modalData.quantity }}</p>
          <p><strong>Unit Price:</strong> ₱{{ modalData.unit_price }}</p>
          <p><strong>Total:</strong> ₱{{ modalData.total }}</p>
          <p><strong>Ref:</strong> {{ modalData.reference }}</p>
          <button @click="closeModal" class="mt-4 w-full bg-gray-700 text-white py-2 rounded">
            Close
          </button>
        </div>
      </div>
    </div>
  </Layout>
</template>
