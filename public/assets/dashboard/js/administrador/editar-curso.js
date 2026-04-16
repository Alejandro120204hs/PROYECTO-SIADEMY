document.addEventListener('DOMContentLoaded', function () {
    const select = document.getElementById('selectAcudiente');
    if (!select) return;

    const allChoices = Array.from(select.querySelectorAll('option'))
        .filter(function (opt) { return opt.value !== '' && !opt.disabled; })
        .map(function (opt) {
            return {
                value: opt.value,
                label: opt.textContent.trim()
            };
        });

    const choices = new Choices(select, {
        searchEnabled: true,
        shouldSort: false,
        placeholder: true,
        placeholderValue: 'Escriba el nombre del docente',
        itemSelectText: '',
        removeItemButton: false,
        choices: [],
        position: 'bottom'
    });

    select.addEventListener('showDropdown', function () {
        choices.clearChoices();
    });

    select.addEventListener('search', function (event) {
        const q = event.detail.value.trim().toLowerCase();

        if (q.length === 0) {
            choices.clearChoices();
            return;
        }

        const filtered = allChoices
            .filter(function (c) { return c.label.toLowerCase().includes(q); })
            .slice(0, 10);

        if (filtered.length > 0) {
            choices.setChoices(filtered, 'value', 'label', true);
        } else {
            choices.setChoices([
                { value: '__no_results__', label: 'No se encontraron resultados', disabled: true }
            ], 'value', 'label', true);
        }
    });

    select.addEventListener('choice', function (event) {
        if (event.detail.choice && event.detail.choice.value === '__no_results__') {
            if (typeof event.preventDefault === 'function') {
                event.preventDefault();
            }
            choices.removeActiveItems();
        }
    });
});
