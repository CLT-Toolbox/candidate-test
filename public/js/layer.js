
// // Load Sortable.js from CDN
(function loadSortable(){
    const s = document.createElement('script');
    s.src = 'https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js';
    s.onload = initLocalState;
    document.head.appendChild(s);
})();

// Local state for changes
const layerChanges = { created: [], updated: {}, deleted: [], reordered: false };
let pendingDeleteId = null;
let activeActionId = null; // id for shared menu actions

// Format numeric values for display/input: remove ".00" but keep decimals when non-zero
function formatNumericDisplay(value){
    if (value === null || value === undefined || value === '') return '';
    // strip non-numeric characters except dot and minus
    const cleaned = String(value).trim().replace(/[^0-9\.\-]+/g, '');
    const num = Number(cleaned);
    if (isNaN(num)) return String(value);
    // Integer -> no decimal
    if (Number.isInteger(num)) return String(num);
    // Non-integer -> use JS string conversion which trims unnecessary zeros
    return String(num);
}

function initLocalState(){
    const tbody = document.getElementById('layersTableBody');
    if (!tbody) return;

    Sortable.create(tbody, {
        handle: '.handle',
        animation: 150,
        ghostClass: 'bg-gray-100',
        onEnd: function(){
            layerChanges.reordered = true;
            // update DOM order display
                updateOrderDisplay();
                // update created items' layer_order to match new positions
                Array.from(tbody.querySelectorAll('tr:not(#noLayersRow)')).forEach((row, idx) => {
                const orig = row.dataset.originalId;
                const order = idx + 1;
                row.dataset.layerOrder = order;
                if (orig === 'new'){
                    const id = row.dataset.layerId;
                    const found = layerChanges.created.find(l => l.id === id);
                    if (found) found.layer_order = order;
                }
            });
            updateTotals();
        }
    });
    // initial totals and empty-row state
    updateTotals();
    ensureEmptyRowVisibility();
}

function updateOrderDisplay(){
    const tbody = document.getElementById('layersTableBody');
    Array.from(tbody.querySelectorAll('tr:not(#noLayersRow)')).forEach((row, idx) => {
        const disp = row.querySelector('.order-display');
        if (disp) disp.textContent = idx + 1;
        row.dataset.layerOrder = idx + 1;
    });
    updateTotals();
}

function openAddLayerModal() {
    document.getElementById('editingLayerId').value = '';
    document.getElementById('modalTitle').textContent = 'Add Layer';
    document.getElementById('addLayerSubmitBtn').textContent = 'Add Layer';
    document.getElementById('addLayerModalBackdrop').classList.remove('hidden');
    document.getElementById('addLayerModalContainer').classList.remove('hidden');
}

function closeAddLayerModal() {
    document.getElementById('addLayerModalBackdrop').classList.add('hidden');
    document.getElementById('addLayerModalContainer').classList.add('hidden');
    document.getElementById('addLayerForm').reset();
    // reset edit state
    const editing = document.getElementById('editingLayerId');
    if (editing) editing.value = '';
    const submitBtn = document.getElementById('addLayerSubmitBtn');
    if (submitBtn) submitBtn.textContent = 'Add Layer';
    const modalTitle = document.getElementById('modalTitle');
    if (modalTitle) modalTitle.textContent = 'Add Layer';
}

