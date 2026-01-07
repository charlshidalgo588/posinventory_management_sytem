<template>
  <Layout title="Home">
    <div class="w-full min-h-screen bg-gray-50 p-4 md:p-6">
      <div class="max-w-[1800px] mx-auto space-y-8">
        <!-- ====================== SALES ORDER SUMMARY ====================== -->
        <div class="rounded-3xl shadow-xl bg-white p-10">
          <div class="mb-8 flex justify-between items-start flex-wrap gap-4">
            <div>
              <h1 class="text-4xl font-bold text-gray-800">Sales Order Summary</h1>

              <div class="mt-4">
                <p class="text-lg text-gray-600">Total Sales This Month</p>
                <p class="text-3xl font-bold text-orange-500">₱{{ formatNumber(monthlyTotal) }}</p>
              </div>
            </div>

            <select
              v-model="selectedDateRange"
              @change="loadDashboard"
              class="text-sm bg-white border border-gray-300 rounded-lg p-2 shadow-sm hover:scale-105 transition"
            >
              <option value="today">Today</option>
              <option value="yesterday">Yesterday</option>
              <option value="this_week">This Week</option>
              <option value="last_week">Last Week</option>
              <option value="this_month">This Month</option>
              <option value="last_month">Last Month</option>
              <option value="this_year">This Year</option>
              <option value="last_year">Last Year</option>
            </select>
          </div>

          <div class="bg-white rounded-2xl shadow-lg p-6">
            <div class="relative w-full aspect-[16/4]">
              <canvas id="monthlySalesChart"></canvas>
            </div>
          </div>
        </div>

        <!-- ================= BODY GRID ================== -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
          <!-- SALES ACTIVITY -->
          <div class="lg:col-span-2 bg-white rounded-2xl shadow-lg">
            <div class="bg-sky-0 p-4 font-semibold text-gray-900 text-lg">Sales Activity</div>

            <div class="overflow-x-auto">
              <table class="min-w-full text-sm divide-y divide-gray-200">
                <thead class="bg-orange-100">
                  <tr>
                    <th class="px-4 py-3 text-left font-medium">Metric</th>
                    <th class="px-4 py-3 text-left font-medium">Value</th>
                    <th class="px-4 py-3 text-left font-medium">Unit</th>
                  </tr>
                </thead>

                <tbody class="divide-y divide-gray-300">
                  <tr class="hover:bg-orange-50">
                    <td class="px-4 py-2">Total Profit</td>
                    <td class="px-4 py-2 font-semibold text-green-600">
                      ₱{{ formatNumber(totalProfit) }}
                    </td>
                    <td class="px-4 py-2">Peso</td>
                  </tr>
                  <tr class="hover:bg-orange-50">
                    <td class="px-4 py-2">Today Sales</td>
                    <td class="px-4 py-2 font-semibold text-blue-700">
                      ₱{{ formatNumber(todaySales) }}
                    </td>
                    <td class="px-4 py-2">Peso</td>
                  </tr>
                  <tr class="hover:bg-orange-50">
                    <td class="px-4 py-2">Items Sold</td>
                    <td class="px-4 py-2 font-semibold">{{ itemsSoldToday }}</td>
                    <td class="px-4 py-2">Products</td>
                  </tr>
                  <tr class="hover:bg-orange-50">
                    <td class="px-4 py-2">Transactions</td>
                    <td class="px-4 py-2 font-semibold">{{ transactionsToday }}</td>
                    <td class="px-4 py-2">Qty</td>
                  </tr>
                  <tr class="hover:bg-orange-50">
                    <td class="px-4 py-2">Void</td>
                    <td class="px-4 py-2 font-semibold text-red-600">
                      {{ voidTransactionsToday }}
                    </td>
                    <td class="px-4 py-2">Qty</td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>

          <!-- ITEM DETAILS -->
          <div class="bg-white rounded-2xl shadow-lg">
            <div class="bg-sky-0 p-4 font-semibold text-gray-900 text-lg">Item Details</div>

            <table class="min-w-full text-sm">
              <tbody class="divide-y divide-gray-300">
                <tr>
                  <td class="px-4 py-2 text-red-600">Low Stock Items</td>
                  <td class="px-4 py-2 font-semibold text-red-600">
                    {{ inventorySummary.low_stock_items }}
                  </td>
                </tr>
                <tr>
                  <td class="px-4 py-2">Item Groups</td>
                  <td class="px-4 py-2 font-semibold">{{ inventorySummary.total_items }}</td>
                </tr>
                <tr>
                  <td class="px-4 py-2">Active Items</td>
                  <td class="px-4 py-2 font-semibold">{{ inventorySummary.active_items }}</td>
                </tr>
              </tbody>
            </table>
          </div>

          <!-- INVENTORY SUMMARY -->
          <div class="bg-white rounded-2xl shadow-lg">
            <div class="bg-sky-0 p-4 font-semibold text-gray-900 text-lg">Inventory Summary</div>

            <table class="min-w-full text-sm">
              <tbody class="divide-y divide-gray-300">
                <tr>
                  <td class="px-4 py-2">Quantity in Hand</td>
                  <td class="px-4 py-2 font-semibold">{{ inventorySummary.quantity_in_hand }}</td>
                </tr>
                <tr>
                  <td class="px-4 py-2">Quantity to Receive</td>
                  <td class="px-4 py-2 font-semibold">
                    {{ inventorySummary.quantity_to_receive }}
                  </td>
                </tr>
              </tbody>
            </table>
          </div>

          <!-- TOP SELLING -->
          <div class="lg:col-span-2 bg-white rounded-2xl shadow-lg">
            <div class="bg-sky-0 p-4 flex justify-between items-center font-semibold text-lg">
              <span>Top Selling Items</span>

              <select
                v-model="selectedDateRange"
                @change="loadDashboard"
                class="text-sm bg-white border border-gray-300 p-2 rounded-lg shadow-sm hover:scale-105"
              >
                <option value="today">Today</option>
                <option value="this_month">This Month</option>
                <option value="this_year">This Year</option>
              </select>
            </div>

            <table class="min-w-full text-sm">
              <thead class="bg-orange-100">
                <tr>
                  <th class="px-6 py-3">Product Name</th>
                  <th class="px-6 py-3">Quantity Sold</th>
                  <th class="px-6 py-3">Total Sales</th>
                  <th class="px-6 py-3">Category</th>
                </tr>
              </thead>

              <tbody class="divide-y divide-gray-300">
                <tr v-for="(item, index) in topSellingItems" :key="index">
                  <td class="px-6 py-4">{{ item.ProductName }}</td>
                  <td class="px-6 py-4">{{ item.total_quantity }}</td>
                  <td class="px-6 py-4">₱{{ formatNumber(item.total_sales) }}</td>
                  <td class="px-6 py-4">{{ item.CategoryName || 'N/A' }}</td>
                </tr>

                <tr v-if="topSellingItems.length === 0">
                  <td colspan="4" class="px-6 py-4 text-center text-gray-500">No data available</td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </Layout>
