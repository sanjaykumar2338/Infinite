(() => {
  const root = globalThis;
  const runtime = root.chrome && root.chrome.runtime ? root.chrome.runtime : null;
  const storage = root.chrome && root.chrome.storage ? root.chrome.storage.local : null;
  const manifest = runtime && runtime.getManifest ? runtime.getManifest() : {};
  const apiBaseUrl = "https://www.infinitesugar.com/api";
  const verificationMessage = "Unable to verify your subscription. Please check your connection or sign in again.";
  const originalFetch = root.fetch ? root.fetch.bind(root) : null;
  let lastHeartbeatAt = 0;

  root.INFINITE_SUGAR_CONFIG = {
    apiBaseUrl,
    extensionVersion: manifest.version || "unknown",
    verificationMessage
  };

  const apiUrlFrom = (input) => {
    if (typeof input === "string") {
      return input;
    }

    if (input && typeof input.url === "string") {
      return input.url;
    }

    if (input instanceof URL) {
      return input.toString();
    }

    return "";
  };

  const rewriteUrl = (url) => url;

  const rewriteInput = (input) => {
    const originalUrl = apiUrlFrom(input);
    const nextUrl = rewriteUrl(originalUrl);

    if (!originalUrl || originalUrl === nextUrl) {
      return input;
    }

    if (typeof input === "string" || input instanceof URL) {
      return nextUrl;
    }

    return new Request(nextUrl, input);
  };

  const rememberApiError = (message) => {
    if (!storage) {
      return;
    }

    storage.set({
      backendApiError: message || verificationMessage,
      backendApiErrorAt: Date.now()
    });
  };

  const clearApiError = () => {
    if (!storage) {
      return;
    }

    storage.set({
      backendApiError: null,
      backendApiErrorAt: null
    });
  };

  const authHeaderFrom = (init) => {
    const headers = new Headers(init && init.headers ? init.headers : undefined);
    return headers.get("Authorization");
  };

  const heartbeat = async (authorization) => {
    if (!authorization || !originalFetch) {
      return;
    }

    const now = Date.now();

    if (now - lastHeartbeatAt < 60000) {
      return;
    }

    lastHeartbeatAt = now;

    try {
      await originalFetch(apiBaseUrl + "/extension/heartbeat", {
        method: "POST",
        headers: {
          Authorization: authorization,
          "Content-Type": "application/json"
        },
        body: JSON.stringify({
          extension_version: manifest.version || null,
          platform: (root.navigator && root.navigator.platform ? root.navigator.platform : "Chrome desktop").slice(0, 120)
        })
      });
    } catch (error) {
      console.debug("[InfiniteSugar] Extension heartbeat failed.", error);
    }
  };

  if (!originalFetch || root.__infiniteSugarFetchConfigured) {
    return;
  }

  root.__infiniteSugarFetchConfigured = true;

  root.fetch = async (input, init = {}) => {
    const originalUrl = apiUrlFrom(input);
    const rewrittenUrl = rewriteUrl(originalUrl);
    const isInfiniteSugarApi = rewrittenUrl.startsWith(apiBaseUrl);

    try {
      const response = await originalFetch(rewriteInput(input), init);

      if (isInfiniteSugarApi && response.ok) {
        clearApiError();

        if (rewrittenUrl.endsWith("/access/check")) {
          heartbeat(authHeaderFrom(init));
        }
      }

      if (isInfiniteSugarApi && !response.ok && /\/(me|access\/check|extension\/heartbeat)(\?|$)/.test(rewrittenUrl)) {
        rememberApiError(verificationMessage);
      }

      return response;
    } catch (error) {
      if (isInfiniteSugarApi) {
        rememberApiError(verificationMessage);
      }

      throw error;
    }
  };
})();
