
if ('serviceWorker' in navigator) {
    navigator.serviceWorker.register('/bibledictionary/smiths-bible-dictionary/sw.js')
        .then(() => console.log("Service Worker Registered"))
        .catch(err => console.log("Service Worker Failed:", err));
}
        
document.addEventListener("DOMContentLoaded", () => {
  const installBtn = document.getElementById("installAppBtn");
  let deferredPrompt;

  window.addEventListener("beforeinstallprompt", (e) => {
    e.preventDefault();
    deferredPrompt = e;
    installBtn.style.display = "block";
  });

  installBtn.addEventListener("click", async () => {
    if (deferredPrompt) {
      deferredPrompt.prompt();
      const choice = await deferredPrompt.userChoice;
      console.log("User choice:", choice.outcome);
      deferredPrompt = null;
    }
  });
});

let fontScale = 1;  // multiplier
  const body = document.body;

  function applyZoom() {
    body.style.fontSize = (16 * fontScale) + "px";
  }

  function zoomIn() {
    fontScale += 0.1;
    applyZoom();
  }

  function zoomOut() {
    if (fontScale > 0.5) { // minimum limit
      fontScale -= 0.1;
      applyZoom();
    }
  }

  function resetZoom() {
    fontScale = 1;
    applyZoom();
  }

  applyZoom(); // initialize