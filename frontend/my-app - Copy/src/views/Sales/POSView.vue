<template>
  <Layout>
    <div class="flex h-screen bg-gray-50">
      <!-- =======================
           LEFT: PRODUCT LIST
      ======================== -->
      <div class="w-2/3 p-6 overflow-y-auto">
        <div class="mb-6 flex items-center space-x-4">
          <div class="flex-1 relative">
            <input
              v-model="search"
              type="text"
              placeholder="Search products..."
              class="w-full pl-10 pr-4 py-2 rounded-lg border border-gray-300"
            />
            <i class="fa-solid fa-search absolute left-3 top-3 text-gray-400"></i>
          </div>

          <select v-model="selectedCategory" class="px-4 py-2 rounded-lg border">
            <option value="">All Categories</option>
            <option v-for="cat in categories" :key="cat.CategoryID" :value="cat.CategoryID">
              {{ cat.CategoryName }}
            </option>
          </select>
        </div>

        <!-- PRODUCT GRID -->
        <div class="grid grid-cols-3 gap-4">
          <div
            v-for="product in filteredProducts"
            :key="product.id"
            @click="openQuantityModal(product)"
            class="product-card bg-white rounded-lg shadow p-4 cursor-pointer"
          >
            <div
              class="aspect-square mb-3 bg-gray-100 rounded-lg overflow-hidden flex items-center justify-center"
            >
              <img
                v-if="product.image"
                :src="product.image"
                class="w-full h-full object-contain"
                @error="onImgError(product)"
              />
              <i v-else class="fa-solid fa-image text-gray-400 text-4xl"></i>
            </div>

            <h3 class="font-medium text-gray-900 truncate">{{ product.name }}</h3>
            <p class="text-sm text-gray-500 mb-2">SKU: {{ product.sku }}</p>

            <div class="flex justify-between items-center">
              <span class="text-lg font-semibold text-blue-600">
                ₱{{ product.price.toFixed(2) }}
              </span>
              <span class="text-sm text-gray-500">Stock: {{ product.stock }}</span>
            </div>
          </div>
        </div>
      </div>

      <!-- =======================
           RIGHT: CART
      ======================== -->
      <div class="w-1/3 bg-white border-l flex flex-col">
        <div class="p-6 border-b">
          <h2 class="text-xl font-bold">Current Sale</h2>

          <div class="mt-2 flex items-center space-x-2">
            <input
              v-model="customerName"
              type="text"
              placeholder="Customer Name"
              class="flex-1 px-3 py-2 rounded-lg border"
            />
            <button @click="generateCustomerName" class="px-3 py-2 text-blue-600">
              <i class="fa-solid fa-user-plus"></i>
            </button>
          </div>
        </div>

        <!-- CART ITEMS -->
        <div class="flex-1 overflow-y-auto p-6">
          <div v-if="groupedCart.length === 0" class="text-center text-gray-400">
            No items added.
          </div>

          <div
            v-for="item in groupedCart"
            :key="item.key"
            class="flex justify-between items-center p-3 bg-gray-50 rounded-lg mb-3"
          >
            <div>
              <h4 class="font-medium">{{ item.name }}</h4>
              <p class="text-sm text-gray-500">
                {{ item.totalQty }} × ₱{{ item.price.toFixed(2) }}
              </p>
            </div>

            <div class="flex items-center space-x-3">
              <span class="font-medium"> ₱{{ (item.totalQty * item.price).toFixed(2) }} </span>
              <button @click="removeFromCart(item.key)" class="text-red-500">
                <i class="fa-solid fa-trash"></i>
              </button>
            </div>
          </div>
        </div>

        <!-- SUMMARY -->
        <div class="border-t p-6 bg-gray-50">
          <div class="space-y-2">
            <div class="flex justify-between text-sm">
              <span>Subtotal</span>
              <span>₱{{ subtotal.toFixed(2) }}</span>
            </div>

            <div class="flex justify-between text-sm">
              <span>VAT (12%)</span>
              <span>₱{{ vat.toFixed(2) }}</span>
            </div>

            <div class="flex justify-between text-sm">
              <span>Discount</span>
              <div class="flex items-center space-x-2">
                <input
                  v-model.number="discountValue"
                  type="number"
                  class="w-20 px-2 py-1 rounded border text-right"
                />
                <select v-model="discountType" class="px-2 py-1 rounded border">
                  <option value="%">%</option>
                  <option value="PHP">₱</option>
                </select>
              </div>
            </div>

            <div class="border-t pt-3 flex justify-between items-center">
              <span class="text-lg font-bold">Total</span>
              <span class="text-2xl font-bold text-blue-600"> ₱{{ total.toFixed(2) }} </span>
            </div>
          </div>

          <div class="mt-6 space-y-3">
            <button
              @click="openCashModal"
              class="w-full bg-blue-600 text-white py-3 rounded-lg disabled:opacity-50"
              :disabled="isProcessing"
            >
              <i v-if="!isProcessing" class="fa-solid fa-cash-register mr-2"></i>
              <i v-else class="fa-solid fa-spinner fa-spin mr-2"></i>
              {{ isProcessing ? 'Processing...' : 'Process Payment' }}
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- =======================
         QUANTITY MODAL
    ======================== -->
    <Modal v-if="showQtyModal" @close="showQtyModal = false">
      <template #title>{{ selectedProduct?.name }}</template>

      <div class="text-sm space-y-1">
        <p><strong>Base Price:</strong> ₱{{ selectedProduct?.price }}</p>
        <p><strong>Available Stock:</strong> {{ availableStock }}</p>
        <p><strong>In Cart:</strong> {{ inCart(selectedProduct?.id) }}</p>
      </div>

      <!-- CUT DIMENSIONS -->
      <div class="mt-4 space-y-2">
        <label class="text-sm font-medium">Cut Size (meters)</label>

        <div class="grid grid-cols-2 gap-3">
          <!-- LENGTH -->
          <div>
            <label class="block text-xs text-gray-600 mb-1">Length (m)</label>
            <input
              v-model.number="cutLength"
              type="number"
              min="0.1"
              step="0.1"
              class="w-full border rounded px-2 py-1"
              placeholder="e.g. 2.5"
            />
          </div>

          <!-- WIDTH -->
          <div>
            <label class="block text-xs text-gray-600 mb-1">Width (m)</label>
            <input
              v-model.number="cutWidth"
              type="number"
              min="0.1"
              step="0.1"
              class="w-full border rounded px-2 py-1"
              placeholder="e.g. 1.2"
            />
          </div>
        </div>

        <p class="text-sm text-gray-600">
          Area: <strong>{{ cutArea.toFixed(2) }}</strong> sqm
        </p>

        <p class="text-sm text-blue-600 font-semibold">
          Adjusted Price: ₱{{ cutPrice.toFixed(2) }}
        </p>
      </div>

      <div class="mt-4">
        <label class="text-sm font-medium">Quantity</label>
        <div class="flex items-center space-x-3 mt-2">
          <button @click="qty--" :disabled="qty <= 1" class="w-10 h-10 border rounded-lg">
            <i class="fa-solid fa-minus"></i>
          </button>

          <input v-model.number="qty" type="number" class="w-20 text-center border rounded-lg" />

          <button @click="qty++" class="w-10 h-10 border rounded-lg">
            <i class="fa-solid fa-plus"></i>
          </button>
        </div>
      </div>

      <template #footer>
        <button @click="showQtyModal = false" class="px-4 py-2 rounded-lg">Cancel</button>
        <button @click="addToCart" class="px-4 py-2 bg-blue-600 text-white rounded-lg">
          Add to Cart
        </button>
      </template>
    </Modal>

    <!-- =======================
         CASH PAYMENT MODAL
    ======================== -->
    <Modal v-if="showCashModal" @close="showCashModal = false">
      <template #title>Cash Payment</template>

      <div class="space-y-3">
        <div class="flex justify-between text-sm">
          <span>Total Amount:</span>
          <strong>₱{{ total.toFixed(2) }}</strong>
        </div>

        <div>
          <label>Amount Received</label>
          <input
            v-model.number="amountReceived"
            type="number"
            class="w-full px-3 py-2 border rounded mt-1"
          />
        </div>

        <div class="flex justify-between text-sm">
          <span>Change:</span>
          <strong>₱{{ change.toFixed(2) }}</strong>
        </div>
      </div>

      <template #footer>
        <button @click="showCashModal = false" class="px-4 py-2 rounded">Cancel</button>
        <button
          @click="completePayment"
          class="px-4 py-2 bg-blue-600 text-white rounded"
          :disabled="isProcessing"
        >
          <i v-if="isProcessing" class="fa-solid fa-spinner fa-spin mr-2"></i>
          {{ isProcessing ? 'Processing...' : 'Complete Payment' }}
        </button>
      </template>
    </Modal>
  </Layout>

  <!-- =======================
     PRINT RECEIPT AREA
