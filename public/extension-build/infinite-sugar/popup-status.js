(() => {
  const zoomUrl = (url) => {
    try {
      const parsed = new URL(url);
      const host = parsed.hostname.toLowerCase();
      return (host === "zoom.us" || host.endsWith(".zoom.us")) && /^\/(wc|j|my)(\/|$)/i.test(parsed.pathname);
    } catch (error) {
      return false;
    }
  };

  const humanPlan = (plan) => {
    if (!plan) {
      return "Checking subscription";
    }

    return plan.charAt(0).toUpperCase() + plan.slice(1);
  };

  const statusText = (state, activeTab) => {
    if (!state.authUid && !state.authToken) {
      return "Not signed in";
    }

    if (state.backendApiError) {
      return "Connection/API error";
    }

    if (!state.backendAccess) {
      return "Checking subscription";
    }

    const plan = state.backendAccess.plan || "free";
    const canGuide = Boolean(state.backendAccess.can_use_spark_call || state.backendAccess.can_use_live_insights);
    const onZoom = activeTab && zoomUrl(activeTab.url || "");

    if (!onZoom) {
      return "Unsupported page";
    }

    if (!canGuide) {
      return plan === "free" ? "Free plan" : humanPlan(plan) + " inactive";
    }

    if (state.guidanceActive) {
      return "Guidance active";
    }

    if (plan === "spark") {
      return "Spark active - Guidance ready";
    }

    if (plan === "forge") {
      return "Forge active - Guidance ready";
    }

    if (plan === "tester" || plan === "admin") {
      return humanPlan(plan) + " access - Guidance ready";
    }

    return "Guidance ready";
  };

  const render = async () => {
    const storage = await chrome.storage.local.get([
      "authUid",
      "authToken",
      "backendAccess",
      "backendApiError",
      "guidanceActive",
      "status"
    ]);
    const tabs = await chrome.tabs.query({ active: true, currentWindow: true }).catch(() => []);
    const activeTab = tabs[0] || null;
    const panel = document.getElementById("infinite-sugar-status-panel") || document.createElement("div");
    const manifest = chrome.runtime.getManifest();
    const onZoom = activeTab && zoomUrl(activeTab.url || "");
    const text = statusText(storage, activeTab);

    panel.id = "infinite-sugar-status-panel";
    panel.style.cssText = [
      "margin:12px 16px 16px",
      "padding:10px 12px",
      "border:1px solid rgba(37,99,235,.25)",
      "border-radius:8px",
      "background:#eff6ff",
      "color:#111827",
      "font:12px/1.45 system-ui,-apple-system,BlinkMacSystemFont,Segoe UI,sans-serif"
    ].join(";");
    panel.innerHTML = `
      <div style="font-weight:700">${text}</div>
      <div>${onZoom ? "Zoom page detected" : "Open a supported Zoom web meeting page, then click Start Guidance."}</div>
      <div style="margin-top:4px;color:#4b5563">Version ${manifest.version}</div>
    `;

    document.body.appendChild(panel);
  };

  if (document.readyState === "loading") {
    document.addEventListener("DOMContentLoaded", render, { once: true });
  } else {
    render();
  }

  chrome.storage.onChanged.addListener(() => {
    render();
  });
})();
