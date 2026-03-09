<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import axios from 'axios';
import { ref } from 'vue';

interface benchmarkresults {
    fortran: number;
    php: number;
    python: number;
}

const results = ref<benchmarkresults | null>(null);
const isLoading = ref(false);

const runTest = async () => {
    isLoading.value = true;
    try {
        const response = await axios.get<benchmarkresults>('/api/benchmark');
        results.value = response.data;
    } catch (error) {
        console.error(error);
    }
    isLoading.value = false;
};
</script>

<template>
    <Head title="Performance Benchmark | Technical Demonstration" />
    
    <div class="min-h-screen bg-[#734d8e] flex flex-col relative font-serif antialiased overflow-x-hidden text-white">
        <div class="absolute right-0 top-0 bottom-0 w-[15%] bg-white/10 pointer-events-none overflow-hidden hidden md:block">
            <div class="h-full w-full opacity-30 flex flex-col justify-around py-4">
                <svg v-for="i in 40" :key="i" width="100%" height="20" viewBox="0 0 100 20" preserveAspectRatio="none">
                    <path d="M0 10 Q 25 20, 50 10 T 100 10" stroke="white" fill="transparent" stroke-width="0.5" />
                </svg>
            </div>
        </div>

        <main class="flex-1 flex flex-col px-8 md:px-20 lg:px-32 py-16 relative z-10 max-w-7xl w-full mx-auto">
            <header class="mb-12">
                <Link href="/" class="text-white/50 hover:text-white transition-colors uppercase text-[10px] tracking-[0.4em] mb-8 inline-block text-left">← Back to Portal</Link>
                <div class="w-24 h-1 bg-white mb-8"></div>
                <h1 class="text-6xl md:text-8xl font-bold tracking-tighter uppercase mb-2 text-white text-left">BENCHMARK</h1>
                <div class="h-[1px] w-full max-w-2xl bg-white/40"></div>
                <p class="mt-6 text-xl text-white/70 font-sans font-light tracking-wide max-w-xl italic text-left">
                    A comparative analysis of computational throughput across statically compiled and dynamically interpreted environments.
                </p>
            </header>

            <section class="flex flex-col gap-12 mb-24 w-full">
                <div class="flex flex-col gap-8 w-full">
                    <div class="grid grid-cols-1 gap-4 max-w-md">
                        <button 
                            @click="runTest" 
                            :disabled="isLoading"
                            class="bg-white text-[#734d8e] font-sans font-black uppercase tracking-[0.3em] py-6 transition-all hover:bg-opacity-90 active:scale-[0.98] shadow-xl disabled:opacity-50"
                        >
                            {{ isLoading ? 'Executing 50M Cycles...' : 'Initialize Benchmark' }}
                        </button>
                    </div>

                    <div v-if="results" class="grid grid-cols-1 md:grid-cols-3 gap-6 w-full">
                        <div class="bg-white/5 backdrop-blur-md border border-white/10 p-8 transition-all hover:bg-white/10">
                            <p class="font-sans text-[10px] font-bold uppercase tracking-[0.3em] text-white/40 mb-2 text-left">Native Binary</p>
                            <h2 class="text-2xl font-bold uppercase mb-6 text-left">Fortran 90</h2>
                            <div class="text-5xl font-black tracking-tighter text-white text-left">{{ results.fortran }}<span class="text-xl ml-1 font-light opacity-50">s</span></div>
                        </div>
                        
                        <div class="bg-white/5 backdrop-blur-md border border-white/10 p-8 transition-all hover:bg-white/10">
                            <p class="font-sans text-[10px] font-bold uppercase tracking-[0.3em] text-white/40 mb-2 text-left">Server Script</p>
                            <h2 class="text-2xl font-bold uppercase mb-6 text-left">PHP 8.5</h2>
                            <div class="text-5xl font-black tracking-tighter text-white text-left">{{ results.php }}<span class="text-xl ml-1 font-light opacity-50">s</span></div>
                        </div>

                        <div class="bg-white/5 backdrop-blur-md border border-white/10 p-8 transition-all hover:bg-white/10">
                            <p class="font-sans text-[10px] font-bold uppercase tracking-[0.3em] text-white/40 mb-2 text-left">Interpreted</p>
                            <h2 class="text-2xl font-bold uppercase mb-6 text-left">Python 3</h2>
                            <div class="text-5xl font-black tracking-tighter text-white text-left">{{ results.python }}<span class="text-xl ml-1 font-light opacity-50">s</span></div>
                        </div>
                    </div>

                    <div class="font-sans text-white/70 leading-relaxed space-y-6 border-l border-white/20 pl-8 w-full max-w-2xl mt-4">
                        <div class="py-2 space-y-4 text-left">
                            <p class="text-[10px] font-bold uppercase tracking-[0.2em] text-white/40">The Test Function</p>
                            <div class="text-2xl md:text-3xl font-serif italic text-white">
                                $$ \sum_{i=1}^{50,000,000} \sqrt{i} $$
                            </div>
                        </div>
                        <p class="text-sm font-light text-white/60 text-left">
                            This heavy numerical loop tests the raw Floating-Point processing capability of each runtime environment.
                        </p>
                    </div>
                </div>
            </section>

            <section class="w-full max-w-5xl border-t border-white/10 pt-16 grid grid-cols-1 md:grid-cols-2 gap-16 pb-32">
                <div class="space-y-8 text-left">
                    <h3 class="text-3xl font-bold tracking-tight uppercase">Compiled vs. Interpreted</h3>
                    <div class="font-sans text-white/60 space-y-4 leading-relaxed font-light text-sm text-left">
                        <p>The benchmark illustrates the core difference between <strong>Static Compilation</strong> and <strong>Dynamic Interpretation</strong>. Fortran converts source code directly into machine-level instructions before execution.</p>
                        <p>In contrast, PHP and Python must translate instructions during runtime. This "Translation Tax" results in a massive performance delta when executing millions of sequential operations.</p>
                    </div>
                </div>

                <div class="space-y-8 text-left">
                    <h3 class="text-3xl font-bold tracking-tight uppercase">Implementation Details</h3>
                    <div class="font-sans text-white/60 space-y-4 leading-relaxed font-light text-sm text-left">
                        <p>To ensure a fair comparison, all three implementations utilize the same algorithm and data precision (Double Precision FP64).</p>
                        <ul class="space-y-4">
                            <li class="border-l-2 border-white/20 pl-4 text-left">
                                <strong class="text-white block mb-1 uppercase text-[10px] tracking-widest">Execution Path</strong>
                                Laravel invokes the native Fortran binary via a system-level shell command, capturing the high-resolution output stream directly.
                            </li>
                            <li class="border-l-2 border-white/20 pl-4 text-left">
                                <strong class="text-white block mb-1 uppercase text-[10px] tracking-widest">Resource Utilization</strong>
                                Fortran leverages CPU instruction piping and register-level optimizations, bypassing the overhead of virtual machines or garbage collectors.
                            </li>
                        </ul>
                    </div>
                </div>
            </section>

            <footer class="mt-auto pt-20 grid grid-cols-1 md:grid-cols-2 gap-8 items-end w-full">
                <div class="font-sans space-y-2 text-xs font-medium tracking-[0.25em] text-white/50 uppercase text-left">
                    <p class="text-white/90">MAQUILANG, DERICK XERXES</p>
                    <p class="text-white/90">RUYERAS, ELLA BIANCA</p>
                </div>
                <div class="hidden md:flex justify-end font-sans text-[10px] tracking-[0.5em] text-white/30 uppercase italic whitespace-nowrap">
                    CS-EPC 323: Design and Implementation of Programming Languages
                </div>
            </footer>
        </main>
    </div>
</template>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Crimson+Pro:wght@200;400;700&display=swap');
.font-serif { font-family: 'Crimson Pro', serif; }
.font-sans { font-family: ui-sans-serif, system-ui, -apple-system, sans-serif; }
h1 { line-height: 0.8; }
</style>
