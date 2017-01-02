CMD /C move config\lotgd.yml config\lotgd-run.yml
CMD /C move config\lotgd-test.yml config\lotgd.yml

CMD /C phpunit --stop-on-failure

CMD /C move config\lotgd.yml config\lotgd-test.yml
CMD /C move config\lotgd-run.yml config\lotgd.yml