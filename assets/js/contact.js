document.addEventListener('DOMContentLoaded', () => {
    const copyButton = document.querySelector('[data-copy-key]');
    const keyField = document.querySelector('#pgp-key');

    if (!copyButton || !keyField) return;

    copyButton.addEventListener('click', async () => {
        const key = keyField.value.trim();

        if (key.includes('PASTE_JOEY_PUBLIC_KEY_HERE')) {
            copyButton.textContent = 'Add key first';
            return;
        }

        try {
            await navigator.clipboard.writeText(key);
            copyButton.textContent = 'Copied';
        } catch (error) {
            keyField.focus();
            keyField.select();
            copyButton.textContent = 'Select & copy';
        }

        window.setTimeout(() => {
            copyButton.textContent = 'Copy key';
        }, 1800);
    });
});
