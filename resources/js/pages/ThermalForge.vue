<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import axios from 'axios';
import { ref, onMounted, onUnmounted } from 'vue';

const canvasRef = ref<HTMLCanvasElement | null>(null);
const isPlaying = ref(false);
const isDrawing = ref(false);
const gridSize = 50;
const cellSize = 12;
const grid = ref(Array.from({ length: gridSize }, () => Array(gridSize).fill(0.0)));
let drawLoop: number | null = null;
const lastMousePos = { x: 0, y: 0 };

const getThermalColor = (temp: number) => {
    const t = Math.max(0, Math.min(100, temp));
    if (t <= 0) return '#020205';
    let r, g, b;
    if (t < 25) {
        r = Math.floor(t * 2);
        g = 0;
        b = Math.floor(t * 8);
    } else if (t < 50) {
        r = Math.floor((t - 25) * 10);
        g = 0;
        b = 200;
    } else if (t < 75) {
        r = 255;
        g = Math.floor((t - 50) * 8);
        b = Math.floor(200 - (t - 50) * 8);
    } else {
        r = 255;
        g = 255;
        b = Math.floor((t - 75) * 10);
    }
    return `rgb(${r}, ${g}, ${b})`;
};

const drawGrid = () => {
    const canvas = canvasRef.value;
    const ctx = canvas?.getContext('2d');
    if (!ctx || !canvas) return;
    ctx.clearRect(0, 0, canvas.width, canvas.height);
    for (let i = 0; i < gridSize; i++) {
        for (let j = 0; j < gridSize; j++) {
            ctx.fillStyle = getThermalColor(grid.value[i][j]);
            ctx.fillRect(j * cellSize, i * cellSize, cellSize, cellSize);
        }
    }
    if (isDrawing.value) {
        ctx.beginPath();
        ctx.arc(lastMousePos.x * cellSize + cellSize / 2, lastMousePos.y * cellSize + cellSize / 2, cellSize * 2, 0, Math.PI * 2);
        ctx.strokeStyle = 'rgba(255, 255, 255, 0.3)';
        ctx.lineWidth = 1;
        ctx.stroke();
    }
};

const applyHeatAtPoint = (x: number, y: number) => {
    if (x < 0 || x >= gridSize || y < 0 || y >= gridSize) return;
    for (let i = -2; i <= 2; i++) {
        for (let j = -2; j <= 2; j++) {
            const ny = y + i;
            const nx = x + j;
            if (nx >= 0 && nx < gridSize && ny >= 0 && ny < gridSize) {
                const dist = Math.sqrt(i * i + j * j);
                const addedHeat = Math.max(0, (2.5 - dist) * 45);
                grid.value[ny][nx] = Math.min(100, grid.value[ny][nx] + addedHeat);
            }
        }
    }
};

const startDrawing = (event: MouseEvent) => {
    isDrawing.value = true;
    updateMousePos(event);
    drawLoop = window.setInterval(() => {
        applyHeatAtPoint(lastMousePos.x, lastMousePos.y);
        if (!isPlaying.value) drawGrid();
    }, 16);
};

const updateMousePos = (event: MouseEvent) => {
    const rect = canvasRef.value?.getBoundingClientRect();
    if (!rect) return;
    lastMousePos.x = Math.floor((event.clientX - rect.left) / cellSize);
    lastMousePos.y = Math.floor((event.clientY - rect.top) / cellSize);
    
    if (isDrawing.value) {
        applyHeatAtPoint(lastMousePos.x, lastMousePos.y);
        if (!isPlaying.value) drawGrid();
    }
};

const stopDrawing = () => {
    isDrawing.value = false;
    if (drawLoop) clearInterval(drawLoop);
    if (!isPlaying.value) drawGrid();
};

const step = async () => {
    if (!isPlaying.value) return;
    try {
        const res = await axios.post('/api/simulate-heat', { grid: grid.value });
        if (res.data.grid && Array.isArray(res.data.grid) && res.data.grid.length === 50) {
            grid.value = res.data.grid;
            drawGrid();
        }
        requestAnimationFrame(step);
    } catch {
        isPlaying.value = false;
    }
};

const toggle = () => {
    isPlaying.value = !isPlaying.value;
    if (isPlaying.value) step();
};

const reset = () => {
    isPlaying.value = false;
    grid.value = Array.from({ length: gridSize }, () => Array(gridSize).fill(0.0));
    drawGrid();
};

onMounted(() => drawGrid());
onUnmounted(() => {
    isPlaying.value = false;
    if (drawLoop) clearInterval(drawLoop);
});
</script>

