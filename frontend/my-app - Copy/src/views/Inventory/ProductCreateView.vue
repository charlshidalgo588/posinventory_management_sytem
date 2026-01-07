<template>
  <Layout>
    <div class="p-6 bg-gray-50 min-h-screen">
      <!-- PAGE TITLE -->
      <h1 class="text-3xl font-extrabold text-gray-800 mb-6">Add new item</h1>

      <!-- ERROR -->
      <div v-if="errorMessage" class="mb-4 bg-red-100 text-red-700 px-4 py-3 rounded-lg shadow">
        {{ errorMessage }}
      </div>

      <!-- SUCCESS -->
      <div
        v-if="successMessage"
        class="mb-4 bg-green-100 text-green-700 px-4 py-3 rounded-lg shadow"
      >
        {{ successMessage }}
      </div>

      <!-- BASIC INFORMATION -->
      <div class="bg-white p-6 rounded-xl shadow mb-10">
        <h2 class="text-xl font-semibold mb-4">Basic Information</h2>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
          <!-- LEFT -->
          <div class="space-y-4">
            <div>
              <label class="block text-sm font-semibold">Product Name*</label>
              <input
                v-model="form.ProductName"
                type="text"
                class="w-full border rounded-lg px-3 py-2"
                required
              />
            </div>

            <div>
              <label class="block text-sm font-semibold">SKU</label>
              <input
                :value="form.SKU"
                disabled
                class="w-full border rounded-lg px-3 py-2 bg-gray-100"
              />
              <p class="text-xs text-gray-500">SKU is auto-generated</p>
            </div>

            <div>
              <label class="block text-sm font-semibold">Unit*</label>
              <select v-model="form.Unit" class="w-full border rounded-lg px-3 py-2" required>
                <option value="">Select Unit</option>
                <option v-for="u in units" :key="u" :value="u">
                  {{ u }}
                </option>
              </select>
            </div>

            <div class="flex items-center gap-2">
              <input type="checkbox" v-model="form.IsReturnable" />
              <label class="text-sm">Returnable Item</label>
            </div>
          </div>

          <!-- IMAGE -->
          <div>
            <label class="block text-sm font-semibold mb-2"> Product Image </label>

            <div
              class="w-full h-48 border-2 border-dashed rounded-xl flex flex-col justify-center items-center text-gray-400 relative"
            >
              <img v-if="preview" :src="preview" class="h-40 object-contain" />

              <div v-else>
                <i class="fa-solid fa-cloud-arrow-up text-4xl mb-2"></i>
                <p class="text-center">
                  Click to upload or drag here<br />
                  PNG / JPG (max 5MB)
                </p>
              </div>

              <input
                type="file"
                class="absolute inset-0 opacity-0 cursor-pointer"
                @change="uploadImage"
              />
            </div>
          </div>
        </div>
      </div>

      <!-- CLASSIFICATION -->
      <div class="bg-white p-6 rounded-xl shadow mb-10">
        <h2 class="text-xl font-semibold mb-4">Classification</h2>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
          <div>
            <label class="block text-sm font-semibold">Category*</label>
            <select v-model="form.CategoryID" class="w-full border rounded-lg px-3 py-2" required>
              <option value="">Select Category</option>
              <option v-for="c in categories" :key="c.CategoryID" :value="c.CategoryID">
                {{ c.CategoryName }}
              </option>
            </select>
          </div>

          <div>
            <label class="block text-sm font-semibold">Supplier</label>
            <select v-model="form.SupplierID" class="w-full border rounded-lg px-3 py-2">
              <option value="">Select Supplier</option>
              <option v-for="s in suppliers" :key="s.SupplierID" :value="s.SupplierID">
                {{ s.SupplierName }}
              </option>
            </select>
          </div>
        </div>
      </div>

      <!-- DETAILS -->
      <div class="bg-white p-6 rounded-xl shadow mb-10">
        <h2 class="text-xl font-semibold mb-4">Product Details</h2>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
          <div>
            <label class="block text-sm font-semibold">Brand</label>
            <input v-model="form.Brand" class="input" />
          </div>

          <div>
            <label class="block text-sm font-semibold">Description</label>
            <textarea v-model="form.Description" class="input"></textarea>
          </div>
        </div>
      </div>

      <!-- SPECIFICATIONS -->
      <div class="bg-white p-6 rounded-xl shadow mb-10">
        <h2 class="text-xl font-semibold mb-4">Specifications</h2>

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

          <!-- LENGTH (WITH UNIT) -->
          <div>
            <label>Length</label>
            <div class="flex gap-2">
              <input v-model="form.Length" type="number" class="input w-2/3" />
              <select v-model="form.LengthUnit" class="input w-1/3">
                <option value="ft">ft</option>
                <option value="m">m</option>
                <option value="cm">cm</option>
                <option value="in">in</option>
              </select>
            </div>
          </div>

          <!-- WIDTH (WITH UNIT) -->
          <div>
            <label>Width</label>
            <div class="flex gap-2">
              <input v-model="form.Width" type="number" class="input w-2/3" />
              <select v-model="form.WidthUnit" class="input w-1/3">
                <option value="ft">ft</option>
                <option value="m">m</option>
                <option value="cm">cm</option>
                <option value="in">in</option>
              </select>
            </div>
          </div>

          <div>
            <label class="block">
              Thickness / Gauge
              <span class="text-xs text-gray-500 ml-1">(mm / gauge)</span>
            </label>

            <input v-model="form.Thickness" type="number" step="0.01" class="input" />

            <p class="text-xs text-gray-500 mt-1">
              Example: <strong>0.40</strong> (mm) or <strong>26</strong> (gauge)
            </p>
          </div>
        </div>

        <div class="mt-4">
          <label>Weight</label>
          <div class="flex gap-2">
            <input v-model="form.Weight" type="number" class="input w-28" />
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
      <div class="bg-white p-6 rounded-xl shadow mb-10">
        <h2 class="text-xl font-semibold mb-4">Pricing</h2>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
          <div>
            <label>Selling Price*</label>
            <input v-model="form.SellingPrice" type="number" class="input" />
          </div>

          <div>
            <label>Cost Price*</label>
            <input v-model="form.CostPrice" type="number" class="input" />
          </div>
        </div>
      </div>

      <!-- INVENTORY -->
      <div class="bg-white p-6 rounded-xl shadow mb-20">
        <h2 class="text-xl font-semibold mb-4">Inventory</h2>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
          <div>
            <label>Opening Stock</label>
            <input v-model="form.OpeningStock" type="number" class="input" />
          </div>

          <div>
            <label>Reorder Level</label>
            <input v-model="form.ReorderLevel" type="number" class="input" />
          </div>
        </div>
      </div>

      <!-- BUTTONS -->
      <div class="flex justify-end gap-3 mb-10">
        <RouterLink to="/products" class="px-6 py-2 bg-gray-200 rounded-lg hover:bg-gray-300">
          Cancel
        </RouterLink>

        <button
          @click="saveProduct"
          class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700"
        >
          Save
        </button>
      </div>
    </div>
  </Layout>
