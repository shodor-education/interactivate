#!/bin/bash

IFS=''; while read -r LINE; do
  if [[ "${LINE}" =~ "SHORTNAME::".* ]]; then
    FILENAME="$(echo "${LINE}" | awk -F '::' '{print $2}').html";
  else
    echo "${LINE}" | sed 's/^    //' >> ${FILENAME};
  fi;
done <tmp;
