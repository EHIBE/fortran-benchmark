<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import axios from 'axios';
import { ref, onMounted, onUnmounted } from 'vue';

interface Particle {
    x: number;
    y: number;
    vx: number;
    vy: number;
    m: number;
}

const canvasRef = ref<HTMLCanvasElement | null>(null);
const isPlaying = ref(false);
const mode = ref<'fortran' | 'php'>('fortran');
const particleCount = 1000;
const particles = ref<Particle[]>([]);
let animationFrame: number;

const initGalaxy = () => {
    const newParticles: Particle[] = [];
    const centerX = 300;
    const centerY = 300;
    for (let i = 0; i < particleCount; i++) {
        const angle = Math.random() * Math.PI * 2;
        const radius = 20 + Math.random() * 220;
        const speed = Math.sqrt(600000 / radius) * 0.14; 
        newParticles.push({
            x: centerX + Math.cos(angle) * radius,
            y: centerY + Math.sin(angle) * radius,
            vx: -Math.sin(angle) * speed,
            vy: Math.cos(angle) * speed,
            m: 10 + Math.random() * 50
        });
    }
    particles.value = newParticles;
    draw();
};

const draw = () => {
    const canvas = canvasRef.value;
    const ctx = canvas?.getContext('2d');
    if (!ctx || !canvas) return;
    ctx.fillStyle = 'rgba(5, 5, 15, 0.4)';
    ctx.fillRect(0, 0, canvas.width, canvas.height);
    ctx.fillStyle = '#fde047';
    ctx.shadowBlur = 20;
    ctx.shadowColor = '#fde047';
    ctx.beginPath();
    ctx.arc(300, 300, 6, 0, Math.PI * 2);
    ctx.fill();
    ctx.shadowBlur = 0;
    particles.value.forEach(p => {
        ctx.beginPath();
        const size = Math.max(0.5, p.m / 25);
        ctx.fillStyle = mode.value === 'fortran' ? '#60a5fa' : '#f87171';
        ctx.arc(p.x, p.y, size, 0, Math.PI * 2);
        ctx.fill();
    });
};

const updateSimulation = async () => {
    if (!isPlaying.value) return;
    const endpoint = mode.value === 'fortran' ? '/api/simulate-fortran' : '/api/simulate-php';
    try {
        const response = await axios.post(endpoint, { particles: particles.value });
        particles.value = response.data.particles;
        draw();
        animationFrame = requestAnimationFrame(updateSimulation);
    } catch {
        isPlaying.value = false;
    }
};

const startSimulation = (selectedMode: 'fortran' | 'php') => {
    mode.value = selectedMode;
    if (!isPlaying.value) {
        isPlaying.value = true;
        updateSimulation();
    }
};

const stopSimulation = () => {
    isPlaying.value = false;
    cancelAnimationFrame(animationFrame);
};

onMounted(() => initGalaxy());
onUnmounted(() => stopSimulation());
</script>