function addNewLayerLocal(event){
    event.preventDefault();
    const form = event.target;
    const thickness = form.elements['thickness'].value;
    const width = form.elements['width'].value;
    const angle = form.elements['angle'].value;
    const editingId = document.getElementById('editingLayerId').value;

    const tbody = document.getElementById('layersTableBody');

    if (editingId){
        // editing existing or new local
        const layerOrderInput = form.elements['layer_order'];
        const parsed = layerOrderInput ? parseInt(layerOrderInput.value) : NaN;
        const newOrderVal = Number.isFinite(parsed) ? parsed : null;

        // build helper to swap two rows in tbody by indices (ignore placeholder)
        const swapRowsByIndex = (i1, i2) => {
            const nodes = Array.from(tbody.querySelectorAll('tr:not(#noLayersRow)'));
            if (i1 < 0 || i2 < 0 || i1 >= nodes.length || i2 >= nodes.length) return;
            const tmp = nodes[i1]; nodes[i1] = nodes[i2]; nodes[i2] = tmp;
            // reappend in new order (appendChild moves existing nodes)
            nodes.forEach(n => tbody.appendChild(n));
            updateOrderDisplay();
        };

        if (editingId.startsWith('new_')){
            // update created array
            const idx = layerChanges.created.findIndex(l => l.id === editingId);
            if (idx !== -1){
                layerChanges.created[idx].thickness = thickness;
                layerChanges.created[idx].width = width;
                layerChanges.created[idx].angle = angle;
                if (newOrderVal) layerChanges.created[idx].layer_order = newOrderVal;
            }
            const row = tbody.querySelector(`tr[data-layer-id="${editingId}"]`);
            if (row){
                row.dataset.thickness = thickness;
                row.dataset.width = width;
                row.dataset.angle = angle;
                row.querySelector('.thickness-display').textContent = formatNumericDisplay(thickness) + 'mm';
                row.querySelector('.width-display').textContent = formatNumericDisplay(width) + 'mm';
                row.querySelector('.angle-display').textContent = formatNumericDisplay(angle) + '°';
                if (newOrderVal){
                    const nodes = Array.from(tbody.children);
                    const currentIndex = nodes.indexOf(row);
                    const targetIndex = Math.max(0, Math.min(newOrderVal - 1, nodes.length - 1));
                    if (currentIndex !== -1 && targetIndex !== -1 && currentIndex !== targetIndex){
                        swapRowsByIndex(currentIndex, targetIndex);
                    }
                    // sync created array orders with DOM
                    Array.from(tbody.querySelectorAll('tr')).forEach((r, i) => {
                        if (r.dataset.originalId === 'new'){
                            const cid = r.dataset.layerId;
                            const found = layerChanges.created.find(l => l.id === cid);
                            if (found) found.layer_order = i + 1;
                        }
                    });
                }
            }
        } else {
            // existing row
            const id = parseInt(editingId);
            layerChanges.updated[id] = layerChanges.updated[id] || {};
            layerChanges.updated[id].thickness = thickness;
            layerChanges.updated[id].width = width;
            layerChanges.updated[id].angle = angle;
            const row = tbody.querySelector(`tr[data-original-id="${id}"]`);
            if (row){
                row.dataset.thickness = thickness;
                row.dataset.width = width;
                row.dataset.angle = angle;
                row.querySelector('.thickness-display').textContent = formatNumericDisplay(thickness) + 'mm';
                row.querySelector('.width-display').textContent = formatNumericDisplay(width) + 'mm';
                row.querySelector('.angle-display').textContent = formatNumericDisplay(angle) + '°';
                if (newOrderVal){
                    const nodes = Array.from(tbody.children);
                    const currentIndex = nodes.indexOf(row);
                    const targetIndex = Math.max(0, Math.min(newOrderVal - 1, nodes.length - 1));
                    if (currentIndex !== -1 && targetIndex !== -1 && currentIndex !== targetIndex){
                        swapRowsByIndex(currentIndex, targetIndex);
                    }
                    // if order changed, persist in updated payload
                    layerChanges.updated[id].layer_order = targetIndex + 1;
                }
            }
        }
        closeAddLayerModal();
        return;
    }

    // create new local layer
    // remove placeholder row if present so counts/positions are accurate
    const noRow = document.getElementById('noLayersRow');
    if (noRow) noRow.remove();
    const nextOrder = tbody.querySelectorAll('tr:not(#noLayersRow)').length + 1;
    const requestedOrder = form.elements['layer_order'] ? parseInt(form.elements['layer_order'].value) : NaN;
    const maxIndex = tbody.querySelectorAll('tr:not(#noLayersRow)').length;
    const insertAt = Number.isFinite(requestedOrder) && requestedOrder > 0 ? Math.max(0, Math.min(requestedOrder - 1, maxIndex)) : maxIndex;
    const newId = 'new_' + Date.now();
    const newLayer = { id: newId, layer_order: insertAt + 1, thickness, width, angle };

    layerChanges.created.push(newLayer);

    const tr = document.createElement('tr');
    tr.className = 'hover:bg-gray-50 transition-colors cursor-move';
    tr.dataset.layerId = newId;
    tr.dataset.originalId = 'new';
    tr.dataset.thickness = thickness;
    tr.dataset.width = width;
    tr.dataset.angle = angle;
    tr.dataset.layerOrder = newLayer.layer_order;
    tr.innerHTML = `
        <td class="px-6 py-3.5">
            <div class="flex items-center gap-2 text-gray-700">
                <svg class="w-4 h-4 text-gray-400 cursor-grab handle" fill="currentColor" viewBox="0 0 20 20"><path d="M8 9a2 2 0 100-4 2 2 0 000 4zm0 2a2 2 0 100-4 2 2 0 000 4zm0 2a2 2 0 100-4 2 2 0 000 4zM12 9a2 2 0 100-4 2 2 0 000 4zm0 2a2 2 0 100-4 2 2 0 000 4zm0 2a2 2 0 100-4 2 2 0 000 4z"/></svg>
                <span class="order-display">${newLayer.layer_order}</span>
            </div>
        </td>
        <td class="px-4 py-3.5"><span class="thickness-display text-gray-800">${formatNumericDisplay(thickness)}mm</span></td>
        <td class="px-4 py-3.5"><span class="width-display text-gray-800">${formatNumericDisplay(width)}mm</span></td>
        <td class="px-4 py-3.5"><span class="angle-display text-gray-800">${formatNumericDisplay(angle)}°</span></td>
        <td class="px-4 py-3.5"><span class="inline-flex items-center gap-1 px-2 py-1 rounded text-xs font-medium bg-green-50 text-green-700 border border-green-200"><span class="w-2 h-2 rounded-full bg-green-500"></span>C24</span></td>
        <td class="px-4 py-3.5">
            <div class="relative">
                <button class="px-2 py-1 text-sm text-gray-600 hover:bg-gray-100 rounded-full" onclick="toggleRowMenu(event, '${newId}')" aria-expanded="false">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path d="M10 6a2 2 0 110-4 2 2 0 010 4zm0 7a2 2 0 110-4 2 2 0 010 4zm0 7a2 2 0 110-4 2 2 0 010 4z"/></svg>
                </button>
                <!-- per-row menu removed; shared action menu will be used -->
            </div>
        </td>
    `;

    const currentDataRows = Array.from(tbody.querySelectorAll('tr:not(#noLayersRow)'));
    if (insertAt >= currentDataRows.length) tbody.appendChild(tr); else tbody.insertBefore(tr, currentDataRows[insertAt]);
    // refresh display orders and dataset
    updateOrderDisplay();
    Array.from(tbody.querySelectorAll('tr:not(#noLayersRow)')).forEach((r, i) => r.dataset.layerOrder = i+1);
    // sync created array orders
    Array.from(tbody.querySelectorAll('tr:not(#noLayersRow)')).forEach((r, i) => {
        if (r.dataset.originalId === 'new'){
            const cid = r.dataset.layerId;
            const found = layerChanges.created.find(l => l.id === cid);
            if (found) found.layer_order = i+1;
        }
    });
    // hide placeholder if present
    ensureEmptyRowVisibility();
    closeAddLayerModal();
}