======================== -->
  <div id="receipt">
    <h3 class="text-center font-bold text-lg">SHEEM STEEL CONSTRUCTION</h3>
    <p class="text-xs text-center">Roofing & Steel Supplies</p>
    <hr class="my-2" />

    <p class="text-sm">Date: {{ new Date().toLocaleString() }}</p>
    <p class="text-sm">Customer: {{ customerName }}</p>

    <hr class="my-2" />

    <!-- ITEMS -->
    <div v-for="item in groupedCart" :key="item.key" class="flex justify-between text-sm mb-1">
      <div>
        <p class="font-medium">
          {{ item.name }}
        </p>
        <p class="text-xs text-gray-600">{{ item.totalQty }} × ₱{{ item.price.toFixed(2) }}</p>
      </div>

      <div class="text-right">₱{{ (item.totalQty * item.price).toFixed(2) }}</div>
    </div>

    <hr class="my-2" />

    <!-- TOTALS -->
    <div class="text-sm flex justify-between">
      <span>Subtotal</span>
      <span>₱{{ subtotal.toFixed(2) }}</span>
    </div>

    <div class="text-sm flex justify-between">
      <span>VAT (12%)</span>
      <span>₱{{ vat.toFixed(2) }}</span>
    </div>

    <div class="text-sm flex justify-between">
      <span>Discount</span>
      <span>- ₱{{ discountAmount.toFixed(2) }}</span>
    </div>

    <hr class="my-2" />

    <div class="text-sm flex justify-between font-bold">
      <span>Total</span>
      <span>₱{{ total.toFixed(2) }}</span>
    </div>

    <div class="text-sm flex justify-between">
      <span>Cash</span>
      <span>₱{{ amountReceived.toFixed(2) }}</span>
    </div>

    <div class="text-sm flex justify-between">
      <span>Change</span>
      <span>₱{{ change.toFixed(2) }}</span>
    </div>

    <hr class="my-2" />

    <p class="text-center text-xs mt-2">Thank you for your purchase!</p>
    <p class="text-center text-xs">Please come again.</p>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import Layout from '@/components/Layout.vue'
