document.addEventListener("DOMContentLoaded", function () {

    const modalBackdrop = document.getElementById('modalBackdrop');
    const modalContainer = document.getElementById('modalContainer');
    const supplierForm = document.getElementById('supplierForm');
    const modalTitle = document.getElementById('modalTitle');
    const submitBtn = document.getElementById('submitBtn');
    const methodInput = document.getElementById('methodInput');

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

});