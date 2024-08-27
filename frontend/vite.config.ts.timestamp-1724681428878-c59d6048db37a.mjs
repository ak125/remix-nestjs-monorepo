// vite.config.ts
import { vitePlugin as remix } from "file:///C:/Users/asus/nestjs-remix-monorepo/node_modules/@remix-run/dev/dist/index.js";
import { installGlobals } from "file:///C:/Users/asus/nestjs-remix-monorepo/node_modules/@remix-run/node/dist/index.js";
import { resolve } from "path";
import { flatRoutes } from "file:///C:/Users/asus/nestjs-remix-monorepo/node_modules/remix-flat-routes/dist/index.js";
import { defineConfig } from "file:///C:/Users/asus/nestjs-remix-monorepo/node_modules/vite/dist/node/index.js";
import tsconfigPaths from "file:///C:/Users/asus/nestjs-remix-monorepo/node_modules/vite-tsconfig-paths/dist/index.mjs";
var __vite_injected_original_dirname = "C:\\Users\\asus\\nestjs-remix-monorepo\\frontend";
var MODE = process.env.NODE_ENV;
installGlobals();
var vite_config_default = defineConfig({
  resolve: {
    preserveSymlinks: true
  },
  build: {
    cssMinify: MODE === "production",
    sourcemap: true,
    commonjsOptions: {
      include: [/frontend/, /backend/, /node_modules/]
    }
  },
  plugins: [
    // cjsInterop({
    // 	dependencies: ['remix-utils', 'is-ip', '@markdoc/markdoc'],
    // }),
    tsconfigPaths({}),
    remix({
      ignoredRouteFiles: ["**/*"],
      future: {
        v3_fetcherPersist: true
      },
      // When running locally in development mode, we use the built in remix
      // server. This does not understand the vercel lambda module format,
      // so we default back to the standard build output.
      // ignoredRouteFiles: ['**/.*', '**/*.test.{js,jsx,ts,tsx}'],
      serverModuleFormat: "esm",
      routes: async (defineRoutes) => {
        return flatRoutes("routes", defineRoutes, {
          ignoredRouteFiles: [
            ".*",
            "**/*.css",
            "**/*.test.{js,jsx,ts,tsx}",
            "**/__*.*"
            // This is for server-side utilities you want to colocate next to
            // your routes without making an additional directory.
            // If you need a route that includes "server" or "client" in the
            // filename, use the escape brackets like: my-route.[server].tsx
            // 	'**/*.server.*',
            // 	'**/*.client.*',
          ],
          // Since process.cwd() is the server directory, we need to resolve the path to remix project
          appDir: resolve(__vite_injected_original_dirname, "app")
        });
      }
    })
  ]
});
export {
  vite_config_default as default
};
//# sourceMappingURL=data:application/json;base64,ewogICJ2ZXJzaW9uIjogMywKICAic291cmNlcyI6IFsidml0ZS5jb25maWcudHMiXSwKICAic291cmNlc0NvbnRlbnQiOiBbImNvbnN0IF9fdml0ZV9pbmplY3RlZF9vcmlnaW5hbF9kaXJuYW1lID0gXCJDOlxcXFxVc2Vyc1xcXFxhc3VzXFxcXG5lc3Rqcy1yZW1peC1tb25vcmVwb1xcXFxmcm9udGVuZFwiO2NvbnN0IF9fdml0ZV9pbmplY3RlZF9vcmlnaW5hbF9maWxlbmFtZSA9IFwiQzpcXFxcVXNlcnNcXFxcYXN1c1xcXFxuZXN0anMtcmVtaXgtbW9ub3JlcG9cXFxcZnJvbnRlbmRcXFxcdml0ZS5jb25maWcudHNcIjtjb25zdCBfX3ZpdGVfaW5qZWN0ZWRfb3JpZ2luYWxfaW1wb3J0X21ldGFfdXJsID0gXCJmaWxlOi8vL0M6L1VzZXJzL2FzdXMvbmVzdGpzLXJlbWl4LW1vbm9yZXBvL2Zyb250ZW5kL3ZpdGUuY29uZmlnLnRzXCI7aW1wb3J0IHsgdml0ZVBsdWdpbiBhcyByZW1peCB9IGZyb20gXCJAcmVtaXgtcnVuL2RldlwiO1xuaW1wb3J0IHsgaW5zdGFsbEdsb2JhbHMgfSBmcm9tIFwiQHJlbWl4LXJ1bi9ub2RlXCI7XG5pbXBvcnQgeyByZXNvbHZlIH0gZnJvbSBcInBhdGhcIjtcbmltcG9ydCB7IGZsYXRSb3V0ZXMgfSBmcm9tIFwicmVtaXgtZmxhdC1yb3V0ZXNcIjtcbmltcG9ydCB7IGRlZmluZUNvbmZpZyB9IGZyb20gXCJ2aXRlXCI7XG5pbXBvcnQgdHNjb25maWdQYXRocyBmcm9tIFwidml0ZS10c2NvbmZpZy1wYXRoc1wiO1xuXG5jb25zdCBNT0RFID0gcHJvY2Vzcy5lbnYuTk9ERV9FTlY7XG5pbnN0YWxsR2xvYmFscygpO1xuXG5leHBvcnQgZGVmYXVsdCBkZWZpbmVDb25maWcoe1xuICByZXNvbHZlOiB7XG4gICAgcHJlc2VydmVTeW1saW5rczogdHJ1ZSxcbiAgfSxcbiAgYnVpbGQ6IHtcbiAgICBjc3NNaW5pZnk6IE1PREUgPT09IFwicHJvZHVjdGlvblwiLFxuICAgIHNvdXJjZW1hcDogdHJ1ZSxcbiAgICBjb21tb25qc09wdGlvbnM6IHtcbiAgICAgIGluY2x1ZGU6IFsvZnJvbnRlbmQvLCAvYmFja2VuZC8sIC9ub2RlX21vZHVsZXMvXSxcbiAgICB9LFxuICB9LFxuICBwbHVnaW5zOiBbXG4gICAgLy8gY2pzSW50ZXJvcCh7XG4gICAgLy8gXHRkZXBlbmRlbmNpZXM6IFsncmVtaXgtdXRpbHMnLCAnaXMtaXAnLCAnQG1hcmtkb2MvbWFya2RvYyddLFxuICAgIC8vIH0pLFxuICAgIHRzY29uZmlnUGF0aHMoe30pLFxuICAgIHJlbWl4KHtcbiAgICAgIGlnbm9yZWRSb3V0ZUZpbGVzOiBbXCIqKi8qXCJdLFxuICAgICAgZnV0dXJlOiB7XG4gICAgICAgIHYzX2ZldGNoZXJQZXJzaXN0OiB0cnVlLFxuICAgICAgfSxcblxuICAgICAgLy8gV2hlbiBydW5uaW5nIGxvY2FsbHkgaW4gZGV2ZWxvcG1lbnQgbW9kZSwgd2UgdXNlIHRoZSBidWlsdCBpbiByZW1peFxuICAgICAgLy8gc2VydmVyLiBUaGlzIGRvZXMgbm90IHVuZGVyc3RhbmQgdGhlIHZlcmNlbCBsYW1iZGEgbW9kdWxlIGZvcm1hdCxcbiAgICAgIC8vIHNvIHdlIGRlZmF1bHQgYmFjayB0byB0aGUgc3RhbmRhcmQgYnVpbGQgb3V0cHV0LlxuICAgICAgLy8gaWdub3JlZFJvdXRlRmlsZXM6IFsnKiovLionLCAnKiovKi50ZXN0Lntqcyxqc3gsdHMsdHN4fSddLFxuICAgICAgc2VydmVyTW9kdWxlRm9ybWF0OiBcImVzbVwiLFxuXG4gICAgICByb3V0ZXM6IGFzeW5jIChkZWZpbmVSb3V0ZXMpID0+IHtcbiAgICAgICAgcmV0dXJuIGZsYXRSb3V0ZXMoXCJyb3V0ZXNcIiwgZGVmaW5lUm91dGVzLCB7XG4gICAgICAgICAgaWdub3JlZFJvdXRlRmlsZXM6IFtcbiAgICAgICAgICAgIFwiLipcIixcbiAgICAgICAgICAgIFwiKiovKi5jc3NcIixcbiAgICAgICAgICAgIFwiKiovKi50ZXN0Lntqcyxqc3gsdHMsdHN4fVwiLFxuICAgICAgICAgICAgXCIqKi9fXyouKlwiLFxuICAgICAgICAgICAgLy8gVGhpcyBpcyBmb3Igc2VydmVyLXNpZGUgdXRpbGl0aWVzIHlvdSB3YW50IHRvIGNvbG9jYXRlIG5leHQgdG9cbiAgICAgICAgICAgIC8vIHlvdXIgcm91dGVzIHdpdGhvdXQgbWFraW5nIGFuIGFkZGl0aW9uYWwgZGlyZWN0b3J5LlxuICAgICAgICAgICAgLy8gSWYgeW91IG5lZWQgYSByb3V0ZSB0aGF0IGluY2x1ZGVzIFwic2VydmVyXCIgb3IgXCJjbGllbnRcIiBpbiB0aGVcbiAgICAgICAgICAgIC8vIGZpbGVuYW1lLCB1c2UgdGhlIGVzY2FwZSBicmFja2V0cyBsaWtlOiBteS1yb3V0ZS5bc2VydmVyXS50c3hcbiAgICAgICAgICAgIC8vIFx0JyoqLyouc2VydmVyLionLFxuICAgICAgICAgICAgLy8gXHQnKiovKi5jbGllbnQuKicsXG4gICAgICAgICAgXSxcbiAgICAgICAgICAvLyBTaW5jZSBwcm9jZXNzLmN3ZCgpIGlzIHRoZSBzZXJ2ZXIgZGlyZWN0b3J5LCB3ZSBuZWVkIHRvIHJlc29sdmUgdGhlIHBhdGggdG8gcmVtaXggcHJvamVjdFxuICAgICAgICAgIGFwcERpcjogcmVzb2x2ZShfX2Rpcm5hbWUsIFwiYXBwXCIpLFxuICAgICAgICB9KTtcbiAgICAgIH0sXG4gICAgfSksXG4gIF0sXG59KTtcbiJdLAogICJtYXBwaW5ncyI6ICI7QUFBZ1UsU0FBUyxjQUFjLGFBQWE7QUFDcFcsU0FBUyxzQkFBc0I7QUFDL0IsU0FBUyxlQUFlO0FBQ3hCLFNBQVMsa0JBQWtCO0FBQzNCLFNBQVMsb0JBQW9CO0FBQzdCLE9BQU8sbUJBQW1CO0FBTDFCLElBQU0sbUNBQW1DO0FBT3pDLElBQU0sT0FBTyxRQUFRLElBQUk7QUFDekIsZUFBZTtBQUVmLElBQU8sc0JBQVEsYUFBYTtBQUFBLEVBQzFCLFNBQVM7QUFBQSxJQUNQLGtCQUFrQjtBQUFBLEVBQ3BCO0FBQUEsRUFDQSxPQUFPO0FBQUEsSUFDTCxXQUFXLFNBQVM7QUFBQSxJQUNwQixXQUFXO0FBQUEsSUFDWCxpQkFBaUI7QUFBQSxNQUNmLFNBQVMsQ0FBQyxZQUFZLFdBQVcsY0FBYztBQUFBLElBQ2pEO0FBQUEsRUFDRjtBQUFBLEVBQ0EsU0FBUztBQUFBO0FBQUE7QUFBQTtBQUFBLElBSVAsY0FBYyxDQUFDLENBQUM7QUFBQSxJQUNoQixNQUFNO0FBQUEsTUFDSixtQkFBbUIsQ0FBQyxNQUFNO0FBQUEsTUFDMUIsUUFBUTtBQUFBLFFBQ04sbUJBQW1CO0FBQUEsTUFDckI7QUFBQTtBQUFBO0FBQUE7QUFBQTtBQUFBLE1BTUEsb0JBQW9CO0FBQUEsTUFFcEIsUUFBUSxPQUFPLGlCQUFpQjtBQUM5QixlQUFPLFdBQVcsVUFBVSxjQUFjO0FBQUEsVUFDeEMsbUJBQW1CO0FBQUEsWUFDakI7QUFBQSxZQUNBO0FBQUEsWUFDQTtBQUFBLFlBQ0E7QUFBQTtBQUFBO0FBQUE7QUFBQTtBQUFBO0FBQUE7QUFBQSxVQU9GO0FBQUE7QUFBQSxVQUVBLFFBQVEsUUFBUSxrQ0FBVyxLQUFLO0FBQUEsUUFDbEMsQ0FBQztBQUFBLE1BQ0g7QUFBQSxJQUNGLENBQUM7QUFBQSxFQUNIO0FBQ0YsQ0FBQzsiLAogICJuYW1lcyI6IFtdCn0K
