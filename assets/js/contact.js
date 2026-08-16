document.addEventListener("DOMContentLoaded", () => {
    const form = document.getElementById("contact-form");
    const status = document.getElementById("form-status");
    const submitButton = form?.querySelector('button[type="submit"]');

    if (!form) {
        console.error("Contact form not found.");
        return;
    }

    form.addEventListener("submit", async (event) => {
        event.preventDefault();

        status.textContent = "";
        status.className = "form-status";

        const name = document.getElementById("name").value.trim();
        const email = document.getElementById("email").value.trim();
        const subject = document.getElementById("subject").value.trim();
        const message = document.getElementById("message").value.trim();

        if (!name || !email || !message) {
            status.textContent = "Please complete all required fields.";
            status.classList.add("error");
            return;
        }

        if (submitButton) {
            submitButton.disabled = true;
            submitButton.textContent = "Sending...";
        }

        try {
            const response = await fetch("/api/contact.php", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json"
                },
                body: JSON.stringify({
                    name: name,
                    email: email,
                    subject: subject,
                    message: message
                })
            });

            const result = await response.json();

            if (!response.ok || !result.success) {
                throw new Error(
                    result.message || "Unable to send message."
                );
            }

            status.textContent = result.message;
            status.classList.add("success");

            form.reset();

        } catch (error) {

            console.error("Contact form error:", error);

            status.textContent =
                error.message ||
                "Unable to send your message. Please try again.";

            status.classList.add("error");

        } finally {

            if (submitButton) {
                submitButton.disabled = false;
                submitButton.textContent = "Send Message";
            }
        }
    });

    // PGP copy button
    const copyButton = document.querySelector("[data-copy-key]");
    const pgpKey = document.getElementById("pgp-key");

    if (copyButton && pgpKey) {
        copyButton.addEventListener("click", async () => {
            try {
                await navigator.clipboard.writeText(
                    pgpKey.value.trim()
                );

                const originalText = copyButton.textContent;

                copyButton.textContent = "Copied!";

                setTimeout(() => {
                    copyButton.textContent = originalText;
                }, 2000);

            } catch (error) {
                console.error(
                    "Unable to copy PGP key:",
                    error
                );
            }
        });
    }
});