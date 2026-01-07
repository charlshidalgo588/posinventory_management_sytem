<script setup>
import { ref, computed } from 'vue'
import Layout from '@/components/Layout.vue'
import { useRoute, useRouter } from 'vue-router'

const route = useRoute()
const router = useRouter()

/* |--------------------------------------------------------------------------
| MOCK SALE DATA (Replace with API Later)
|-------------------------------------------------------------------------- */
const sale = ref({
  SaleID: route.params.id,
  SaleDate: '2024-02-12 14:30',
  PaymentMethod: 'Cash',
  CustomerName: 'Walk-in Customer',
  DiscountAmount: 20.0,
  AmountPaid: 900.0,
  clerk: {
    name: 'System',
  },
  items: [
    {
      ProductName: 'Steel Bar 10mm',
      Quantity: 2,
      Price: 350,
    },
    {
      ProductName: 'Roof Sheet (Red)',
      Quantity: 1,
      Price: 260,
    },
  ],
})

/* |--------------------------------------------------------------------------
| COMPUTED TOTALS
|-------------------------------------------------------------------------- */
const subtotal = computed(() =>
  sale.value.items.reduce((sum, item) => sum + item.Price * item.Quantity, 0),
)

const vat = computed(() => subtotal.value * 0.12)
const total = computed(() => subtotal.value + vat.value - sale.value.DiscountAmount)
const change = computed(() => sale.value.AmountPaid - total.value)

const formatted = (num) => `₱${Number(num).toFixed(2)}`

/* |--------------------------------------------------------------------------
| PRINT RECEIPT
|-------------------------------------------------------------------------- */
function printReceipt() {
  window.print()
}
</script>

<template>
  <Layout>
    <div class="p-10">
      <div class="max-w-7xl mx-auto">
        <!-- Header -->
        <div class="flex justify-between items-center mb-6">
          <div>
            <h1 class="text-2xl font-bold">Sales Receipt Details</h1>
            <p class="text-gray-600">SR-{{ String(sale.SaleID).padStart(5, '0') }}</p>
          </div>

          <div class="flex space-x-4 no-print">
            <RouterLink
              to="/sales-transaction"
              class="bg-gray-500 hover:bg-gray-600 text-white font-semibold py-2 px-4 rounded"
            >
              Back to Sales
            </RouterLink>

            <button
              class="bg-blue-500 hover:bg-blue-600 text-white font-semibold py-2 px-4 rounded"
              @click="printReceipt"
            >
              Print Receipt
            </button>
          </div>
        </div>

        <!-- Main Panel -->
        <div class="bg-white rounded-lg shadow-lg p-6 mb-6">
          <div class="grid grid-cols-2 gap-6 mb-6">
            <!-- Sale Info -->
            <div>
              <h2 class="text-lg font-semibold mb-4">Sale Information</h2>
              <div class="space-y-2">
                <p><span class="font-medium">Date:</span> {{ sale.SaleDate }}</p>
                <p><span class="font-medium">Customer:</span> {{ sale.CustomerName }}</p>
                <p><span class="font-medium">Payment Method:</span> {{ sale.PaymentMethod }}</p>
                <p><span class="font-medium">Status:</span> PAID</p>
              </div>
            </div>

            <!-- Summary -->
            <div>
              <h2 class="text-lg font-semibold mb-4">Transaction Summary</h2>
              <div class="space-y-2">
                <p><span class="font-medium">Subtotal:</span> {{ formatted(subtotal) }}</p>
                <p><span class="font-medium">VAT (12%):</span> {{ formatted(vat) }}</p>
                <p>
                  <span class="font-medium">Discount:</span> {{ formatted(sale.DiscountAmount) }}
                </p>

                <p class="text-lg font-bold">
                  <span class="font-medium">Total Amount:</span> {{ formatted(total) }}
                </p>

                <p>
                  <span class="font-medium">Amount Paid:</span> {{ formatted(sale.AmountPaid) }}
                </p>
                <p><span class="font-medium">Change:</span> {{ formatted(change) }}</p>
              </div>
            </div>
          </div>

          <!-- Items -->
          <h2 class="text-lg font-semibold mb-4">Items Purchased</h2>

          <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
              <thead class="bg-gray-50">
                <tr>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                    Product
                  </th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                    Qty
                  </th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                    Unit Price
                  </th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                    Subtotal
                  </th>
                </tr>
              </thead>

              <tbody class="bg-white divide-y divide-gray-200">
                <tr v-for="item in sale.items" :key="item.ProductName">
                  <td class="px-6 py-4 whitespace-nowrap">
                    {{ item.ProductName }}
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    {{ item.Quantity }}
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    {{ formatted(item.Price) }}
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    {{ formatted(item.Price * item.Quantity) }}
                  </td>
                </tr>
              </tbody>
            </table>
          </div>

          <!-- Extra Info -->
          <div class="mt-8">
            <h2 class="text-lg font-semibold mb-4">Transaction Details</h2>
            <div class="space-y-2">
              <p><span class="font-medium">Transaction ID:</span> {{ sale.SaleID }}</p>
              <p><span class="font-medium">Processed By:</span> {{ sale.clerk.name }}</p>
              <p><span class="font-medium">Created At:</span> {{ sale.SaleDate }}</p>
              <p><span class="font-medium">Last Updated:</span> {{ sale.SaleDate }}</p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </Layout>
</template>

<style scoped>
.no-print {
  display: flex;
}
</style>

<!-- PRINT CSS (MUST NOT BE SCOPED) -->
<style>
@media print {
  body * {
    visibility: hidden;
  }

  .p-10,
  .p-10 * {
    visibility: visible;
  }

  .p-10 {
    position: absolute;
    left: 0;
    top: 0;
    width: 100%;
  }

  button,
  a,
  .no-print {
    display: none !important;
  }
}
</style>
