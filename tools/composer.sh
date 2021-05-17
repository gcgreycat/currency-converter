#!/bin/bash

SCRIPT=$(readlink -f "$0")
SCRIPTPATH=$(dirname "$SCRIPT")

"$SCRIPTPATH"/toolkit.sh composer "$@"
