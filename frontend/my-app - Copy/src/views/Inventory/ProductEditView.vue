<template>
  <Layout>
    <div class="p-10">
      <div class="bg-white rounded-xl shadow-md p-6 max-w-[1800px] mx-auto">
        <!-- HEADER -->
        <div class="flex justify-between items-center mb-6">
          <h1 class="text-2xl font-bold text-gray-800">Edit Product</h1>

          <RouterLink
            to="/products"
            class="bg-gray-300 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-400"
          >
            Back to List
          </RouterLink>
        </div>

        <!-- SUCCESS & ERROR MESSAGE -->
        <div v-if="success" class="alert-success">{{ success }}</div>
        <div v-if="error" class="alert-error">{{ error }}</div>

        <!-- FORM -->
        <form @submit.prevent="updateProduct" class="space-y-12">
          <!-- BASIC INFORMATION -->
          <div>
            <h2 class="section-title">Basic Information</h2>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
              <div class="space-y-4">
                <div>
                  <label>Product Name*</label>
                  <input v-model="form.ProductName" class="input" required />
                </div>

                <div>
                  <label>SKU</label>
                  <input :value="form.SKU" class="input bg-gray-100" disabled />
                </div>

                <div>
                  <label>Unit*</label>
                  <select v-model="form.Unit" class="input" required>
                    <option value="">Select Unit</option>
                    <option v-for="u in units" :key="u" :value="u">
                      {{ u }}
                    </option>
                  </select>
                </div>

                <div class="flex items-center gap-2">
                  <input type="checkbox" v-model="form.IsReturnable" />
                  <label>Returnable Item</label>
                </div>
              </div>

              <!-- IMAGE -->
              <div>
                <label>Product Image</label>

                <div class="flex items-center gap-4 mt-2">
                  <img
                    v-if="existingImage && !previewImage"
                    :src="existingImage"
                    class="preview-img"
                  />
                  <img v-if="previewImage" :src="previewImage" class="preview-img" />

                  <div v-if="!existingImage && !previewImage" class="text-gray-500">
                    No image available
                  </div>

                  <input
                    type="file"
                    accept="image/*"
                    @change="handleImageUpload"
                    class="file-input"
                  />
                </div>
              </div>
            </div>
          </div>

          <!-- CLASSIFICATION -->
          <div>
            <h2 class="section-title">Classification</h2>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
              <div>
                <label>Category*</label>
                <select v-model="form.CategoryID" class="input" required>
                  <option value="">Select Category</option>
                  <option v-for="c in categories" :key="c.CategoryID" :value="c.CategoryID">
                    {{ c.CategoryName }}
                  </option>
                </select>
              </div>

              <div>
                <label>Supplier</label>
                <select v-model="form.SupplierID" class="input">
                  <option value="">Select Supplier</option>
                  <option v-for="s in suppliers" :key="s.SupplierID" :value="s.SupplierID">
                    {{ s.SupplierName }}
                  </option>
                </select>
              </div>
            </div>
          </div>

          <!-- DETAILS -->
          <div>
            <h2 class="section-title">Product Details</h2>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
              <div>
                <label>Brand</label>
                <input v-model="form.Brand" class="input" />
              </div>

              <div>
                <label>Description</label>
                <textarea v-model="form.Description" class="input"></textarea>
              </div>
            </div>
          </div>

          <!-- SPECIFICATIONS -->
          <div>
            <h2 class="section-title">Specifications</h2>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
              <div>
                <label>Material</label>
                <input v-model="form.Material" class="input" />
              </div>

              <div>
                <label>Profile / Type</label>
                <input v-model="form.ProfileType" class="input" />
              </div>

              <div>
                <label>Color</label>
                <input v-model="form.Color" class="input" />
              </div>

              <!-- LENGTH -->
              <div>
                <label>Length</label>
                <div class="flex gap-2">
                  <input
                    v-model.number="form.Length"
                    type="number"
                    step="0.01"
                    class="input w-2/3"
                  />
                  <select v-model="form.LengthUnit" class="input w-1/3">
                    <option value="ft">ft</option>
                    <option value="m">m</option>
                    <option value="cm">cm</option>
                    <option value="in">in</option>
                  </select>
                </div>
              </div>

              <!-- WIDTH -->
              <div>
                <label>Width</label>
                <div class="flex gap-2">
                  <input
                    v-model.number="form.Width"
                    type="number"
                    step="0.01"
                    class="input w-2/3"
                  />
                  <select v-model="form.WidthUnit" class="input w-1/3">
                    <option value="ft">ft</option>
                    <option value="m">m</option>
                    <option value="cm">cm</option>
                    <option value="in">in</option>
                  </select>
                </div>
              </div>

              <!-- THICKNESS (FIXED) -->
              <div>
                <label class="block">
                  Thickness / Gauge
                  <span class="text-xs text-gray-500 ml-1">(mm / gauge)</span>
                </label>

                <input
                  v-model.number="form.Thickness"
                  type="number"
                  step="0.01"
                  min="0"
                  class="input"
                />

                <p class="text-xs text-gray-500 mt-1">
                  Example: <strong>0.40</strong> (mm) or <strong>26</strong> (gauge)
                </p>
              </div>
            </div>

            <div class="mt-4">
              <label>Weight</label>
              <div class="flex gap-2">
                <input v-model.number="form.Weight" type="number" step="0.01" class="input w-28" />
                <select v-model="form.WeightUnit" class="input w-28">
                  <option value="g">g</option>
                  <option value="kg">kg</option>
                  <option value="mL">mL</option>
                  <option value="L">L</option>
                </select>
              </div>
            </div>
          </div>

          <!-- PRICING -->
          <div>
            <h2 class="section-title">Pricing</h2>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
              <div>
                <label>Selling Price*</label>
                <input v-model.number="form.SellingPrice" type="number" step="0.01" class="input" />
              </div>

              <div>
                <label>Cost Price*</label>
                <input v-model.number="form.CostPrice" type="number" step="0.01" class="input" />
              </div>
            </div>
          </div>

          <!-- INVENTORY -->
          <div>
            <h2 class="section-title">Inventory</h2>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
              <div>
                <label>Stock Adjustment</label>
                <input v-model.number="form.stock_adjustment" type="number" class="input" />
                <p class="text-sm text-gray-500">
                  Current stock: {{ product?.inventory?.QuantityOnHand }}
                </p>
              </div>

              <div>
                <label>Reorder Level</label>
                <input v-model.number="form.ReorderLevel" type="number" class="input" />
              </div>
            </div>
          </div>

          <!-- BUTTONS -->
          <div class="flex justify-end gap-4 pt-6">
            <RouterLink to="/products" class="btn-secondary">Cancel</RouterLink>
            <button type="submit" class="btn-primary">
              {{ loading ? 'Saving...' : 'Save Changes' }}
            </button>
          </div>
        </form>
      </div>
    </div>
  </Layout>
