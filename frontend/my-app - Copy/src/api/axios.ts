import axios from 'axios'

const api = axios.create({
  baseURL: 'http://localhost:8000',
  withCredentials: true,
  xsrfCookieName: 'XSRF-TOKEN', // name of the cookie Sanctum sets
  xsrfHeaderName: 'X-XSRF-TOKEN', // name of header Laravel expects
})

// FORCE Axios to attach CSRF token from cookie
api.interceptors.request.use((config) => {
  const token = getCookie('XSRF-TOKEN')
  if (token) {
    config.headers['X-XSRF-TOKEN'] = decodeURIComponent(token)
  }
  return config
})

// Helper to read cookies (needed for Windows + Chrome)
function getCookie(name) {
  const match = document.cookie.match(new RegExp('(^| )' + name + '=([^;]+)'))
  if (match) return match[2]
}

export default api
