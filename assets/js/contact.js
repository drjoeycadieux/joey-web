document.addEventListener('DOMContentLoaded', () => {
    // PGP Key copy functionality
    const copyButton = document.querySelector('[data-copy-key]');
    const keyField = document.querySelector('#pgp-key');

    if (copyButton && keyField) {
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
    }

    // Contact form - Formspree integration
    const contactForm = document.getElementById('contact-form');
    const formStatus = document.getElementById('form-status');

    if (contactForm) {
        contactForm.addEventListener('submit', (e) => {
            // Show loading state
            formStatus.className = 'form-status loading';
            formStatus.textContent = 'Sending message...';
            const submitButton = contactForm.querySelector('button[type="submit"]');
            submitButton.disabled = true;
        });

        // Listen for successful form submission redirect
        window.addEventListener('beforeunload', () => {
            const submitButton = contactForm?.querySelector('button[type="submit"]');
            if (submitButton) {
                submitButton.disabled = false;
            }
        });
    }
});
