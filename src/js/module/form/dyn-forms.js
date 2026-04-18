import { observeDom } from '../observe-dom.js';

export default function dynForms(formId) {
    const formWrapper = document.getElementById(formId);
    const dynForm = formWrapper.querySelector('form');

    observeDom(formWrapper, () => {
        updateVisibilities();
    });

    function getValue(name) {
        const nameSelector = `input[name="form${name}"], select[name="form${name}"], textarea[name="form${name}"]`;
        const elements = document.querySelectorAll(nameSelector);
        let array = [];
        let isArray = false;

        for (const el of elements) {
            const type = el.getAttribute('type');
            if (type === 'radio') {
                if (el.checked) return el.value;
            } else if (type === 'checkbox') {
                isArray = true;
                if (el.checked) array.push(el.value);
            } else if (el.value !== undefined) {
                array.push(el.value);
            }
        }

        if (isArray || array.length > 1) return array;
        if (array.length > 0) return array[0];
        return undefined;
    }

    function resetField(field) {
        const radios = field.querySelectorAll('input[type="radio"]');
        const checkboxes = field.querySelectorAll('input[type="checkbox"]:checked');
        const selects = field.querySelectorAll('select');

        radios.forEach(radio => radio.checked = false);
        checkboxes.forEach(checkbox => checkbox.checked = false);
        selects.forEach(select => select.value = '');
    }

    function equalOrInArray(value, expected) {
        return Array.isArray(value) ? value.includes(expected) : value === expected;
    }

    function moreThan(value, limit) {
        limit = parseInt(limit);
        return Array.isArray(value) ? value.length > limit : value !== undefined && limit === 0;
    }

    function lessThan(value, limit) {
        limit = parseInt(limit);
        return Array.isArray(value) ? value.length < limit : !(value === undefined && limit > 0);
    }

    function dateBefore(date, before) {
        const d1 = Date.parse(date), d2 = Date.parse(before);
        return !isNaN(d1) && !isNaN(d2) && d1 < d2;
    }

    function dateAfter(date, after) {
        const d1 = Date.parse(date), d2 = Date.parse(after);
        return !isNaN(d1) && !isNaN(d2) && d1 > d2;
    }

    function countValidRules(rules) {
        if (!rules) return 0;
        let count = 0;

        for (const key of Object.keys(rules)) {
            const rule = rules[key];
            const value = getValue(rule.field);

            const score = getScore(rule.field);
            let pass = false;

            switch (rule.condition) {
                case 'is': pass = equalOrInArray(value, rule.value); break;
                case 'is-not': pass = !equalOrInArray(value, rule.value); break;
                case 'more-than': pass = moreThan(value, rule.value); break;
                case 'less-than': pass = lessThan(value, rule.value); break;
                case 'date-before': pass = dateBefore(value, rule.value); break;
                case 'date-after': pass = dateAfter(value, rule.value); break;
                case 'score-below': pass = score < rule.value; break;
                case 'score-above': pass = score > rule.value; break;
                default: console.log('Unknown test: ' + rule.condition);
            }

            if (pass) count++;
        }

        return count;
    }

    function setVisibility(field) {
        const rules = field.dataset.rules ? JSON.parse(field.dataset.rules) : null;
        const showHide = field.dataset.showHide || 'show';
        const allAny = field.dataset.allAny || 'all';
        const counter = countValidRules(rules || []);
        const ruleCount = rules ? Object.keys(rules).length : 0;

        let show = true;

        if (showHide === 'hide' && allAny === 'any' && counter > 0) show = false;
        if (showHide === 'hide' && allAny === 'all' && counter === ruleCount) show = false;
        if (showHide === 'show' && allAny === 'any' && counter === 0) show = false;
        if (showHide === 'show' && allAny === 'all' && counter !== ruleCount) show = false;

        if (show) {
            field.style.display = '';
        } else {
            field.style.display = 'none';
            resetField(field);
        }
    }

    function getScore(name) {
        const elements = formWrapper.querySelectorAll(`[name="form${name}"]`);
        let total = 0;

        elements.forEach(el => {
            if (!el.checked) return;

            const rules = el.dataset.rules ? JSON.parse(el.dataset.rules) : null;
            const counter = countValidRules(rules);
            const score = parseInt(el.dataset.score || 0);
            const altScore = parseInt(el.dataset.alternativeScore || 0);
            const allAny = el.dataset.allAny || 'all';

            if (allAny === 'all' && rules.length === counter) {
                total += altScore;
            } else if (allAny === 'any' && counter > 0) {
                total += altScore;
            } else {
                total += score;
            }
        });

        return total;
    }

    function updateVisibilities() {
        formWrapper.querySelectorAll('.collapse-on-change').forEach(el => el.style.display = 'none');
        dynForm.querySelectorAll('.form-group').forEach(setVisibility);
    }

    dynForm.addEventListener('submit', (e) => {
        const active = document.activeElement;
        if (active.tagName !== 'BUTTON' || !active.getAttribute('value')) {
            e.preventDefault();
        }
        const adviceInput = dynForm.querySelector('input[name="advice"]');
        if (adviceInput) {
            adviceInput.value = active.getAttribute('value') || '';
        }
    });

    dynForm.querySelectorAll('input, select').forEach(input => {
        input.addEventListener('change', updateVisibilities);
    });

    function uncheckedOther(input) {
        const group = input.closest('.form-group');
        const others = group.querySelectorAll('input:checked:not(.none-of-the-above)');
        if (others.length > 0) {
            others.forEach(el => el.checked = false);
            updateVisibilities();
        }
    }

    formWrapper.querySelectorAll('input.none-of-the-above').forEach(input => {
        input.addEventListener('change', () => uncheckedOther(input));
    });

    function uncheckedNone(input) {
        const group = input.closest('.form-group');
        const none = group.querySelectorAll('.none-of-the-above:checked');
        if (none.length > 0) {
            none.forEach(el => el.checked = false);
            updateVisibilities();
        }
    }

    formWrapper.querySelectorAll('input[type="checkbox"]:not(.none-of-the-above)').forEach(input => {
        input.addEventListener('change', () => uncheckedNone(input));
    });

    updateVisibilities();
}
