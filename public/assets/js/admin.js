// JS del panel de administración.

document.addEventListener('DOMContentLoaded', () => {
    function escapeHtml(texto) {
        const div = document.createElement('div');
        div.textContent = texto;
        return div.innerHTML;
    }

    function initCascadaSubcategorias(catSelect, subSelect, opciones) {
        if (!catSelect || !subSelect) {
            return;
        }

        const subcategoriaActual = subSelect.dataset.actual || '';
        const incluirTodas = Boolean(opciones && opciones.incluirTodas);
        const placeholderVacio = incluirTodas ? 'Todas' : '— Sin subcategoría —';
        const placeholderSinOpciones = '— Esta categoría no tiene subcategorías —';

        async function cargarSubcategorias(categoriaId) {
            subSelect.innerHTML = '<option value="">— Cargando… —</option>';
            subSelect.disabled = true;

            if (!categoriaId) {
                subSelect.innerHTML = `<option value="">${placeholderVacio}</option>`;
                subSelect.disabled = false;
                return;
            }

            try {
                const res = await fetch(`subcategorias_json.php?categoria_id=${encodeURIComponent(categoriaId)}`);
                const data = await res.json();

                if (!Array.isArray(data) || data.length === 0) {
                    subSelect.innerHTML = `<option value="">${placeholderSinOpciones}</option>`;
                    subSelect.disabled = true;
                    return;
                }

                let html = `<option value="">${placeholderVacio}</option>`;
                for (const sub of data) {
                    const selected = String(sub.id) === String(subcategoriaActual) ? 'selected' : '';
                    html += `<option value="${sub.id}" ${selected}>${escapeHtml(sub.nombre)}</option>`;
                }
                subSelect.innerHTML = html;
                subSelect.disabled = false;
            } catch (e) {
                subSelect.innerHTML = '<option value="">Error al cargar</option>';
                subSelect.disabled = false;
            }
        }

        catSelect.addEventListener('change', () => cargarSubcategorias(catSelect.value));

        if (catSelect.value) {
            cargarSubcategorias(catSelect.value);
        }
    }

    // Formulario de producto (nuevo / editar): categoría + subcategoría opcional
    initCascadaSubcategorias(
        document.getElementById('categoria-select'),
        document.getElementById('subcategoria-select')
    );

    // Filtros del listado de productos: categoría + subcategoría
    initCascadaSubcategorias(
        document.getElementById('categoria'),
        document.getElementById('subcategoria'),
        { incluirTodas: true }
    );

    // Preview de fotos al elegirlas en el input multiple (nuevo / editar producto)
    const inputImagenes = document.getElementById('imagenes-input');
    const previewImagenes = document.getElementById('preview-imagenes');

    if (inputImagenes && previewImagenes) {
        const sinFotosPrevias = inputImagenes.dataset.sinFotos === '1';

        inputImagenes.addEventListener('change', (e) => {
            previewImagenes.innerHTML = '';
            const archivos = Array.from(e.target.files);

            if (archivos.length > 10) {
                alert('Solo se pueden subir 10 imágenes a la vez. Se tomarán las primeras 10.');
            }

            archivos.slice(0, 10).forEach((archivo, idx) => {
                if (!archivo.type.startsWith('image/')) {
                    return;
                }

                const esPrincipal = idx === 0 && sinFotosPrevias;
                const reader = new FileReader();
                reader.onload = (ev) => {
                    const div = document.createElement('div');
                    div.className = 'preview-item';
                    div.innerHTML = `
                        <img src="${ev.target.result}" alt="Preview">
                        ${esPrincipal ? '<span class="badge-principal">Principal</span>' : ''}
                    `;
                    previewImagenes.appendChild(div);
                };
                reader.readAsDataURL(archivo);
            });
        });
    }
});
