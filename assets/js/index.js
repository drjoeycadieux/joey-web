document.addEventListener('DOMContentLoaded', function () {
    const text = document.querySelector('.typewriter-text');
    const textContent = text.innerText.trim();
    text.innerText = '';

    function typeWriter(text, i) {
        if (i < textContent.length) {
            text.innerText += textContent.charAt(i);
            setTimeout(function () {
                typeWriter(text, i + 1);
            }, 75); // Adjust typing speed here
        } else {
            // After finishing typing, wait 2 seconds then restart
            setTimeout(function () {
                text.innerText = '';
                typeWriter(text, 0);
            }, 2000); // Wait 2 seconds before restarting
        }
    }

    typeWriter(text, 0);
});
