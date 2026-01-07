<template>
  <Layout>
    <div class="p-10">
      <div class="w-full mx-auto">
        <!-- HEADER -->
        <div class="flex justify-between items-center mb-6">
          <h1 class="text-2xl font-bold">Sales Receipts</h1>

          <div class="flex space-x-4">
            <button
              v-if="selectedSales.length > 0"
              @click="openPasswordModal"
              class="bg-red-500 hover:bg-red-600 text-white font-semibold py-2 px-4 rounded"
            >
              Delete Selected
            </button>

            <RouterLink
              to="/pos"
              class="bg-blue-500 hover:bg-blue-600 text-white font-semibold py-2 px-4 rounded"
            >
              + New Sale
            </RouterLink>
          </div>
        </div>

        <!-- PERIOD DROPDOWN -->
        <div class="relative inline-block text-left mb-4 period-dropdown">
          <button
            @click="togglePeriodDropdown"
            class="inline-flex justify-center w-full rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50"
          >
            <span class="font-bold">VIEW BY:</span>
            <span class="ml-2">Period: {{ selectedPeriodLabel }}</span>

            <svg class="-mr-1 ml-2 h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
              <path
                fill-rule="evenodd"
                d="M5.23 7.21a.75.75 0 011.06.02L10 11.586l3.71-4.356a.75.75 0 011.14.976l-4.25 5a.75.75 0 01-1.14 0l-4.25-5a.75.75 0 01.02-1.06z"
              />
            </svg>
          </button>

          <div
            v-if="periodDropdown"
            class="origin-top-right absolute right-0 mt-2 w-48 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 z-10"
          >
            <div class="py-1">
              <button
                v-for="p in periods"
                :key="p.key"
                @click="setPeriod(p.key)"
                class="dropdown-item"
                :class="{ 'bg-gray-200 font-semibold': selectedPeriod === p.key }"
              >
                {{ p.label }}
              </button>
            </div>
          </div>
        </div>

        <!-- SALES TABLE -->
        <div class="overflow-x-auto bg-white rounded-lg shadow">
          <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-100">
              <tr>
                <th class="px-6 py-3">
                  <input
                    type="checkbox"
                    v-model="selectAll"
                    @change="toggleSelectAll"
                    class="h-4 w-4 text-blue-600"
                  />
                </th>
                <th class="th">Date</th>
                <th class="th">Receipt #</th>
                <th class="th">Customer</th>
                <th class="th">Amount</th>
                <th class="th">Payment</th>
                <th class="th">Status</th>
                <th class="th">Clerk</th>
              </tr>
            </thead>

            <tbody class="bg-white divide-y divide-gray-200">
              <tr v-for="sale in filteredSales" :key="sale.SaleID">
                <td class="px-6 py-4">
                  <input
                    type="checkbox"
                    class="h-4 w-4 text-blue-600"
                    :value="sale.SaleID"
                    v-model="selectedSales"
                  />
                </td>

                <td class="td">{{ sale.formatted_date }}</td>

                <td class="td font-semibold">SR-{{ String(sale.SaleID).padStart(5, '0') }}</td>

                <td class="td">{{ sale.CustomerName }}</td>

                <td class="td">₱{{ formatAmount(sale.TotalAmount) }}</td>

                <td class="td">{{ sale.PaymentMethod }}</td>

                <td class="td">
                  <span
                    :class="{
                      'text-green-600 font-semibold': sale.Status === 'Paid',
                      'text-red-600 font-semibold': sale.Status === 'Void',
                    }"
                  >
                    {{ sale.Status }}
                  </span>
                </td>

                <td class="td">{{ sale.ClerkName }}</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <!-- PASSWORD MODAL -->
    <div
      v-if="passwordModal"
      class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center"
    >
      <div class="bg-white p-6 rounded-lg shadow-lg w-96">
        <h2 class="text-lg font-bold mb-4 text-red-600">Admin Verification Required</h2>

        <label class="block mb-2 text-sm font-semibold"> Enter Admin Password: </label>

        <input
          type="password"
          v-model="adminPassword"
          class="w-full border rounded p-2 mb-4"
          placeholder="Password"
        />

        <div class="flex justify-end space-x-3">
          <button
            @click="closePasswordModal"
            class="px-4 py-2 bg-gray-300 hover:bg-gray-400 rounded"
          >
            Cancel
          </button>

          <button
            @click="confirmDelete"
            class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700"
          >
            Confirm Delete
          </button>
        </div>
      </div>
    </div>
  </Layout>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue'
