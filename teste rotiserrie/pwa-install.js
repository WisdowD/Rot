let deferredPrompt;

window.addEventListener("beforeinstallprompt", (e) => {
    e.preventDefault();
    deferredPrompt = e;

    const btn = document.getElementById("installApp");
    if (btn) btn.style.display = "inline-block"; 
});

document.getElementById("installApp")?.addEventListener("click", async () => {
    if (!deferredPrompt) return;

    deferredPrompt.prompt();
    deferredPrompt = null;
});
