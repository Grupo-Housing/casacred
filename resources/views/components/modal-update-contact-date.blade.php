<div>
    <div id="miModal" class="fixed inset-0 overflow-y-auto hidden" style="z-index: 10000">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 transition-opacity" aria-hidden="true">
                <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
            </div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            <div class="inline-block px-4 pt-5 pb-4 overflow-hidden text-left align-bottom transition-all transform bg-white rounded-lg shadow-xl sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <div>
                    <div class="mt-3 text-center sm:mt-5">
                        <h3 class="text-lg font-medium leading-6 text-gray-900">
                            Actualizar fecha de contacto
                        </h3>
                        <div class="mt-2">
                            <p class="text-sm text-gray-500">
                                Ingrese un comentario y la fecha en que se contacta con el cliente
                            </p>
                        </div>
                        <div class="mt-2">

                            <!-- New Radio Buttons for Contact Result -->
                            <div class="p-4 bg-gray-50 rounded-lg">
                                <p class="text-sm font-medium text-gray-700 mb-2">Resultado del Contacto:</p>
                                <div class="flex items-center justify-around space-x-4">
                                    <div class="flex items-center">
                                        <input id="contesta" name="respuesta" type="radio" value="CONTESTA" class="h-4 w-4 text-green-600 border-gray-300 focus:ring-green-500 rounded-full">
                                        <label for="contesta" class="ml-2 block text-sm font-medium text-gray-700">CONTESTA</label>
                                    </div>
                                    <div class="flex items-center">
                                        <input id="no_contesta" name="respuesta" type="radio" value="NO CONTESTA" class="h-4 w-4 text-red-600 border-gray-300 focus:ring-red-500 rounded-full">
                                        <label for="no_contesta" class="ml-2 block text-sm font-medium text-gray-700">NO CONTESTA</label>
                                    </div>
                                </div>
                            </div>

                            <textarea id="comentario" class="w-full px-3 py-2 text-gray-700 border rounded-lg focus:outline-none focus:ring focus:border-blue-300" rows="4" placeholder="Comentario......"></textarea>
                        </div>
                    </div>
                </div>
                <div class="mt-5 sm:mt-6 flex gap-2">
                    <button type="button" class="inline-flex justify-center w-full px-4 py-2 text-base font-medium text-white bg-red-600 border border-transparent rounded-md shadow-sm hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500" onclick="cerrarModal()">
                        Cerrar
                    </button>
                    <button type="button" onclick="saveDataContactDate()" class="inline-flex justify-center w-full px-4 py-2 text-base font-medium text-white bg-green-600 border border-transparent rounded-md shadow-sm hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                        Guardar
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Message Modal -->
    <div id="messageModal" class="fixed inset-0 overflow-y-auto hidden" style="z-index: 100;">
        <div class="flex items-center justify-center min-h-screen p-4 text-center">
            <div class="fixed inset-0 transition-opacity" aria-hidden="true">
                <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
            </div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            <div class="inline-block px-4 pt-5 pb-4 overflow-hidden text-left align-bottom transition-all transform bg-white rounded-lg shadow-xl sm:my-8 sm:align-middle sm:max-w-sm sm:w-full">
                <div class="text-center">
                    <h3 id="messageTitle" class="text-lg font-bold leading-6 text-gray-900"></h3>
                    <div class="mt-2">
                        <p id="messageText" class="text-sm text-gray-500"></p>
                    </div>
                </div>
                <div class="mt-5 text-center">
                    <button type="button" class="inline-flex justify-center w-full px-4 py-2 text-base font-medium text-white bg-blue-600 border border-transparent rounded-md shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500" onclick="cerrarMessageModal()">
                        Cerrar
                    </button>
                </div>
            </div>
        </div>
     </div>

</div>

<script>
    function abrirModal() {
        document.getElementById('miModal').classList.remove('hidden');
    }

    function cerrarModal() {
        document.getElementById('miModal').classList.add('hidden');
    }

    function cleanData(){
        document.getElementById('comentario').value = "";

        // Obtener todos los radio buttons con el nombre "respuesta"
        const radioButtons = document.getElementsByName('respuesta');

        // Recorrer los botones para encontrar el que está seleccionado y desmarcarlo
        radioButtons.forEach(radio => {
            if (radio.checked) {
                radio.checked = false;
            }
        });

    }

    // Function to show the message modal
    function showMessageModal(title, message) {
        document.getElementById('messageTitle').textContent = title;
        document.getElementById('messageText').textContent = message;
        document.getElementById('messageModal').classList.remove('hidden');
    }

    // Function to close the message modal
    function cerrarMessageModal() {
        document.getElementById('messageModal').classList.add('hidden');
    }

    function saveDataContactDate() {
    
        const comentario   = document.getElementById('comentario').value.trim();
        const product_code = document.getElementById('product_code').value;

        const respuestaElement = document.querySelector('input[name="respuesta"]:checked');
        const respuesta = respuestaElement ? respuestaElement.value : '';

        if (!comentario || !respuesta) {
            showMessageModal('Error de Formulario', 'Por favor, complete todos los campos.');
            return;
        }

        fetch("{{ Route('update.contact.date') }}", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                comentario:         comentario,
                respuesta_contacto: respuesta,
                product_code:       product_code
                // ✅ Ya no necesitamos enviar queue_id
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                cerrarModal();
                cleanData();
                showMessageModal('Éxito', 'Fecha de contacto actualizada con éxito.');

                // ✅ Solo disparar evento para que el carousel avance visualmente
                window.dispatchEvent(new CustomEvent('contact-done'));

            } else {
                showMessageModal('Error', 'Error al actualizar la fecha de contacto.');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showMessageModal('Error', 'Se produjo un error al procesar la solicitud.');
        });
    }
</script>