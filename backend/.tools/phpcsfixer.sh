#!/usr/bin/env sh
set -e

dir="${PWD}/.tools/bin"
file_path="$dir/phpcsfixer.phar"
version="3.53.0"

is_current_version()
{
  current_version=$(php "$file_path" --version)

  if echo "$current_version" | grep -q "$version"; then
    echo true
  else
    echo false
  fi
}

download_version()
{
  download_version=$1

  if ! [ -f "$file_path" ]; then
    mkdir -p "$dir"

    echo "Downloading version $download_version..."
    curl -L https://github.com/PHP-CS-Fixer/PHP-CS-Fixer/releases/download/v"$download_version"/php-cs-fixer.phar -o "$file_path"
  fi
}

update_version()
{
  is_current_version=$(is_current_version)

  if [ "$is_current_version" = false ]; then
    echo "Deleting outdated version..."
    rm "$file_path"
    download_version "$version"
  fi
}

download_version "$version"
update_version "$version"

mkdir -p .tools/cache
php "$file_path" "$@"
