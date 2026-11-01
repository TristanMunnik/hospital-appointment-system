// Show only the "Add a User" fields that apply to the chosen role.
// Without JavaScript every field stays visible, so the form still works.
document.addEventListener('DOMContentLoaded', function () {
    const roleSelect = document.getElementById('roleSelect');
    const roleRows = document.querySelectorAll('[data-roles]');

    function updateFields() {
        roleRows.forEach(function (row) {
            const visible = row.dataset.roles === roleSelect.value;
            row.hidden = !visible;
            row.querySelectorAll('input').forEach(function (input) {
                input.disabled = !visible;
            });
        });
    }

    roleSelect.addEventListener('change', updateFields);
    updateFields();
});
