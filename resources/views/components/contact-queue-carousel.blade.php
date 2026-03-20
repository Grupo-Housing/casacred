{{-- Panel flotante arrastrable de Cola de Contacto --}}
<div 
    x-data="contactCarousel()" 
    x-init="loadQueue()"
    id="contact-queue-panel"
    style="position: fixed; bottom: 24px; right: 24px; width: 340px; z-index: 9999; cursor: default;"
    class="bg-white rounded-xl shadow-2xl border border-gray-200 overflow-hidden"
    x-show="panelVisible"
    x-transition>

    {{-- Header arrastrable --}}
    <div 
        id="panel-header"
        class="flex items-center justify-between px-4 py-3 bg-gray-800 text-white select-none"
        style="cursor: grab;">
        <div class="flex items-center gap-2">
            <span class="text-sm">📋</span>
            <span class="text-sm font-semibold">Cola de Contacto</span>
            <span class="text-xs bg-gray-600 px-2 py-0.5 rounded-full" x-text="total + ' pendientes'"></span>
        </div>
        <div class="flex items-center gap-2">
            {{-- Minimizar --}}
            <button @click="minimized = !minimized" class="text-gray-300 hover:text-white text-xs focus:outline-none" title="Minimizar">
                <span x-text="minimized ? '▲' : '▼'"></span>
            </button>
            {{-- Cerrar --}}
            <button @click="panelVisible = false" class="text-gray-300 hover:text-white focus:outline-none" title="Cerrar">✕</button>
        </div>
    </div>

    {{-- Cuerpo colapsable --}}
    <div x-show="!minimized" x-transition>

        {{-- Loading --}}
        <div x-show="loading" class="flex flex-col items-center justify-center py-8 text-gray-400">
            <svg class="animate-spin h-6 w-6 mb-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"></path>
            </svg>
            <span class="text-xs">Cargando propiedades...</span>
        </div>

        {{-- Sin propiedades --}}
        <div x-show="!loading && total === 0" class="text-center py-8 px-4">
            <div class="text-3xl mb-2">✅</div>
            <p class="text-sm text-green-600 font-medium">¡No tienes propiedades pendientes!</p>
            <p class="text-xs text-gray-400 mt-1">Vuelve mañana para nuevas asignaciones</p>
        </div>

        {{-- Tarjeta --}}
        <div x-show="!loading && total > 0" class="p-4">
            <template x-if="items[current]">
                <div>
                    {{-- Navegación superior --}}
                    <div class="flex items-center justify-between mb-3">
                        <span class="text-xs text-gray-400">
                            Propiedad <span x-text="current + 1"></span> de <span x-text="total"></span>
                        </span>
                        <span class="text-xs bg-yellow-100 text-yellow-700 px-2 py-0.5 rounded-full font-medium">Pendiente</span>
                    </div>

                    {{-- Info propiedad --}}
                    <div class="bg-gray-50 rounded-lg p-3 mb-3 space-y-2">
                        <p class="font-bold text-gray-800 text-sm leading-tight" x-text="items[current].listing.listing_title ?? 'Sin título'"></p>
                        <p class="text-xs text-gray-400">
                            Código: <span class="font-mono font-medium text-gray-600" x-text="items[current].listing.product_code"></span>
                        </p>
                        <div class="border-t pt-2 space-y-1">
                            <p class="text-xs text-gray-500">
                                📅 Último contacto: 
                                <span class="font-medium text-gray-700" 
                                    x-text="items[current].listing.contact_at ? items[current].listing.contact_at.substring(0,10) : 'Nunca contactado'">
                                </span>
                            </p>
                            <p class="text-xs text-gray-500">
                                📵 Sin respuesta: 
                                <span class="font-medium text-gray-700" 
                                    x-text="items[current].listing.no_answer_at ? items[current].listing.no_answer_at.substring(0,10) : '—'">
                                </span>
                            </p>
                        </div>
                    </div>

                    {{-- Inputs ocultos --}}
                    <input type="hidden" id="product_code" :value="items[current].listing.product_code">
                    <input type="hidden" id="current_queue_id" :value="items[current].id">

                    {{-- Botones de acción --}}
                    <div class="flex gap-2">
                        {{-- Ir a la propiedad --}}
                        <a 
                            :href="'/admin/tw/edit/' + items[current].listing.id"
                            class="flex-1 flex items-center justify-center gap-1 bg-gray-800 hover:bg-gray-900 text-white text-xs font-medium py-2 px-3 rounded-lg transition">
                            ✏️ Ver Propiedad
                        </a>
                        {{-- Saltar --}}
                        <button
                            @click="skipCurrent()"
                            class="flex items-center justify-center gap-1 bg-gray-100 hover:bg-gray-200 text-gray-600 text-xs font-medium py-2 px-3 rounded-lg transition"
                            title="Saltar al final de la cola">
                            ⏭
                        </button>
                    </div>

                    {{-- Navegación inferior --}}
                    <div class="flex items-center justify-between mt-3 pt-3 border-t">
                        <button
                            @click="prev()"
                            :disabled="current === 0"
                            class="text-xs text-blue-600 hover:underline disabled:text-gray-300 disabled:cursor-not-allowed">
                            ← Anterior
                        </button>
                        <div class="flex gap-1">
                            <template x-for="(item, i) in items.slice(0, 5)" :key="i">
                                <div :class="i === current ? 'bg-gray-800 w-4' : 'bg-gray-300 w-2'" 
                                     class="h-2 rounded-full transition-all duration-300">
                                </div>
                            </template>
                        </div>
                        <button
                            @click="next()"
                            :disabled="current >= total - 1"
                            class="text-xs text-blue-600 hover:underline disabled:text-gray-300 disabled:cursor-not-allowed">
                            Siguiente →
                        </button>
                    </div>
                </div>
            </template>
        </div>
    </div>
