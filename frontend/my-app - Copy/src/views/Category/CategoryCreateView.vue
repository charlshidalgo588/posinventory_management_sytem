<template>
  <Layout title="Add New Category">
    <div class="p-10">
      <div class="bg-white rounded-2xl shadow-md p-8 max-w-5xl mx-auto">
        <!-- PAGE TITLE -->
        <h1 class="text-2xl font-semibold mb-6">Add new category</h1>

        <!-- SUCCESS MESSAGE -->
        <div
          v-if="successMessage"
          class="mb-4 bg-green-100 text-green-700 px-4 py-3 rounded-lg shadow"
        >
          {{ successMessage }}
        </div>

        <!-- ERROR MESSAGE -->
        <div v-if="errorMessage" class="mb-4 bg-red-100 text-red-700 px-4 py-3 rounded-lg shadow">
          {{ errorMessage }}
        </div>

        <!-- SECTION TITLE -->
        <h2 class="text-lg font-semibold text-gray-700 mb-6">Category Information</h2>

        <!-- FORM -->
        <div class="space-y-6">
          <div>
            <label class="block text-sm font-medium mb-2">Category Name*</label>
            <input
              type="text"
              v-model="categoryName"
              placeholder="Enter category name"
              class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring focus:ring-blue-200 outline-none"
            />
          </div>
        </div>

        <!-- ACTION BUTTONS -->
        <div class="flex justify-end mt-10 space-x-4">
          <RouterLink
            to="/categories"
            class="px-5 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition"
          >
            Cancel
          </RouterLink>

          <button
            @click="saveCategory"
            class="px-5 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition"
          >
            Save
          </button>
        </div>
      </div>
    </div>
  </Layout>
</template>

<script setup>
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import Layout from '@/components/Layout.vue'
import api from '@/api/axios' // Axios instance

const router = useRouter()

// Form state
const categoryName = ref('')
const successMessage = ref('')
const errorMessage = ref('')

/* -------------------------------------------------------------
   SAVE CATEGORY (POST to Laravel API)
------------------------------------------------------------- */
async function saveCategory() {
  successMessage.value = ''
  errorMessage.value = ''

  if (!categoryName.value.trim()) {
    errorMessage.value = 'Category name is required.'
    return
  }

  try {
    await api.post('/api/categories', {
      CategoryName: categoryName.value,
    })

    successMessage.value = 'Category added successfully!'

    // Redirect after short delay
    setTimeout(() => router.push('/categories'), 700)
  } catch (err) {
    console.error('Save category error:', err)

    errorMessage.value =
      err.response?.data?.errors?.CategoryName?.[0] ||
      err.response?.data?.message ||
      'Failed to add category.'
  }
}
</script>

<style scoped></style>
