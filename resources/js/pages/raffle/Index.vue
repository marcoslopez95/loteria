<script setup lang="ts">
import {ref} from "vue"
import { Head, Link } from '@inertiajs/vue3';
import { dashboard, login, logout, register } from '@/routes';
import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogHeader,
    DialogTitle,
    DialogTrigger,
} from '@/components/ui/dialog'


withDefaults(
    defineProps<{
        numbers: string[];
        taken?: string[];
        canRegister?: boolean;
    }>(),
    {
        canRegister: true,
        taken: () => [],
    },
);
const modal = ref(true)
</script>

<template>
    <Dialog :open="modal">
        <DialogContent @close="modal = false">
            <DialogHeader>
                <DialogTitle>¡Margarita te espera!</DialogTitle>
                <DialogDescription>
                    <img src="SORTEO-VIAJE-min.png"/>
                </DialogDescription>
            </DialogHeader>
        </DialogContent>
    </Dialog>
    <Head title="Rifa • Viaje a Margarita" />

    <div
        class="min-h-screen bg-gradient-to-b from-[#0fb9b1] via-[#38bdf8] to-[#facc15] text-[#0b1b1b] dark:from-[#0e3a3a] dark:via-[#0a4b6b] dark:to-[#4a3b0d]"
    >
        <!-- Top Bar -->
        <header
            class="mx-auto flex max-w-6xl items-center justify-between px-4 py-4"
        >
            <div class="flex items-center gap-3">
                <div
                    class="grid h-9 w-9 place-items-center rounded-full bg-white/90 shadow-sm ring-1 ring-black/5 dark:bg-white/10 dark:ring-white/10"
                >
                    <span class="text-xl" aria-hidden>🏝️</span>
                </div>
                <div>
                    <p class="text-sm/5 font-semibold text-white drop-shadow">
                        Rifa Oficial
                    </p>
                    <p class="text-xs/4 text-white/80">
                        Viaje seguro a Isla de Margarita
                    </p>
                </div>
            </div>
            <nav class="flex items-center gap-2">
                <Link
                    v-if="$page.props.auth?.user"
                    :href="dashboard()"
                    class="rounded-md bg-white/90 px-3 py-1.5 text-sm font-medium text-teal-900 shadow transition hover:bg-white dark:bg-white/15 dark:text-white/90 dark:hover:bg-white/25"
                    >Dashboard</Link>
                <Link
                    v-if="$page.props.auth?.user"
                    :href="logout()"
                    class="rounded-md bg-white/90 px-3 py-1.5 text-sm font-medium text-teal-900 shadow transition hover:bg-white dark:bg-white/15 dark:text-white/90 dark:hover:bg-white/25"
                    >Salir</Link>
                <template v-else>
                    <Link
                        :href="login()"
                        class="rounded-md px-3 py-1.5 text-sm font-medium text-white/90 ring-1 ring-white/30 transition hover:bg-white/10"
                        >Entrar</Link>
                    <!--          <Link v-if="canRegister" :href="register()" class="rounded-md bg-white/90 px-3 py-1.5 text-sm font-medium text-teal-900 shadow hover:bg-white transition dark:bg-white/15 dark:text-white/90 dark:hover:bg-white/25">Crear cuenta</Link>-->
                </template>
            </nav>
        </header>

        <!-- Hero -->
        <section class="mx-auto max-w-6xl px-4 pt-4 pb-6 sm:pt-8">
            <div
                class="rounded-2xl bg-white/75 p-5 shadow-lg ring-1 ring-black/5 backdrop-blur-md dark:bg-white/10 dark:ring-white/10"
            >
                <h1
                    class="text-2xl font-bold text-teal-900 drop-shadow-sm dark:text-white"
                >
                    Rifa: Escápate a Margarita
                </h1>
                <p class="mt-1 text-sm text-teal-800/80 dark:text-white/80">
                    Elige tu número de la suerte entre 000 y 999. Plataforma
                    respaldada y segura.
                </p>
                <div class="mt-3 flex flex-wrap items-center gap-2 text-xs">
                    <span
                        class="inline-flex items-center gap-1 rounded-full bg-teal-600/90 px-2 py-1 font-medium text-white shadow"
                    >
                        <svg
                            width="14"
                            height="14"
                            viewBox="0 0 24 24"
                            fill="none"
                            class="opacity-90"
                            aria-hidden
                        >
                            <path
                                d="M20 7L9 18l-5-5"
                                stroke="currentColor"
                                stroke-width="2"
                                stroke-linecap="round"
                                stroke-linejoin="round"
                            />
                        </svg>
                        Pagos protegidos
                    </span>
                    <span
                        class="inline-flex items-center gap-1 rounded-full bg-sky-600/90 px-2 py-1 font-medium text-white shadow"
                    >
                        <svg
                            width="14"
                            height="14"
                            viewBox="0 0 24 24"
                            fill="none"
                            class="opacity-90"
                            aria-hidden
                        >
                            <path
                                d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"
                                stroke="currentColor"
                                stroke-width="2"
                                stroke-linecap="round"
                                stroke-linejoin="round"
                            />
                        </svg>
                        Sitio verificado
                    </span>
                    <span
                        class="inline-flex items-center gap-1 rounded-full bg-yellow-500/90 px-2 py-1 font-medium text-white shadow"
                    >
                        ✨ Premios reales
                    </span>
                </div>
            </div>
        </section>

        <!-- Numbers Grid -->
        <section class="mx-auto max-w-6xl px-4 pb-16">
            <div
                class="rounded-2xl bg-white/80 p-4 shadow-lg ring-1 ring-black/5 backdrop-blur-md dark:bg-white/5 dark:ring-white/10"
            >
                <div class="mb-3 flex items-center justify-between">
                    <h2
                        class="text-lg font-semibold text-teal-900 dark:text-white"
                    >
                        Números disponibles
                    </h2>
                    <div class="text-xs text-teal-800/80 dark:text-white/70">
                        Total: {{ numbers.length }}
                    </div>
                </div>
                <div
                    class="grid gap-2"
                    style="
                        grid-template-columns: repeat(
                            auto-fill,
                            minmax(56px, 1fr)
                        );
                    "
                >
                    <div
                        v-for="n in numbers"
                        :key="n"
                        class="group relative isolate overflow-hidden rounded-lg bg-white text-center text-sm font-semibold text-teal-900 ring-1 ring-black/5 transition hover:-translate-y-0.5 hover:shadow-md dark:bg-white/10 dark:text-white dark:ring-white/10"
                        :class="{ 'is-taken': taken.includes(n) }"
                    >
                        <div
                            class="absolute inset-0 -z-10 opacity-0 transition group-hover:opacity-100"
                            aria-hidden
                        >
                            <div
                                class="absolute -top-6 -left-6 h-12 w-12 rotate-45 bg-teal-400/40 blur-xl"
                            />
                            <div
                                class="absolute -right-6 -bottom-6 h-12 w-12 -rotate-45 bg-yellow-300/40 blur-xl"
                            />
                        </div>
                        <div class="px-2 py-2">
                            {{ n }}
                        </div>
                    </div>
                </div>
                <p
                    class="mt-4 text-center text-xs text-teal-800/70 dark:text-white/60"
                >
                    Visualizando 000–999
                </p>
            </div>
        </section>

        <footer
            class="border-t border-white/30 bg-white/20 py-6 text-center text-xs text-teal-900 backdrop-blur-md dark:border-white/10 dark:bg-white/5 dark:text-white/70"
        >
            © {{ new Date().getFullYear() }} Rifa Margarita — Operado con
            seguridad y transparencia.
        </footer>

    </div>
</template>

<style>
.is-taken {
    background-color: red;
}
</style>
