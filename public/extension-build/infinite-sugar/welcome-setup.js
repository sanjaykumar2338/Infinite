(() => {
  const manifest = chrome.runtime.getManifest();
  const setupUrl = "https://www.infinitesugar.com/extension#install-instructions";

  const render = () => {
    const panel = document.createElement("section");
    panel.style.cssText = [
      "max-width:760px",
      "margin:24px auto 0",
      "padding:20px",
      "border:1px solid rgba(37,99,235,.25)",
      "border-radius:8px",
      "background:#eff6ff",
      "color:#111827",
      "font:16px/1.55 system-ui,-apple-system,BlinkMacSystemFont,Segoe UI,sans-serif"
    ].join(";");
    panel.innerHTML = `
      <h1 style="margin:0 0 8px;font-size:24px">InfiniteSugar is installed successfully.</h1>
      <p style="margin:0 0 12px">Version ${manifest.version}</p>
      <div style="display:flex;flex-wrap:wrap;gap:10px">
        <button id="infinite-sugar-open-popup" style="border:0;border-radius:999px;background:#1d4ed8;color:white;font-weight:700;padding:10px 16px;cursor:pointer">Sign in</button>
        <a href="${setupUrl}" target="_blank" rel="noopener" style="border:1px solid #93c5fd;border-radius:999px;color:#1d4ed8;font-weight:700;padding:9px 16px;text-decoration:none">Setup instructions</a>
      </div>
    `;

    document.body.prepend(panel);

    document.getElementById("infinite-sugar-open-popup").addEventListener("click", async () => {
      if (chrome.action && chrome.action.openPopup) {
        await chrome.action.openPopup().catch(() => chrome.tabs.create({ url: chrome.runtime.getURL("popup.html") }));
      } else {
        chrome.tabs.create({ url: chrome.runtime.getURL("popup.html") });
      }
    });
  };

  if (document.readyState === "loading") {
    document.addEventListener("DOMContentLoaded", render, { once: true });
  } else {
    render();
  }
})();
