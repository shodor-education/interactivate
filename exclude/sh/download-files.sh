#!/bin/bash

while IFS=',' read -ra LINE; do
  curl ${LINE[1]} -k --output ${LINE[0]};
done <${1};
