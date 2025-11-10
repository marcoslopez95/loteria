<script setup lang="ts">
import { Head, Link, Form } from '@inertiajs/vue3'
import { create as currencyCreate, edit as currencyEdit, destroy as currencyDestroy } from '@/routes/currencies'
import { Button } from '@/components/ui/button'

withDefaults(defineProps<{
  currencies: Array<{ id: number; code: string; name: string; symbol: string; active: boolean }>
}>(), {})
</script>

<template>
  <Head title="Monedas" />
  <div class="mx-auto max-w-5xl p-6">
    <div class="mb-4 flex items-center justify-between">
      <h1 class="text-2xl font-semibold">Monedas</h1>
      <Link :href="currencyCreate().url" class="rounded-md bg-black/90 px-3 py-2 text-white hover:bg-black">Nueva Moneda</Link>
    </div>

    <div class="overflow-hidden rounded-md border">
      <table class="min-w-full divide-y divide-black/10 dark:divide-white/10">
        <thead class="bg-black/5 dark:bg-white/10">
          <tr>
            <th class="px-4 py-2 text-left text-sm">Código</th>
            <th class="px-4 py-2 text-left text-sm">Nombre</th>
            <th class="px-4 py-2 text-left text-sm">Símbolo</th>
            <th class="px-4 py-2 text-left text-sm">Activa</th>
            <th class="px-4 py-2 text-right text-sm">Acciones</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="c in currencies" :key="c.id" class="odd:bg-white even:bg-black/2.5 dark:odd:bg-white/5">
            <td class="px-4 py-2 text-sm font-mono">{{ c.code }}</td>
            <td class="px-4 py-2 text-sm">{{ c.name }}</td>
            <td class="px-4 py-2 text-sm">{{ c.symbol }}</td>
            <td class="px-4 py-2 text-sm">{{ c.active ? 'Sí' : 'No' }}</td>
            <td class="px-4 py-2 text-right text-sm">
              <Link :href="currencyEdit([c.id]).url" class="rounded-md border px-2 py-1">Editar</Link>
              <Form v-bind="currencyDestroy.form([c.id])" class="inline">
                <Button type="submit" variant="destructive" class="ml-2">Eliminar</Button>
              </Form>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</template>
