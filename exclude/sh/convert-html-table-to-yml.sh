#!/bin/bash

NUM_TDS_IN_TR=0;
FIRST_LINE_OF_TD=1;
IFS=''; while read -r LINE; do
  if [ "${LINE}" = "<tr>" ]; then
    :;
  elif [ "${LINE}" = "<td>" ]; then
    if [ ${NUM_TDS_IN_TR} -eq 0 ]; then
      echo -n "  - function: \"";
    elif [ ${NUM_TDS_IN_TR} -eq 1 ]; then
      echo -n "    symbol: \"";
    elif [ ${NUM_TDS_IN_TR} -eq 2 ]; then
      echo -n "    examples: \"";
    else
      echo -n "    meaning: \"";
    fi;
  elif [ "${LINE}" = "</td>" ]; then
    echo "\"";
    FIRST_LINE_OF_TD=1;
    NUM_TDS_IN_TR=$((NUM_TDS_IN_TR + 1));
  elif [ "${LINE}" = "</tr>" ]; then
    NUM_TDS_IN_TR=0;
  else
    if [ ${FIRST_LINE_OF_TD} -eq 0 ]; then
      echo -n " ";
    fi
    echo -n "${LINE}";
    FIRST_LINE_OF_TD=0;
  fi;
done <${1};
