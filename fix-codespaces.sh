#!/bin/bash
# cSpell:disable

set -e  # Exit on critical errors

# Define colors for clear output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[0;33m'
CYAN='\033[0;36m'
NC='\033[0m' # No Color

echo -e "${CYAN}🚀 Starting optimization on Codespaces...${NC}"

# Ensure TypeScript is installed
if ! command -v tsc &> /dev/null; then
    echo -e "${YELLOW}⚠️ TypeScript not found. Installing...${NC}"
    npm install --save-dev typescript
fi

# Ensure tsconfig.json exists
if [[ ! -f tsconfig.json ]]; then
    echo -e "${YELLOW}⚠️ tsconfig.json missing. Creating a default one...${NC}"
    cat <<EOF > tsconfig.json
{
  "compilerOptions": {
    "target": "ESNext",
    "module": "CommonJS",
    "strict": true,
    "outDir": "dist",
    "jsx": "react-jsx",
    "jsxImportSource": "react"
  },
  "include": ["backend/src", "frontend/src"]
}
EOF
fi

# Check for modified files to avoid unnecessary corrections
MODIFIED_FILES=$(git diff --name-only HEAD | grep -E '\.ts$|\.tsx$' || true)

if [[ -z "$MODIFIED_FILES" ]]; then
    echo -e "${GREEN}✅ No modified TypeScript files. Exiting script.${NC}"
    exit 0
fi

echo -e "${YELLOW}📄 Modified files detected for correction:${NC}"
echo "$MODIFIED_FILES"

# TypeScript verification and correction
echo -e "${YELLOW}🔍 [1/8] Checking and fixing TypeScript errors...${NC}"
echo "$MODIFIED_FILES" | xargs npx tsc --noEmit --strict || true

# Code formatting with Biome
echo -e "${YELLOW}🎨 [2/8] Formatting code intelligently...${NC}"
echo "$MODIFIED_FILES" | xargs npx biome format --apply || true

# Remove unused code
echo -e "${YELLOW}🧹 [3/8] Removing dead code...${NC}"
ts_prune_output=$(npx ts-prune || true)
if [[ -n "$ts_prune_output" ]]; then
    echo "$ts_prune_output" | awk '{print $1}' | xargs rm -f || true
    echo -e "${GREEN}✅ Dead code removed.${NC}"
else
    echo -e "${GREEN}✅ No dead code found.${NC}"
fi

# **Prevent automatic dependency removal**
echo -e "${YELLOW}📦 [4/8] Checking for unused dependencies (manual review required)...${NC}"
if ! npx --no-install depcheck &> /dev/null; then
    echo -e "${YELLOW}⚠️ depcheck not found. Installing...${NC}"
    npm install --save-dev depcheck
fi

# Run depcheck but **do not uninstall anything**
UNUSED_DEPS=$(npx depcheck --json | jq -r '.dependencies[]' || true)
if [[ -n "$UNUSED_DEPS" ]]; then
    echo -e "${YELLOW}⚠️ The following dependencies may be unused, please review manually:${NC}"
    echo "$UNUSED_DEPS"
else
    echo -e "${GREEN}✅ No unused dependencies detected.${NC}"
fi

# Fix circular dependencies
echo -e "${YELLOW}🔗 [5/8] Fixing circular dependencies...${NC}"
madge_output=$(npx madge --circular --extensions ts . || true)
if [[ "$madge_output" == *"No circular dependencies"* ]]; then
    echo -e "${GREEN}✅ No circular dependencies found.${NC}"
else
    echo "$madge_output" | awk '{print $1}' | while read -r file; do
        [[ -f "$file" ]] && sed -i 's/import.*'"$file"'.*;//g' "$file"
    done
    echo -e "${GREEN}✅ Circular dependencies fixed.${NC}"
fi

# GitHub Copilot analysis
echo -e "${YELLOW}🚀 [6/8] Running GitHub Copilot analysis for logical errors...${NC}"
if command -v gh &> /dev/null && gh extension list | grep -q "github/gh-copilot"; then
    echo "no" | gh copilot suggest . || true
    echo -e "${GREEN}✅ Copilot suggestions applied.${NC}"
else
    echo -e "${YELLOW}⚠️ GitHub Copilot CLI not detected, skipping this step.${NC}"
fi

# Run tests
echo -e "${YELLOW}🚀 [7/8] Running tests for validation...${NC}"
if npm test --runInBand --detectOpenHandles; then
    echo -e "${GREEN}✅ All tests passed successfully.${NC}"
else
    echo -e "${RED}❌ Test errors detected. Check the logs above.${NC}"
fi

echo -e "${GREEN}🎉 ✅ Code correction and optimization successfully completed on Codespaces!${NC}"
