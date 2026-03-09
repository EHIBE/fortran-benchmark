<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';

const tools = [
    {
        name: 'Performance Benchmark',
        desc: 'Comparative analysis of computational throughput between PHP and Fortran 90.',
        url: '/benchmark',
        icon: 'M13 10V3L4 14h7v7l9-11h-7z'
    },
    {
        name: 'ThermalForge',
        desc: 'Real-time 2D heat diffusion utilizing discrete numerical method solvers.',
        url: '/heat-simulator',
        icon: 'M17.657 18.657A8 8 0 016.343 7.343S7 9 9 10c0-2 .5-5 2.986-7C14 5 16.09 5.777 17.656 7.343A7.975 7.975 0 0120 13a7.975 7.975 0 01-2.343 5.657z'
    },
    {
        name: 'GalaxyEngine',
        desc: 'Advanced N-body gravity simulator processing millions of orbital interactions.',
        url: '/galaxy-engine',
        icon: 'M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z'
    },
    {
        name: 'StratoMesh',
        desc: 'Scalar field simulation for atmospheric grids using Laplacian equations.',
        url: '/strato-mesh',
        icon: 'M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z'
    }
];
</script>

<template>
    <Head title="Fortran | Technical Demonstration" />
    
    <div class="min-h-screen bg-[#734d8e] flex flex-col relative font-serif antialiased overflow-x-hidden text-white">
        <div class="absolute right-0 top-0 bottom-0 w-[20%] pointer-events-none overflow-hidden hidden md:block bg-[#eaf0f8]">
            <div class="h-full w-full opacity-30" style="background-image: url('data:image/svg+xml,%3Csvg width=\'24\' height=\'12\' viewBox=\'0 0 24 12\' xmlns=\'http://www.w3.org/2000/svg\'%3E%3Cpath d=\'M0 6 Q 6 0, 12 6 T 24 6\' stroke=\'%233b82f6\' fill=\'none\' stroke-width=\'1.5\'/%3E%3C/svg%3E'); background-repeat: repeat;"></div>
        </div>

        <main class="w-full md:w-[80%] flex-1 flex flex-col pl-8 pr-8 md:pl-24 lg:pl-32 md:pr-24 py-16 relative z-10">
            <header class="mt-12 md:mt-24 mb-16 flex flex-col items-start">
                <h1 class="text-7xl md:text-[8.5rem] lg:text-[11rem] font-serif tracking-wide uppercase mb-6 text-white leading-none">
                    FORTRAN
                </h1>
                <div class="h-[2px] w-full max-w-[42rem] bg-black/50"></div>
            </header>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-16 w-full max-w-5xl">
                <Link 
                    v-for="tool in tools" 
                    :key="tool.url" 
                    :href="tool.url"
                    class="group relative bg-white/5 backdrop-blur-sm border border-white/10 p-8 transition-all duration-500 hover:bg-white/10 hover:border-white/30"
                >
                    <div class="relative z-10 font-sans h-full flex flex-col">
                        <div class="w-10 h-10 bg-white/10 flex items-center justify-center mb-6 group-hover:bg-white group-hover:text-[#734d8e] transition-all duration-300">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" :d="tool.icon" />
                            </svg>
                        </div>
                        
                        <h2 class="text-xl font-bold uppercase tracking-widest mb-4 group-hover:translate-x-1 transition-transform duration-300 text-left">
                            {{ tool.name }}
                        </h2>
                        <p class="text-white/60 text-sm font-light leading-relaxed mb-8 flex-grow text-left">
                            {{ tool.desc }}
                        </p>
                        
                        <div class="flex items-center justify-start text-[10px] font-bold uppercase tracking-[0.3em] text-white/40 group-hover:text-white transition-colors mt-auto">
                            Launch Matrix
                            <span class="ml-2 group-hover:translate-x-2 transition-transform duration-300">→</span>
                        </div>
                    </div>
                </Link>
            </div>

            <footer class="mt-auto flex flex-col items-start w-full max-w-5xl pt-8 pb-8">
                <div class="font-sans text-sm md:text-base font-bold tracking-[0.15em] text-white uppercase space-y-2 mb-6">
                    <p>MAQUILANG, DERICK XERXES</p>
                    <p>RUYERAS, ELLA BIANCA</p>
                </div>
                
                <p class="text-white/80 font-sans font-light tracking-wide text-sm md:text-base italic max-w-3xl mb-8 leading-relaxed text-left">
                    A technical demonstration showcasing high-performance scientific computing within a modern web ecosystem. Designed to analyze computational throughput and real-time numerical solvers natively processed in Fortran.
                </p>
                
                <div class="w-full border-t border-white/10 pt-6">
                    <div class="font-sans text-[10px] tracking-[0.5em] text-white/30 uppercase italic text-left">
                        CS-EPC 323: Design and Implementation of Programming Languages
                    </div>
                </div>
            </footer>
        </main>
    </div>
</template>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Crimson+Pro:wght@300;400;600&display=swap');

.font-serif {
    font-family: 'Crimson Pro', serif;
}

.font-sans {
    font-family: ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
}
</style>