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
});
