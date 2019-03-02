#!/bin/bash
# Usage: ./tests/bin.sh xdebug_on
# Runs the PHPUnit tests with html coverage output in the VVV wordpress-default site.
# Setting xdebug_off or leaving it unset will run PHPUnit without creating a coverage report.

set -e

path=`pwd | sed 's/.*\(\/www\)/\1/g'`

xdebug=$1
if [ -z "$xdebug" ]; then
	xdebug="xdebug_off"
fi

if [ $xdebug = "xdebug_on" ]; then
    COVERAGE="--coverage-html /srv/$path/coverage";
fi

vagrant ssh -c "$xdebug && cd "/srv/$path" && phpunit $COVERAGE"
