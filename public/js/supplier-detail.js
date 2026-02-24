document.addEventListener("DOMContentLoaded", function () {
    const modalBackdrop = document.getElementById('modalBackdrop');
    const modalContainer = document.getElementById('modalContainer');
    const modalTitle = document.getElementById('modalTitle');
    const submitBtn = document.getElementById('submitBtn');
    const methodInput = document.getElementById('methodInput');
    
    const supplierForm = document.getElementById('supplierForm');
    const supplierName = document.getElementById('supplierName');
    const supplierEmail = document.getElementById('supplierEmail');
    const supplierAddress = document.getElementById('supplierAddress');
    const deleteForm = document.getElementById('deleteForm');

    // OPEN / CLOSE MODAL
    // ===============================
    window.openModal = function () {
        modalBackdrop.classList.remove('hidden');
        modalContainer.classList.remove('hidden');
    }

    window.closeModal = function () {
        modalBackdrop.classList.add('hidden');
        modalContainer.classList.add('hidden');
        supplierForm.reset();
    }

    modalBackdrop.addEventListener('click', closeModal);

    // EDIT
    // ===============================
    window.openEditModal = function (id, name, email, address, is_active) {        
        
        modalTitle.textContent = 'Edit Supplier';
        submitBtn.textContent = 'Update';

        supplierName.value = name;
        supplierEmail.value = email || '';
        supplierAddress.value = address || '';
        document.querySelector('input[name="is_active"][value="' + is_active + '"]').checked = true;

        supplierForm.action = "/suppliers/" + id;
        methodInput.value = "PUT";

        openModal();
    }

    // CLT LAYUP MODAL
    // =====================================================
    window.openLayupModal = function (supplierId) {
        const form = document.getElementById('cltLayupForm');

        form.action = form.getAttribute('action');

        document.getElementById('layupName').value = '';
        document.getElementById('layupSpeciesGrade').value = '';
        document.getElementById('layupStatus').value = '1';
        document.getElementById('cltSupplierId').value = supplierId;

        document.getElementById('layupModalBackdrop').classList.remove('hidden');
        document.getElementById('layupModalContainer').classList.remove('hidden');

        document.getElementById('layupName').focus();
    };

    window.closeLayupModal = function () {
        document.getElementById('layupModalBackdrop').classList.add('hidden');
        document.getElementById('layupModalContainer').classList.add('hidden');
    };

    // document.getElementById('layupModalBackdrop').addEventListener('click', closeLayupModal);


    // ACTION MENU (VIEW / EDIT / DELETE)
    // =====================================================
    let currentLayup = null;
    window.openActionsMenu = function (event, btn) {
        event.stopPropagation();

        const menu = document.getElementById('actionsMenu');

        currentLayup = {
            id: btn.dataset.id,
            name: btn.dataset.name,
            species: btn.dataset.species,
            status: btn.dataset.status,
            supplier_id: btn.dataset.supplierId
        };

        const rect = btn.getBoundingClientRect();
        menu.style.top = (rect.bottom + window.scrollY + 6) + 'px';
        menu.style.left = (rect.left + window.scrollX) + 'px';

        menu.classList.remove('hidden');

        document.getElementById('actionsView').onclick = viewLayup;
        document.getElementById('actionsEdit').onclick = editLayup;
        document.getElementById('actionsDelete').onclick = deleteLayup;
    };

    function closeActionsMenu() {
        const menu = document.getElementById('actionsMenu');
        if (menu) menu.classList.add('hidden');
        currentLayup = null;
    }

    document.addEventListener('click', function () {
        closeActionsMenu();
    });


    function viewLayup() {
        if (!currentLayup) return closeActionsMenu();
        window.location.href = '/clt-layups/' + currentLayup.id;
    }

    function editLayup() {
        if (!currentLayup) return closeActionsMenu();

        const form = document.getElementById('cltLayupForm');
        form.action = '/clt-layups/' + currentLayup.id;

        let methodInput = document.getElementById('cltLayupMethod');
        if (!methodInput) {
            methodInput = document.createElement('input');
            methodInput.type = 'hidden';
            methodInput.name = '_method';
            methodInput.id = 'cltLayupMethod';
            form.appendChild(methodInput);
        }
        methodInput.value = 'PUT';

        document.getElementById('layupName').value = currentLayup.name || '';
        document.getElementById('layupSpeciesGrade').value = currentLayup.species || '';
        document.getElementById('layupStatus').value = currentLayup.status || '1';
        document.getElementById('cltSupplierId').value = currentLayup.supplier_id || '';

        const submitBtn = form.querySelector('button[type="submit"]');
        if (submitBtn) submitBtn.textContent = 'Update Layup';

        document.getElementById('layupModalBackdrop').classList.remove('hidden');
        document.getElementById('layupModalContainer').classList.remove('hidden');

        closeActionsMenu();
    }

    function deleteLayup() {
        if (!currentLayup) return closeActionsMenu();
        Swal.fire({
            title: 'Are you sure?',
            text: "You won't to delete " + currentLayup.name + " ? This action can delete related CLT Layers!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.isConfirmed) {
                const delForm = document.getElementById('cltDeleteForm');
                delForm.action = '/clt-layups/' + currentLayup.id;
                delForm.submit();
            }
        });
    }


    // IMPORT LAYUP MODAL
    // =====================================================
    window.openImportModal = function () {
        document.getElementById('importModalBackdrop').classList.remove('hidden');
        document.getElementById('importModalContainer').classList.remove('hidden');
        document.getElementById('importFile').value = '';
        document.getElementById('fileName').classList.add('hidden');
        document.getElementById('confirmImportBtn').disabled = true;
    };

    window.closeImportModal = function () {
        document.getElementById('importModalBackdrop').classList.add('hidden');
        document.getElementById('importModalContainer').classList.add('hidden');
    };

    const importModalBackdrop = document.getElementById('importModalBackdrop');
    if (importModalBackdrop) {
        importModalBackdrop.addEventListener('click', closeImportModal);
    }


    // FILE UPLOAD HANDLING
    // =====================================================
    const fileDropZone = document.getElementById('fileDropZone');
    const importFile = document.getElementById('importFile');
    const fileNameDisplay = document.getElementById('fileName');
    const fileNameText = document.getElementById('fileNameText');
    const confirmImportBtn = document.getElementById('confirmImportBtn');

    if (fileDropZone && importFile) {
        // Click to upload
        fileDropZone.addEventListener('click', () => importFile.click());

        // Drag and drop
        fileDropZone.addEventListener('dragover', (e) => {
            e.preventDefault();
            e.stopPropagation();
            fileDropZone.classList.add('border-green-500', 'bg-green-50');
            fileDropZone.classList.remove('border-gray-300', 'bg-gray-50');
        });

        fileDropZone.addEventListener('dragleave', (e) => {
            e.preventDefault();
            e.stopPropagation();
            fileDropZone.classList.remove('border-green-500', 'bg-green-50');
            fileDropZone.classList.add('border-gray-300', 'bg-gray-50');
        });

        fileDropZone.addEventListener('drop', (e) => {
            e.preventDefault();
            e.stopPropagation();
            fileDropZone.classList.remove('border-green-500', 'bg-green-50');
            fileDropZone.classList.add('border-gray-300', 'bg-gray-50');

            const files = e.dataTransfer.files;
            if (files.length > 0) {
                importFile.files = files;
                handleFileSelect();
            }
        });

        // File input change
        importFile.addEventListener('change', handleFileSelect);
    }

    function handleFileSelect() {
        const file = importFile.files[0];
        if (file) {
            // Validate file type
            const allowedTypes = ['text/csv', 'application/json'];
            const fileExtension = file.name.split('.').pop().toLowerCase();
            
            if (!allowedTypes.includes(file.type) && fileExtension !== 'csv' && fileExtension !== 'json') {
                alert('Please select a valid CSV or JSON file');
                importFile.value = '';
                fileNameDisplay.classList.add('hidden');
                confirmImportBtn.disabled = true;
                return;
            }

            // Validate file size (10MB max)
            if (file.size > 10 * 1024 * 1024) {
                alert('File size must be less than 10MB');
                importFile.value = '';
                fileNameDisplay.classList.add('hidden');
                confirmImportBtn.disabled = true;
                return;
            }

            fileNameText.textContent = file.name;
            fileNameDisplay.classList.remove('hidden');
            confirmImportBtn.disabled = false;
        }
    }


    // IMPORT (with review flow)
    // =====================================================
    const importForm = document.getElementById('importForm');
    const conflictModalBackdrop = document.getElementById('conflictModalBackdrop');
    const conflictModalContainer = document.getElementById('conflictModalContainer');
    let conflictState = { conflicts: [], to_update: [], to_create: [], decisions: [], currentIndex: 0, fileName: '' };

    function openConflictModal() {
        conflictModalBackdrop.classList.remove('hidden');
        conflictModalContainer.classList.remove('hidden');
        renderConflictList();
        renderCurrentConflict();
    }

    function closeConflictModal() {
        conflictModalBackdrop.classList.add('hidden');
        conflictModalContainer.classList.add('hidden');
    }

    if (conflictModalBackdrop) conflictModalBackdrop.addEventListener('click', closeConflictModal);

    if (importForm) {
        importForm.addEventListener('submit', function (e) {
            const strategy = document.getElementById('conflictStrategy').value;
            if (strategy === 'review') {
                e.preventDefault();
                submitForReview();
            }
        });
    }

    async function submitForReview() {
        const formData = new FormData(importForm);
        try {
            const res = await fetch(importForm.action, {
                method: 'POST',
                headers: { 'X-Requested-With': 'XMLHttpRequest' },
                body: formData,
            });
            if (!res.ok) {
                const text = await res.text();
                alert('Import failed: ' + text);
                return;
            }

            const data = await res.json();
            conflictState.conflicts = data.conflicts || [];
            conflictState.to_create = data.to_create || [];
            conflictState.to_update = data.to_update || [];
            
            // Store filename from file input
            const fileInput = document.getElementById('importFile');
            if (fileInput && fileInput.files.length > 0) {
                conflictState.fileName = fileInput.files[0].name;
            }

            // initialize decisions: conflicts default to keep existing
            conflictState.decisions = conflictState.conflicts.map(c => ({
                action: 'keep',
                existing_id: c.existing.id,
                incoming: c.incoming,
                name: c.name,
            }));

            // prepare accept/update decisions for non-conflicting existing layups
            (conflictState.to_update || []).forEach(u => {
                conflictState.decisions.push({ action: 'accept', existing_id: u.existing.id, incoming: u.incoming, name: u.incoming.name });
            });

            // finally prepare create decisions for non-conflicting new layups
            conflictState.to_create.forEach(tc => {
                conflictState.decisions.push({ action: 'create', incoming: tc });
            });

            conflictState.currentIndex = 0;

            // Close the import modal first to avoid modal overlap
            if (typeof closeImportModal === 'function') closeImportModal();
            openConflictModal();
        } catch (err) {
            console.error(err);
            alert('Failed to submit for review');
        }
    }

    function renderConflictList() {
        const list = document.getElementById('conflictList');
        const resolvedList = document.getElementById('resolvedList');
        const resolvedSection = document.getElementById('resolvedSection');
        const conflictCount = document.getElementById('conflictCount');
        
        list.innerHTML = '';
        resolvedList.innerHTML = '';
        
        // Count conflicts and show count
        conflictCount.textContent = `(${conflictState.conflicts.length})`;
        
        // Render active conflicts
        conflictState.conflicts.forEach((c, idx) => {
            const li = document.createElement('li');
            const isActive = idx === conflictState.currentIndex;
            li.className = `flex items-center gap-2 p-3 rounded-lg text-sm cursor-pointer transition-colors ${
                isActive ? 'bg-blue-50 border border-blue-200' : 'hover:bg-gray-50 border border-transparent'
            }`;
            
            // Red dot indicator
            const dot = document.createElement('span');
            dot.className = 'w-2 h-2 rounded-full bg-red-500 flex-shrink-0';
            
            const text = document.createElement('span');
            text.className = isActive ? 'text-gray-900 font-medium' : 'text-gray-700';
            text.textContent = c.name;
            
            li.appendChild(dot);
            li.appendChild(text);
            li.onclick = () => { conflictState.currentIndex = idx; renderCurrentConflict(); renderConflictList(); };
            list.appendChild(li);
        });
        
        // Render to_update items as resolved
        if (conflictState.to_update && conflictState.to_update.length > 0) {
            resolvedSection.classList.remove('hidden');
            conflictState.to_update.forEach((t, idx) => {
                const conflictsCount = (conflictState.conflicts || []).length;
                const itemIndex = conflictsCount + idx;
                const isActive = itemIndex === conflictState.currentIndex;
                const li = document.createElement('li');
                li.className = `flex items-center gap-2 p-3 rounded-lg text-sm cursor-pointer transition-colors ${
                    isActive ? 'bg-blue-50 border border-blue-200' : 'hover:bg-gray-50 border border-transparent'
                }`;
                
                // Green checkmark indicator
                const check = document.createElement('span');
                check.innerHTML = '<svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>';
                
                const text = document.createElement('span');
                text.className = isActive ? 'text-gray-900 font-medium' : 'text-gray-700';
                text.textContent = t.incoming.name || '(no name)';
                
                li.appendChild(check);
                li.appendChild(text);
                li.onclick = () => {
                    conflictState.currentIndex = itemIndex;
                    renderCurrentConflict();
                    renderConflictList();
                };
                resolvedList.appendChild(li);
            });
        } else {
            resolvedSection.classList.add('hidden');
        }
    }

    function renderCurrentConflict() {
        // combined count = conflicts + to_update
        const conflictsCount = (conflictState.conflicts || []).length;
        const toUpdateCount = (conflictState.to_update || []).length;
        const total = conflictsCount + toUpdateCount;
        const pager = document.getElementById('conflictPager');
        const fileName = conflictState.fileName || '';
        
        // Update header with filename
        document.getElementById('conflictFileName').textContent = fileName ? `[${fileName}]` : '';
        
        pager.textContent = total === 0 ? '0 of 0' : (conflictState.currentIndex + 1) + ' of ' + total + ' DISCREPANCIES';

        const existingPanel = document.getElementById('existingPanel');
        const incomingPanel = document.getElementById('incomingPanel');
        const layupTitle = document.getElementById('layupTitle');
        const layerCountBadge = document.getElementById('layerCountBadge');
        const existingMetadata = document.getElementById('existingMetadata');
        const incomingMetadata = document.getElementById('incomingMetadata');
        
        existingPanel.innerHTML = '';
        incomingPanel.innerHTML = '';

        if (total === 0) {
            existingPanel.innerHTML = '<div class="text-sm text-gray-500">No conflicts detected.</div>';
            incomingPanel.innerHTML = '<div class="text-sm text-gray-500">No conflicts detected.</div>';
            layupTitle.textContent = 'No conflicts';
            layerCountBadge.textContent = '0 LAYERS';
            return;
        }

        // determine whether current index points to a conflict or a to_update item
        let currentItem = null;
        let isUpdateItem = false;
        if (conflictState.currentIndex < conflictsCount) {
            currentItem = conflictState.conflicts[conflictState.currentIndex];
        } else {
            isUpdateItem = true;
            currentItem = conflictState.to_update[conflictState.currentIndex - conflictsCount];
        }
        
        // Update title and metadata
        const name = currentItem.incoming?.name || currentItem.name || '(no name)';
        layupTitle.textContent = name + ' Comparison';
        const layerCount = (currentItem.incoming?.layers || []).length;
        layerCountBadge.textContent = layerCount + ' LAYER' + (layerCount !== 1 ? 'S' : '');
        
        // Update metadata
        if (currentItem.existing) {
            const updated = currentItem.existing.updated_at || '';
            existingMetadata.textContent = updated ? `Last updated: ${new Date(updated).toLocaleDateString()}` : '';
        }
        incomingMetadata.textContent = 'Source: Line 34 in CSV';

        let ex = null, inc = null;
        if (isUpdateItem) {
            ex = currentItem.existing;
            inc = currentItem.incoming;
        } else {
            ex = currentItem.existing;
            inc = currentItem.incoming;
        }

        existingPanel.appendChild(renderLayupSummary(ex));
        incomingPanel.appendChild(renderLayupSummary(inc, true, ex));

        // Wire up buttons
        document.getElementById('keepExistingBtn').onclick = () => setDecisionForCurrent('keep');
        document.getElementById('acceptIncomingBtn').onclick = () => setDecisionForCurrent('accept');
        document.getElementById('duplicateBtn').onclick = () => setDecisionForCurrent('duplicate');
        document.getElementById('prevBtn').onclick = prevConflict;
        document.getElementById('nextBtn').onclick = nextConflict;
        document.getElementById('applyDecisionsBtn').onclick = applyDecisions;
        document.getElementById('conflictRejectAll').onclick = rejectAll;
    }

    function renderLayupSummary(obj, isIncoming = false, compareTo = null) {
        const container = document.createElement('div');
        container.className = 'space-y-3';

        const name = document.createElement('div');
        name.className = 'text-sm font-semibold text-gray-900';
        name.textContent = obj.name || '(no name)';
        container.appendChild(name);

        const meta = document.createElement('div');
        meta.className = 'text-xs text-gray-600 space-y-1';
        const specDiv = document.createElement('div');
        specDiv.innerHTML = 'Species/Grade: <span class="text-gray-900">' + (obj.species_grade || 'N/A') + '</span>';
        const statusDiv = document.createElement('div');
        statusDiv.innerHTML = 'Status: <span class="text-gray-900">' + (obj.status ?? 'N/A') + '</span>';
        meta.appendChild(specDiv);
        meta.appendChild(statusDiv);
        container.appendChild(meta);

        // layers table with better styling
        const table = document.createElement('table');
        table.className = 'w-full text-xs mt-4 border-collapse';
        const thead = document.createElement('thead');
        const headerRow = document.createElement('tr');
        headerRow.className = 'border-b border-gray-200';
        ['ORDER', 'THICKNESS (MM)', 'WIDTH (MM)', 'ANGLE (°)'].forEach(heading => {
            const th = document.createElement('th');
            th.className = 'text-left py-2 px-2 text-[10px] font-semibold text-gray-600 uppercase tracking-wider';
            th.textContent = heading;
            headerRow.appendChild(th);
        });
        thead.appendChild(headerRow);
        table.appendChild(thead);
        
        const tbody = document.createElement('tbody');
        (obj.layers || []).forEach((layer, idx) => {
            const tr = document.createElement('tr');
            tr.className = 'border-b border-gray-100 hover:bg-gray-50';
            
            const orderTd = document.createElement('td');
            orderTd.className = 'py-2 px-2';
            orderTd.textContent = layer.layer_order ?? '';
            
            const thkTd = document.createElement('td');
            thkTd.className = 'py-2 px-2';
            thkTd.textContent = layer.thickness ?? '';
            
            const widthTd = document.createElement('td');
            widthTd.className = 'py-2 px-2';
            widthTd.textContent = layer.width ?? '';
            
            const angleTd = document.createElement('td');
            angleTd.className = 'py-2 px-2';
            angleTd.textContent = layer.angle ?? '';
            
            // Highlight differences in red
            if (compareTo && compareTo.layers && compareTo.layers[idx]) {
                const other = compareTo.layers[idx];
                const cellsToDiff = [
                    { td: thkTd, val: layer.thickness, otherVal: other.thickness },
                    { td: widthTd, val: layer.width, otherVal: other.width },
                    { td: angleTd, val: layer.angle, otherVal: other.angle }
                ];
                cellsToDiff.forEach(cell => {
                    if ((cell.val ?? '') !== (cell.otherVal ?? '')) {
                        cell.td.className += ' bg-red-50 font-semibold text-red-700';
                    }
                });
            }
            
            tr.appendChild(orderTd);
            tr.appendChild(thkTd);
            tr.appendChild(widthTd);
            tr.appendChild(angleTd);
            tbody.appendChild(tr);
        });
        table.appendChild(tbody);
        container.appendChild(table);

        return container;
    }

    function setDecisionForCurrent(action) {
        const idx = conflictState.currentIndex;
        if (!conflictState.decisions[idx]) {
            // If decisions array shorter, try to find matching by existing id
            // but safest is to extend decisions to cover index
            while (conflictState.decisions.length <= idx) {
                conflictState.decisions.push({ action: 'keep' });
            }
        }
            conflictState.decisions[idx].action = action;
            // advance to next if any (consider conflicts + to_update)
            const conflictsCount = (conflictState.conflicts || []).length;
            const toUpdateCount = (conflictState.to_update || []).length;
            const combinedTotal = conflictsCount + toUpdateCount;
            if (idx < combinedTotal - 1) {
                conflictState.currentIndex++;
            }
            renderConflictList();
            renderCurrentConflict();
    }

    function prevConflict() {
        if (conflictState.currentIndex > 0) {
            conflictState.currentIndex--;
            renderConflictList();
            renderCurrentConflict();
        }
    }

    function nextConflict() {
        const conflictsCount = (conflictState.conflicts || []).length;
        const toUpdateCount = (conflictState.to_update || []).length;
        const total = conflictsCount + toUpdateCount;
        if (conflictState.currentIndex < total - 1) {
            conflictState.currentIndex++;
            renderConflictList();
            renderCurrentConflict();
        }
    }

    function rejectAll() {
        if (!confirm('Reject entire import? This will cancel the import and not save any data.')) return;
        closeConflictModal();
        alert('Import rejected. No changes were saved.');
    }

    async function applyDecisions() {
        // include create decisions for non-conflicts
        const payload = {
            supplier_id: document.getElementById('importSupplierId').value,
            decisions: conflictState.decisions
        };

        try {
            const res = await fetch('/clt-layups/import/resolve', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content') },
                body: JSON.stringify(payload),
            });
            if (!res.ok) {
                const txt = await res.text();
                alert('Failed to apply resolutions: ' + txt);
                return;
            }
            const data = await res.json();
            if (data.success) {
                closeConflictModal();
                location.reload();
            } else {
                alert('Failed to apply resolutions');
            }
        } catch (err) {
            console.error(err);
            alert('Error applying resolutions');
        }
    }


    // AUTO HIDE ALERT (SUCCESS / ERROR)
    // =====================================================
    setTimeout(() => {
        const successAlert = document.getElementById('successAlert');
        const errorAlert = document.getElementById('errorAlert');

        if (successAlert) successAlert.style.display = 'none';
        if (errorAlert) errorAlert.style.display = 'none';
    }, 4000);

});