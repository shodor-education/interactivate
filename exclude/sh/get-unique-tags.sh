#!/bin/bash

grep '<' ${1} | awk -F '<' '{print $2}' | awk -F '>' '{print $1}' | sort | uniq;
