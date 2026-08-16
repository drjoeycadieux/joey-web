const response = await fetch("/api/contact.php", {
    method: "POST",
    headers: {
        "Content-Type": "application/json",
        "Accept": "application/json"
    },
    body: JSON.stringify({
        name: document.getElementById("name").value.trim(),
        email: document.getElementById("email").value.trim(),
        subject: document.getElementById("subject").value.trim(),
        message: document.getElementById("message").value.trim()
    })
});

const responseText = await response.text();

console.log("HTTP status:", response.status);
console.log("PHP response:", responseText);

if (!responseText.trim()) {
    throw new Error(
        "PHP returned an empty response. HTTP status: " +
        response.status
    );
}

const result = JSON.parse(responseText);

if (!response.ok || !result.success) {
    throw new Error(result.message || "Message could not be sent.");
}