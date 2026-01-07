<template>
  <Layout>
    <div class="p-10">
      <div class="bg-white rounded-xl shadow-md p-6">
        <!-- HEADER -->
        <div class="flex justify-between items-center mb-6">
          <h1 class="text-2xl font-bold text-gray-800">
            {{ product.ProductName }}
          </h1>

          <div class="flex space-x-3">
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

        <!-- LOADING -->
        <div v-if="loading" class="text-center py-10 text-gray-500">Loading product details...</div>

        <!-- CONTENT -->
        <div v-else class="grid grid-cols-1 md:grid-cols-3 gap-6">
          <!-- LEFT COLUMN -->
          <div>
            <div class="bg-gray-50 rounded-lg p-4">
              <img
                v-if="product.Product_Image"
                :src="imageUrl(product.Product_Image)"
                :alt="product.ProductName"
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
                  <p>{{ product.SKU || 'N/A' }}</p>
                </div>

                <div>
                  <h3 class="text-sm font-medium text-gray-500">Category</h3>
                  <p>{{ product.category?.CategoryName || 'N/A' }}</p>
                </div>

                <div>
                  <h3 class="text-sm font-medium text-gray-500">Brand</h3>
                  <p>{{ product.Brand || 'N/A' }}</p>
                </div>
              </div>
            </div>
          </div>

          <!-- MIDDLE COLUMN -->
          <div>
            <div class="bg-gray-50 rounded-lg p-4">
              <h2 class="text-lg font-semibold mb-4">Product Details</h2>

              <div class="space-y-4">
                <div>
                  <h3 class="text-sm font-medium text-gray-500">Description</h3>
                  <p>{{ product.Description || 'No description available' }}</p>
                </div>

                <div>
                  <h3 class="text-sm font-medium text-gray-500">Unit</h3>
                  <p>{{ product.Unit }}</p>
                </div>

                <div>
                  <h3 class="text-sm font-medium text-gray-500">Weight</h3>
                  <p v-if="product.Weight">{{ product.Weight }} {{ product.WeightUnit }}</p>
                  <p v-else>N/A</p>
                </div>

                <div>
                  <h3 class="text-sm font-medium text-gray-500">Returnable</h3>
                  <p>{{ product.IsReturnable ? 'Yes' : 'No' }}</p>
                </div>
              </div>
            </div>
          </div>

          <!-- RIGHT COLUMN -->
          <div>
            <div class="bg-gray-50 rounded-lg p-4">
              <h2 class="text-lg font-semibold mb-4">Pricing & Inventory</h2>

              <div class="space-y-4">
                <div>
                  <h3 class="text-sm font-medium text-gray-500">Selling Price</h3>
                  <p class="text-lg font-semibold text-green-600">
                    ₱{{ formatPrice(product.SellingPrice) }}
                  </p>
                </div>

                <div>
                  <h3 class="text-sm font-medium text-gray-500">Cost Price</h3>
                  <p>₱{{ formatPrice(product.CostPrice) }}</p>
                </div>

                <div>
                  <h3 class="text-sm font-medium text-gray-500">Current Stock</h3>
                  <p>{{ product.inventory?.QuantityOnHand ?? 0 }} units</p>

                  <span
                    v-if="
                      product.inventory &&
                      product.inventory.QuantityOnHand <= product.inventory.ReorderLevel
                    "
                    class="text-red-500 text-sm"
                  >
                    Low Stock
                  </span>
                </div>

                <div>
                  <h3 class="text-sm font-medium text-gray-500">Reorder Level</h3>
                  <p>{{ product.inventory?.ReorderLevel ?? 0 }}</p>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- 🔹 SPECIFICATIONS (NEW, SAFE ADDITION) -->
        <div class="mt-6">
          <div class="bg-gray-50 rounded-lg p-4">
            <h2 class="text-lg font-semibold mb-4">Specifications</h2>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
              <div>
                <h3 class="text-sm font-medium text-gray-500">Material</h3>
                <p>{{ product.Material || 'N/A' }}</p>
              </div>

              <div>
                <h3 class="text-sm font-medium text-gray-500">Profile Type</h3>
                <p>{{ product.ProfileType || 'N/A' }}</p>
              </div>

              <div>
                <h3 class="text-sm font-medium text-gray-500">Color</h3>
                <p>{{ product.Color || 'N/A' }}</p>
              </div>

              <div>
                <h3 class="text-sm font-medium text-gray-500">Length</h3>
                <p>
                  {{ product.Length ? `${product.Length} ${product.LengthUnit || ''}` : 'N/A' }}
                </p>
              </div>

              <div>
                <h3 class="text-sm font-medium text-gray-500">Width</h3>
                <p>
                  {{ product.Width ? `${product.Width} ${product.WidthUnit || ''}` : 'N/A' }}
                </p>
              </div>

              <div>
                <h3 class="text-sm font-medium text-gray-500">Thickness</h3>
                <p>{{ product.Thickness || 'N/A' }}</p>
              </div>
            </div>
          </div>
        </div>

        <!-- SUPPLIER INFORMATION -->
        <div class="mt-6">
          <div class="bg-gray-50 rounded-lg p-4">
            <h2 class="text-lg font-semibold mb-4">Supplier Information</h2>

            <div
              v-if="product.suppliers?.length"
              class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4"
            >
              <div
                v-for="s in product.suppliers"
                :key="s.SupplierID"
                class="bg-white p-4 rounded-lg shadow"
              >
                <h3 class="font-medium">{{ s.SupplierName }}</h3>

                <div class="mt-2 text-sm text-gray-600 space-y-1">
                  <p v-if="s.ContactNumber">Phone: {{ s.ContactNumber }}</p>
                  <p v-if="s.Email">Email: {{ s.Email }}</p>
                  <p v-if="s.Address">Address: {{ s.Address }}</p>
                </div>
              </div>
            </div>

            <p v-else class="text-gray-500">No suppliers assigned to this product.</p>
          </div>
        </div>
      </div>
    </div>
  </Layout>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useRoute } from 'vue-router'
import api from '@/api/axios'
import Layout from '@/components/Layout.vue'

const route = useRoute()
const product = ref({})
const loading = ref(true)

function formatPrice(val) {
  return Number(val || 0).toLocaleString(undefined, {
    minimumFractionDigits: 2,
    maximumFractionDigits: 2,
  })
}

function imageUrl(path) {
  return `http://localhost:8000/storage/${path}`
}

async function loadProduct() {
  try {
    loading.value = true
    const id = route.params.id
    const res = await api.get(`/api/products/${id}`)
    product.value = res.data
  } catch (error) {
    console.error('Failed to load product:', error)
  } finally {
    loading.value = false
  }
}

onMounted(loadProduct)
</script>

<style scoped></style>
