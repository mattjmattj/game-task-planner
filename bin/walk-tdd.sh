#!/bin/sh

for commit in $(git rev-list --grep="#tdd" --reverse 40c44e9..HEAD)
do
    git rev-list --format=%B --max-count=1 $commit
    git checkout -q $commit
    read k
    echo "===================================================="
done