<template>
  <Layout title="Inventory Logs">
    <!-- ✅ Added id so print can target this section -->
    <div id="inventory-print-area" class="p-6 space-y-8">
      <!-- ✅ Print-only header (shows only on paper) -->
      <div class="print-only mb-6">
        <h1 class="text-xl font-bold">Sheem Steel Construction Company</h1>
        <p class="text-sm">Inventory Logs</p>
        <p class="text-sm">Printed: {{ new Date().toLocaleString() }}</p>
        <hr class="my-3" />
      </div>

      <!-- Summary Cards -->
      <div>
        <h2 class="mb-4 text-xl font-bold text-gray-700">Inventory Activity Summary</h2>
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-6">
          <div
            class="p-5 bg-gradient-to-r from-blue-0 to-blue-0 rounded-xl shadow-xl hover:shadow-2xl transition-all duration-300"
          >
            <div class="text-sm font-medium text-blue-700">Total Adjustments</div>
            <div class="mt-2 text-3xl font-extrabold text-blue-800">
              {{ summary.total_adjustments }}
            </div>
          </div>

          <div
            class="p-5 bg-gradient-to-r from-blue-0 to-blue-0 rounded-xl shadow-xl hover:shadow-2xl transition-all duration-300"
          >
            <div class="text-sm font-medium text-blue-700">Total Stock In</div>
            <div class="mt-2 text-3xl font-extrabold text-blue-800">
              {{ summary.total_stock_in }}
            </div>
          </div>

          <div
            class="p-5 bg-gradient-to-r from-red-100 to-red-50 rounded-xl shadow-xl hover:shadow-2xl transition-all duration-300"
          >
            <div class="text-sm font-medium text-red-700">Total Stock Out</div>
            <div class="mt-2 text-3xl font-extrabold text-red-800">
              {{ summary.total_stock_out }}
            </div>
          </div>

          <div
            class="p-5 bg-gradient-to-r from-blue-0 to-blue-0 rounded-xl shadow-xl hover:shadow-2xl transition-all duration-300"
          >
            <div class="text-sm font-medium text-blue-700">Manual Adjustments</div>
            <div class="mt-2 text-3xl font-extrabold text-blue-800">
              {{ summary.manual_adjustments }}
            </div>
          </div>
        </div>
      </div>

      <!-- Recent Activity -->
      <div>
        <h2 class="mb-4 text-xl font-bold text-gray-700">Recent Activity</h2>

        <div class="p-4 bg-blue-0 rounded-2xl shadow-lg overflow-x-auto border border-blue-100">
          <table id="recentActivityTable" class="min-w-full divide-y divide-blue-200 table-auto">
            <thead class="bg-gray-300">
              <tr>
                <th class="px-6 py-3 text-xs font-semibold text-left text-gray-900 uppercase">
                  Product
                </th>
                <th class="px-6 py-3 text-xs font-semibold text-left text-gray-900 uppercase">
                  Type
                </th>
                <th class="px-6 py-3 text-xs font-semibold text-left text-gray-900 uppercase">
                  Quantity
                </th>
                <th class="px-6 py-3 text-xs font-semibold text-left text-gray-900 uppercase">
                  Date
                </th>
                <th class="px-6 py-3 text-xs font-semibold text-left text-gray-900 uppercase">
                  Notes
                </th>
              </tr>
            </thead>

            <tbody class="bg-white divide-y divide-blue-200">
              <tr
                v-for="item in recentActivity"
                :key="item.id"
                class="hover:bg-blue-50 transition-colors"
              >
                <td class="px-6 py-4 whitespace-nowrap">
                  <div class="text-sm font-medium text-gray-900">{{ item.ProductName }}</div>
                  <div class="text-xs text-gray-400">{{ item.SKU }}</div>
                </td>

                <td class="px-6 py-4 whitespace-nowrap">
                  <span
                    class="px-3 py-1 inline-flex text-xs font-semibold rounded-full shadow-sm"
                    :class="{
                      'bg-green-100 text-green-800': item.type === 'stock_in',
                      'bg-red-100 text-red-800': item.type === 'stock_out',
                      'bg-yellow-100 text-yellow-800':
                        item.type !== 'stock_in' && item.type !== 'stock_out',
                    }"
                  >
                    {{ item.type }}
                  </span>
                </td>

                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                  {{ item.quantity }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-400">
                  {{ item.created_at }}
                </td>
                <td class="px-6 py-4 text-sm text-gray-500">{{ item.notes }}</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

      <!-- Top Adjusted Products -->
      <div>
        <h2 class="mb-4 text-xl font-bold text-gray-700">Top Adjusted Products</h2>

        <div class="p-4 bg-blue-0 rounded-2xl shadow-lg overflow-x-auto border border-blue-100">
          <table id="topAdjustedTable" class="min-w-full divide-y divide-blue-200 table-auto">
            <thead class="bg-blue-100">
              <tr>
                <th class="px-6 py-3 text-left text-xs font-semibold text-blue-700 uppercase">
                  Product
                </th>
                <th class="px-6 py-3 text-left text-xs font-semibold text-blue-700 uppercase">
                  Adjustments
                </th>
                <th class="px-6 py-3 text-left text-xs font-semibold text-green-700 uppercase">
                  Total Stock In
                </th>
                <th class="px-6 py-3 text-left text-xs font-semibold text-red-700 uppercase">
                  Total Stock Out
                </th>
              </tr>
            </thead>

            <tbody class="bg-white divide-y divide-blue-200">
              <tr
                v-for="item in topAdjustedProducts"
                :key="item.id"
                class="hover:bg-blue-50 transition-colors"
              >
                <td class="px-6 py-4 whitespace-nowrap">
                  <div class="text-sm font-medium text-gray-900">{{ item.ProductName }}</div>
                  <div class="text-xs text-gray-400">{{ item.SKU }}</div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                  {{ item.adjustment_count }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-green-600">
                  +{{ item.total_stock_in }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-red-600">
                  -{{ item.total_stock_out }}
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

      <!-- Full Inventory Logs -->
      <div>
        <h2 class="mb-4 text-xl font-bold text-gray-700">Inventory Logs</h2>

        <div class="p-4 bg-blue-0 rounded-2xl shadow-lg overflow-x-auto border border-blue-100">
          <table id="inventoryLogsTable" class="min-w-full divide-y divide-blue-200 table-auto">
            <thead class="bg-blue-100">
              <tr>
                <th class="px-6 py-3 text-left text-xs font-semibold text-blue-700 uppercase">
                  Product
                </th>
                <th class="px-6 py-3 text-left text-xs font-semibold text-blue-700 uppercase">
                  Category
                </th>
                <th class="px-6 py-3 text-left text-xs font-semibold text-blue-700 uppercase">
                  Type
                </th>
                <th class="px-6 py-3 text-left text-xs font-semibold text-blue-700 uppercase">
                  Quantity
                </th>
                <th class="px-6 py-3 text-left text-xs font-semibold text-blue-700 uppercase">
                  Date
                </th>
                <th class="px-6 py-3 text-left text-xs font-semibold text-blue-700 uppercase">
                  Notes
                </th>
              </tr>
            </thead>

            <tbody class="bg-white divide-y divide-blue-200">
              <tr
                v-for="item in inventoryLogs"
                :key="item.id"
                class="hover:bg-blue-50 transition-colors"
              >
                <td class="px-6 py-4 whitespace-nowrap">
                  <div class="text-sm font-medium text-gray-900">{{ item.ProductName }}</div>
                  <div class="text-xs text-gray-400">{{ item.SKU }}</div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                  {{ item.CategoryName }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <span
                    class="px-3 py-1 inline-flex text-xs font-semibold rounded-full shadow-sm"
                    :class="{
                      'bg-green-100 text-green-800': item.type === 'stock_in',
                      'bg-red-100 text-red-800': item.type === 'stock_out',
                      'bg-yellow-100 text-yellow-800':
                        item.type !== 'stock_in' && item.type !== 'stock_out',
                    }"
                  >
                    {{ item.type }}
                  </span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                  {{ item.quantity }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-400">
                  {{ item.created_at }}
                </td>
                <td class="px-6 py-4 text-sm text-gray-500">{{ item.notes }}</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </Layout>
</template>

<script setup>
import { ref, onMounted, nextTick, onBeforeUnmount } from 'vue'
import Layout from '@/components/Layout.vue'
import api from '@/api/axios'

const summary = ref({
  total_adjustments: 0,
  total_stock_in: 0,
  total_stock_out: 0,
  manual_adjustments: 0,
})

const recentActivity = ref([])
const topAdjustedProducts = ref([])
const inventoryLogs = ref([])

let dtRecent = null
let dtTop = null
let dtLogs = null

// jQuery + DataTables
import 'https://code.jquery.com/jquery-3.5.1.min.js'
import 'https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js'
import 'https://cdn.datatables.net/buttons/2.2.2/js/dataTables.buttons.min.js'
import 'https://cdn.datatables.net/buttons/2.2.2/js/buttons.html5.min.js'
import 'https://cdn.datatables.net/buttons/2.2.2/js/buttons.print.min.js'

async function loadInventoryLogs() {
  try {
    const res = await api.get('/api/inventory/logs')

    summary.value = res.data.summary
    recentActivity.value = res.data.recent_activity
    topAdjustedProducts.value = res.data.top_adjusted_products
    inventoryLogs.value = res.data.inventory_logs
  } catch (err) {
    console.error('Inventory logs API error:', err)
  }
}

// ✅ Added: print current page (not DataTables about:blank)
function printNow() {
  window.print()
}

onMounted(async () => {
  await loadInventoryLogs()
  await nextTick()

  setTimeout(() => {
    dtRecent = $('#recentActivityTable').DataTable({
      dom: '<"flex flex-wrap items-center justify-between"<"flex items-center"B><"flex items-center"lf>>rtip',
      buttons: [
        { extend: 'copy', text: 'Copy', className: 'dt-button' },
        { extend: 'csv', text: 'CSV', className: 'dt-button' },

        // ✅ FIX: replace DataTables print with real window.print()
        { text: 'Print', className: 'dt-button', action: () => printNow() },
      ],
      order: [[4, 'desc']],
      pageLength: 25,
    })

    dtTop = $('#topAdjustedTable').DataTable({
      dom: '<"flex items-center lf">rtip',
      pageLength: 25,
    })

    dtLogs = $('#inventoryLogsTable').DataTable({
      dom: '<"flex items-center lf">rtip',
      pageLength: 25,
    })
  }, 200)
})

onBeforeUnmount(() => {
  if (dtRecent) dtRecent.destroy()
  if (dtTop) dtTop.destroy()
  if (dtLogs) dtLogs.destroy()
})
</script>

<style>
/* (your styles unchanged) */
.dt-buttons {
  margin-bottom: 1rem;
}
.dt-button {
  background-color: #1d4ed8 !important;
  color: white !important;
  border: none !important;
  padding: 0.5rem 1rem !important;
  border-radius: 0.375rem !important;
  font-size: 0.875rem !important;
  font-weight: 500 !important;
  margin-right: 0.5rem !important;
  transition: all 0.2s !important;
}
.dt-button:hover {
  background-color: #1e40af !important;
  transform: translateY(-1px) !important;
}
.dataTables_filter input,
.dataTables_length select {
  border: 1px solid #93c5fd !important;
  border-radius: 0.375rem !important;
  padding: 0.5rem !important;
}
.dataTables_info {
  color: #1e3a8a !important;
  font-size: 0.875rem !important;
}
.paginate_button {
  border: 1px solid #93c5fd !important;
  border-radius: 0.375rem !important;
  padding: 0.5rem 1rem !important;
  margin: 0 0.25rem !important;
  color: #1e40af !important;
}
.paginate_button.current {
  background-color: #1d4ed8 !important;
  color: white !important;
  border-color: #1d4ed8 !important;
}
.paginate_button:hover:not(.current) {
  background-color: #dbeafe !important;
  color: #1e3a8a !important;
}

/* ✅ Added: Print fixes */
.print-only {
  display: none;
}

@media print {
  /* show only the inventory section */
  body * {
    visibility: hidden !important;
  }
  #inventory-print-area,
  #inventory-print-area * {
    visibility: visible !important;
  }
  #inventory-print-area {
    position: absolute;
    left: 0;
    top: 0;
    width: 100%;
    padding: 16px;
    background: white;
  }

  /* show print header */
  .print-only {
    display: block !important;
  }

  /* hide datatable controls during print */
  .dt-buttons,
  .dataTables_filter,
  .dataTables_length,
  .dataTables_paginate,
  .dataTables_info {
    display: none !important;
  }

  /* keep table header on each printed page */
  thead {
    display: table-header-group !important;
  }

  tr,
  td,
  th {
    page-break-inside: avoid !important;
  }
}
</style>
