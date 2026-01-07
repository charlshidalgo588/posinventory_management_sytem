import { createApp } from 'vue'
import { createPinia } from 'pinia'
import App from './App.vue'
import router from './router'
import '@fortawesome/fontawesome-free/css/all.min.css'

// 🧩 Import Element Plus
import ElementPlus from 'element-plus'
import 'element-plus/dist/index.css'

// 🎨 Import Tailwind CSS
import './assets/main.css'

const app = createApp(App)

app.use(createPinia())
app.use(router)
app.use(ElementPlus) // ✅ register Element Plus

app.mount('#app')
