<script setup lang="ts">
import { Head, Form, Link } from '@inertiajs/vue3'
import { Button } from '@/components/ui/button'
import { Input } from '@/components/ui/input'
import { Label } from '@/components/ui/label'
import InputError from '@/components/InputError.vue'
import { store as currencyStore, index as currencyIndex } from '@/routes/currencies'
</script>

<template>
  <Head title="Crear Moneda" />
  <div class="mx-auto max-w-xl p-6">
    <div class="mb-6 flex items-center justify-between">
      <h1 class="text-2xl font-semibold">Nueva Moneda</h1>
      <Link :href="currencyIndex().url" class="rounded-md border px-3 py-2">Volver</Link>
    </div>

    <Form v-bind="currencyStore.form()" v-slot="{ errors, processing }" class="grid gap-6 rounded-lg border bg-white/50 p-4 dark:bg-white/5">
      <div>
        <Label for="code">Código</Label>
        <Input id="code" name="code" placeholder="USD" />
        <InputError :message="errors.code" />
      </div>
      <div>
        <Label for="name">Nombre</Label>
        <Input id="name" name="name" placeholder="US Dollar" />
        <InputError :message="errors.name" />
      </div>
      <div>
        <Label for="symbol">Símbolo</Label>
        <Input id="symbol" name="symbol" placeholder="$" />
        <InputError :message="errors.symbol" />
      </div>
      <label class="flex items-center gap-2">
        <input type="checkbox" name="active" value="1" checked />
        <span>Activa</span>
      </label>

      <div class="flex justify-end">
        <Button type="submit" :disabled="processing">{{ processing ? 'Creando...' : 'Crear' }}</Button>
      </div>
    </Form>
  </div>
</template>