import Modal from '@/components/Modal.vue'
import api from '@/api/axios'

const products = ref([])
const categories = ref([])
const search = ref('')
const selectedCategory = ref('')
const cart = ref([])

const customerName = ref('')
const showQtyModal = ref(false)
const showCashModal = ref(false)
const isProcessing = ref(false)

const selectedProduct = ref(null)
const qty = ref(1)
const cutLength = ref(1)
const cutWidth = ref(1)

const discountType = ref('%')
const discountValue = ref(0)
const amountReceived = ref(0)

function onImgError(product) {
  product.image = null
}

onMounted(async () => {
  const prodRes = await api.get('/api/product-list')
  products.value = prodRes.data.map((p) => ({
    id: p.ProductID,
    name: p.ProductName,
    sku: p.SKU,
    price: Number(p.SellingPrice),
    stock: Number(p.Stock),
    image: p.ImageURL || null,
    category: p.CategoryID,
  }))

  const catRes = await api.get('/api/categories')
  categories.value = catRes.data.categories ?? catRes.data
})

const filteredProducts = computed(() =>
  products.value.filter(
    (p) =>
      p.name.toLowerCase().includes(search.value.toLowerCase()) &&
      (!selectedCategory.value || p.category == selectedCategory.value),
  ),
)

function generateCustomerName() {
  customerName.value = `Customer-${Date.now()}`
}