import { RouterLink } from 'vue-router'
import Layout from '@/components/Layout.vue'
import api from '@/api/axios'

/* -------------------------------------------------------
   SALES DATA
------------------------------------------------------- */
const sales = ref([])

async function loadSales() {
  try {
    const res = await api.get('/api/sales')

    sales.value = res.data.map((s) => ({
      ...s,

      // ✅ Already formatted by API
      formatted_date: s.SaleDate,

      CustomerName: s.CustomerName || 'Unknown',
      ClerkName: s.ClerkName || 'Unknown',
    }))
  } catch (err) {
    console.error('Failed to load sales:', err)
  }
}

onMounted(loadSales)

/* -------------------------------------------------------
   PERIOD FILTER
------------------------------------------------------- */
const periods = [
  { key: 'today', label: 'Today' },
  { key: 'week', label: 'This Week' },
  { key: 'month', label: 'This Month' },
  { key: 'quarter', label: 'This Quarter' },
  { key: 'year', label: 'This Year' },
]

const selectedPeriod = ref('month')
const periodDropdown = ref(false)

const selectedPeriodLabel = computed(
  () => periods.find((p) => p.key === selectedPeriod.value)?.label,
)

function togglePeriodDropdown() {
  periodDropdown.value = !periodDropdown.value
}

function setPeriod(period) {
  selectedPeriod.value = period
  periodDropdown.value = false
}

/* -------------------------------------------------------
   CLICK OUTSIDE HANDLER
------------------------------------------------------- */
function handleClickOutside(e) {
  if (!e.target.closest('.period-dropdown')) {
    periodDropdown.value = false
  }
}

onMounted(() => document.addEventListener('click', handleClickOutside))
onUnmounted(() => document.removeEventListener('click', handleClickOutside))

/* -------------------------------------------------------
   FILTERED SALES
------------------------------------------------------- */
const filteredSales = computed(() => {
  const now = new Date()

  return sales.value.filter((s) => {
    const d = new Date(s.SaleDate)

    if (selectedPeriod.value === 'today') return d.toDateString() === now.toDateString()

    if (selectedPeriod.value === 'week') {
      const start = new Date(now)
      start.setDate(now.getDate() - now.getDay())
      start.setHours(0, 0, 0, 0)
      return d >= start
    }

    if (selectedPeriod.value === 'month')
      return d.getMonth() === now.getMonth() && d.getFullYear() === now.getFullYear()

    if (selectedPeriod.value === 'quarter')
      return (
        Math.floor(d.getMonth() / 3) === Math.floor(now.getMonth() / 3) &&
        d.getFullYear() === now.getFullYear()
      )

    if (selectedPeriod.value === 'year') return d.getFullYear() === now.getFullYear()

    return true
  })
})

/* -------------------------------------------------------
   SELECT ROWS
------------------------------------------------------- */
const selectedSales = ref([])
const selectAll = ref(false)

function toggleSelectAll() {
  selectedSales.value = selectAll.value ? filteredSales.value.map((s) => s.SaleID) : []
}

/* -------------------------------------------------------
   DELETE MODAL
------------------------------------------------------- */
const passwordModal = ref(false)
const adminPassword = ref('')

function openPasswordModal() {
  if (!selectedSales.value.length) return alert('Select a sale.')
  passwordModal.value = true
}

function closePasswordModal() {
  passwordModal.value = false
  adminPassword.value = ''
}

async function confirmDelete() {
  await api.post('/api/sales/bulk-delete', {
    saleIds: selectedSales.value,
    admin_password: adminPassword.value,
  })

  await loadSales()
  selectedSales.value = []
  selectAll.value = false
  closePasswordModal()
}

/* -------------------------------------------------------
   FORMAT
------------------------------------------------------- */
const formatAmount = (v) => Number(v).toLocaleString()
</script>

<style scoped>
.th {
  @apply px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase;
}
.td {
  @apply px-6 py-4 whitespace-nowrap text-sm;
}
.dropdown-item {
  @apply block w-full text-left px-4 py-2 text-sm hover:bg-gray-100;
}
</style>
