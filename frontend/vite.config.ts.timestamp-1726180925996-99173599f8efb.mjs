// ../frontend/vite.config.ts
import { vitePlugin as remix } from "file:///C:/Users/asus/project/nestjs-remix-monorepo/node_modules/@remix-run/dev/dist/index.js";
import { installGlobals } from "file:///C:/Users/asus/project/nestjs-remix-monorepo/node_modules/@remix-run/node/dist/index.js";
import { resolve } from "path";
import { flatRoutes } from "file:///C:/Users/asus/project/nestjs-remix-monorepo/node_modules/remix-flat-routes/dist/index.js";
import { defineConfig } from "file:///C:/Users/asus/project/nestjs-remix-monorepo/node_modules/vite/dist/node/index.js";
import tsconfigPaths from "file:///C:/Users/asus/project/nestjs-remix-monorepo/node_modules/vite-tsconfig-paths/dist/index.mjs";
var __vite_injected_original_dirname = "C:\\Users\\asus\\project\\nestjs-remix-monorepo\\frontend";
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
//# sourceMappingURL=data:application/json;base64,ewogICJ2ZXJzaW9uIjogMywKICAic291cmNlcyI6IFsiLi4vZnJvbnRlbmQvdml0ZS5jb25maWcudHMiXSwKICAic291cmNlc0NvbnRlbnQiOiBbImNvbnN0IF9fdml0ZV9pbmplY3RlZF9vcmlnaW5hbF9kaXJuYW1lID0gXCJDOlxcXFxVc2Vyc1xcXFxhc3VzXFxcXHByb2plY3RcXFxcbmVzdGpzLXJlbWl4LW1vbm9yZXBvXFxcXGZyb250ZW5kXCI7Y29uc3QgX192aXRlX2luamVjdGVkX29yaWdpbmFsX2ZpbGVuYW1lID0gXCJDOlxcXFxVc2Vyc1xcXFxhc3VzXFxcXHByb2plY3RcXFxcbmVzdGpzLXJlbWl4LW1vbm9yZXBvXFxcXGZyb250ZW5kXFxcXHZpdGUuY29uZmlnLnRzXCI7Y29uc3QgX192aXRlX2luamVjdGVkX29yaWdpbmFsX2ltcG9ydF9tZXRhX3VybCA9IFwiZmlsZTovLy9DOi9Vc2Vycy9hc3VzL3Byb2plY3QvbmVzdGpzLXJlbWl4LW1vbm9yZXBvL2Zyb250ZW5kL3ZpdGUuY29uZmlnLnRzXCI7aW1wb3J0IHsgdml0ZVBsdWdpbiBhcyByZW1peCB9IGZyb20gXCJAcmVtaXgtcnVuL2RldlwiO1xyXG5pbXBvcnQgeyBpbnN0YWxsR2xvYmFscyB9IGZyb20gXCJAcmVtaXgtcnVuL25vZGVcIjtcclxuaW1wb3J0IHsgcmVzb2x2ZSB9IGZyb20gXCJwYXRoXCI7XHJcbmltcG9ydCB7IGZsYXRSb3V0ZXMgfSBmcm9tIFwicmVtaXgtZmxhdC1yb3V0ZXNcIjtcclxuaW1wb3J0IHsgZGVmaW5lQ29uZmlnIH0gZnJvbSBcInZpdGVcIjtcclxuaW1wb3J0IHRzY29uZmlnUGF0aHMgZnJvbSBcInZpdGUtdHNjb25maWctcGF0aHNcIjtcclxuXHJcbmNvbnN0IE1PREUgPSBwcm9jZXNzLmVudi5OT0RFX0VOVjtcclxuaW5zdGFsbEdsb2JhbHMoKTtcclxuXHJcbmV4cG9ydCBkZWZhdWx0IGRlZmluZUNvbmZpZyh7XHJcbiAgcmVzb2x2ZToge1xyXG4gICAgcHJlc2VydmVTeW1saW5rczogdHJ1ZSxcclxuICB9LFxyXG4gIGJ1aWxkOiB7XHJcbiAgICBjc3NNaW5pZnk6IE1PREUgPT09IFwicHJvZHVjdGlvblwiLFxyXG4gICAgc291cmNlbWFwOiB0cnVlLFxyXG4gICAgY29tbW9uanNPcHRpb25zOiB7XHJcbiAgICAgIGluY2x1ZGU6IFsvZnJvbnRlbmQvLCAvYmFja2VuZC8sIC9ub2RlX21vZHVsZXMvXSxcclxuICAgIH0sXHJcbiAgfSxcclxuICBwbHVnaW5zOiBbXHJcbiAgICAvLyBjanNJbnRlcm9wKHtcclxuICAgIC8vIFx0ZGVwZW5kZW5jaWVzOiBbJ3JlbWl4LXV0aWxzJywgJ2lzLWlwJywgJ0BtYXJrZG9jL21hcmtkb2MnXSxcclxuICAgIC8vIH0pLFxyXG4gICAgdHNjb25maWdQYXRocyh7fSksXHJcbiAgICByZW1peCh7XHJcbiAgICAgIGlnbm9yZWRSb3V0ZUZpbGVzOiBbXCIqKi8qXCJdLFxyXG4gICAgICBmdXR1cmU6IHtcclxuICAgICAgICB2M19mZXRjaGVyUGVyc2lzdDogdHJ1ZSxcclxuICAgICAgfSxcclxuXHJcbiAgICAgIC8vIFdoZW4gcnVubmluZyBsb2NhbGx5IGluIGRldmVsb3BtZW50IG1vZGUsIHdlIHVzZSB0aGUgYnVpbHQgaW4gcmVtaXhcclxuICAgICAgLy8gc2VydmVyLiBUaGlzIGRvZXMgbm90IHVuZGVyc3RhbmQgdGhlIHZlcmNlbCBsYW1iZGEgbW9kdWxlIGZvcm1hdCxcclxuICAgICAgLy8gc28gd2UgZGVmYXVsdCBiYWNrIHRvIHRoZSBzdGFuZGFyZCBidWlsZCBvdXRwdXQuXHJcbiAgICAgIC8vIGlnbm9yZWRSb3V0ZUZpbGVzOiBbJyoqLy4qJywgJyoqLyoudGVzdC57anMsanN4LHRzLHRzeH0nXSxcclxuICAgICAgc2VydmVyTW9kdWxlRm9ybWF0OiBcImVzbVwiLFxyXG5cclxuICAgICAgcm91dGVzOiBhc3luYyAoZGVmaW5lUm91dGVzKSA9PiB7XHJcbiAgICAgICAgcmV0dXJuIGZsYXRSb3V0ZXMoXCJyb3V0ZXNcIiwgZGVmaW5lUm91dGVzLCB7XHJcbiAgICAgICAgICBpZ25vcmVkUm91dGVGaWxlczogW1xyXG4gICAgICAgICAgICBcIi4qXCIsXHJcbiAgICAgICAgICAgIFwiKiovKi5jc3NcIixcclxuICAgICAgICAgICAgXCIqKi8qLnRlc3Que2pzLGpzeCx0cyx0c3h9XCIsXHJcbiAgICAgICAgICAgIFwiKiovX18qLipcIixcclxuICAgICAgICAgICAgLy8gVGhpcyBpcyBmb3Igc2VydmVyLXNpZGUgdXRpbGl0aWVzIHlvdSB3YW50IHRvIGNvbG9jYXRlIG5leHQgdG9cclxuICAgICAgICAgICAgLy8geW91ciByb3V0ZXMgd2l0aG91dCBtYWtpbmcgYW4gYWRkaXRpb25hbCBkaXJlY3RvcnkuXHJcbiAgICAgICAgICAgIC8vIElmIHlvdSBuZWVkIGEgcm91dGUgdGhhdCBpbmNsdWRlcyBcInNlcnZlclwiIG9yIFwiY2xpZW50XCIgaW4gdGhlXHJcbiAgICAgICAgICAgIC8vIGZpbGVuYW1lLCB1c2UgdGhlIGVzY2FwZSBicmFja2V0cyBsaWtlOiBteS1yb3V0ZS5bc2VydmVyXS50c3hcclxuICAgICAgICAgICAgLy8gXHQnKiovKi5zZXJ2ZXIuKicsXHJcbiAgICAgICAgICAgIC8vIFx0JyoqLyouY2xpZW50LionLFxyXG4gICAgICAgICAgXSxcclxuICAgICAgICAgIC8vIFNpbmNlIHByb2Nlc3MuY3dkKCkgaXMgdGhlIHNlcnZlciBkaXJlY3RvcnksIHdlIG5lZWQgdG8gcmVzb2x2ZSB0aGUgcGF0aCB0byByZW1peCBwcm9qZWN0XHJcbiAgICAgICAgICBhcHBEaXI6IHJlc29sdmUoX19kaXJuYW1lLCBcImFwcFwiKSxcclxuICAgICAgICB9KTtcclxuICAgICAgfSxcclxuICAgIH0pLFxyXG4gIF0sXHJcbn0pO1xyXG4iXSwKICAibWFwcGluZ3MiOiAiO0FBQTBWLFNBQVMsY0FBYyxhQUFhO0FBQzlYLFNBQVMsc0JBQXNCO0FBQy9CLFNBQVMsZUFBZTtBQUN4QixTQUFTLGtCQUFrQjtBQUMzQixTQUFTLG9CQUFvQjtBQUM3QixPQUFPLG1CQUFtQjtBQUwxQixJQUFNLG1DQUFtQztBQU96QyxJQUFNLE9BQU8sUUFBUSxJQUFJO0FBQ3pCLGVBQWU7QUFFZixJQUFPLHNCQUFRLGFBQWE7QUFBQSxFQUMxQixTQUFTO0FBQUEsSUFDUCxrQkFBa0I7QUFBQSxFQUNwQjtBQUFBLEVBQ0EsT0FBTztBQUFBLElBQ0wsV0FBVyxTQUFTO0FBQUEsSUFDcEIsV0FBVztBQUFBLElBQ1gsaUJBQWlCO0FBQUEsTUFDZixTQUFTLENBQUMsWUFBWSxXQUFXLGNBQWM7QUFBQSxJQUNqRDtBQUFBLEVBQ0Y7QUFBQSxFQUNBLFNBQVM7QUFBQTtBQUFBO0FBQUE7QUFBQSxJQUlQLGNBQWMsQ0FBQyxDQUFDO0FBQUEsSUFDaEIsTUFBTTtBQUFBLE1BQ0osbUJBQW1CLENBQUMsTUFBTTtBQUFBLE1BQzFCLFFBQVE7QUFBQSxRQUNOLG1CQUFtQjtBQUFBLE1BQ3JCO0FBQUE7QUFBQTtBQUFBO0FBQUE7QUFBQSxNQU1BLG9CQUFvQjtBQUFBLE1BRXBCLFFBQVEsT0FBTyxpQkFBaUI7QUFDOUIsZUFBTyxXQUFXLFVBQVUsY0FBYztBQUFBLFVBQ3hDLG1CQUFtQjtBQUFBLFlBQ2pCO0FBQUEsWUFDQTtBQUFBLFlBQ0E7QUFBQSxZQUNBO0FBQUE7QUFBQTtBQUFBO0FBQUE7QUFBQTtBQUFBO0FBQUEsVUFPRjtBQUFBO0FBQUEsVUFFQSxRQUFRLFFBQVEsa0NBQVcsS0FBSztBQUFBLFFBQ2xDLENBQUM7QUFBQSxNQUNIO0FBQUEsSUFDRixDQUFDO0FBQUEsRUFDSDtBQUNGLENBQUM7IiwKICAibmFtZXMiOiBbXQp9Cg==
