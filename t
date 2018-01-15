#!/bin/bash
mv config/lotgd.yml config/lotgd-run.yml
mv config/lotgd-test.yml config/lotgd.yml

r=0
a=$(vendor/bin/phpunit --colors=always $*)
r=$?

mv config/lotgd.yml config/lotgd-test.yml
mv config/lotgd-run.yml config/lotgd.yml

echo "$a"
exit $r