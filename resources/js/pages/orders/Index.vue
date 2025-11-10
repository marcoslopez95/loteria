<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3'
import { create as ordersCreate, show as ordersShow } from '@/routes/orders'
import { dashboard } from '@/routes'

withDefaults(defineProps<{
  orders: {
    data: Array<{
      id: number
      status: 'PorPagar' | 'Pagada' | 'Cancelada'
      total: string | number
      user?: { id: number; name: string; email: string } | null
      created_at?: string
    }>
    links?: Array<any>
  }
}>(), {})

function badgeColor(status: string) {
  switch (status) {
    case 'Pagada': return 'bg-green-600 text-white'
    case 'Cancelada': return 'bg-red-600 text-white'
    default: return 'bg-yellow-500 text-black'
  }
}
</script>

<template>
  <Head title="Órdenes" />
  <div class="mx-auto max-w-6xl p-6">
    <div class="mb-4 flex items-center justify-between">
      <h1 class="text-2xl font-semibold">Órdenes</h1>
      <div class="flex items-center gap-2">
        <Link :href="dashboard()" class="rounded-md border px-3 py-2">Menú principal</Link>
        <Link :href="ordersCreate().url" class="rounded-md bg-black/90 px-3 py-2 text-white hover:bg-black">Nueva Orden</Link>
      </div>
    </div>

    <div class="overflow-hidden rounded-md border">
      <table class="min-w-full divide-y divide-black/10 dark:divide-white/10">
        <thead class="bg-black/5 dark:bg-white/10">
          <tr>
            <th class="px-4 py-2 text-left text-sm font-medium">#</th>
            <th class="px-4 py-2 text-left text-sm font-medium">Cliente</th>
            <th class="px-4 py-2 text-left text-sm font-medium">Estado</th>
            <th class="px-4 py-2 text-right text-sm font-medium">Total</th>
            <th class="px-4 py-2 text-left text-sm font-medium">Creada</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="o in orders.data" :key="o.id" class="odd:bg-white even:bg-black/2.5 dark:odd:bg-white/5">
            <td class="px-4 py-2 text-sm">
              <Link :href="ordersShow([o.id]).url" class="font-medium underline">#{{ o.id }}</Link>
            </td>
            <td class="px-4 py-2 text-sm">{{ o.user?.name ?? '—' }}</td>
            <td class="px-4 py-2 text-sm"><span class="rounded px-2 py-0.5 text-xs font-semibold" :class="badgeColor(o.status)">{{ o.status }}</span></td>
            <td class="px-4 py-2 text-right text-sm font-mono">${{ o.total }}</td>
            <td class="px-4 py-2 text-sm">{{ o.created_at?.substring(0,10) ?? '' }}</td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</template>
