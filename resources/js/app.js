import './bootstrap';
import * as bootstrap from 'bootstrap';
import Choices from 'choices.js';

// viabiliza o uso no blade
window.Choices = Choices;
window.bootstrap = bootstrap;

// Recupera somente o formulário enviado, sem perder as escolhas após um erro.
document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('[data-recuperar-formulario]').forEach(function (modal) {
        const dados = JSON.parse(modal.dataset.recuperarFormulario);
        const form = modal.querySelector('form');

        Object.entries(dados).forEach(function ([nome, valor]) {
            const campo = form.elements.namedItem(nome) || form.elements.namedItem(`${nome}[]`);
            if (!campo || nome === '_token' || nome === '_method') return;

            if (campo.multiple) {
                const valores = Array.isArray(valor) ? valor.map(String) : [];
                if (campo.escolhasInstance) {
                    campo.escolhasInstance.removeActiveItems();
                    campo.escolhasInstance.setChoiceByValue(valores);
                } else {
                    Array.from(campo.options).forEach(option => option.selected = valores.includes(option.value));
                }
            } else if (typeof valor === 'string' || typeof valor === 'number' || valor === null) {
                campo.value = valor ?? '';
            }
        });

        if (form.dataset.updateUrl && /^\d+$/.test(String(dados._registro))) {
            form.action = form.dataset.updateUrl.replace('ID_FALSO', dados._registro);
        }

        bootstrap.Modal.getOrCreateInstance(modal).show();
    });
});
