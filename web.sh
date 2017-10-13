#!/bin/sh

project="${PWD##*/}"
project=$(echo $project | tr '[:upper:]' '[:lower:]' | sed -e "s/-//g")

eval "docker exec -it ${project}_web_1 $@"