<template>
    <Head title="ThermalForge | Technical Demonstration" />

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
                <h1 class="text-6xl md:text-8xl font-bold tracking-tighter uppercase mb-2 text-white text-left">THERMALFORGE</h1>
                <div class="h-[1px] w-full max-w-2xl bg-white/40"></div>
                <p class="mt-6 text-xl text-white/70 font-sans font-light tracking-wide max-w-xl italic text-left">
                    Real-time 2D heat diffusion utilizing discrete numerical method solvers within a native Fortran environment.
                </p>
            </header>

            <section class="flex flex-col lg:flex-row gap-12 items-center lg:items-start mb-24 w-full">
                <div class="relative group shrink-0 w-[600px] h-[600px] flex items-center justify-center cursor-crosshair">
                    <div class="absolute -inset-4 bg-white/5 rounded-[2rem] blur-2xl opacity-50 group-hover:opacity-100 transition duration-1000"></div>
                    <div class="relative bg-white/5 backdrop-blur-md border border-white/10 p-4 shadow-2xl overflow-hidden flex items-center justify-center rounded-lg">
                        <canvas 
                            ref="canvasRef" 
                            :width="gridSize * cellSize" 
                            :height="gridSize * cellSize"
                            @mousedown="startDrawing"
                            @mousemove="updateMousePos"
                            @mouseup="stopDrawing"
                            @mouseleave="stopDrawing"
                            class="bg-[#020205] block shadow-inner rounded-sm"
                        ></canvas>
                    </div>
                </div>

                <div class="flex-1 flex flex-col gap-8 w-full max-w-lg">
                    <div class="grid grid-cols-1 gap-4">
                        <div class="grid grid-cols-2 gap-4">
                            <button @click="toggle" 
                                class="bg-white text-[#734d8e] font-sans font-black uppercase tracking-[0.3em] py-6 transition-all hover:bg-opacity-90 active:scale-[0.98] shadow-xl disabled:opacity-50">
                                {{ isPlaying ? 'Deactivate' : 'Initialize' }}
                            </button>
                            <button @click="reset" 
                                class="bg-white/10 backdrop-blur-sm border border-white/20 hover:bg-white/20 py-6 text-white font-sans font-bold uppercase tracking-[0.2em] transition-all">
                                Reset Matrix
                            </button>
                        </div>
                    </div>

                    <div class="grid grid-cols-3 gap-4 bg-white/5 border border-white/10 p-6">
                        <div class="text-center">
                            <p class="text-white/40 text-[9px] font-black uppercase tracking-widest mb-1">Architecture</p>
                            <p class="text-white font-mono text-xs uppercase">x64-Fortran</p>
                        </div>
                        <div class="text-center border-x border-white/10">
                            <p class="text-white/40 text-[9px] font-black uppercase tracking-widest mb-1">Subsystem</p>
                            <p class="text-white font-mono text-xs uppercase">Laravel-FPM</p>
                        </div>
                        <div class="text-center">
                            <p class="text-white/40 text-[9px] font-black uppercase tracking-widest mb-1">Method</p>
                            <p class="text-white font-mono text-xs uppercase">FTCS Diff</p>
                        </div>
                    </div>

                    <div class="font-sans text-white/70 leading-relaxed space-y-6 border-l border-white/20 pl-8">
                        <div class="py-2 space-y-4 text-left">
                            <p class="text-[10px] font-bold uppercase tracking-[0.2em] text-white/40">Continuous Model</p>
                            <div class="text-2xl md:text-3xl font-serif italic text-white">
                                $$ \frac{\partial u}{\partial t} = \alpha \nabla^2 u $$
                            </div>
                        </div>

                        <div class="py-2 space-y-4 text-left">
                            <p class="text-[10px] font-bold uppercase tracking-[0.2em] text-white/40">Discrete Approximation (FTCS)</p>
                            <div class="text-sm md:text-base font-mono text-white/90 bg-white/5 p-4 rounded border border-white/10 leading-relaxed">
                                $$ u_{i,j}^{n+1} = u_{i,j}^n + \alpha \left( u_{i+1,j}^n + u_{i-1,j}^n + u_{i,j+1}^n + u_{i,j-1}^n - 4u_{i,j}^n \right) $$
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <section class="w-full max-w-5xl border-t border-white/10 pt-16 grid grid-cols-1 md:grid-cols-2 gap-16 pb-32">
                <div class="space-y-8 text-left">
                    <h3 class="text-3xl font-bold tracking-tight uppercase">Thermal Dynamics</h3>
                    <div class="font-sans text-white/60 space-y-4 leading-relaxed font-light text-sm text-left">
                        <p>ThermalForge models the physical distribution of temperature across a 2D surface. Using the Laplacian operator ($\nabla^2$), the system compares the heat of a single cell against its four cardinal neighbors.</p>
                        <p>If a cell is hotter than its surroundings, energy is transferred outward; if colder, it absorbs energy. Over time, the system moves toward <strong>Thermal Equilibrium</strong>, where heat is distributed uniformly across the entire grid.</p>
                    </div>
                </div>

                <div class="space-y-8 text-left">
                    <h3 class="text-3xl font-bold tracking-tight uppercase">Performance Engineering</h3>
                    <div class="font-sans text-white/60 space-y-4 leading-relaxed font-light text-sm text-left">
                        <p>This simulation processes a 50x50 mesh, requiring 2,500 independent floating-point calculations for every single timestep. The Fortran engine executes 10 internal sub-steps per frame to balance the diffusion velocity against real-time input.</p>
                        <ul class="space-y-4">
                            <li class="border-l-2 border-white/20 pl-4 text-left">
                                <strong class="text-white block mb-1 uppercase text-[10px] tracking-widest">Fortran Vectorization</strong>
                                By utilizing statically allocated 2D arrays and aggressive compiler optimizations, the backend handles exactly 25,000 thermal operations per API call near-instantly.
                            </li>
                            <li class="border-l-2 border-white/20 pl-4 text-left">
                                <strong class="text-white block mb-1 uppercase text-[10px] tracking-widest">Stability Constraint</strong>
                                The diffusivity constant ($\alpha$) is capped at 0.24 to satisfy the stability criterion of the explicit finite difference method, preventing numerical divergence.
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
canvas { border-radius: 4px; image-rendering: pixelated; }
</style>
