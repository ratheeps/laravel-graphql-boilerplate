#!/bin/bash

set -e

# Flag for database migrations
if [ -z "${ENABLE_DB_MIGRATIONS}" ]; then
  export ENABLE_DB_MIGRATIONS='no'
fi

# Flag for cronjobs
if [ -z "${ENABLE_CRONJOBS}" ]; then
  export ENABLE_CRONJOBS='no'
fi

# Flag for supervisor
if [ -z "${ENABLE_SUPERVISOR}" ]; then
  export ENABLE_SUPERVISOR='no'
fi


if [ ! -z "${CRONJOB_LIST}" ]; then
  echo "Configuring Cron Jobs"
  # copy cron shell scripts to its destination
  mkdir -p /usr/local/bin/cron-scripts
  cp /usr/local/var/cronjobs/scripts/*.template /usr/local/bin/cron-scripts/

  # copy script template, embedding environment variables, and create shell scripts files
  for f in /usr/local/bin/cron-scripts/*.template; do
      envsubst "$EXPORT_ENVIRONMENT_VARIABLES" < $f > ${f%.template}
  done

  # make cron shell scripts executable
  find /usr/local/bin/cron-scripts/ -type f -iname "*.sh" -exec chmod +x {} \;

  # render environment variables within cron jobs txt files
  for i in $(echo $CRONJOB_LIST | sed "s/,/ /g")
  do
      envsubst "$EXPORT_ENVIRONMENT_VARIABLES" < /usr/local/var/cronjobs/"$i"-cronjobs.txt > /usr/local/var/cronjobs/"$i"-cronjobs-ready.txt
  done

  # populate cron tab from cronjobs-ready files
  find /usr/local/var/cronjobs -name '*-cronjobs-ready.txt' -exec cat {} \; | crontab -
  crontab -l

  # start cron services in background
  echo "Starting cron service"
  /usr/sbin/crond -b -l 8
fi


# Check if supervisord are enabled
if [ "${ENABLE_SUPERVISOR}" = 'yes' ]; then
  if [ ! -z "${SUPERVISOR_LIST}" ]; then
      echo "Configuring supervisor processes"
      for i in $(echo $SUPERVISOR_LIST | sed "s/,/ /g")
      do
          mv /etc/supervisor/conf.d/"$i"-supervisor.template /etc/supervisor/conf.d/"$i"-supervisor.conf
      done

      echo "Starting supervisor service"
      supervisord --configuration "/etc/supervisor/supervisord.conf"
  fi
fi
