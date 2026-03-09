<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import axios from 'axios';
import { ref, onMounted, onUnmounted } from 'vue';

const canvasRef = ref<HTMLCanvasElement | null>(null);
const isPlaying = ref(false);
const mode = ref<'fortran' | 'php'>('fortran');
const grid = ref<number[]>(new Array(10000).fill(0));
const execTime = ref('0.00');
let animationFrame: number;
let isDrawing = false;

const initAtmosphere = () => {
    const newGrid = new Array(10000).fill(0);
    const n = 100;
    
    for (let i = 0; i < 5; i++) {
        const cx = Math.floor(Math.random() * 80) + 10;
        const cy = Math.floor(Math.random() * 80) + 10;
        
        for (let y = -8; y <= 8; y++) {
            for (let x = -8; x <= 8; x++) {
                if (x*x + y*y <= 64) {
                    newGrid[(cy + y) * n + (cx + x)] = 1.0;
                }
            }
        }
    }
    
    grid.value = newGrid;
    draw();
};

const draw = () => {
    const canvas = canvasRef.value;
    const ctx = canvas?.getContext('2d');
    
    if (!ctx || !canvas) {
        return;
    }

    const imgData = ctx.createImageData(100, 100);
    
    for (let i = 0; i < 10000; i++) {
        const v = Math.min(Math.max(grid.value[i], 0), 1);
        const h = (1.0 - v) * 240; 
        
        let r = 0, g = 0, b = 0;
        if (v > 0) {
            const rgb = hslToRgb(h / 360, 1, 0.5);
            r = rgb[0]; g = rgb[1]; b = rgb[2];
        } else {
            r = 5; g = 5; b = 15; 
        }

        imgData.data[i * 4] = r;
        imgData.data[i * 4 + 1] = g;
        imgData.data[i * 4 + 2] = b;
        imgData.data[i * 4 + 3] = v > 0 ? 255 : 100;
    }

    const offscreen = document.createElement('canvas');
    offscreen.width = 100;
    offscreen.height = 100;
    offscreen.getContext('2d')?.putImageData(imgData, 0, 0);

    ctx.imageSmoothingEnabled = false;
    ctx.drawImage(offscreen, 0, 0, 600, 600);
};

const hslToRgb = (h: number, s: number, l: number) => {
    let r, g, b;
    if (s === 0) {
        r = g = b = l;
    } else {
        const hue2rgb = (p: number, q: number, t: number) => {
            if (t < 0) t += 1;
            if (t > 1) t -= 1;
            if (t < 1/6) return p + (q - p) * 6 * t;
            if (t < 1/2) return q;
            if (t < 2/3) return p + (q - p) * (2/3 - t) * 6;
            return p;
        };
        const q = l < 0.5 ? l * (1 + s) : l + s - l * s;
        const p = 2 * l - q;
        r = hue2rgb(p, q, h + 1/3);
        g = hue2rgb(p, q, h);
        b = hue2rgb(p, q, h - 1/3);
    }
    return [Math.round(r * 255), Math.round(g * 255), Math.round(b * 255)];
};

const handleInteract = (e: MouseEvent) => {
    if (!isDrawing && e.type !== 'mousedown') return;
    isDrawing = e.type === 'mousedown' || e.type === 'mousemove';
    if (e.type === 'mouseup' || e.type === 'mouseleave') {
        isDrawing = false;
        return;
    }
    
    const rect = canvasRef.value?.getBoundingClientRect();
    if (!rect) return;
    
    const cx = Math.floor((e.clientX - rect.left) / 6);
    const cy = Math.floor((e.clientY - rect.top) / 6);
    
    for (let y = -3; y <= 3; y++) {
        for (let x = -3; x <= 3; x++) {
            if (cx + x >= 0 && cx + x < 100 && cy + y >= 0 && cy + y < 100 && x*x + y*y <= 9) {
                grid.value[(cy + y) * 100 + (cx + x)] = 1.0;
            }
        }
    }
    draw();
};

