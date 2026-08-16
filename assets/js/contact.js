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

    // Contact form submission
    const contactForm = document.getElementById('contact-form');
    const formStatus = document.getElementById('form-status');

    if (contactForm) {
        contactForm.addEventListener('submit', async (e) => {
            e.preventDefault();

            // Collect form data
            const formData = new FormData(contactForm);
            const data = {
                name: formData.get('name').trim(),
                email: formData.get('email').trim(),
                subject: formData.get('subject').trim() || null,
                message: formData.get('message').trim()
            };

            // Validate required fields
            if (!data.name || !data.email || !data.message) {
                formStatus.className = 'form-status error';
                formStatus.textContent = 'Please fill in all required fields.';
                return;
            }

            // Validate email format
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test(data.email)) {
                formStatus.className = 'form-status error';
                formStatus.textContent = 'Please enter a valid email address.';
                return;
            }

            // Show loading state
            formStatus.className = 'form-status loading';
            formStatus.textContent = 'Sending message...';
            const submitButton = contactForm.querySelector('button[type="submit"]');
            submitButton.disabled = true;

            try {
                const response = await fetch('/api/process-contact.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify(data)
                });

                const result = await response.json();

                if (result.success) {
                    formStatus.className = 'form-status success';
                    formStatus.textContent = 'Message sent successfully! We\'ll get back to you soon.';
                    contactForm.reset();
                    setTimeout(() => {
                        formStatus.textContent = '';
                        formStatus.className = 'form-status';
                    }, 5000);
                } else {
                    formStatus.className = 'form-status error';
                    formStatus.textContent = result.message || 'Failed to send message. Please try again.';
                }
            } catch (error) {
                console.error('Error:', error);
                formStatus.className = 'form-status error';
                formStatus.textContent = 'An error occurred. Please try again later.';
            } finally {
                submitButton.disabled = false;
            }
        });
    }
});
