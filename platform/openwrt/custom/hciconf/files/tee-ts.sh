#!/bin/ash

while IFS= read -r l; do
	printf '%s %s\n' "$(date +"%y%m%d %H%M%S")" "$l" >> "$1"
	echo "$l"
done
