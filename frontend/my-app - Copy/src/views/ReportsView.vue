<template>
  <Layout title="Reports">
    <!-- ✅ ADD ID HERE so print targets only this area -->
    <div id="reports-print-area" class="p-6 space-y-10 w-full max-w-none">
      <!-- SALES ACTIVITY -->
      <div>
        <h2 class="mb-4 text-xl font-bold text-gray-700">Sales Activity</h2>

        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-6">
          <div
            v-for="card in salesActivity"
            :key="card.label"
            class="p-6 bg-white rounded-xl shadow-lg hover:shadow-xl transition"
          >
            <div class="text-sm text-blue-600 font-medium">{{ card.label }}</div>
            <div class="mt-2 text-3xl font-extrabold text-blue-800">{{ card.value }}</div>
          </div>
        </div>
      </div>

      <!-- INVENTORY SUMMARY -->
      <div>
        <h2 class="mb-4 text-xl font-bold text-gray-700">Inventory Summary</h2>

        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-5 gap-6">
          <div
            v-for="box in inventorySummaryBoxes"
            :key="box.label"
            class="p-6 rounded-xl shadow-lg hover:shadow-xl transition"
            :class="box.isRed ? 'bg-red-50' : 'bg-white'"
          >
            <div class="text-sm font-medium" :class="box.isRed ? 'text-red-700' : 'text-blue-600'">
              {{ box.label }}
            </div>

            <div
              class="mt-2 text-3xl font-extrabold"
              :class="box.isRed ? 'text-red-800' : 'text-blue-800'"
            >
              {{ box.value }}
            </div>
          </div>
        </div>
      </div>

      <!-- TOP SELLING ITEMS -->
      <div>
        <h2 class="mb-4 text-xl font-bold text-gray-700">Top Selling Items This Month</h2>

        <div class="p-6 bg-white rounded-2xl shadow-lg overflow-x-auto">
          <!-- BUTTONS + CONTROLS (matches Blade layout) -->
          <div class="flex items-center justify-between mb-4">
            <div class="flex items-center gap-3">
              <button id="copyBtn" class="custom-export-btn">Copy</button>
              <button id="csvBtn" class="custom-export-btn">CSV</button>
              <button id="printBtn" class="custom-export-btn">Print</button>
            </div>

            <!-- DataTables Length + Search -->
            <div class="flex gap-4 items-center" id="datatableControls"></div>
          </div>

          <!-- TABLE -->
          <table id="topSellingItemsTable" class="min-w-full divide-y divide-blue-200 table-auto">
            <thead class="bg-blue-100">
              <tr>
                <th class="px-4 py-2 text-left text-xs font-semibold text-blue-700 uppercase">
                  Product Name
                </th>
                <th class="px-4 py-2 text-left text-xs font-semibold text-blue-700 uppercase">
                  Quantity Sold
                </th>
                <th class="px-4 py-2 text-left text-xs font-semibold text-blue-700 uppercase">
                  Total Sales
                </th>
                <th class="px-4 py-2 text-left text-xs font-semibold text-blue-700 uppercase">
                  Category
                </th>
              </tr>
            </thead>

            <tbody class="bg-white divide-y divide-blue-200">
              <tr
                v-for="item in topSellingItems"
                :key="item.id"
                class="hover:bg-blue-50 transition"
              >
                <td class="px-4 py-2">{{ item.ProductName }}</td>
                <td class="px-4 py-2">{{ item.total_quantity }}</td>
                <td class="px-4 py-2">₱{{ price(item.total_sales) }}</td>
                <td class="px-4 py-2">{{ item.CategoryName }}</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

      <!-- MONTHLY SALES -->
      <div>
        <h2 class="mb-4 text-xl font-bold text-gray-700">Monthly Sales</h2>

        <div class="p-6 bg-white rounded-2xl shadow-lg">
          <div class="flex justify-between mb-6">
            <span class="text-sm text-blue-700">Total Sales This Month</span>
            <span class="text-3xl font-bold text-blue-800">₱{{ price(monthlyTotal) }}</span>
          </div>

          <div class="h-64"><canvas id="monthlySalesChart"></canvas></div>
        </div>
      </div>
    </div>
  </Layout>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import Layout from '@/components/Layout.vue'
import Chart from 'chart.js/auto'
import api from '@/api/axios'

// jQuery + DataTables
import 'https://code.jquery.com/jquery-3.5.1.min.js'
import 'https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js'
import 'https://cdn.datatables.net/buttons/2.2.2/js/dataTables.buttons.min.js'
import 'https://cdn.datatables.net/buttons/2.2.2/js/buttons.html5.min.js'
import 'https://cdn.datatables.net/buttons/2.2.2/js/buttons.print.min.js'
import 'https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js'

/* ---------------------------------------------------------
   SAFE DEFAULT VALUES (UI WILL ALWAYS SHOW THESE)
--------------------------------------------------------- */
const salesActivity = ref([
  { label: "This Month's Sales", value: '₱0.00' },
  { label: 'Items Sold This Month', value: 0 },
  { label: 'Transactions This Month', value: 0 },
  { label: 'Monthly Profit', value: '₱0.00' },
])

