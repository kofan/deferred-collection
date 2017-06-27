#!/bin/bash
error=false

while [ "$#" -gt 0 ] ; do
    current=$1
    shift

    if [ ! -d $current ] && [ ! -f $current ] ; then
        echo "Invalid directory or file: $current"
        error=true
        continue
    fi

    for file in `find $current -type f \( -name '*.php' \)` ; do
        lint_result=`php -l $file`

        if [ "$lint_result" != "No syntax errors detected in $file" ] ; then
            echo $lint_result
            error=true
        fi
    done
done

if [ "$error" = true ] ; then
    exit 1
else
    exit 0
fi
