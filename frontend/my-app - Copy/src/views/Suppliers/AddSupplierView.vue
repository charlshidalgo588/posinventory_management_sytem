<template>
  <Layout :title="'Add New Supplier'">
    <div class="p-10">
      <div class="bg-white rounded-xl shadow-md p-6">
        <!-- SUCCESS MESSAGE -->
        <div v-if="success" class="bg-green-100 text-green-700 px-4 py-3 rounded-lg mb-4">
          {{ success }}
        </div>

        <!-- ERROR MESSAGE -->
        <div v-if="error" class="bg-red-100 text-red-700 px-4 py-3 rounded-lg mb-4">
          {{ error }}
        </div>

        <!-- FORM -->
        <form @submit.prevent="saveSupplier" class="space-y-8">
          <h1 class="text-2xl font-bold mb-6">Add new supplier</h1>

          <!-- Supplier Information Section -->
          <div class="relative pb-6">
            <h2 class="text-xl font-semibold mb-4 text-gray-800">Supplier Information</h2>

            <div class="max-w-2xl mx-auto space-y-4">
              <!-- Supplier Name -->
              <div>
                <label class="block text-sm font-medium mb-2">Supplier Name*</label>
                <input
                  v-model="form.SupplierName"
                  type="text"
                  class="w-full border border-gray-300 rounded-lg px-3 py-2"
                  required
                />
              </div>

              <!-- Contact Information -->
              <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                  <label class="block text-sm font-medium mb-2">Contact Number</label>
                  <input
                    v-model="form.ContactNumber"
                    type="tel"
                    class="w-full border border-gray-300 rounded-lg px-3 py-2"
                  />
                </div>

                <div>
                  <label class="block text-sm font-medium mb-2">Email</label>
                  <input
                    v-model="form.Email"
                    type="email"
                    class="w-full border border-gray-300 rounded-lg px-3 py-2"
                  />
                </div>
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

            <div
              class="absolute bottom-0 left-0 right-0 h-px bg-gradient-to-r from-transparent via-gray-200 to-transparent"
            ></div>
          </div>

          <!-- BUTTONS -->
          <div class="flex justify-end space-x-4 mt-6">
            <RouterLink
              to="/suppliers"
              class="bg-gray-300 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-400"
            >
              Cancel
            </RouterLink>

            <button
              type="submit"
              class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700"
            >
              Save
            </button>
          </div>
        </form>
      </div>
    </div>
  </Layout>
</template>

<script setup>
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import Layout from '@/components/Layout.vue'
import api from '@/api/axios' // ✅ use your axios instance

const router = useRouter()

const form = ref({
  SupplierName: '',
  ContactNumber: '',
  Email: '',
  Address: '',
})

const success = ref('')
const error = ref('')

/* SAVE SUPPLIER (connected to Laravel API) */
const saveSupplier = async () => {
  success.value = ''
  error.value = ''

  try {
    await api.post('/api/suppliers', form.value) // ✅ Correct backend route

    success.value = 'Supplier added successfully!'
    setTimeout(() => router.push('/suppliers'), 800)
  } catch (err) {
    console.error('Save supplier error:', err)
    error.value = 'Failed to save supplier. Please check your input.'
  }
}
</script>