function openQuantityModal(product) {
  selectedProduct.value = product
  qty.value = 1
  cutLength.value = 1
  cutWidth.value = 1
  showQtyModal.value = true
}

const cutArea = computed(() => cutLength.value * cutWidth.value)
const cutPrice = computed(() =>
  selectedProduct.value ? selectedProduct.value.price * cutArea.value : 0,
)

function inCart(id) {
  return cart.value.filter((i) => i.id === id).reduce((s, i) => s + i.qty, 0)
}

const availableStock = computed(() =>
  selectedProduct.value ? selectedProduct.value.stock - inCart(selectedProduct.value.id) : 0,
)

function addToCart() {
  const key = `${selectedProduct.value.id}-${cutLength.value}x${cutWidth.value}`

  cart.value.push({
    key,
    id: selectedProduct.value.id,
    name: `${selectedProduct.value.name} (${cutLength.value}×${cutWidth.value})`,
    price: cutPrice.value,
    qty: qty.value,
  })

  showQtyModal.value = false
}

const groupedCart = computed(() => {
  const groups = {}
  cart.value.forEach((item) => {
    if (!groups[item.key]) groups[item.key] = { ...item, totalQty: 0 }
    groups[item.key].totalQty += item.qty
  })
  return Object.values(groups)
})

function removeFromCart(key) {
  cart.value = cart.value.filter((i) => i.key !== key)
}

const subtotal = computed(() => cart.value.reduce((s, i) => s + i.qty * i.price, 0))
const vat = computed(() => subtotal.value * 0.12)
const discountAmount = computed(() =>
  discountType.value === '%' ? subtotal.value * (discountValue.value / 100) : discountValue.value,
)
const total = computed(() => subtotal.value + vat.value - discountAmount.value)
const change = computed(() => Math.max(amountReceived.value - total.value, 0))

function openCashModal() {
  if (!customerName.value) {
    alert('Please enter customer name before processing payment.')
    return
  }

  if (!cart.value.length) {
    alert('Cart is empty. Please add products first.')
    return
  }

  showCashModal.value = true
}

async function completePayment() {
  if (amountReceived.value < total.value) {
    alert('Amount received is not enough.')
    return
  }

  isProcessing.value = true

  await api.post('/api/sales/process', {
    total_amount: total.value,
    discount_amount: discountAmount.value,
    amount_paid: amountReceived.value,
    customer_name: customerName.value,
    items: groupedCart.value.map((i) => ({
      product_id: i.id,
      quantity: i.totalQty,
      price: i.price,
    })),
  })

  window.print()

  alert('Payment successful! Transaction completed.')

  cart.value = []
  customerName.value = ''
  amountReceived.value = 0
  showCashModal.value = false
  isProcessing.value = false
}
</script>

<style>
#receipt {
  display: none;
}

@media print {
  body * {
    visibility: hidden !important;
  }

  #receipt,
  #receipt * {
    visibility: visible !important;
  }

  #receipt {
    display: block !important;
    position: absolute;
    left: 0;
    top: 0;
    width: 280px;
    padding: 10px;
    background: white;
    font-size: 14px;
    line-height: 18px;
  }
}
</style>