const updateSimulation = async () => {
    if (!isPlaying.value) {
        return;
    }
    
    const endpoint = mode.value === 'fortran' ? '/api/strato-simulate-fortran' : '/api/strato-simulate-php';
    const startTime = performance.now();

    try {
        const response = await axios.post(endpoint, { grid: grid.value });
        grid.value = response.data.grid;
        execTime.value = (performance.now() - startTime).toFixed(2);
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

onMounted(() => initAtmosphere());
onUnmounted(() => stopSimulation());
</script>

<template>
    <Head title="StratoMesh | Technical Demonstration" />

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
                <h1 class="text-6xl md:text-8xl font-bold tracking-tighter uppercase mb-2 text-white text-left">STRATOMESH</h1>
                <div class="h-[1px] w-full max-w-2xl bg-white/40"></div>
                <p class="mt-6 text-xl text-white/70 font-sans font-light tracking-wide max-w-xl italic text-left">
                    High-performance scalar field simulation for atmospheric data grids using the Laplacian advection-diffusion equation.
                </p>
            </header>

            <section class="flex flex-col lg:flex-row gap-12 items-center lg:items-start mb-24 w-full">
                <div class="relative group shrink-0 w-[600px] h-[600px] flex items-center justify-center cursor-crosshair">
                    <div class="absolute -inset-4 bg-white/5 rounded-[2rem] blur-2xl opacity-50 group-hover:opacity-100 transition duration-1000"></div>
                    <div class="relative bg-white/5 backdrop-blur-md border border-white/10 p-4 shadow-2xl overflow-hidden flex items-center justify-center rounded-lg">
                        <canvas 
                            ref="canvasRef" 
                            width="600" 
                            height="600"
                            class="bg-[#020205] block shadow-inner rounded-sm"
                            @mousedown="handleInteract"
                            @mousemove="handleInteract"
                            @mouseup="handleInteract"
                            @mouseleave="handleInteract"
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
                            <button @click="initAtmosphere" 
                                class="bg-white/10 backdrop-blur-sm border border-white/20 hover:bg-white/20 py-6 text-white font-sans font-bold uppercase tracking-[0.2em] transition-all">
                                Seed Storms
                            </button>
                            <button @click="stopSimulation" 
                                class="bg-white/5 border border-white/10 hover:bg-white/10 py-6 text-white font-sans font-bold uppercase tracking-[0.2em] transition-all">
                                Deactivate
                            </button>
                        </div>
                    </div>

                    <div class="grid grid-cols-3 gap-4 bg-white/5 border border-white/10 p-6">
                        <div class="text-center">
                            <p class="text-white/40 text-[9px] font-black uppercase tracking-widest mb-1">Compute Load</p>
                            <p class="text-white font-mono text-xs uppercase">500k Ops/Frame</p>
                        </div>
                        <div class="text-center border-x border-white/10">
                            <p class="text-white/40 text-[9px] font-black uppercase tracking-widest mb-1">Active Subsystem</p>
                            <p class="text-white font-mono text-xs uppercase">{{ isPlaying ? mode : 'Idle' }}</p>
                        </div>
                        <div class="text-center">
                            <p class="text-white/40 text-[9px] font-black uppercase tracking-widest mb-1">Latency</p>
                            <p class="text-white font-mono text-xs uppercase">{{ execTime }} ms</p>
                        </div>
                    </div>

                    <div class="font-sans text-white/70 leading-relaxed space-y-6 border-l border-white/20 pl-8">
                        <div class="py-2 space-y-4 text-left">
                            <p class="text-[10px] font-bold uppercase tracking-[0.2em] text-white/40">Grid Smoothing Equation</p>
                            <div class="text-2xl md:text-3xl font-serif italic text-white">
                                $$ \frac{\partial \phi}{\partial t} = \alpha \nabla^2 \phi $$
                            </div>
                        </div>

                        <div class="py-2 space-y-4 text-left">
                            <p class="text-[10px] font-bold uppercase tracking-[0.2em] text-white/40">Vectorized State Execution</p>
                            <div class="text-lg md:text-xl font-mono text-white/90 bg-white/5 p-4 rounded border border-white/10 leading-relaxed">
                                $$ \phi_{n+1} = \phi_n + D \left( \phi_{i \pm 1, j} + \phi_{i, j \pm 1} - 4\phi_{i,j} \right) $$
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <section class="w-full max-w-5xl border-t border-white/10 pt-16 grid grid-cols-1 md:grid-cols-2 gap-16 pb-32">
                <div class="space-y-8 text-left">
                    <h3 class="text-3xl font-bold tracking-tight uppercase">Meteorological Load</h3>
                    <div class="font-sans text-white/60 space-y-4 leading-relaxed font-light text-sm text-left">
                        <p>StratoMesh relies on a continuous 10,000-cell scalar field to represent atmospheric density. The system calculates 50 internal diffusion steps per rendered frame, mirroring the mathematical load of the Weather Research and Forecasting (WRF) model.</p>
                        <p>You can interact with the environment by clicking and dragging on the grid to create artificial high-pressure zones, watching how the engine naturally diffuses the atmosphere.</p>
                    </div>
                </div>

                <div class="space-y-8 text-left">
                    <h3 class="text-3xl font-bold tracking-tight uppercase">Language Architecture</h3>
                    <div class="font-sans text-white/60 space-y-4 leading-relaxed font-light text-sm text-left">
                        <p>This demonstration contrasts interpreted iteration with Fortran's hardware-level array operations.</p>
                        <ul class="space-y-4">
                            <li class="border-l-2 border-white/20 pl-4 text-left">
                                <strong class="text-white block mb-1 uppercase text-[10px] tracking-widest">Fortran Binary</strong>
                                Calculates all 10,000 cells simultaneously using native multidimensional array slicing. Its speed completely negates the massive penalty of file I/O operations.
                            </li>
                            <li class="border-l-2 border-white/20 pl-4 text-left">
                                <strong class="text-white block mb-1 uppercase text-[10px] tracking-widest">PHP Runtime</strong>
                                Requires sequential manual calculation of 500,000 individual neighbor checks per frame. Despite having zero I/O penalty, the sheer volume of iterations causes visible CPU strain.
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