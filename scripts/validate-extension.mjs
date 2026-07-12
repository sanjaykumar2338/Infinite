import fs from "node:fs";
import path from "node:path";

const root = path.resolve("public/extension-build/infinite-sugar");
const manifestPath = path.join(root, "manifest.json");
const disallowedHostPatterns = [/mickolidia/i, /127\.0\.0\.1/i, /localhost/i];
const requiredRootFiles = [
  "manifest.json",
  "service-worker.js",
  "background.js",
  "popup.html",
  "popup.js",
  "contentScript.js",
  "contentStyle.css",
  "extension-config.js",
  "popup-status.js",
  "welcome.html",
  "welcome-setup.js",
  "assets/icon.png"
];

const fail = (message) => {
  console.error(message);
  process.exitCode = 1;
};

const readJson = (file) => JSON.parse(fs.readFileSync(file, "utf8"));

const walk = (dir) => fs.readdirSync(dir, { withFileTypes: true }).flatMap((entry) => {
  const full = path.join(dir, entry.name);
  return entry.isDirectory() ? walk(full) : [full];
});

if (!fs.existsSync(manifestPath)) {
  fail("manifest.json is missing from the extension root.");
  process.exit(1);
}

const manifest = readJson(manifestPath);
const allFiles = walk(root);
const relativeFiles = allFiles.map((file) => path.relative(root, file));

for (const file of requiredRootFiles) {
  if (!fs.existsSync(path.join(root, file))) {
    fail(`Missing required extension file: ${file}`);
  }
}

if (manifest.manifest_version !== 3) {
  fail("manifest_version must be 3.");
}

if (manifest.version !== "1.6.2") {
  fail(`Expected extension version 1.6.2, found ${manifest.version}.`);
}

if (manifest.background?.service_worker !== "service-worker.js") {
  fail("Manifest must use service-worker.js so production config loads before background.js.");
}

const hosts = manifest.host_permissions || [];
if (!hosts.includes("https://www.infinitesugar.com/*")) {
  fail("Production backend host permission is missing.");
}

for (const host of hosts) {
  if (disallowedHostPatterns.some((pattern) => pattern.test(host))) {
    fail(`Disallowed host permission found: ${host}`);
  }
}

const contentScript = manifest.content_scripts?.[0];
if (!contentScript || !contentScript.js?.includes("extension-config.js") || !contentScript.js?.includes("contentScript.js")) {
  fail("Content script must preload extension-config.js and contentScript.js.");
}

for (const script of contentScript?.js || []) {
  if (!fs.existsSync(path.join(root, script))) {
    fail(`Manifest references missing content script: ${script}`);
  }
}

for (const css of contentScript?.css || []) {
  if (!fs.existsSync(path.join(root, css))) {
    fail(`Manifest references missing content stylesheet: ${css}`);
  }
}

for (const [size, icon] of Object.entries(manifest.icons || {})) {
  if (!fs.existsSync(path.join(root, icon))) {
    fail(`Manifest references missing ${size}px icon: ${icon}`);
  }
}

for (const html of ["popup.html", "welcome.html", "options.html", "support.html", "analyzer.html", "report-runner.html"]) {
  const htmlPath = path.join(root, html);
  if (!fs.existsSync(htmlPath)) {
    continue;
  }

  const source = fs.readFileSync(htmlPath, "utf8");
  const scripts = [...source.matchAll(/src="([^"]+)"/g)].map((match) => match[1]);

  for (const script of scripts) {
    if (!fs.existsSync(path.join(root, script))) {
      fail(`${html} references missing script ${script}.`);
    }
  }
}

for (const file of allFiles) {
  const relative = path.relative(root, file);

  if (relative.includes("node_modules/") || relative.endsWith(".map") || relative.endsWith(".zip")) {
    fail(`Extension build contains excluded file: ${relative}`);
  }

  if (path.basename(file) === "manifest.json" && relative !== "manifest.json") {
    fail(`Duplicate manifest found: ${relative}`);
  }

  if (/\.(js|json|html|css)$/.test(file)) {
    const source = fs.readFileSync(file, "utf8");
    for (const pattern of disallowedHostPatterns) {
      if (pattern.test(source)) {
        fail(`Disallowed host text found in ${relative}.`);
      }
    }
  }
}

if (relativeFiles.filter((file) => file === "manifest.json").length !== 1) {
  fail("Extension build must contain exactly one root manifest.json.");
}

if (!process.exitCode) {
  console.log("Extension artifact validation passed.");
}
