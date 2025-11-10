<script setup lang="ts">
import { Head, Form, Link } from '@inertiajs/vue3'
import { Button } from '@/components/ui/button'
import { Input } from '@/components/ui/input'
import { Label } from '@/components/ui/label'
import InputError from '@/components/InputError.vue'
import paymentsRoutes from '@/routes/orders/payments'
import { edit as ordersEdit, index as ordersIndex } from '@/routes/orders'
import { dashboard } from '@/routes'
import { ref, computed, watch } from 'vue'

const props = defineProps<{
  order: {
    id: number
    status: 'PorPagar' | 'Pagada' | 'Cancelada'
    total: string | number
    notes?: string | null
    user?: { id: number; name: string; email: string } | null
    items: Array<{ id: number; number: string }>
    payments: Array<{ id: number; amount: string | number; exchange_rate: string | number; reference: string; paid_at?: string | null; currency?: { id: number; code: string; symbol: string } }>
  }
  paidAmountUsd: number | string
  currencies: Array<{ id: number; code: string; name: string; symbol: string; active: boolean }>
}>()

const canPay = computed(() => props.order.status === 'PorPagar')

// Payment form state
const amount = ref('')
const currencyId = ref<number | null>(null)
const exchangeRate = ref('1')
const reference = ref('')
const paidAt = ref<string>('')

// Default currency: USD if available
const usd = computed(() => props.currencies.find(c => c.code === 'USD'))
const ves = computed(() => props.currencies.find(c => c.code === 'VES'))

if (usd.value) {
  currencyId.value = usd.value.id
  exchangeRate.value = '1'
}

watch(currencyId, (id) => {
  const curr = props.currencies.find(c => c.id === id)
  if (!curr) return
  if (curr.code === 'USD') {
    exchangeRate.value = '1'
  } else if (curr.code === 'VES') {
    exchangeRate.value = '221'
  }
})

function paymentPayload() {
  return {
    amount: amount.value,
    currency_id: currencyId.value,
    exchange_rate: exchangeRate.value,
    reference: reference.value,
    paid_at: paidAt.value || undefined,
  }
}

function statusBadge(status: string) {
  switch (status) {
    case 'Pagada': return 'bg-green-600 text-white'
    case 'Cancelada': return 'bg-red-600 text-white'
    default: return 'bg-yellow-500 text-black'
  }
}
</script>

