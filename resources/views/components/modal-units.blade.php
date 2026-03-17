<div>
    <div id="modalUnits" class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 hidden z-50">
        <div class="bg-white rounded-lg shadow-xl w-9/12 flex flex-col" style="height: 90vh">
            <!-- CONTENIDO SCROLLEABLE -->
            <h2 class="text-lg px-5 mt-4 font-semibold pb-4">Unidades de la propiedad {{ $listing->product_code }}</h2>
            <div class="overflow-y-auto p-5">
                @foreach ($units as $unit)
                    <div class="w-full mb-4 border rounded-lg shadow-sm p-3 bg-white">
                        <form id="unit-form-{{ $unit->id }}">
                        
                            <div class="grid grid-cols-5 gap-4">
                                <!-- NOMBRE -->
                                <div class="mb-2">
                                    <label class="text-sm font-medium text-gray-700">Nombre</label>
                                    <input type="text" name="name" value="{{ $unit->name }}"
                                        class="w-full border-b bg-transparent focus:outline-none"
                                        disabled>
                                </div>
                            
                                <!-- UNIT NUMBER -->
                                <div class="mb-2">
                                    <label class="text-sm font-medium text-gray-700">Número de Unidad</label>
                                    <input type="text" name="unit_number" value="{{ $unit->unit_number }}"
                                        class="w-full border-b bg-transparent focus:outline-none"
                                        disabled>
                                </div>

                                <!-- TIPO DE PROPIEDAD -->
                                <div class="mb-2">
                                    <label class="text-sm font-medium text-gray-700">Tipo de Propiedad</label>
                                    <select name="listing_type_id"
                                            class="w-full border rounded px-2 focus:outline-none focus:ring"
                                            disabled>
                                        <option value="">-- Seleccione --</option>
                                        @foreach ($types as $type)
                                            <option value="{{ $type->id }}"
                                                {{ $unit->listing_type_id == $type->id ? 'selected' : '' }}>
                                                {{ $type->type_title }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            
                                <!-- FLOOR -->
                                <div class="mb-2">
                                    <label class="text-sm font-medium text-gray-700">Piso</label>
                                    <input type="number" name="floor" value="{{ $unit->floor }}"
                                        class="w-full border-b bg-transparent focus:outline-none"
                                        disabled>
                                </div>
                            
                                <!-- AREA M2 -->
                                <div class="mb-2">
                                    <label class="text-sm font-medium text-gray-700">Área m²</label>
                                    <input type="number" step="0.01" name="area_m2" value="{{ $unit->area_m2 }}"
                                        class="w-full border-b bg-transparent focus:outline-none"
                                        disabled>
                                </div>
                            
                                <!-- BEDROOMS -->
                                <div class="mb-2">
                                    <label class="text-sm font-medium text-gray-700">Habitaciones</label>
                                    <input type="number" name="bedrooms" value="{{ $unit->bedrooms }}"
                                        class="w-full border-b bg-transparent focus:outline-none"
                                        disabled>
                                </div>
                            
                                <!-- BATHROOMS -->
                                <div class="mb-2">
                                    <label class="text-sm font-medium text-gray-700">Baños</label>
                                    <input type="number" name="bathrooms" value="{{ $unit->bathrooms }}"
                                        class="w-full border-b bg-transparent focus:outline-none"
                                        disabled>
                                </div>
                            
                                <!-- PRICE -->
                                <div class="mb-2">
                                    <label class="text-sm font-medium text-gray-700">Precio</label>
                                    <input type="number" step="0.01" name="price" value="{{ $unit->price }}"
                                        class="w-full border-b bg-transparent focus:outline-none"
                                        disabled>
                                </div>
                            
                                <!-- MIN PRICE -->
                                <div class="mb-2">
                                    <label class="text-sm font-medium text-gray-700">Precio Mínimo</label>
                                    <input type="number" step="0.01" name="min_price" value="{{ $unit->min_price }}"
                                        class="w-full border-b bg-transparent focus:outline-none"
                                        disabled>
                                </div>

                                <div class="mb-2">
                                    <label class="text-sm font-medium text-gray-700">Precio de Renta</label>
                                    <input type="number" step="0.01" name="rent_price" value="{{ $unit->rent_price }}"
                                        class="w-full border-b bg-transparent focus:outline-none"
                                        disabled>
                                </div>
                            
                                <!-- STATUS -->
                                <div class="mb-2">
                                    <label class="text-sm font-medium text-gray-700">Estado</label>
                                    <select name="status"
                                            class="w-full border rounded px-2 focus:outline-none focus:ring"
                                            disabled>
                                        <option value="available" {{ $unit->status === 'available' ? 'selected' : '' }}>Disponible</option>
                                        <option value="sold" {{ $unit->status === 'sold' ? 'selected' : '' }}>No disponible</option>
                                    </select>
                                </div>
                            
                            </div>

                            <div class="grid grid-cols-1">
                                <!-- DESCRIPTION -->
                                <div class="mb-2">
                                    <label class="text-sm font-medium text-gray-700">Detalles</label>
                                    <textarea name="description" rows="3"
                                            class="w-full border rounded px-2 py-1 focus:outline-none"
                                            disabled>{{ $unit->description }}</textarea>
                                </div>
                            </div>
                        
                            <!-- BOTONES -->
                            <div class="flex gap-2 mt-2">
                                <button type="button"
                                        class="edit-btn bg-blue-500 hover:bg-blue-600 text-white px-3 py-1 rounded text-sm"
                                        data-unit="{{ $unit->id }}">
                                    Editar
                                </button>
                                <button type="button"
                                        class="save-btn bg-green-500 hover:bg-green-600 text-white px-3 py-1 rounded text-sm hidden"
                                        data-unit="{{ $unit->id }}">
                                    Guardar
                                </button>
                            </div>
                        </form>
                        <p id="msg-{{ $unit->id }}" class="text-xs mt-2"></p>
                        @if($unit->status === "sold")
                            <p class="text-red-600 font-semibold">Esta unidad no está disponible</p>
                        @endif
                    </div>
                @endforeach
            </div>
            <!-- BOTÓN CERRAR FIJO -->
            <div class="border-t p-4 bg-white sticky bottom-0 flex justify-end rounded-lg">
                <button type="button"
                    class="px-4 py-2 text-base font-medium text-white bg-red-600 rounded-md shadow-sm hover:bg-red-700"
                    onclick="cerrarModalUnits()">
                    Cerrar
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    function abrirModalUnits() {
        document.getElementById('modalUnits').classList.remove('hidden');
    }

    function cerrarModalUnits() {
        document.getElementById('modalUnits').classList.add('hidden');
    }

    document.querySelectorAll('.edit-btn').forEach(btn => {
        btn.addEventListener('click', function () {
            const id = this.dataset.unit;
            const form = document.getElementById(`unit-form-${id}`);
            form.querySelectorAll('input, textarea, select').forEach(input => input.removeAttribute('disabled'));
            this.classList.add('hidden');
            form.querySelector(`.save-btn[data-unit="${id}"]`).classList.remove('hidden');
        });
    });

    //Ruta para actualizar la unidad
    const unitUpdateUrl = "{{ route('units.update', ['unit' => ':id']) }}";

    document.querySelectorAll('.save-btn').forEach(btn => {
        btn.addEventListener('click', function () {
            const id = this.dataset.unit;
            const form = document.getElementById(`unit-form-${id}`);
            const msg = document.getElementById(`msg-${id}`);

            const formData = new FormData(form);
            const data = {};
            formData.forEach((value, key) => data[key] = value);

            const url = unitUpdateUrl.replace(':id', id);

            fetch(url, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify(data)
            })
            .then(res => res.json())
            .then(res => {
                if (res.success) {
                    msg.innerText = '✅ Actualizado correctamente';
                    msg.classList.add('text-green-600');
                } else {
                    msg.innerText = '❌ Error al actualizar';
                    msg.classList.add('text-red-600');
                }

                //Clean the message
                setTimeout(() => {
                    msg.innerText = ''; // Clear the text
                    msg.classList.remove('text-green-600', 'text-red-600'); // Remove the color classes
                }, 3000);

                form.querySelectorAll('input, textarea, select').forEach(input => input.setAttribute('disabled', true));
                this.classList.add('hidden');
                form.querySelector(`.edit-btn[data-unit="${id}"]`).classList.remove('hidden');
            })
            .catch(err => {
                msg.innerText = '❌ Error al enviar datos';
                msg.classList.add('text-red-600');
            });
        });
    });
</script>