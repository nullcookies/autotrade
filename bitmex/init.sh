#!/bin/bash
clear

y=$(date +"%Y")
m=$(date +"%m")
d=$(date +"%d")
H=$(date +"%H")
M=$(date +"%M")
S=$(date +"%S")

echo "Server time $H:$M"
date
echo ""

exec("ps -U #user# -u #user# u", $output, $result);
foreach ($output AS $line) if(strpos($line, "test.php")) echo "found";