</template>

<script setup>
import Layout from '@/components/Layout.vue'
import api from '@/api/axios'
import { ref, onMounted, watch } from 'vue'
import { useRouter, onBeforeRouteLeave } from 'vue-router'

const router = useRouter()

/* FORM */
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
  SellingPrice: '',
  CostPrice: '',
  OpeningStock: '',
  ReorderLevel: '',
})

/* SNAPSHOT FOR UNSAVED CHECK */
const initialFormSnapshot = ref(JSON.stringify(form.value))

const preview = ref(null)
const imageFile = ref(null)

const units = ['Piece', 'Box', 'Pack', 'Set', 'Roll', 'Kg', 'g', 'L', 'mL']
const categories = ref([])
const suppliers = ref([])

const errorMessage = ref('')
const successMessage = ref('')
const hasSaved = ref(false)

/* SKU AUTO */
watch(
  () => form.value.ProductName,
  async (name) => {
    if (!name || name.length < 2) {
      form.value.SKU = ''
      return
    }

    const res = await api.get(`/api/generate-sku/${encodeURIComponent(name)}`)
    form.value.SKU = res.data.sku
  },
)

/* IMAGE */
function uploadImage(e) {
  const file = e.target.files[0]
  if (!file) return
  imageFile.value = file
  preview.value = URL.createObjectURL(file)
}

/* LOAD DATA */
async function loadCategories() {
  const res = await api.get('/api/categories')
  categories.value = res.data.categories
}

async function loadSuppliers() {
  const res = await api.get('/api/suppliers')
  suppliers.value = res.data.suppliers
}

onMounted(() => {
  loadCategories()
  loadSuppliers()
  initialFormSnapshot.value = JSON.stringify(form.value)
})

/* ROUTE LEAVE CONFIRMATION */
onBeforeRouteLeave(() => {
  if (hasSaved.value) return true

  const current = JSON.stringify(form.value)
  if (current !== initialFormSnapshot.value) {
    return window.confirm('You have unsaved changes. Are you sure you want to leave this page?')
  }

  return true
})

/* SAVE */
async function saveProduct() {
  errorMessage.value = ''

  try {
    const fd = new FormData()

    for (const key in form.value) {
      if (key === 'IsReturnable') {
        fd.append(key, form.value.IsReturnable ? 1 : 0)
      } else {
        fd.append(key, form.value[key] ?? '')
      }
    }

    if (imageFile.value) {
      fd.append('Product_Image', imageFile.value)
    }

    await api.post('/api/products', fd)

    hasSaved.value = true
    successMessage.value = 'Product saved successfully!'
    setTimeout(() => router.push('/products'), 800)
  } catch (err) {
    errorMessage.value = err?.response?.data?.message || 'Failed to save product'
  }
}
</script>

<style scoped>
.input {
  @apply w-full border rounded-lg px-3 py-2;
}
</style>
