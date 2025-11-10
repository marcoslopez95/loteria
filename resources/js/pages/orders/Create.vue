<script setup lang="ts">
import { Head, Form, Link } from '@inertiajs/vue3'
import { Button } from '@/components/ui/button'
import { Input } from '@/components/ui/input'
import { Label } from '@/components/ui/label'
import InputError from '@/components/InputError.vue'
import { create as ordersCreate, store as ordersStore } from '@/routes/orders'
import usersRoutes from '@/routes/users'
import { dashboard } from '@/routes'
import { ref, computed, watch } from 'vue'

// Local state for selecting existing user or quick user creation
const mode = ref<'existing' | 'quick'>('quick')
const userId = ref<number | null>(null)

// Quick user fields
const quickName = ref('')
const quickEmail = ref('')

// User search
const query = ref('')
const results = ref<Array<{ id: number; name: string; email: string }>>([])
const searching = ref(false)
let searchAbort = new AbortController()

watch(query, async (q) => {
  if (q.trim() === '') {
    results.value = []
    return
  }
  try {
    searching.value = true
    searchAbort.abort()
    searchAbort = new AbortController()
    const url = usersRoutes.search.url({ query: { q } })
    const res = await fetch(url, { signal: searchAbort.signal })
    if (!res.ok) return
    results.value = await res.json()
  } catch (e) {
    // ignore
  } finally {
    searching.value = false
  }
})

// Numbers grid 000–999 selection
const allNumbers = Array.from({ length: 1000 }, (_, i) => String(i).padStart(3, '0'))
const selected = ref<Set<string>>(new Set())

function toggle(n: string) {
  if (selected.value.has(n)) {
    selected.value.delete(n)
  } else {
    selected.value.add(n)
  }
}

const total = computed(() => (selected.value.size * 5).toFixed(2))

// Build payload for submit
function buildPayload() {
  const items = Array.from(selected.value).map((n) => ({ number: n }))
  if (mode.value === 'existing' && userId.value) {
    return { user_id: userId.value, items }
  }
  return {
    quick_user: { name: quickName.value, email: quickEmail.value },
    items,
  }
}
</script>

<template>
  <Head title="Crear Orden" />
  <div class="mx-auto max-w-6xl p-6">
    <div class="mb-6 flex items-center justify-between">
      <h1 class="text-2xl font-semibold">Nueva Orden</h1>
      <Link :href="dashboard()" class="rounded-md border px-3 py-2">Menú principal</Link>
    </div>

    <Form v-bind="ordersStore.form()" :transform="() => buildPayload()" v-slot="{ errors, processing, setData }" class="grid gap-8">
      <!-- User selector -->
      <section class="rounded-lg border bg-white/50 p-4 dark:bg-white/5">
        <h2 class="mb-3 text-lg font-medium">Cliente</h2>
        <div class="flex gap-6">
          <div class="space-y-3 w-full">
            <div class="flex items-center gap-3">
              <input id="mode-quick" type="radio" class="size-4" name="mode" value="quick" v-model="mode" />
              <Label for="mode-quick">Crear usuario rápido</Label>
            </div>
            <div v-if="mode === 'quick'" class="grid gap-3 sm:grid-cols-2">
              <div>
                <Label for="quick-name">Nombre</Label>
                <Input id="quick-name" placeholder="Nombre y Apellido" v-model="quickName" />
                <InputError :message="errors['quick_user.name']" />
              </div>
              <div>
                <Label for="quick-email">Email</Label>
                <Input id="quick-email" type="email" placeholder="cliente@example.com" v-model="quickEmail" />
                <InputError :message="errors['quick_user.email']" />
              </div>
              <p class="col-span-2 text-xs text-neutral-500">La contraseña inicial se establece en el backend.</p>
            </div>
          </div>

          <div class="space-y-3 w-full">
            <div class="flex items-center gap-3">
              <input id="mode-existing" type="radio" class="size-4" name="mode" value="existing" v-model="mode" />
              <Label for="mode-existing">Seleccionar usuario existente</Label>
            </div>
            <div v-if="mode === 'existing'" class="grid gap-3">
              <div>
                <Label for="search">Buscar</Label>
                <Input id="search" placeholder="Nombre o email" v-model="query" />
              </div>
              <div v-if="results.length" class="max-h-48 overflow-auto rounded-md border">
                <ul>
                  <li v-for="u in results" :key="u.id" @click="userId = u.id" class="flex cursor-pointer items-center justify-between px-3 py-2 hover:bg-black/5 dark:hover:bg-white/10">
                    <div class="text-sm">
                      <div class="font-medium">{{ u.name }}</div>
                      <div class="text-neutral-500">{{ u.email }}</div>
                    </div>
                    <input type="radio" :checked="userId === u.id" />
                  </li>
                </ul>
              </div>
              <InputError :message="errors.user_id" />
            </div>
          </div>
        </div>
      </section>

      <!-- Numbers -->
      <section class="rounded-lg border bg-white/50 p-4 dark:bg-white/5">
        <div class="mb-3 flex items-center justify-between">
          <h2 class="text-lg font-medium">Números (000–999)</h2>
          <div class="text-sm text-neutral-600 dark:text-neutral-300">Seleccionados: {{ selected.size }}</div>
        </div>
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

      <div class="flex items-center justify-end gap-3">
        <Button type="submit" :disabled="processing">{{ processing ? 'Creando...' : 'Crear Orden' }}</Button>
      </div>
    </Form>
  </div>
</template>
