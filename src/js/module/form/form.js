import dynForm from "./dyn-forms";
import FilesUpload from "./files-upload.js";
import t from './../translations.js'

export default function form() {
    const iframes = document.querySelectorAll('iframe[data-form-id]');
    for (let i = 0; i < iframes.length; i++) {
        const form = new skeletonForm(iframes[i]);
        form.loadForm(iframes[i]);
    }
}

export class skeletonForm {
    constructor(iframe) {
        this.iframe = iframe;
    }

    loadForm(iframe) {
        const self = this;
        const iframeDoc = iframe.contentDocument || iframe.contentWindow.document;
        if (iframeDoc.readyState !== 'complete') {
            iframe.onload = function () {
                self.loadForm(iframe);
            };
            return;
        }

        const formId = iframe.getAttribute('data-form-id');
        const messageId = iframe.getAttribute('data-message-id');
        const defaultData = JSON.parse(iframe.getAttribute('data-default-data') ?? '{}');

        const emsForm = new window.emsForm({
            idForm: formId,
            idMessage: messageId,
            idIframe: iframe.id,
            context: self,
            defaultData,
            onLoad() {
                self.onLoad(this.elementForm, this.elementMessage);
            },
            onSubmit() {
                self.onSubmit(this.elementForm, this.elementMessage);
            },
            onError(errorMessage) {
                self.onError(this.elementForm, this.elementMessage, errorMessage);
            },
            onResponse(json) {
                self.onResponse(this.elementForm, this.elementMessage, json);
            }
        });
        emsForm.init();
    }

    onLoad(elementForm, elementMessage) {
        dynForm(elementForm.id);
        const fileFields = elementForm.querySelectorAll('input[type=file]');
        for (let i = 0; i < fileFields.length; i++) {
            const filesUpload = new FilesUpload();
            filesUpload.load(fileFields[i]);
        }

        const firstInvalid = elementForm.querySelector('.is-invalid');
        if (firstInvalid) {
            this.focus_on_invalid(firstInvalid);
        }
    }

    onSubmit(elementForm, elementMessage) {
        const inputs = elementForm.querySelectorAll('input, button, textarea, select');
        for (let i = 0; i < inputs.length; i++) {
            inputs[i].setAttribute('disabled', true);
        }
    }

    onError(elementForm, elementMessage, errorMessage) {
        this.addErrorMessage(elementMessage, t('form.error_try_later'));
    }

    onResponse(elementForm, elementMessage, json) {
        const responses = JSON.parse(json);
        let displayedMessage = false;
        for (let i = 0; i < responses.length; i++) {
            const response = JSON.parse(responses[i]);
            if (response.status === 'error') {
                this.addErrorMessage(elementMessage, t('form.error', { '%message%': response.data }));
                return;
            } else if (response.uid !== undefined) {
                this.addSuccessMessage(elementMessage, t('form.saved', { '%uid%': response.uid }));
                displayedMessage = true;
            }
        }
        if (!displayedMessage) {
            this.addSuccessMessage(elementMessage, t('form.processed'));
        }
    }

    addSuccessMessage(elementMessage, message) {
        const div = document.createElement('div');
        div.classList.add('p-3', 'mb-2', 'alert', 'alert-success', 'rounded');
        div.innerHTML = message;
        elementMessage.appendChild(div);
    }

    addErrorMessage(elementMessage, message) {
        const div = document.createElement('div');
        div.classList.add('p-3', 'mb-2', 'alert', 'alert-warning', 'rounded');
        div.innerHTML = message;
        elementMessage.appendChild(div);
    }

    focus_on_invalid(input) {
        const group = input.closest('.form-group');
        if (!group) return;

        const top = group.getBoundingClientRect().top + window.scrollY;

        window.scrollTo({
            top: top,
            behavior: 'smooth'
        });

        setTimeout(() => {
            input.focus();
        }, 300);
    }
}