<template>
  <Head :title="`Orden #${order.id}`" />
  <div class="mx-auto max-w-6xl p-6 space-y-6">
    <div class="flex items-center justify-between">
      <div>
        <h1 class="text-2xl font-semibold">Orden #{{ order.id }}</h1>
        <p class="text-sm text-neutral-600 dark:text-neutral-300">Cliente: {{ order.user?.name ?? '—' }} — Total: ${{ order.total }} — Pagado: ${{ paidAmountUsd }}</p>
      </div>
      <div class="flex items-center gap-2">
        <span class="rounded px-2 py-0.5 text-xs font-semibold" :class="statusBadge(order.status)">{{ order.status }}</span>
        <Link :href="dashboard()" class="rounded-md border px-3 py-2">Menú principal</Link>
        <Link :href="ordersEdit([order.id]).url" class="rounded-md bg-black/90 px-3 py-2 text-white hover:bg-black">Editar</Link>
        <Link :href="ordersIndex().url" class="rounded-md border px-3 py-2">Volver a órdenes</Link>
      </div>
    </div>

    <section class="rounded-lg border bg-white/50 p-4 dark:bg-white/5">
      <h2 class="mb-2 text-lg font-medium">Números</h2>
      <div class="grid gap-2" style="grid-template-columns: repeat(auto-fill, minmax(60px, 1fr));">
        <div v-for="i in order.items" :key="i.id" class="rounded-md bg-white px-2 py-2 text-center font-semibold ring-1 ring-black/10 dark:bg-white/10 dark:ring-white/10">{{ i.number }}</div>
      </div>
    </section>

    <section class="rounded-lg border bg-white/50 p-4 dark:bg-white/5">
      <div class="mb-3 flex items-center justify-between">
        <h2 class="text-lg font-medium">Pagos</h2>
        <div class="text-sm text-neutral-600 dark:text-neutral-300">Se permiten pagos sólo cuando la orden está PorPagar</div>
      </div>

      <div v-if="order.payments.length" class="mb-4 overflow-hidden rounded-md border">
        <table class="min-w-full divide-y divide-black/10 dark:divide-white/10">
          <thead class="bg-black/5 dark:bg-white/10">
            <tr>
              <th class="px-4 py-2 text-left text-sm">Referencia</th>
              <th class="px-4 py-2 text-right text-sm">Monto</th>
              <th class="px-4 py-2 text-right text-sm">Tasa</th>
              <th class="px-4 py-2 text-left text-sm">Moneda</th>
              <th class="px-4 py-2 text-left text-sm">Fecha</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="p in order.payments" :key="p.id" class="odd:bg-white even:bg-black/2.5 dark:odd:bg-white/5">
              <td class="px-4 py-2 text-sm font-mono">{{ p.reference }}</td>
              <td class="px-4 py-2 text-right text-sm">{{ Number(p.amount).toFixed(2) }}</td>
              <td class="px-4 py-2 text-right text-sm">{{ Number(p.exchange_rate).toFixed(2) }}</td>
              <td class="px-4 py-2 text-sm">{{ p.currency?.code ?? '' }}</td>
              <td class="px-4 py-2 text-sm">{{ p.paid_at?.substring(0,10) ?? '' }}</td>
            </tr>
          </tbody>
        </table>
      </div>

      <div v-if="canPay" class="rounded-md border p-4">
        <Form v-bind="paymentsRoutes.store.form([order.id])" :transform="() => paymentPayload()" v-slot="{ errors, processing }" class="grid gap-4 sm:grid-cols-5">
          <div>
            <Label for="amount">Monto</Label>
            <Input id="amount" name="amount" type="number" step="0.01" min="0.01" v-model="amount" />
            <InputError :message="errors.amount" />
          </div>
          <div>
            <Label for="currency">Moneda</Label>
            <select id="currency" name="currency_id" v-model.number="currencyId" class="w-full rounded-md border bg-white px-3 py-2 text-sm dark:bg-white/10 dark:text-white dark:border-white/10">
              <option v-for="c in currencies" :value="c.id" :key="c.id">{{ c.code }} ({{ c.symbol }})</option>
            </select>
            <InputError :message="errors.currency_id" />
          </div>
          <div>
            <Label for="rate">Tasa</Label>
            <Input id="rate" name="exchange_rate" type="number" step="0.00000001" min="0.00000001" v-model="exchangeRate" />
            <InputError :message="errors.exchange_rate" />
          </div>
          <div>
            <Label for="reference">Referencia</Label>
            <Input id="reference" name="reference" v-model="reference" />
            <InputError :message="errors.reference" />
          </div>
          <div>
            <Label for="paid_at">Fecha</Label>
            <Input id="paid_at" name="paid_at" type="datetime-local" v-model="paidAt" />
            <InputError :message="errors.paid_at" />
          </div>

          <div class="col-span-5">
            <InputError :message="errors.payment" />
          </div>

          <div class="col-span-5 flex justify-end">
            <Button type="submit" :disabled="processing">{{ processing ? 'Agregando...' : 'Agregar pago' }}</Button>
          </div>
        </Form>
      </div>

      <div v-else class="rounded-md border border-yellow-300/60 bg-yellow-100/60 p-3 text-sm text-yellow-900 dark:border-yellow-600/50 dark:bg-yellow-300/10 dark:text-yellow-200">
        No se pueden agregar pagos a una orden {{ order.status }}.
      </div>
    </section>
  </div>
</template>
