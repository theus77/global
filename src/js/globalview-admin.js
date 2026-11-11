import $ from 'jquery';
import 'jquery-ui/ui/widgets/sortable';
import 'ckeditor4/ckeditor.js';

window.$ = window.jQuery = $;

function updateContent(url, data) {
    $.ajax({
        type: 'POST',
        url,
        data: { content: data }
    }).fail(function onFail() {
        window.alert('Impossible de sauver la derniere modification!');
    });
}

function onWysiwygChange(evt) {
    updateContent($(evt.editor.element.$).attr('data-update-url'), evt.editor.getData());
}

function inlineEdit() {
    if (!$(this).attr('contenteditable')) {
        $(this).attr('contenteditable', true);
        const editor = window.CKEDITOR.inline(this);
        editor.on('change', onWysiwygChange);
    }
}

function updateSortableWeights(containerSelector, payloadKey) {
    $(containerSelector).sortable({
        stop: function onStop() {
            $(this).find('> div').each(function updateWeight(idx, element) {
                const data = $(element).data('object');
                data[payloadKey].weight = idx;
                $.ajax({
                    type: 'POST',
                    url: $(element).data('url'),
                    data: { data }
                }).fail(function onFail() {
                    window.alert('Impossible de sauver la derniere modification!');
                });
            });
        }
    });

    $(containerSelector).disableSelection();
}

$(function initAdmin() {
    $('div.wysiwygContent').on('dblclick', inlineEdit);
    $('div.wysiwyg').each(inlineEdit);

    $('input.singleline').on('input', function onInput() {
        updateContent($(this).attr('data-update-url'), this.value);
    });

    updateSortableWeights('#sortableFlights', 'Flight');
    updateSortableWeights('#sortableGallery', 'Gallery');
});
