#!/bin/bash

IFS=''; while read -r LINE; do
  if [[ "${LINE}" =~ "FILENAME::".* ]]; then
    FILENAME="$(echo "${LINE}" | awk -F '::' '{print $2}').html";
  else
    echo "${LINE}" >> ${FILENAME};
  fi;
done <${1};
