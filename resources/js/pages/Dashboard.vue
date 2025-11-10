<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3'
import { index as ordersIndex } from '@/routes/orders'
import { index as currenciesIndex } from '@/routes/currencies'
import { home as homeRoute } from '@/routes'

// No props required for now
</script>

<template>
  <Head title="Dashboard" />

  <div class="mx-auto max-w-6xl p-6">
    <h1 class="text-2xl font-semibold tracking-tight">Dashboard</h1>
    <p class="mt-1 text-sm text-black/60 dark:text-white/70">Elige un módulo para gestionar.</p>

    <div class="mt-6 grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
      <!-- Órdenes -->
      <Link :href="ordersIndex().url" class="group block rounded-xl border border-black/10 bg-white p-5 shadow-sm ring-1 ring-black/5 transition hover:-translate-y-0.5 hover:shadow dark:border-white/10 dark:bg-white/5 dark:ring-white/10">
        <div class="flex items-center justify-between">
          <div>
            <h2 class="text-lg font-semibold">Órdenes</h2>
            <p class="mt-1 text-sm text-black/60 dark:text-white/70">Crear, listar y gestionar órdenes.</p>
          </div>
          <span class="rounded-full bg-black/90 px-2 py-1 text-xs font-medium text-white transition group-hover:bg-black">Ir</span>
        </div>
      </Link>

      <!-- Monedas -->
      <Link :href="currenciesIndex().url" class="group block rounded-xl border border-black/10 bg-white p-5 shadow-sm ring-1 ring-black/5 transition hover:-translate-y-0.5 hover:shadow dark:border-white/10 dark:bg-white/5 dark:ring-white/10">
        <div class="flex items-center justify-between">
          <div>
            <h2 class="text-lg font-semibold">Monedas</h2>
            <p class="mt-1 text-sm text-black/60 dark:text-white/70">Administrar monedas y símbolos.</p>
          </div>
          <span class="rounded-full bg-black/90 px-2 py-1 text-xs font-medium text-white transition group-hover:bg-black">Ir</span>
        </div>
      </Link>

      <!-- Rifa / Inicio -->
      <Link :href="homeRoute().url" class="group block rounded-xl border border-black/10 bg-white p-5 shadow-sm ring-1 ring-black/5 transition hover:-translate-y-0.5 hover:shadow dark:border-white/10 dark:bg-white/5 dark:ring-white/10">
        <div class="flex items-center justify-between">
          <div>
            <h2 class="text-lg font-semibold">Rifa (Inicio)</h2>
            <p class="mt-1 text-sm text-black/60 dark:text-white/70">Ver grilla 000–999 y portada.</p>
          </div>
          <span class="rounded-full bg-black/90 px-2 py-1 text-xs font-medium text-white transition group-hover:bg-black">Ir</span>
        </div>
      </Link>
    </div>
  </div>
</template>