</template>

<script setup>
import Layout from '@/components/Layout.vue'
import api from '@/api/axios'
import { ref, onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'

const route = useRoute()
const router = useRouter()
const productId = route.params.id

const product = ref(null)
const categories = ref([])
const suppliers = ref([])

const form = ref({
  ProductName: '',
  SKU: '',
  Unit: '',
  IsReturnable: false,
  CategoryID: '',
  SupplierID: '',
  Brand: '',
  Description: '',
  Material: '',
  ProfileType: '',
  Color: '',
  Length: null,
  LengthUnit: 'ft',
  Width: null,
  WidthUnit: 'ft',
  Thickness: null,
  Weight: 0,
  WeightUnit: 'g',
  SellingPrice: 0,
  CostPrice: 0,
  stock_adjustment: 0,
  ReorderLevel: 0,
})

const units = ['Piece', 'Box', 'Pack', 'Set', 'Roll', 'Kg', 'g', 'L', 'mL']

const success = ref('')
const error = ref('')
const loading = ref(false)

const existingImage = ref(null)
const newImageFile = ref(null)
const previewImage = ref(null)

function handleImageUpload(e) {
  newImageFile.value = e.target.files[0]
  previewImage.value = URL.createObjectURL(newImageFile.value)
}

/* LOAD PRODUCT DATA */
async function loadData() {
  try {
    const p = await api.get(`/api/products/${productId}`)
    const cats = await api.get('/api/categories')
    const sups = await api.get('/api/suppliers')

    product.value = p.data
    categories.value = cats.data.categories
    suppliers.value = sups.data.suppliers

    Object.assign(form.value, {
      ProductName: p.data.ProductName,
      SKU: p.data.SKU,
      Unit: p.data.Unit,
      IsReturnable: p.data.IsReturnable,

      // ✅ FIXED
      CategoryID: p.data.category?.CategoryID ?? '',
      SupplierID: p.data.suppliers?.[0]?.SupplierID ?? '',

      // ✅ FIXED
      Brand: p.data.Brand ?? '',
      Description: p.data.Description ?? '',

      Material: p.data.Material,
      ProfileType: p.data.ProfileType,
      Color: p.data.Color,

      Length: p.data.Length,
      LengthUnit: p.data.LengthUnit || 'ft',
      Width: p.data.Width,
      WidthUnit: p.data.WidthUnit || 'ft',
      Thickness: p.data.Thickness,

      Weight: p.data.Weight,
      WeightUnit: p.data.WeightUnit,

      SellingPrice: p.data.SellingPrice,
      CostPrice: p.data.CostPrice,

      stock_adjustment: 0,
      ReorderLevel: p.data.inventory?.ReorderLevel ?? 0,
    })

    existingImage.value = p.data.ImageURL
  } catch {
    error.value = 'Failed to load product data.'
  }
}

/* UPDATE PRODUCT */
async function updateProduct() {
  loading.value = true
  success.value = ''
  error.value = ''

  try {
    const fd = new FormData()

    fd.append('IsReturnable', form.value.IsReturnable ? 1 : 0)

    for (const key in form.value) {
      if (key === 'IsReturnable' || key === 'SKU') continue
      fd.append(key, form.value[key] ?? '')
    }

    if (newImageFile.value) {
      fd.append('Product_Image', newImageFile.value)
    }

    fd.append('_method', 'PUT')

    await api.post(`/api/products/${productId}`, fd)

    success.value = 'Product updated successfully!'
    setTimeout(() => router.push('/products'), 800)
  } catch {
    error.value = 'Update failed. Check inputs.'
  }

  loading.value = false
}

onMounted(loadData)
</script>

<style scoped>
.input {
  @apply w-full border rounded-lg px-3 py-2;
}
.preview-img {
  @apply h-32 w-32 object-cover rounded-lg border;
}
.btn-primary {
  @apply bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700;
}
.btn-secondary {
  @apply bg-gray-300 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-400;
}
.section-title {
  @apply text-xl font-semibold mb-4;
}
.alert-success {
  @apply bg-green-100 text-green-700 px-4 py-3 rounded-lg;
}
.alert-error {
  @apply bg-red-100 text-red-700 px-4 py-3 rounded-lg;
}
.file-input {
  @apply text-sm text-gray-600 file:bg-blue-50 file:text-blue-700 file:px-4 file:py-2 file:rounded file:border-0 hover:file:bg-blue-100;
}
</style>
