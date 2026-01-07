<template>
  <Layout title="Settings">
    <div class="min-h-screen bg-gray-50 p-4 sm:p-8">
      <!-- Breadcrumb and Page Header -->
      <div class="max-w-4xl mx-auto mb-8">
        <nav class="mb-4">
          <ol class="flex items-center space-x-2 text-sm">
            <li>
              <RouterLink
                to="/home"
                class="text-gray-500 hover:text-gray-700 transition-colors duration-200"
              >
                Dashboard
              </RouterLink>
            </li>
            <li class="flex items-center space-x-2">
              <span class="text-gray-400">/</span>
              <span class="text-gray-700 font-medium">Settings</span>
            </li>
          </ol>
        </nav>

        <div class="flex items-center justify-between">
          <div>
            <h1 class="text-3xl font-bold text-gray-900">Account Settings</h1>
            <p class="mt-2 text-sm text-gray-600">Manage your account settings and preferences</p>
          </div>

          <div class="flex items-center space-x-4">
            <span class="text-sm text-gray-500"> Last updated: {{ lastUpdated }} </span>
          </div>
        </div>
      </div>

      <!-- Settings Card -->
      <div class="max-w-4xl mx-auto bg-white rounded-xl shadow-sm border border-gray-200">
        <!-- Success -->
        <div
          v-if="successMessage"
          class="m-6 p-4 text-sm text-green-700 bg-green-50 rounded-lg border border-green-200 flex items-center"
        >
          <i class="fas fa-check-circle text-green-500 mr-3"></i>
          {{ successMessage }}
        </div>

        <!-- Errors -->
        <div
          v-if="errors.length > 0"
          class="m-6 p-4 text-sm text-red-700 bg-red-50 rounded-lg border border-red-200 flex items-start"
        >
          <i class="fas fa-exclamation-circle mt-0.5 mr-3"></i>
          <ul class="space-y-1">
            <li v-for="(err, i) in errors" :key="i">{{ err }}</li>
          </ul>
        </div>

        <!-- Form -->
        <form @submit.prevent="saveChanges" class="divide-y divide-gray-200">
          <!-- Personal Information -->
          <div class="p-6 sm:p-8">
            <div class="flex items-center mb-6">
              <div class="h-8 w-8 rounded-full bg-blue-100 flex items-center justify-center mr-3">
                <i class="fas fa-user text-blue-600"></i>
              </div>
              <h2 class="text-xl font-semibold text-gray-900">Personal Information</h2>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2"> Full Name </label>
                <input
                  type="text"
                  v-model="form.name"
                  class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 px-4 py-2.5"
                  placeholder="Enter your full name"
                />
              </div>

              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2"> Email Address </label>
                <input
                  type="email"
                  v-model="form.email"
                  class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 px-4 py-2.5"
                  placeholder="Enter your email"
                />
              </div>
            </div>
          </div>

          <!-- Password Section -->
          <div class="p-6 sm:p-8">
            <div class="flex items-center mb-6">
              <div class="h-8 w-8 rounded-full bg-blue-100 flex items-center justify-center mr-3">
                <i class="fas fa-lock text-blue-600"></i>
              </div>
              <h2 class="text-xl font-semibold text-gray-900">Security</h2>
            </div>

            <div class="space-y-6">
              <!-- CURRENT PASSWORD -->
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                  Current Password
                </label>
                <div class="relative">
                  <input
                    :type="visibility.current ? 'text' : 'password'"
                    v-model="form.current_password"
                    id="current_password"
                    class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 px-4 py-2.5 pr-10"
                    placeholder="Enter your current password"
                  />
                  <button
                    type="button"
                    class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600"
                    @click="toggleVisibility('current')"
                  >
                    <i
                      :class="visibility.current ? 'fa-regular fa-eye-slash' : 'fa-regular fa-eye'"
                    ></i>
                  </button>
                </div>
              </div>

              <!-- NEW + CONFIRM PASSWORD -->
              <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-2"> New Password </label>
                  <div class="relative">
                    <input
                      :type="visibility.new ? 'text' : 'password'"
                      v-model="form.new_password"
                      id="new_password"
                      class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 px-4 py-2.5 pr-10"
                      placeholder="Enter new password"
                    />
                    <button
                      type="button"
                      class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600"
                      @click="toggleVisibility('new')"
                    >
                      <i
                        :class="visibility.new ? 'fa-regular fa-eye-slash' : 'fa-regular fa-eye'"
                      ></i>
                    </button>
                  </div>
                </div>

                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-2">
                    Confirm New Password
                  </label>
                  <div class="relative">
                    <input
                      :type="visibility.confirm ? 'text' : 'password'"
                      v-model="form.new_password_confirmation"
                      id="new_password_confirmation"
                      class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 px-4 py-2.5 pr-10"
                      placeholder="Confirm new password"
                    />
                    <button
                      type="button"
                      class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600"
                      @click="toggleVisibility('confirm')"
                    >
                      <i
                        :class="
                          visibility.confirm ? 'fa-regular fa-eye-slash' : 'fa-regular fa-eye'
                        "
                      ></i>
                    </button>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Form Actions -->
          <div class="px-6 py-4 sm:px-8 bg-gray-50 rounded-b-xl">
            <div class="flex justify-end space-x-3">
              <RouterLink
                to="/home"
                class="inline-flex justify-center py-2.5 px-5 border border-gray-300 text-sm font-medium rounded-lg text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-200"
              >
                Cancel
              </RouterLink>

              <button
                type="submit"
                class="inline-flex justify-center py-2.5 px-5 border border-transparent text-sm font-medium rounded-lg text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-200"
              >
                Save Changes
              </button>
            </div>
          </div>
        </form>
      </div>
    </div>
  </Layout>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import Layout from '@/components/Layout.vue'
import api from '@/api/axios'

const lastUpdated = ref('')
const successMessage = ref('')
const errors = ref([])

const form = ref({
  name: '',
  email: '',
  current_password: '',
  new_password: '',
  new_password_confirmation: '',
})

const visibility = ref({
  current: false,
  new: false,
  confirm: false,
})

function toggleVisibility(field) {
  visibility.value[field] = !visibility.value[field]
}

/* ===========================
   LOAD USER DATA
=========================== */
async function loadUser() {
  try {
    const res = await api.get('/api/user')
    form.value.name = res.data.name
    form.value.email = res.data.email
    lastUpdated.value = new Date().toLocaleDateString()
  } catch (err) {
    console.error(err)
  }
}

onMounted(loadUser)

/* ===========================
   SAVE CHANGES
=========================== */
async function saveChanges() {
  successMessage.value = ''
  errors.value = []

  try {
    // 1️⃣ Update name + email
    await api.put('/api/user', {
      name: form.value.name,
      email: form.value.email,
    })

    // 2️⃣ Update password (ONLY if filled)
    if (form.value.new_password) {
      await api.put('/api/user/password', {
        current_password: form.value.current_password,
        new_password: form.value.new_password,
        new_password_confirmation: form.value.new_password_confirmation,
      })
    }

    successMessage.value = 'Changes saved successfully!'
    form.value.current_password = ''
    form.value.new_password = ''
    form.value.new_password_confirmation = ''
    lastUpdated.value = new Date().toLocaleDateString()
  } catch (err) {
    if (err.response?.data?.errors) {
      errors.value = Object.values(err.response.data.errors).flat()
    } else {
      errors.value = ['Something went wrong. Please try again.']
    }
  }
}
</script>
