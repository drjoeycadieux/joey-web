document.addEventListener('DOMContentLoaded', function () {
    const text = document.querySelector('.typewriter-text');
    const textContent = text.innerText.trim();
    text.innerText = '';

    function typeWriter(text, i) {
        if (i < textContent.length) {
            text.innerText += textContent.charAt(i);
            setTimeout(function () {
                typeWriter(text, i + 1);
            }, 100); // Adjust typing speed here
        }
    }

    typeWriter(text, 0);
});
