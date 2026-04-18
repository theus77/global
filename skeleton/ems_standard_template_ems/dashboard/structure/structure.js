window.addEventListener('emsReady', function () {
    Object.values(window.jsonMenuNestedComponents).forEach((jmn) => {
        jmn.element.addEventListener('jmn-load', (event) => onJmnLoad(jmn, event));
        jmn.element.addEventListener('jmn-add', (event) => onJmnAdd(jmn, event));
        jmn.element.addEventListener('jmn-modal-custom', (event) => onJmnModalCustom(jmn, event));
    });
});

function onJmnLoad(jmn, event) {
    event.preventDefault();
    jmn.element.querySelectorAll('.btn-new').forEach((button) => {
        button.addEventListener('click', () => onClickNew(jmn, button));
    });
    jmn.element.querySelectorAll('.btn-detach').forEach((button) => {
        button.addEventListener('click', () => onClickDetach(jmn, button));
    });
    jmn.loading(false);
}

function onJmnAdd(jmn, event) {
    const data = event.detail?.data;
    if (!data?.success) return;
    event.preventDefault();
    createForItem(jmn, event.detail.data.item);
}

function onJmnModalCustom(jmn, event) {
    let data = event.detail.data;
    if (data.hasOwnProperty('modalName') && data.modalName === 'modal_existing_page') {
        event.preventDefault();
        onModalExisting(jmn, event.detail.ajaxModal, data.item.id);
    }
}

function onClickNew(jmn, button) {
    const item = JSON.parse(button.dataset.item);
    createForItem(jmn, item);
}

function onClickDetach(jmn, button) {
    jmn.loading(true);
    updateDocument(button.dataset.docType, button.dataset.docId, { structure_item: null }).then((json) => {
        if (json.hasOwnProperty('success') || json.success) jmn.itemDelete(button.dataset.itemId);
    });
}

function onModalExisting(jmn, ajaxModal, itemParentId) {
    document.getElementById('jmn-existing-table').addEventListener('click', (event) => {
        if (event.target.classList.contains('btn-add-existing-content')) onChooseExisting(jmn, event.target);
    });

    async function onChooseExisting(jmn, button) {
        try {
            const { locales } = getInfo(jmn);
            const itemType = button.dataset.itemType;
            const contentType = button.dataset.contentType;

            jmn.loading(true);
            ajaxModal.close();

            const docId = button.dataset.docId;
            const doc = await getDocument(contentType, docId);

            const object = {};
            for (const locale of locales) {
                const title = doc.revision?.[locale]?.title;
                if (title) { object[locale] = { label: title }; }
            }

            let item = { type: itemType, object: object };
            const responseAdd = await jmn.itemAdd(itemParentId, item, null);
            const addedItemId = responseAdd.item?.id;

            if (addedItemId) {
                const data = { 'structure_item': addedItemId };
                const json = await updateDocument(contentType, docId, data);

                if (json?.success) {
                    jmn.load({ activeItemId: addedItemId });
                }
            }
        } catch (error) {
            console.error('Error in onChooseExisting:', error);
        }
    }
}

function getInfo(jmn) {
    const infoElement = jmn.element.querySelector('.jmn-info');
    if (!infoElement) return;

    return JSON.parse(infoElement.dataset.info);
}

function createForItem(jmn, item) {
    const { locales, contentTypes } = getInfo(jmn);
    const contentType = contentTypes[item.type];
    if (!contentType) {
        jmn.load({ activeItemId: item.id });
        return;
    }

    jmn.loading(true);
    const data = { structure_item: item.id,};
    for (const locale of locales) {
        data[locale] = { title: item.object[locale]?.label || '' };
    }

    post(`/json/data/${contentType}/create?` + new URLSearchParams({ refresh: 1 }), data)
        .then((json) => {
            if (json.hasOwnProperty('revision_id')) window.location = `/data/draft/edit/${json.revision_id}`;
        });
}

function getDocument(contentType, ouuid) {
    return get(`/json/data/${contentType}/${ouuid}`);
}

function updateDocument(contentType, ouuid, data) {
    return post(`/json/data/${contentType}/update/${ouuid}?` + new URLSearchParams({ refresh: 1}), data);
}

async function get(url) {
    let response = await fetch(url, {
        method: "GET",
        headers: { 'Content-Type': 'application/json', 'X-Log-Level': '300'}
    });
    return response.json();
}

async function post(url, data) {
    let response = await fetch(url, {
        method: "POST",
        headers: { 'Content-Type': 'application/json', 'X-Log-Level': '300'},
        body: JSON.stringify(data)
    });
    return response.json();
}