<template>
    <Head title="GalaxyEngine | Technical Demonstration" />

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
                <h1 class="text-6xl md:text-8xl font-bold tracking-tighter uppercase mb-2 text-white text-left">GALAXYENGINE</h1>
                <div class="h-[1px] w-full max-w-2xl bg-white/40"></div>
                <p class="mt-6 text-xl text-white/70 font-sans font-light tracking-wide max-w-xl italic text-left">
                    High-density N-body gravitational solver calculating orbital dynamics across massive particle arrays.
                </p>
            </header>

            <section class="flex flex-col lg:flex-row gap-12 items-center lg:items-start mb-24 w-full">
                <div class="relative group shrink-0 w-[600px] h-[600px] flex items-center justify-center">
                    <div class="absolute -inset-4 bg-white/5 rounded-[2rem] blur-2xl opacity-50 group-hover:opacity-100 transition duration-1000"></div>
                    <div class="relative bg-white/5 backdrop-blur-md border border-white/10 p-4 shadow-2xl overflow-hidden flex items-center justify-center rounded-lg">
                        <canvas 
                            ref="canvasRef" 
                            width="600" 
                            height="600"
                            class="bg-[#020205] block shadow-inner rounded-sm"
                        ></canvas>
                    </div>
                </div>

                <div class="flex-1 flex flex-col gap-8 w-full max-w-lg">
                    <div class="grid grid-cols-1 gap-4">
                        <div class="grid grid-cols-2 gap-4">
                            <button @click="startSimulation('fortran')" 
                                :disabled="isPlaying && mode === 'fortran'"
                                class="bg-white text-[#734d8e] font-sans font-black uppercase tracking-[0.2em] py-6 transition-all hover:bg-opacity-90 active:scale-[0.98] shadow-xl disabled:opacity-50">
                                Run Fortran
                            </button>
                            <button @click="startSimulation('php')" 
                                :disabled="isPlaying && mode === 'php'"
                                class="bg-[#2d1b3d] text-white border border-white/10 font-sans font-black uppercase tracking-[0.2em] py-6 transition-all hover:bg-[#1f122b] active:scale-[0.98] shadow-xl disabled:opacity-50">
                                Run PHP
                            </button>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <button @click="initGalaxy" 
                                class="bg-white/10 backdrop-blur-sm border border-white/20 hover:bg-white/20 py-6 text-white font-sans font-bold uppercase tracking-[0.2em] transition-all">
                                Reset Matrix
                            </button>
                            <button @click="stopSimulation" 
                                class="bg-white/5 border border-white/10 hover:bg-white/10 py-6 text-white font-sans font-bold uppercase tracking-[0.2em] transition-all">
                                Deactivate
                            </button>
                        </div>
                    </div>

                    <div class="grid grid-cols-3 gap-4 bg-white/5 border border-white/10 p-6">
                        <div class="text-center">
                            <p class="text-white/40 text-[9px] font-black uppercase tracking-widest mb-1">Algorithm</p>
                            <p class="text-white font-mono text-xs uppercase">O(N²) Solver</p>
                        </div>
                        <div class="text-center border-x border-white/10">
                            <p class="text-white/40 text-[9px] font-black uppercase tracking-widest mb-1">Active Subsystem</p>
                            <p class="text-white font-mono text-xs uppercase">{{ isPlaying ? mode : 'Idle' }}</p>
                        </div>
                        <div class="text-center">
                            <p class="text-white/40 text-[9px] font-black uppercase tracking-widest mb-1">Density</p>
                            <p class="text-white font-mono text-xs uppercase">1,000 Nodes</p>
                        </div>
                    </div>

                    <div class="font-sans text-white/70 leading-relaxed space-y-6 border-l border-white/20 pl-8">
                        <div class="py-2 space-y-4 text-left">
                            <p class="text-[10px] font-bold uppercase tracking-[0.2em] text-white/40">Gravitational Constant</p>
                            <div class="text-2xl md:text-3xl font-serif italic text-white">
                                $$ F = G \frac{m_1 m_2}{r^2} $$
                            </div>
                        </div>

                        <div class="py-2 space-y-4 text-left">
                            <p class="text-[10px] font-bold uppercase tracking-[0.2em] text-white/40">Velocity Vector</p>
                            <div class="text-lg md:text-xl font-mono text-white/90 bg-white/5 p-4 rounded border border-white/10 leading-relaxed">
                                $$ \vec{v}_{n+1} = \vec{v}_n + \left( \frac{\sum \vec{F}}{m} \right) \Delta t $$
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <section class="w-full max-w-5xl border-t border-white/10 pt-16 grid grid-cols-1 md:grid-cols-2 gap-16 pb-32">
                <div class="space-y-8 text-left">
                    <h3 class="text-3xl font-bold tracking-tight uppercase">The N-Body Challenge</h3>
                    <div class="font-sans text-white/60 space-y-4 leading-relaxed font-light text-sm text-left">
                        <p>GalaxyEngine calculates the mutual gravitational attraction between 1,000 distinct particles. This is a classic problem in celestial mechanics that requires calculating the distance and resulting force for every possible particle pair.</p>
                        <p>With 1,000 particles, the system must process <strong>1,000,000 interactions</strong> per frame. This exponential scaling ($O(N^2)$) quickly exceeds the processing capabilities of high-level languages as $N$ increases.</p>
                    </div>
                </div>

                <div class="space-y-8 text-left">
                    <h3 class="text-3xl font-bold tracking-tight uppercase">Language Architecture</h3>
                    <div class="font-sans text-white/60 space-y-4 leading-relaxed font-light text-sm text-left">
                        <p>This demonstration contrasts the execution models of compiled and interpreted runtimes under extreme mathematical load.</p>
                        <ul class="space-y-4">
                            <li class="border-l-2 border-white/20 pl-4 text-left">
                                <strong class="text-white block mb-1 uppercase text-[10px] tracking-widest">Fortran Binary</strong>
                                Pre-compiled into machine code with nested loop optimization, allowing the CPU to execute millions of floating-point operations.
                            </li>
                            <li class="border-l-2 border-white/20 pl-4 text-left">
                                <strong class="text-white block mb-1 uppercase text-[10px] tracking-widest">PHP Runtime</strong>
                                The Zend Engine must parse and translate code into opcodes at runtime, introducing significant overhead per step.
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
canvas { border-radius: 4px; }
</style>