function openEditLayerModal(layerId){
    const form = document.getElementById('addLayerForm');
    const editingInput = document.getElementById('editingLayerId');
    const submitBtn = document.getElementById('addLayerSubmitBtn');
    const modalTitle = document.getElementById('modalTitle');

    const tbody = document.getElementById('layersTableBody');
    let thickness = '';
    let width = '';
    let angle = '';
    let layerOrder = '';

    if (String(layerId).startsWith('new_')){
        const created = layerChanges.created.find(l => l.id === layerId);
        if (created){ thickness = created.thickness; width = created.width; angle = created.angle; layerOrder = created.layer_order || ''; }
    } else {
        const row = tbody.querySelector(`tr[data-original-id="${layerId}"]`);
        if (row){ thickness = row.dataset.thickness; width = row.dataset.width; angle = row.dataset.angle; layerOrder = row.dataset.layerOrder || row.querySelector('.order-display')?.textContent || ''; }
    }

    form.elements['thickness'].value = formatNumericDisplay(thickness);
    form.elements['width'].value = formatNumericDisplay(width);
    form.elements['angle'].value = formatNumericDisplay(angle);
    form.elements['layer_order'].value = layerOrder;
    editingInput.value = layerId;
    modalTitle.textContent = 'Edit Layer';
    submitBtn.textContent = 'Save Changes';
    document.getElementById('addLayerModalBackdrop').classList.remove('hidden');
    document.getElementById('addLayerModalContainer').classList.remove('hidden');
}

