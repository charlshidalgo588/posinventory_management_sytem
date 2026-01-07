import { defineStore } from 'pinia'
import api from '@/api/axios'

interface User {
  id: number
  name: string
  email: string
}

export const useAuthStore = defineStore('auth', {
  state: () => ({
    user: null as User | null,
    authenticated: false,
    loading: false,
  }),

  actions: {
    // =====================================================
    // Get Sanctum CSRF Cookie
    // =====================================================
    async getCsrfCookie() {
      await api.get('/sanctum/csrf-cookie')
    },

    // =====================================================
    // LOGIN
    // =====================================================
    async login(credentials: { email: string; password: string; remember?: boolean }) {
      this.loading = true

      try {
        // Step 1: Get CSRF cookie first
        await this.getCsrfCookie()

        // Step 2: Log in (session cookie is created here)
        await api.post('/login', {
          email: credentials.email,
          password: credentials.password,
          remember: credentials.remember || false,
        })

        // Step 3: Load authenticated user
        await this.fetchUser()
      } catch (error: any) {
        console.error('LOGIN ERROR:', error.response?.data || error)
        throw error
      } finally {
        this.loading = false
      }
    },

    // =====================================================
    // FETCH AUTHENTICATED USER
    // (Run automatically after login, OR on page reload)
    // =====================================================
    async fetchUser() {
      try {
        const res = await api.get('/api/user')

        this.user = res.data
        this.authenticated = true
      } catch {
        this.user = null
        this.authenticated = false
      }
    },

    // =====================================================
    // LOGOUT
    // =====================================================
    async logout() {
      await api.post('/logout')

      this.user = null
      this.authenticated = false
    },
  },
})
