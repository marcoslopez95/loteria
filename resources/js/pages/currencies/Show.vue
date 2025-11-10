<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3'
import { edit as currencyEdit, index as currencyIndex } from '@/routes/currencies'

withDefaults(defineProps<{
  currency: { id: number; code: string; name: string; symbol: string; active: boolean }
}>(), {})
</script>

<template>
  <Head :title="`Moneda ${currency.code}`" />
  <div class="mx-auto max-w-xl p-6 space-y-4">
    <div class="flex items-center justify-between">
      <h1 class="text-2xl font-semibold">Moneda {{ currency.code }}</h1>
      <div class="flex gap-2">
        <Link :href="currencyEdit([currency.id]).url" class="rounded-md bg-black/90 px-3 py-2 text-white hover:bg-black">Editar</Link>
        <Link :href="currencyIndex().url" class="rounded-md border px-3 py-2">Volver</Link>
      </div>
    </div>

    <div class="rounded-lg border bg-white/50 p-4 text-sm dark:bg-white/5">
      <div class="flex items-center justify-between py-2">
        <div class="text-neutral-500">Código</div>
        <div class="font-mono">{{ currency.code }}</div>
      </div>
      <div class="flex items-center justify-between py-2">
        <div class="text-neutral-500">Nombre</div>
        <div>{{ currency.name }}</div>
      </div>
      <div class="flex items-center justify-between py-2">
        <div class="text-neutral-500">Símbolo</div>
        <div>{{ currency.symbol }}</div>
      </div>
      <div class="flex items-center justify-between py-2">
        <div class="text-neutral-500">Activa</div>
        <div>{{ currency.active ? 'Sí' : 'No' }}</div>
      </div>
    </div>
  </div>
</template>