</template>

<script>
import Chart from 'chart.js/auto'
import api from '@/api/axios'
import Layout from '@/components/Layout.vue'

export default {
  name: 'HomeView',
  components: { Layout },

  data() {
    return {
      selectedDateRange: 'this_month',

      todaySales: 0,
      itemsSoldToday: 0,
      transactionsToday: 0,
      voidTransactionsToday: 0,

      inventorySummary: {},
      monthlySales: [],
      dailyProfit: [],
      topSellingItems: [],
      totalProfit: 0,
      monthlyTotal: 0,

      chart: null,
    }
  },

  mounted() {
    this.loadDashboard()
  },

  methods: {
    /**
     * MAIN DASHBOARD LOAD
     */
    async loadDashboard() {
      try {
        const res = await api.get('/api/dashboard', {
          params: { date_range: this.selectedDateRange },
        })

        // Assign backend response
        Object.assign(this, res.data)

        // still align profit to sales dates (safe to keep)
        this.normalizeProfitData()

        this.$nextTick(() => this.renderChart())
      } catch (err) {
        console.error('Dashboard load error:', err)
      }
    },

    /**
     * Make profit array have the same dates as monthlySales
     */
    normalizeProfitData() {
      const profitMap = {}
      this.dailyProfit.forEach((p) => {
        profitMap[p.date] = p.profit
      })

      this.dailyProfit = this.monthlySales.map((s) => ({
        date: s.date,
        profit: profitMap[s.date] ?? 0,
      }))
    },

    formatNumber(n) {
      return Number(n || 0).toLocaleString()
    },

    formatDate(date) {
      if (!date) return ''
      return new Date(date).toLocaleDateString('en-US', {
        month: 'short',
        day: '2-digit',
      })
    },

    /**
     * RENDER SALES + PROFIT CHART
     * (only days that have sales OR profit)
     */
    renderChart() {
      const el = document.getElementById('monthlySalesChart')
      if (!el) return
      if (this.chart) this.chart.destroy()

      // 🔥 Merge sales + profit, then remove days with both 0
      const merged = this.monthlySales.map((sale) => {
        const profitRow = this.dailyProfit.find((p) => p.date === sale.date)
        return {
          date: sale.date,
          total: sale.total || 0,
          profit: profitRow ? profitRow.profit || 0 : 0,
        }
      })

      const filtered = merged.filter((d) => d.total > 0 || d.profit > 0)

      const labels = filtered.map((d) => this.formatDate(d.date))
      const salesData = filtered.map((d) => d.total)
      const profitData = filtered.map((d) => d.profit)

      this.chart = new Chart(el, {
        type: 'line',
        data: {
          labels,
          datasets: [
            {
              label: 'Daily Sales',
              data: salesData,
              borderColor: '#2563eb',
              backgroundColor: 'rgba(59, 130, 246, 0.25)',
              tension: 0.4,
              fill: true,
              pointRadius: 5,
            },
            {
              label: 'Daily Profit',
              data: profitData,
              borderColor: '#16a34a',
              backgroundColor: 'rgba(22, 163, 74, 0.25)',
              tension: 0.4,
              fill: true,
              pointRadius: 5,
            },
          ],
        },
        options: {
          responsive: true,
          maintainAspectRatio: false,
          plugins: { legend: { display: true } },
          scales: {
            x: { grid: { display: false } },
            y: { grid: { color: '#e5e7eb' } },
          },
        },
      })
    },
  },
}
</script>