const inventorySummaryBoxes = ref([
  { label: 'Quantity in Hand', value: 0 },
  { label: 'Quantity to Receive', value: 0 },
  { label: 'Low Stock Items', value: 0, isRed: true },
  { label: 'Total Items', value: 0 },
  { label: 'Active Items', value: 0 },
])

const topSellingItems = ref([])
const monthlySales = ref([])
const monthlyTotal = ref(0)

/* ---------------------------------------------------------
   LOAD REAL DATA FROM BACKEND
--------------------------------------------------------- */
async function loadReports() {
  try {
    const res = await api.get('/api/reports')

    salesActivity.value = res.data.sales_activity ?? salesActivity.value
    inventorySummaryBoxes.value = res.data.inventory_summary ?? inventorySummaryBoxes.value
    topSellingItems.value = res.data.top_selling ?? []
    monthlySales.value = res.data.monthly_sales ?? []
    monthlyTotal.value = res.data.monthly_total ?? 0
  } catch (err) {
    console.error('Reports API error:', err)
    // UI must NOT break—keep default values
  }
}

/* PRICE FORMATTER */
const price = (v) => Number(v || 0).toLocaleString('en-PH', { minimumFractionDigits: 2 })

/* ---------------------------------------------------------
   INITIALIZE DATATABLE & CHART AFTER DATA LOADS
--------------------------------------------------------- */
onMounted(async () => {
  await loadReports()

  // Destroy previous DataTable safely
  if ($.fn.DataTable.isDataTable('#topSellingItemsTable')) {
    $('#topSellingItemsTable').DataTable().destroy()
  }

  const table = $('#topSellingItemsTable').DataTable({
    paging: true,
    searching: true,
    info: true,
    order: [],
    dom: 'lftip',
    buttons: ['copy', 'csv', 'print'],
  })

  // Move search + length
  const controls = $('#datatableControls')
  controls.append($('#topSellingItemsTable_length'))
  controls.append($('#topSellingItemsTable_filter'))

  // ✅ PRINT: use window.print() of YOUR PAGE area
  // DataTables print sometimes opens about:blank, so force normal print
  document.getElementById('copyBtn').onclick = () => table.button('.buttons-copy').trigger()
  document.getElementById('csvBtn').onclick = () => table.button('.buttons-csv').trigger()

  document.getElementById('printBtn').onclick = () => {
    // optional: temporarily show all rows before print
    table.page.len(-1).draw()
    setTimeout(() => window.print(), 200)
    // restore to default length after printing
    setTimeout(() => table.page.len(10).draw(), 1000)
  }

  // CHART
  const ctx = document.getElementById('monthlySalesChart')
  new Chart(ctx, {
    type: 'line',
    data: {
      labels: monthlySales.value.map((x) => x.date),
      datasets: [
        {
          label: 'Daily Sales',
          data: monthlySales.value.map((x) => x.total),
          borderColor: '#1D4ED8',
          backgroundColor: 'rgba(29, 78, 216, 0.2)',
          borderWidth: 3,
          tension: 0.4,
          fill: true,
        },
      ],
    },
    options: { responsive: true, maintainAspectRatio: false },
  })
})
</script>

<!-- ✅ KEEP YOUR SCOPED STYLES (UI) -->
<style scoped>
.custom-export-btn {
  background: #1d4ed8;
  color: white;
  padding: 6px 14px;
  border-radius: 6px;
  font-size: 14px;
}
.custom-export-btn:hover {
  background: #153eaa;
}

/* ❌ Hide internal DataTables BUTTON BAR so it never shows */
div.dt-buttons {
  display: none !important;
}

/* Search & entries input styling */
#topSellingItemsTable_filter input,
#topSellingItemsTable_length select {
  border: 1px solid #93c5fd !important;
  border-radius: 6px !important;
  padding: 4px 8px !important;
}

/* Pagination */
.paginate_button {
  padding: 4px 10px !important;
  border: 1px solid #d1d5db !important;
}
.paginate_button.current {
  background: #1d4ed8 !important;
  color: white !important;
}
</style>

<!-- ✅ ADD THIS SECOND STYLE BLOCK (NOT SCOPED) FOR PRINT -->
<style>
@media print {
  /* Hide everything outside the report area */
  body * {
    visibility: hidden !important;
  }

  /* Show only the report */
  #reports-print-area,
  #reports-print-area * {
    visibility: visible !important;
  }

  #reports-print-area {
    position: absolute;
    left: 0;
    top: 0;
    width: 100%;
    padding: 20px;
    background: white;
  }

  /* Hide buttons + datatable controls in print */
  #datatableControls,
  .custom-export-btn {
    display: none !important;
  }

  /* Table header repeats */
  thead {
    display: table-header-group !important;
  }

  tr {
    page-break-inside: avoid !important;
  }

  /* Ensure chart prints */
  canvas {
    max-width: 100% !important;
  }
}
</style>
