<script setup lang="ts">
import { Head, Form, Link } from '@inertiajs/vue3'
import { Button } from '@/components/ui/button'
import { Input } from '@/components/ui/input'
import { Label } from '@/components/ui/label'
import InputError from '@/components/InputError.vue'
import { update as ordersUpdate, show as ordersShow } from '@/routes/orders'
import { dashboard } from '@/routes'
import { ref, computed } from 'vue'

const props = defineProps<{
  order: {
    id: number
    status: 'PorPagar' | 'Pagada' | 'Cancelada'
    notes?: string | null
    items: Array<{ id: number; number: string }>
  }
}>()

const cancelOrder = ref(false)
const notes = ref(props.order.notes ?? '')

const allNumbers = Array.from({ length: 1000 }, (_, i) => String(i).padStart(3, '0'))
const selected = ref<Set<string>>(new Set(props.order.items.map(i => i.number)))

function toggle(n: string) {
  if (selected.value.has(n)) {
    selected.value.delete(n)
  } else {
    selected.value.add(n)
  }
}

const total = computed(() => (selected.value.size * 5).toFixed(2))

function buildPayload() {
  const items = Array.from(selected.value).map((n) => ({ number: n }))
  const payload: Record<string, any> = { items, notes: notes.value }
  if (cancelOrder.value) {
    payload.status = 'Cancelada'
  }
  return payload
}
</script>

<template>
  <Head :title="`Editar Orden #${order.id}`" />
  <div class="mx-auto max-w-6xl p-6">
    <div class="mb-6 flex items-center justify-between">
      <h1 class="text-2xl font-semibold">Editar Orden #{{ order.id }}</h1>
      <div class="flex items-center gap-2">
        <Link :href="dashboard()" class="rounded-md border px-3 py-2">Menú principal</Link>
        <Link :href="ordersShow([order.id]).url" class="rounded-md border px-3 py-2">Volver a orden</Link>
      </div>
    </div>

    <Form v-bind="ordersUpdate.form([order.id])" :transform="() => buildPayload()" v-slot="{ errors, processing }" class="grid gap-8">
      <section class="rounded-lg border bg-white/50 p-4 dark:bg-white/5">
        <h2 class="mb-3 text-lg font-medium">Números</h2>
        <div class="grid gap-2" style="grid-template-columns: repeat(auto-fill, minmax(60px, 1fr));">
          <button
            v-for="n in allNumbers"
            :key="n"
            type="button"
            class="rounded-md px-2 py-2 text-sm font-semibold ring-1 transition"
            :class="selected.has(n) ? 'bg-teal-600 text-white ring-teal-700' : 'bg-white dark:bg-white/10 text-teal-900 dark:text-white ring-black/10 dark:ring-white/10 hover:bg-black/5 dark:hover:bg-white/15'"
            @click="toggle(n)"
          >
            {{ n }}
          </button>
        </div>
        <InputError :message="errors['items']" />
        <p class="mt-3 text-sm">Precio por número: <strong>$5.00</strong> — Total: <strong>${{ total }}</strong></p>
      </section>

      <section class="grid gap-3 rounded-lg border bg-white/50 p-4 dark:bg-white/5">
        <div>
          <Label for="notes">Notas</Label>
          <Input id="notes" v-model="notes" placeholder="Notas internas (opcional)" />
          <InputError :message="errors.notes" />
        </div>
        <label class="flex items-center gap-2">
          <input type="checkbox" v-model="cancelOrder" />
          <span>Marcar como Cancelada</span>
        </label>
      </section>

      <div class="flex items-center justify-end gap-3">
        <Button type="submit" :disabled="processing">{{ processing ? 'Guardando...' : 'Guardar cambios' }}</Button>
      </div>
    </Form>
  </div>
</template>
