<template>
  <div class="p-10">
    <div class="bg-white rounded-xl shadow-md p-6">
      <!-- HEADER -->
      <div class="flex justify-between items-center mb-6">
        <div class="flex items-center space-x-4">
          <RouterLink to="/products" class="text-gray-600 hover:text-gray-900">
            <i class="fa-solid fa-arrow-left"></i> Back to Products
          </RouterLink>

          <h1 class="text-2xl font-bold text-gray-800">
            {{ product?.ProductName }}
          </h1>
        </div>

        <div class="flex space-x-3">
          <RouterLink
            :to="`/products/${id}/edit`"
            class="bg-indigo-600 text-white px-4 py-2 rounded-lg hover:bg-indigo-700 transition-colors"
          >
            Edit Product
          </RouterLink>

          <button
            @click="deleteProduct"
            class="bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700 transition-colors"
          >
            Delete Product
          </button>
        </div>
      </div>

      <!-- GRID CONTENT -->
      <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- LEFT COLUMN -->
        <div class="md:col-span-1">
          <div class="bg-gray-50 rounded-lg p-4">
            <img
              v-if="product?.Product_Image"
              :src="imageUrl(product.Product_Image)"
              class="w-full h-64 object-cover rounded-lg mb-4"
            />

            <div
              v-else
              class="w-full h-64 bg-gray-200 rounded-lg flex items-center justify-center mb-4"
            >
              <span class="text-gray-500">No image available</span>
            </div>

            <div class="space-y-4">
              <div>
                <h3 class="text-sm font-medium text-gray-500">SKU</h3>
                <p class="mt-1">{{ product?.SKU ?? 'N/A' }}</p>
              </div>

              <div>
                <h3 class="text-sm font-medium text-gray-500">Category</h3>
                <p class="mt-1">{{ product?.category?.CategoryName }}</p>
              </div>

              <div>
                <h3 class="text-sm font-medium text-gray-500">Brand</h3>
                <p class="mt-1">{{ product?.Brand ?? 'N/A' }}</p>
              </div>
            </div>
          </div>
        </div>

        <!-- MIDDLE COLUMN -->
        <div class="md:col-span-1">
          <div class="bg-gray-50 rounded-lg p-4">
            <h2 class="text-lg font-semibold mb-4">Product Details</h2>

            <div class="space-y-4">
              <div>
                <h3 class="text-sm font-medium text-gray-500">Description</h3>
                <p class="mt-1">
                  {{ product?.Description ?? 'No description available' }}
                </p>
              </div>

              <div>
                <h3 class="text-sm font-medium text-gray-500">Unit</h3>
                <p class="mt-1">{{ product?.Unit }}</p>
              </div>

              <div>
                <h3 class="text-sm font-medium text-gray-500">Weight</h3>
                <p class="mt-1">
                  <span v-if="product?.Weight">
                    {{ product.Weight }} {{ product.WeightUnit }}
                  </span>
                  <span v-else>N/A</span>
                </p>
              </div>

              <div>
                <h3 class="text-sm font-medium text-gray-500">Returnable</h3>
                <p class="mt-1">
                  {{ product?.IsReturnable ? 'Yes' : 'No' }}
                </p>
              </div>
            </div>
          </div>
        </div>

        <!-- RIGHT COLUMN -->
        <div class="md:col-span-1">
          <div class="bg-gray-50 rounded-lg p-4">
            <h2 class="text-lg font-semibold mb-4">Pricing & Inventory</h2>

            <div class="space-y-4">
              <div>
                <h3 class="text-sm font-medium text-gray-500">Selling Price</h3>
                <p class="mt-1 text-lg font-semibold text-green-600">
                  ₱{{ format(product?.SellingPrice) }}
                </p>
              </div>

              <div>
                <h3 class="text-sm font-medium text-gray-500">Cost Price</h3>
                <p class="mt-1">₱{{ format(product?.CostPrice) }}</p>
              </div>

              <div>
                <h3 class="text-sm font-medium text-gray-500">Current Stock</h3>
                <p class="mt-1">{{ product?.inventory?.QuantityOnHand ?? 0 }} units</p>

                <span
                  v-if="
                    (product?.inventory?.QuantityOnHand ?? 0) <=
                    (product?.OpeningStock ?? 0) / 2 - 1
                  "
                  class="text-red-500 text-sm"
                >
                  Low Stock
                </span>
              </div>

              <div>
                <h3 class="text-sm font-medium text-gray-500">Opening Stock</h3>
                <p class="mt-1">{{ product?.OpeningStock ?? 0 }} units</p>
              </div>

              <div>
                <h3 class="text-sm font-medium text-gray-500">Reorder Level</h3>
                <p class="mt-1">{{ product?.ReorderLevel ?? 'Not set' }}</p>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- SUPPLIER INFORMATION -->
      <div class="mt-6">
        <div class="bg-gray-50 rounded-lg p-4">
          <h2 class="text-lg font-semibold mb-4">Supplier Information</h2>

          <div
            v-if="product?.suppliers?.length > 0"
            class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4"
          >
            <div
              v-for="supplier in product.suppliers"
              :key="supplier.SupplierID"
              class="bg-white p-4 rounded-lg shadow"
            >
              <h3 class="font-medium">{{ supplier.SupplierName }}</h3>

              <div class="mt-2 space-y-1 text-sm text-gray-600">
                <p v-if="supplier.ContactNumber">Phone: {{ supplier.ContactNumber }}</p>
                <p v-if="supplier.Email">Email: {{ supplier.Email }}</p>
                <p v-if="supplier.Address">Address: {{ supplier.Address }}</p>
              </div>
            </div>
          </div>

          <p v-else class="text-gray-500">No suppliers assigned to this product.</p>
        </div>
      </div>

      <!-- RECENT TRANSACTIONS -->
      <div class="mt-6">
        <div class="bg-gray-50 rounded-lg p-4">
          <h2 class="text-lg font-semibold mb-4">Recent Transactions</h2>

          <div v-if="product?.transactions?.length > 0" class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
              <thead class="bg-white">
                <tr>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                    Date
                  </th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                    Type
                  </th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                    Quantity
                  </th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                    Unit Price
                  </th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                    Total
                  </th>
                </tr>
              </thead>

              <tbody class="bg-white divide-y divide-gray-200">
                <tr v-for="t in product.transactions.slice(0, 5)" :key="t.TransactionID">
                  <td class="px-6 py-4 text-sm text-gray-900">
                    {{ formatDate(t.TransactionDate) }}
                  </td>

                  <td class="px-6 py-4 text-sm text-gray-900">
                    {{ t.TransactionType }}
                  </td>

                  <td class="px-6 py-4 text-sm text-gray-900">
                    {{ t.QuantityChange }}
                  </td>

                  <td class="px-6 py-4 text-sm text-gray-900">₱{{ format(t.UnitPrice) }}</td>

                  <td class="px-6 py-4 text-sm text-gray-900">₱{{ format(t.TotalAmount) }}</td>
                </tr>
              </tbody>
            </table>
          </div>

          <p v-else class="text-gray-500">No recent transactions found.</p>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import api from '@/api/axios' // ⭐ use axios instance (with credentials!)

const route = useRoute()
const router = useRouter()

const id = route.params.id
const product = ref(null)

// LOAD PRODUCT
onMounted(async () => {
  const res = await api.get(`/products/${id}`)
  product.value = res.data.product
})

const imageUrl = (img) => `http://localhost:8000/storage/${img}`

const format = (num) =>
  num ? Number(num).toLocaleString(undefined, { minimumFractionDigits: 2 }) : '0.00'

const formatDate = (dateStr) =>
  new Date(dateStr).toLocaleString('en-US', {
    month: 'short',
    day: 'numeric',
    year: 'numeric',
    hour: '2-digit',
    minute: '2-digit',
  })

// DELETE PRODUCT
async function deleteProduct() {
  if (!confirm('Are you sure you want to delete this product?')) return

  await api.delete(`/products/${id}`)

  router.push('/products')
}
</script>

<style scoped></style>
