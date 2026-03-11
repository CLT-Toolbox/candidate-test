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

    // ADD
    // ===============================
    window.openAddModal = function () {
        modalTitle.textContent = 'Add Supplier';
        submitBtn.textContent = 'Save';
        supplierForm.action = "/suppliers";
        methodInput.value = "POST";
        supplierForm.reset();
        openModal();
    }

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

    // DELETE
    // ===============================
    window.confirmDelete = function (id, name) {
        Swal.fire({
            title: 'Are you sure?',
            text: "You won't to delete " + name + " ? This action can delete related CLT Layups dan Layers!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.isConfirmed) {
                let form = document.getElementById('deleteForm');
                form.action = '/suppliers/' + id;
                form.submit();
            }
        });
    }

});