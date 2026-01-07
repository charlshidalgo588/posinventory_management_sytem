<template>
  <Layout title="Edit Category">
    <div class="p-10">
      <div class="bg-white rounded-2xl shadow-md p-8 max-w-5xl mx-auto relative">
        <!-- BACK BUTTON -->
        <RouterLink
          to="/categories"
          class="absolute right-6 top-6 px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition"
        >
          Back to List
        </RouterLink>

        <!-- PAGE TITLE -->
        <h1 class="text-2xl font-semibold mb-6">Edit Category</h1>

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
            @click="updateCategory"
            class="px-5 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition"
          >
            Save Changes
          </button>
        </div>
      </div>
    </div>
  </Layout>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import api from '@/api/axios'
import Layout from '@/components/Layout.vue'

const route = useRoute()
const router = useRouter()

const categoryId = route.params.id

const categoryName = ref('')
const errorMessage = ref('')
const successMessage = ref('')

/* -------------------------------------------------------------
   LOAD CATEGORY DATA (FIXED)
------------------------------------------------------------- */
onMounted(async () => {
  try {
    const res = await api.get(`/api/categories/${categoryId}`, {
      withCredentials: true,
    })

    categoryName.value = res.data.category.CategoryName
  } catch (err) {
    console.error(err)
    errorMessage.value = 'Failed to load category.'
  }
})

/* -------------------------------------------------------------
   UPDATE CATEGORY
------------------------------------------------------------- */
async function updateCategory() {
  errorMessage.value = ''
  successMessage.value = ''

  if (!categoryName.value.trim()) {
    errorMessage.value = 'Category name is required.'
    return
  }

  try {
    await api.put(
      `/api/categories/${categoryId}`,
      { CategoryName: categoryName.value },
      { withCredentials: true },
    )

    successMessage.value = 'Category updated successfully!'
    setTimeout(() => router.push('/categories'), 800)
  } catch (err) {
    console.error(err)
    errorMessage.value =
      err.response?.data?.message ||
      err.response?.data?.errors?.CategoryName?.[0] ||
      'Failed to update category.'
  }
}
</script>

<style scoped></style>
