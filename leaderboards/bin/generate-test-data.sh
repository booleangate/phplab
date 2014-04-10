#!/bin/bash

domain=$1
threads=$2
scores=$3

if [ -z "$domain" ]; then
	echo "Must specify a domain."
	echo
	echo "Usage: ./generate-test-data.sh domain threads scores"
	echo "   Example ./generate-test-data.sh localhost 15 1000000"
	echo
	echo "25 threads takes about 5 minutes (on a mac mini)"
	echo
	exit 1;
fi

if [ -z "$threads" ]; then
	threads=15
fi

if [ -z "$scores" ]; then
	scores=1000000
fi

scoresPerThread=$(expr $scores / $threads)
url="http://"$domain"/api/test/generate?count="$scoresPerThread

echo "Executing 'curl $url -X POST' in $threads background threads"

for ((i=1; i<=threads; i++))
do
	curl $url -X POST &
done

echo "You can check on the count of the open threads by doing 'ps aux | grep api/test/generate | wc -l'"
echo

