import { execSync } from "node:child_process";

const url = process.env.LH_URL || "http://localhost:3000";
console.log(`Running Lighthouse CI against ${url}`);
try {
  execSync(`npx -y lighthouse ${url} --preset=desktop --only-categories=performance,accessibility,seo,best-practices --quiet`, { stdio: "inherit" });
} catch (e) {
  process.exit(1);
}
