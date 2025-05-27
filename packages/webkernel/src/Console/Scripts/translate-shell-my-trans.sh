#!/bin/bash

#sudo nano /usr/local/bin/mytrans
#sudo chmod +x /usr/local/bin/mytrans

TERM="$1"
if [ -z "$TERM" ]; then
  echo "Usage: $0 <term>"
  exit 1
fi

BASE_DIR="packages/webkernel/src/lang"
BACKUP_DIR="backups/webkernel/lang"

# Language mapping for translation
declare -A LANG_MAP
LANG_MAP=(
  ["ar"]="ar"
  ["az"]="az"
  ["bg"]="bg"
  ["bn"]="bn"
  ["bs"]="bs"
  ["ca"]="ca"
  ["ckb"]="ku"
  ["cs"]="cs"
  ["da"]="da"
  ["de"]="de"
  ["el"]="el"
  ["en"]="en"
  ["es"]="es"
  ["fa"]="fa"
  ["fi"]="fi"
  ["fr"]="fr"
  ["he"]="he"
  ["hi"]="hi"
  ["hr"]="hr"
  ["hu"]="hu"
  ["hy"]="hy"
  ["id"]="id"
  ["it"]="it"
  ["ja"]="ja"
  ["ka"]="ka"
  ["km"]="km"
  ["ko"]="ko"
  ["ku"]="ku"
  ["lt"]="lt"
  ["lv"]="lv"
  ["mn"]="mn"
  ["ms"]="ms"
  ["my"]="my"
  ["nl"]="nl"
  ["no"]="no"
  ["np"]="ne"
  ["pl"]="pl"
  ["pt_BR"]="pt-BR"
  ["pt_PT"]="pt-PT"
  ["ro"]="ro"
  ["ru"]="ru"
  ["sk"]="sk"
  ["sl"]="sl"
  ["sq"]="sq"
  ["sv"]="sv"
  ["sw"]="sw"
  ["th"]="th"
  ["tr"]="tr"
  ["uk"]="uk"
  ["uz"]="uz"
  ["vi"]="vi"
  ["zh_CN"]="zh-CN"
  ["zh_TW"]="zh-TW"
)

# RTL languages
RTL_LANGS=("ar" "fa" "he" "ku" "ckb")

# Get LTR or RTL direction
get_direction() {
  for rtl in "${RTL_LANGS[@]}"; do
    if [ "$1" = "$rtl" ]; then
      echo "rtl"
      return
    fi
  done
  echo "ltr"
}

# Translate term
translate_term() {
  local file_lang="$1"
  local trans_code="${LANG_MAP[$file_lang]}"

  if [ -z "$trans_code" ]; then
    echo "$TERM"
    return
  fi

  result=$(trans -brief en:"$trans_code" "$TERM" 2>/dev/null)

  # Clean output and ensure it's just the translation
  if [ -n "$result" ]; then
    echo "$result" | grep -v "^Trans" | head -n1
  else
    echo "$TERM"
  fi
}

# Write to translation files
write_translation() {
  local lang="$1"
  local translation="$2"
  local direction=$(get_direction "$lang")

  # Create directories if needed
  for DIR in "$BASE_DIR/$lang" "$BACKUP_DIR/$lang"; do
    mkdir -p "$DIR"
    FILE="$DIR/translations.php"

    # Create or update file
    if [ ! -f "$FILE" ]; then
      cat > "$FILE" << EOF
<?php

return [
    'direction' => '$direction',
    'actions' => [
        '$TERM' => [
            'label' => '$translation',
        ],
    ],
];
EOF
    else
      # Backup existing file
      cp "$FILE" "${FILE}.bak"

      # Create temporary file for editing
      TMP=$(mktemp)

      # Escape special characters in translation
      escaped_translation=$(printf '%s' "$translation" | sed "s/'/\\\\'/g")
      escaped_term=$(printf '%s' "$TERM" | sed "s/'/\\\\'/g")

      sed -e "s/'direction' => '.*'/'direction' => '$direction'/g" "$FILE" > "$TMP"

      # Check if term already exists in file
      if grep -q "'$escaped_term' => \[" "$TMP"; then
        # Term exists, update it
        sed -i -E "s/('$escaped_term' => \[[^]]*'label' => ')[^']*'/\\1$escaped_translation'/g" "$TMP"
      else
        # Term doesn't exist, add it after actions line
        sed -i "/\s*'actions' => \[/a\\        '$escaped_term' => [\n            'label' => '$escaped_translation',\n        ]," "$TMP"
      fi

      # Move temp file to final location
      mv "$TMP" "$FILE"
    fi
  done
}

echo "Translating '$TERM' to all languages..."

for lang in "${!LANG_MAP[@]}"; do
  echo "Processing $lang..."
  translation=$(translate_term "$lang")
  write_translation "$lang" "$translation"
  sleep 0.5
done

echo "Completed. Translations saved to:"
echo "  $BASE_DIR"
echo "  $BACKUP_DIR"
