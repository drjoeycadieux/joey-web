document.addEventListener("DOMContentLoaded", () => {
    const form = document.getElementById("contact-form");
    const status = document.getElementById("form-status");
    const submitButton = form.querySelector('button[type="submit"]');

    form.addEventListener("submit", async (event) => {
        event.preventDefault();

        status.textContent = "Sending...";
        status.className = "form-status";

        submitButton.disabled = true;
        submitButton.textContent = "Sending...";

        const payload = {
            name: document.getElementById("name").value.trim(),
            email: document.getElementById("email").value.trim(),
            subject: document.getElementById("subject").value.trim(),
            message: document.getElementById("message").value.trim()
        };

        try {
            const response = await fetch("/api/contact.php", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "Accept": "application/json"
                },
                body: JSON.stringify(payload)
            });

            // IMPORTANT:
            // Read the response as text first.
            const responseText = await response.text();

            console.log("HTTP status:", response.status);
            console.log("PHP response:", responseText);

            // Server returned absolutely nothing
            if (!responseText.trim()) {
                throw new Error(
                    "PHP returned an empty response. HTTP status: " +
                    response.status
                );
            }

            // Convert response to JSON ourselves
            let result;

            try {
                result = JSON.parse(responseText);
            } catch (error) {
                console.error("Invalid JSON returned by PHP:");
                console.error(responseText);

                throw new Error(
                    "PHP did not return valid JSON. Check the browser console."
                );
            }

            if (!response.ok || !result.success) {
                throw new Error(
                    result.message || "Message could not be sent."
                );
            }

            status.textContent = result.message;
            status.classList.add("success");

            form.reset();

        } catch (error) {
            console.error("Contact form error:", error);

            status.textContent = error.message;
            status.classList.add("error");

        } finally {
            submitButton.disabled = false;
            submitButton.textContent = "Send Message";
        }
    });
});