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
        name: 'StratoMesh',
        desc: 'Scalar field simulation for atmospheric grids using Laplacian equations.',
        url: '/strato-mesh',
        icon: 'M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z'
    },
    {
        name: 'GalaxyEngine',
        desc: 'Advanced N-body gravity simulator processing millions of orbital interactions.',
        url: '/galaxy-engine',
        icon: 'M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z'
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
