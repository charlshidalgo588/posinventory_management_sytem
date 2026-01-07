<template>
  <Layout :title="'Edit Supplier'">
    <div class="p-10">
      <div class="bg-white rounded-xl shadow-md p-6">
        <!-- SUCCESS -->
        <div v-if="success" class="bg-green-100 text-green-700 px-4 py-3 rounded-lg mb-4">
          {{ success }}
        </div>

        <!-- ERROR -->
        <div v-if="error" class="bg-red-100 text-red-700 px-4 py-3 rounded-lg mb-4">
          {{ error }}
        </div>

        <form @submit.prevent="updateSupplier" class="space-y-8">
          <h1 class="text-2xl font-bold mb-6">Edit Supplier</h1>

          <!-- Supplier Information -->
          <div class="relative pb-6">
            <h2 class="text-xl font-semibold mb-4 text-gray-800">Supplier Information</h2>

            <div class="max-w-2xl mx-auto space-y-4">
              <!-- Name -->
              <div>
                <label class="block text-sm font-medium mb-2"> Supplier Name* </label>
                <input
                  v-model="form.SupplierName"
                  type="text"
                  class="w-full border border-gray-300 rounded-lg px-3 py-2"
                  required
                />
              </div>

              <!-- Contact -->
              <div>
                <label class="block text-sm font-medium mb-2"> Contact Number </label>
                <input
                  v-model="form.ContactNumber"
                  type="text"
                  class="w-full border border-gray-300 rounded-lg px-3 py-2"
                />
              </div>

              <!-- Email -->
              <div>
                <label class="block text-sm font-medium mb-2">Email</label>
                <input
                  v-model="form.Email"
                  type="email"
                  class="w-full border border-gray-300 rounded-lg px-3 py-2"
                />
              </div>

              <!-- Address -->
              <div>
                <label class="block text-sm font-medium mb-2">Address</label>
                <textarea
                  v-model="form.Address"
                  rows="3"
                  class="w-full border border-gray-300 rounded-lg px-3 py-2"
                ></textarea>
              </div>
            </div>
          </div>

          <!-- Buttons -->
          <div class="flex justify-end space-x-4">
            <RouterLink
              to="/suppliers"
              class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50"
            >
              Cancel
            </RouterLink>

            <button
              type="submit"
              class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700"
            >
              Update Supplier
            </button>
          </div>
        </form>
      </div>
    </div>
  </Layout>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import Layout from '@/components/Layout.vue'
import api from '@/api/axios' // ✅ Use your axios instance

const route = useRoute()
const router = useRouter()
const supplierId = route.params.id

/* FORM MODEL */
const form = ref({
  SupplierName: '',
  ContactNumber: '',
  Email: '',
  Address: '',
})

const success = ref('')
const error = ref('')

/* LOAD SUPPLIER DATA */
onMounted(async () => {
  try {
    const res = await api.get(`/api/suppliers/${supplierId}`) // ✅ Correct route
    form.value = res.data
  } catch (err) {
    console.error('Load supplier error:', err)
    error.value = 'Failed to load supplier data.'
  }
})

/* UPDATE SUPPLIER */
const updateSupplier = async () => {
  success.value = ''
  error.value = ''

  try {
    await api.post(`/api/suppliers/${supplierId}`, {
      ...form.value,
      _method: 'PUT', // Laravel method spoofing
    })

    success.value = 'Supplier updated successfully!'
    setTimeout(() => router.push('/suppliers'), 800)
  } catch (err) {
    console.error('Update supplier error:', err)
    error.value = 'Failed to update supplier.'
  }
}
</script>

<style scoped></style>
