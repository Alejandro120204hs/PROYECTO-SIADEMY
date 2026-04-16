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

    setTimeout(function () {
        const choicesWrapper = select.closest('.choices');
        if (!choicesWrapper) return;

        const inner = choicesWrapper.querySelector('.choices__inner');
        const input = choicesWrapper.querySelector('input');

        if (inner) {
            inner.setAttribute('tabindex', '3');
        }

        if (input) {
            input.setAttribute('tabindex', '-1');
        }
    }, 50);

    select.addEventListener('showDropdown', function () {
        choices.clearChoices();
        choices.setChoices(allChoices.slice(0, 4), 'value', 'label', true);
    });

    select.addEventListener('search', function (event) {
        const q = event.detail.value.trim().toLowerCase();
        choices.clearChoices();

        if (q.length === 0) {
            choices.setChoices(allChoices.slice(0, 4), 'value', 'label', true);
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
            event.preventDefault();
            choices.removeActiveItems();
        }
    });
});