</div>

{{-- Botón para reabrir el panel si fue cerrado --}}
<button 
    x-data
    x-show="false"
    id="reopen-queue-btn"
    onclick="document.querySelector('[x-data]').__x.$data.panelVisible = true"
    class="fixed bottom-6 right-6 z-50 bg-gray-800 text-white rounded-full w-12 h-12 flex items-center justify-center shadow-lg hover:bg-gray-700 transition"
    title="Abrir Cola de Contacto">
    📋
</button>

<script>
function contactCarousel() {
    return {
        items:     [],
        current:   0,
        total:     0,
        loading:   true,
        minimized: false,
        panelVisible: true,

        async loadQueue() {
            this.loading = true;
            try {
                const res  = await fetch("{{ route('contact.queue.index') }}");
                const data = await res.json();
                this.items   = data.items;
                this.total   = data.total;
                this.current = 0;
            } catch(e) {
                console.error('Error cargando cola:', e);
            }
            this.loading = false;
        },

        async skipCurrent() {
            const queueId = this.items[this.current].id;
            await fetch("{{ route('contact.queue.skip') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ queue_id: queueId })
            });
            // Mover al final del array en lugar de eliminar
            const skipped = this.items.splice(this.current, 1)[0];
            this.items.push(skipped);
            if (this.current >= this.total) this.current = 0;
        },

        next() { if (this.current < this.total - 1) this.current++; },
        prev() { if (this.current > 0) this.current--; },
    }
}

// ── Lógica de arrastre ──────────────────────────────────────
(function() {
    window.addEventListener('load', function() {
        const panel  = document.getElementById('contact-queue-panel');
        const header = document.getElementById('panel-header');
        if (!panel || !header) return;

        let isDragging = false;
        let offsetX = 0;
        let offsetY = 0;

        header.addEventListener('mousedown', function(e) {
            isDragging = true;
            offsetX = e.clientX - panel.getBoundingClientRect().left;
            offsetY = e.clientY - panel.getBoundingClientRect().top;
            header.style.cursor = 'grabbing';
            e.preventDefault();
        });

        document.addEventListener('mousemove', function(e) {
            if (!isDragging) return;
            const x = e.clientX - offsetX;
            const y = e.clientY - offsetY;
            // Mantener dentro de la pantalla
            const maxX = window.innerWidth  - panel.offsetWidth;
            const maxY = window.innerHeight - panel.offsetHeight;
            panel.style.left   = Math.max(0, Math.min(x, maxX)) + 'px';
            panel.style.top    = Math.max(0, Math.min(y, maxY)) + 'px';
            panel.style.right  = 'auto';
            panel.style.bottom = 'auto';
        });

        document.addEventListener('mouseup', function() {
            isDragging = false;
            header.style.cursor = 'grab';
        });
    });
})();
</script>