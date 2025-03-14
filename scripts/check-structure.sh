#!/usr/bin/env bash

# 🎨 Couleurs améliorées pour une meilleure visibilité
COLOR_RESET='\033[0m'
COLOR_HIGHLIGHT='\033[1;36m\033[1m'
COLOR_SUCCESS='\033[1;32m'
COLOR_WARNING='\033[1;33m'
COLOR_ERROR='\033[1;31m'
COLOR_DEBUG='\033[1;34m'

# 📂 Configuration des chemins
SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
SNAPSHOT_DIR="${SCRIPT_DIR}/snapshots"
declare -A SNAPSHOT_FILES=(
  [HISTORY]="history.log"
  [ORIGINAL]="original_structure.txt" 
  [CURRENT]="current_structure.txt"
  [DIFF]="structure_diff.diff"
  [LARGE_FILES]="large_files.txt"
)

# 🔧 Configuration de l'analyse
EXCLUDE_PATTERNS="node_modules|dist|.git|.turbo|.cache|.vscode|.idea|legacy|build|.next|public|coverage|*.lock|*.log|*.zip|*.png|*.jpg"
TREE_OPTIONS="-I '${EXCLUDE_PATTERNS}' --dirsfirst -F -a --noreport"
DU_EXCLUDE="--exclude=*.git* --exclude=*node_modules*"

# 🛠 Initialisation des répertoires
init_directories() {
  mkdir -p "${SNAPSHOT_DIR}"
  for file in "${SNAPSHOT_FILES[@]}"; do
    touch "${SNAPSHOT_DIR}/${file}"
  done
}

# 🔍 Vérification des dépendances
check_dependencies() {
  local missing=()
  
  if ! command -v tree &>/dev/null; then
    missing+=("tree")
  fi

  if ! command -v du &>/dev/null; then
    missing+=("coreutils")
  fi

  if [[ ${#missing[@]} -gt 0 ]]; then
    echo -e "${COLOR_ERROR}❌ Dépendances manquantes : ${missing[*]}${COLOR_RESET}"
    exit 1
  fi
}

# 📸 Générer la structure du projet
generate_structure() {
  local output_file="${SNAPSHOT_DIR}/${SNAPSHOT_FILES[CURRENT]}"
  
  echo -e "${COLOR_DEBUG}🌳 Génération de la structure du projet...${COLOR_RESET}"
  tree ${TREE_OPTIONS} -L 3 backend frontend packages > "${output_file}" 2>/dev/null
  
  if [[ $? -ne 0 ]]; then
    echo -e "${COLOR_ERROR}❌ Échec de la génération de la structure${COLOR_RESET}"
    exit 1
  fi
}

# 🔄 Comparer les structures
compare_structures() {
  local original="${SNAPSHOT_DIR}/${SNAPSHOT_FILES[ORIGINAL]}"
  local current="${SNAPSHOT_DIR}/${SNAPSHOT_FILES[CURRENT]}"
  local diff_output="${SNAPSHOT_DIR}/${SNAPSHOT_FILES[DIFF]}"

  if [[ ! -f "${original}" ]]; then
    cp "${current}" "${original}"
    echo -e "${COLOR_SUCCESS}✅ Structure originale initialisée${COLOR_RESET}"
    return
  fi

  diff -u "${original}" "${current}" > "${diff_output}"
  
  if [[ -s "${diff_output}" ]]; then
    echo -e "${COLOR_WARNING}⚠️  Modifications détectées :${COLOR_RESET}"
    grep '^+' "${diff_output}" | sed -e 's/^+/ /' -e 's/$/\x1b[0m/'
    grep '^-' "${diff_output}" | sed -e 's/^-/ /' -e 's/$/\x1b[0m/'
  else
    echo -e "${COLOR_SUCCESS}✅ Aucun changement structurel détecté${COLOR_RESET}"
    rm "${diff_output}"
  fi
}

# 📊 Analyser l'espace disque
analyze_disk_usage() {
  local output_file="${SNAPSHOT_DIR}/${SNAPSHOT_FILES[LARGE_FILES]}"
  
  echo -e "${COLOR_DEBUG}🔍 Recherche des fichiers volumineux...${COLOR_RESET}"
  du -h ${DU_EXCLUDE} backend frontend packages | sort -hr | head -n 20 > "${output_file}"
  
  echo -e "\n${COLOR_HIGHLIGHT}📦 Top 20 des fichiers :${COLOR_RESET}"
  column -t -s $'\t' "${output_file}"
}

# 📝 Journalisation des activités
log_activity() {
  local log_file="${SNAPSHOT_DIR}/${SNAPSHOT_FILES[HISTORY]}"
  echo "[$(date +'%Y-%m-%d %H:%M:%S')] Analyse effectuée par ${USER}" >> "${log_file}"
}

# 🔄 Mettre à jour la structure de référence
update_reference() {
  cp "${SNAPSHOT_DIR}/${SNAPSHOT_FILES[CURRENT]}" "${SNAPSHOT_DIR}/${SNAPSHOT_FILES[ORIGINAL]}"
  echo -e "${COLOR_SUCCESS}✅ Structure de référence mise à jour${COLOR_RESET}"
}

# 🚀 Exécution principale
main() {
  init_directories
  check_dependencies
  generate_structure
  compare_structures
  analyze_disk_usage
  log_activity

  if [[ -t 0 ]]; then
    read -rp "$(echo -e "${COLOR_WARNING}❓ Mettre à jour la structure de référence ? [y/N] ${COLOR_RESET}")" response
    [[ "${response}" =~ ^[Yy]$ ]] && update_reference
  fi

  echo -e "\n${COLOR_SUCCESS}✨ Analyse terminée avec succès${COLOR_RESET}"
}

main "$@"