function markLayerForDelete(event, layerId){
    if (event && event.preventDefault) event.preventDefault();
    openConfirmDeleteModal(layerId);
}

function openConfirmDeleteModal(layerId){
    pendingDeleteId = layerId;
    if (typeof Swal === 'undefined'){
        // fallback to default confirm
        if (confirm('Delete this layer? This will be applied when you click Save Changes.')){
            confirmDeleteNow();
        } else {
            pendingDeleteId = null;
        }
        return;
    }

    Swal.fire({
        title: 'Delete layer?',
        text: 'This will be applied when you click Save Changes.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#e3342f',
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'Delete',
        cancelButtonText: 'Cancel'
    }).then((result) => {
        if (result.isConfirmed) {
            confirmDeleteNow();
        } else {
            pendingDeleteId = null;
        }
    });
}

function confirmDeleteNow(){
    if (!pendingDeleteId) return closeConfirmDeleteModal();
    const tbody = document.getElementById('layersTableBody');
    if (!tbody) return closeConfirmDeleteModal();

    const layerId = pendingDeleteId;
    // try to find new-created row by data-layer-id
    let row = tbody.querySelector(`tr[data-layer-id="${layerId}"]`);
    if (row && row.dataset.originalId === 'new'){
        // remove from created and DOM
        layerChanges.created = layerChanges.created.filter(l => l.id !== layerId);
        row.remove();
        updateOrderDisplay();
        updateTotals();
        ensureEmptyRowVisibility();
        pendingDeleteId = null;
        return;
    }

    // otherwise treat as existing record
    // try find by original id
    row = tbody.querySelector(`tr[data-original-id="${layerId}"]`);
    const idNum = parseInt(layerId);
    if (!isNaN(idNum)){
        if (!layerChanges.deleted.includes(idNum)) layerChanges.deleted.push(idNum);
    }
    if (row){
        row.style.opacity = '0.5';
        row.classList.add('line-through');
    }
    updateTotals();
    ensureEmptyRowVisibility();
    pendingDeleteId = null;
}

// Row menu toggling

