<template>
  <div
    class="fixed inset-0 bg-black/50 backdrop-blur-sm flex items-center justify-center z-50"
    @click.self="$emit('close')"
  >
    <div class="bg-white rounded-lg shadow-xl w-96 p-6 animate-fade">
      <!-- HEADER -->
      <div class="flex justify-between items-center mb-4">
        <h3 class="text-lg font-semibold text-gray-900">
          <slot name="title"></slot>
        </h3>

        <button @click="$emit('close')" class="text-gray-400 hover:text-gray-600 text-xl">
          <i class="fa-solid fa-xmark"></i>
        </button>
      </div>

      <!-- BODY -->
      <div class="text-gray-700">
        <slot></slot>
      </div>

      <!-- FOOTER -->
      <div class="mt-6 flex justify-end space-x-3">
        <slot name="footer"></slot>
      </div>
    </div>
  </div>
</template>

<style>
.animate-fade {
  animation: fadeIn 0.2s ease-out;
}

@keyframes fadeIn {
  from {
    opacity: 0;
    transform: scale(0.96);
  }
  to {
    opacity: 1;
    transform: scale(1);
  }
}
</style>
