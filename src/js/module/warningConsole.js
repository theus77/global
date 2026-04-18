import t from "./translations.js"

export function showConsoleWarning() {
    if (!window.console) return;

    console.log(
        `%c${t('console.warning_title')}`,
        "background: yellow; color: red; font-size: 25px;"
    );
    console.log(
        `%c${t('console.warning_message')}`,
        "font-size: 20px;  padding: 8px; border-radius: 4px;"
    );
}