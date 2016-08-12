#!/bin/bash
mv config/lotgd.yml config/lotgd-run.yml
mv config/lotgd-test.yml config/lotgd.yml

phpunit --stop-on-failure

mv config/lotgd.yml config/lotgd-test.yml
mv config/lotgd-run.yml config/lotgd.yml