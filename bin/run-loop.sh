#!/bin/sh

mydir="$(cd "$(dirname "$0")"; pwd)"
while true; do
    "$mydir"/server.php
    sleep 1
done

