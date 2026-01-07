import { createRouter, createWebHistory } from 'vue-router'
import { useAuthStore } from '@/stores/auth'

/* ---------------------------------------------------------
   🧩 CORE VIEWS
--------------------------------------------------------- */
import LoginView from '@/views/LoginView.vue'
import HomeView from '@/views/HomeView.vue'
import AboutView from '@/views/AboutView.vue'
import ProfileView from '@/views/ProfileView.vue'
import NotFound from '@/views/NotFound.vue'

/* ---------------------------------------------------------
   ⚙️ SETTINGS
--------------------------------------------------------- */
import SettingsView from '@/views/SettingsView.vue'

/* ---------------------------------------------------------
   📦 INVENTORY VIEWS
--------------------------------------------------------- */
import ProductListView from '@/views/Inventory/ProductListView.vue'
import ProductCreateView from '@/views/Inventory/ProductCreateView.vue'
import ProductEditView from '@/views/Inventory/ProductEditView.vue'
import ProductDetailsView from '@/views/Inventory/ProductDetailsView.vue'
import ProductShowView from '@/views/Inventory/ProductShowView.vue'
import ProductOverviewView from '@/views/Inventory/ProductOverviewView.vue'
import InventoryLogsView from '@/views/InventoryLogsView.vue'

/* ---------------------------------------------------------
   🧾 TRANSACTION VIEWS
--------------------------------------------------------- */
import ProductTransactionView from '@/views/Transaction/ProductTransactionView.vue'
import SalesTransactionsView from '@/views/Transaction/SalesTransactionsView.vue'

/* ---------------------------------------------------------
   🛒 POS
--------------------------------------------------------- */
import POSView from '@/views/Sales/POSView.vue'

/* ---------------------------------------------------------
   🧾 SUPPLIERS
--------------------------------------------------------- */
import SuppliersView from '@/views/Suppliers/SuppliersView.vue'
import SupplierCreateView from '@/views/Suppliers/SupplierCreateView.vue'
import EditSupplierView from '@/views/Suppliers/EditSupplierView.vue'

/* ---------------------------------------------------------
   🗂 CATEGORIES
--------------------------------------------------------- */
import CategoryView from '@/views/Category/CategoryView.vue'
import CategoryCreateView from '@/views/Category/CategoryCreateView.vue'
import CategoryEditView from '@/views/Category/CategoryEditView.vue'

/* ---------------------------------------------------------
   📊 REPORTS
--------------------------------------------------------- */
import ReportsView from '@/views/ReportsView.vue'

/* ---------------------------------------------------------
   🚦 ROUTES
--------------------------------------------------------- */
const routes = [
  /* 🔑 AUTH */
  { path: '/', redirect: '/login' },

  {
    path: '/login',
    name: 'login',
    component: LoginView,
    meta: { guestOnly: true },
  },

  /* 🏠 MAIN (Protected) */
  { path: '/home', name: 'home', component: HomeView, meta: { requiresAuth: true } },
  { path: '/about', name: 'about', component: AboutView, meta: { requiresAuth: true } },
  { path: '/profile', name: 'profile', component: ProfileView, meta: { requiresAuth: true } },

  /* ⚙️ SETTINGS */
  { path: '/settings', name: 'settings', component: SettingsView, meta: { requiresAuth: true } },

  /* 📊 REPORTS */
  { path: '/reports', name: 'reports', component: ReportsView, meta: { requiresAuth: true } },

  /* 📦 INVENTORY */
  { path: '/products', name: 'products', component: ProductListView, meta: { requiresAuth: true } },
  {
    path: '/products/create',
    name: 'product-create',
    component: ProductCreateView,
    meta: { requiresAuth: true },
  },
  {
    path: '/products/:id/edit',
    name: 'product-edit',
    component: ProductEditView,
    props: true,
    meta: { requiresAuth: true },
  },
  {
    path: '/products/:id',
    name: 'product-details',
    component: ProductDetailsView,
    props: true,
    meta: { requiresAuth: true },
  },
  {
    path: '/products/:id/show',
    name: 'product-show',
    component: ProductShowView,
    props: true,
    meta: { requiresAuth: true },
  },
  {
    path: '/products/:id/transactions',
    name: 'product-transactions',
    component: ProductTransactionView,
    props: true,
    meta: { requiresAuth: true },
  },
  {
    path: '/products/:id/overview',
    name: 'product-overview',
    component: ProductOverviewView,
    props: true,
    meta: { requiresAuth: true },
  },

  /* ⭐ INVENTORY LOGS */
  {
    path: '/inventory-logs',
    name: 'inventory-logs',
    component: InventoryLogsView,
    meta: { requiresAuth: true },
  },

  /* 🧾 SALES TRANSACTIONS */
  {
    path: '/sales-transaction',
    name: 'sales-transactions',
    component: SalesTransactionsView,
    meta: { requiresAuth: true },
  },

  /* 🛒 POS */
  { path: '/point-of-sale', name: 'pos', component: POSView, meta: { requiresAuth: true } },

  /* 🧾 SUPPLIERS */
  { path: '/suppliers', name: 'suppliers', component: SuppliersView, meta: { requiresAuth: true } },
  {
    path: '/suppliers/create',
    name: 'supplier-create',
    component: SupplierCreateView,
    meta: { requiresAuth: true },
  },
  {
    path: '/suppliers/:id/edit',
    name: 'supplier-edit',
    component: EditSupplierView,
    props: true,
    meta: { requiresAuth: true },
  },

  /* 🗂 CATEGORIES */
  {
    path: '/categories',
    name: 'categories',
    component: CategoryView,
    meta: { requiresAuth: true },
  },
  {
    path: '/categories/create',
    name: 'category-create',
    component: CategoryCreateView,
    meta: { requiresAuth: true },
  },
  {
    path: '/categories/:id/edit',
    name: 'category-edit',
    component: CategoryEditView,
    props: true,
    meta: { requiresAuth: true },
  },

  /* ❌ 404 */
  { path: '/:pathMatch(.*)*', name: 'not-found', component: NotFound },
]

/* ---------------------------------------------------------
   🔗 ROUTER
--------------------------------------------------------- */
const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes,
})

/* ---------------------------------------------------------
   🔐 ROUTER GUARD (AUTH PROTECTION)
--------------------------------------------------------- */
router.beforeEach(async (to, from, next) => {
  const auth = useAuthStore()

  // 💡 Allow login page to load without checking session
  if (to.path === '/login') {
    return next()
  }

  // 💡 Only check session for protected routes
  if (to.meta.requiresAuth) {
    try {
      if (!auth.authenticated) {
        await auth.fetchUser()
      }
      return next()
    } catch (error) {
      auth.user = null
      auth.authenticated = false
      return next('/login')
    }
  }

  // 💡 Guest-only route (e.g. /login)
  if (to.meta.guestOnly && auth.authenticated) {
    return next('/home')
  }

  next()
})

export default router