function toggleRowMenu(event, id){
    event.stopPropagation();
    closeAllMenus();
    activeActionId = id;
    const shared = document.getElementById('sharedRowMenu');
    if (!shared) return;
    // determine button element (svg or inner element may be target)
    let btn = (event && event.currentTarget) || (event && event.target && event.target.closest && event.target.closest('button')) || (event && event.target) || null;
    if (!btn) btn = document.querySelector(`button[onclick*="${id}"]`);
    const rect = btn ? btn.getBoundingClientRect() : { right: window.innerWidth - 10, bottom: 0 };
    // ensure shared has measurable width (remove hidden briefly if needed)
    shared.classList.remove('hidden');
    const sw = shared.offsetWidth || 160;
    let left = rect.right + window.scrollX - sw + 8;
    let top = rect.bottom + window.scrollY + 6;
    // flip if out of viewport
    if (left + sw > window.scrollX + window.innerWidth) left = Math.max(window.scrollX + 8, rect.left + window.scrollX - sw - 8);
    if (top + shared.offsetHeight > window.scrollY + window.innerHeight) top = Math.max(window.scrollY + 8, rect.top + window.scrollY - shared.offsetHeight - 8);
    shared.style.left = left + 'px';
    shared.style.top = top + 'px';
}

function closeAllMenus(){
    const shared = document.getElementById('sharedRowMenu');
    if (shared) shared.classList.add('hidden');
    activeActionId = null;
}

document.addEventListener('click', function(){
    closeAllMenus();
});

// wire shared menu buttons immediately
(function wireSharedMenu(){
    const editBtn = document.getElementById('sharedEditBtn');
    const delBtn = document.getElementById('sharedDeleteBtn');
    if (editBtn) editBtn.addEventListener('click', function(e){ e.stopPropagation(); if (activeActionId) { openEditLayerModal(activeActionId); closeAllMenus(); } });
    if (delBtn) delBtn.addEventListener('click', function(e){ e.stopPropagation(); if (activeActionId) { openConfirmDeleteModal(activeActionId); closeAllMenus(); } });
})();

function saveAllChanges(){
    // build reordered list
    const tbody = document.getElementById('layersTableBody');
    const reordered = [];
    Array.from(tbody.querySelectorAll('tr:not(#noLayersRow)')).forEach((row, idx) => {
        const orig = row.dataset.originalId;
        if (orig && orig !== 'new') reordered.push({ id: parseInt(orig), layer_order: idx + 1 });
    });

    const allChanges = {
        created: layerChanges.created,
        updated: layerChanges.updated,
        deleted: layerChanges.deleted,
        reordered: reordered
    };

    document.getElementById('changesJson').value = JSON.stringify(allChanges);
    document.getElementById('batchSaveForm').submit();
}

function updateTotals(){
    const tbody = document.getElementById('layersTableBody');
    if (!tbody) return;
    let totalThickness = 0;
    let count = 0;
    Array.from(tbody.querySelectorAll('tr:not(#noLayersRow)')).forEach(row => {
        if (row.classList.contains('line-through')) return; // deleted
        const th = row.dataset.thickness || row.querySelector('.thickness-display')?.textContent || '0';
        const numeric = parseInt(String(th).replace(/[^0-9\.\-]+/g, ''), 10) || 0;
        totalThickness += numeric;
        count++;
    });

    const totalEl = document.getElementById('totalThicknessDisplay');
    const countEl = document.getElementById('totalLayersDisplay');
    const showingEl = document.getElementById('showingCount');
    if (totalEl) totalEl.textContent = totalThickness + 'mm';
    if (countEl) countEl.textContent = count + ' Layers';
    if (showingEl) showingEl.textContent = count;
}

function ensureEmptyRowVisibility(){
    const tbody = document.getElementById('layersTableBody');
    if (!tbody) return;
    const noRow = document.getElementById('noLayersRow');
    const dataRows = Array.from(tbody.querySelectorAll('tr')).filter(r => r.id !== 'noLayersRow' && !r.classList.contains('line-through'));
    if (dataRows.length === 0){
        if (!noRow){
            const tr = document.createElement('tr');
            tr.id = 'noLayersRow';
            tr.innerHTML = '<td colspan="6" class="px-6 py-8 text-center text-gray-500">No layers added yet</td>';
            tbody.appendChild(tr);
        }
    } else {
        if (noRow) noRow.remove();
    }
}