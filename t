#!/bin/bash
mv config/lotgd.yml config/lotgd-run.yml
mv config/lotgd-test.yml config/lotgd.yml
cp daenerys-test.db3.dist daenerys-test.db3

r=0
a=$(vendor/bin/phpunit --colors=always --stop-on-failure $*)
r=$?

mv config/lotgd.yml config/lotgd-test.yml
mv config/lotgd-run.yml config/lotgd.yml
rm daenerys-test.db3

echo "$a"
exit $r