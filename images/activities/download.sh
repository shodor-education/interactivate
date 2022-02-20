#!/bin/bash

while read -r LINE; do
  short_name=$(echo ${LINE} | awk -F 'interactivate/activities/' '{print $2}' | awk -F '/' '{print $1}');
  url_prefix="https://shodor.org";
  url_suffix=$(echo ${LINE} | awk -F '&#39;' '{print $1}');
  extension=$(echo ${url_suffix} | awk -F '.' '{print $2}');
  if [ -z ${extension} ]; then
    echo ${short_name};
  fi
  #curl -k ${url_prefix}${url_suffix} --output $short_name.${extension};
done <media-urls
