#!/bin/bash
# script to mount pogoplug to folder /home/paul/assist-pogo
# for the script to work
# check that folder ~/assist-pogo exists
targetFolder="~/assist-pogo"
pogofs="~/assist-pogo/pogoplugfs"
if [ -d $targetFolder ]; then
else
  mkdir -p ~/assist-pogo
  mkdir -p ~/scripts
fi


if [ -f $pogofs ]; then
else
  cd ~/scripts 
  wget assist.homedns.org/download/pogoplugfs
  wget assist.homedns.org/download/mountpogo
  chmod 751 pogoplugfs mountpogo.sh
fi

~/scripts/pogoplugfs --user pplug@assistsheffield.org.uk --password ilikemypogo --mountpoint ~/assist-pogo
