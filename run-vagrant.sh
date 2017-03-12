#!/bin/bash
#
# A very simple wrapper on vagrant to manage localhost and DO instances.
#
set -e

# we want to run the vagrant scripts in the same directory as this script,
# so grab the full path to the parent dir of this script
VAGRANT_DIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )"

vagrant_type=$2 # either "localhost" or "droplet" (digital ocean instance)


case "$1" in
  start)
        cd ${VAGRANT_DIR}
        echo -n " *** Starting vagrant: "
        if [[ $vagrant_type -eq "droplet" ]]; then
          # prepend the provider argument to the vagrant commands
          vagrant_type="--provider=digital_ocean ${vagrant_type}"
        fi
        vagrant up $vagrant_type
        cd - > /dev/null
  ;;
  reload)
        cd ${VAGRANT_DIR}
        echo -n " *** Reloading and provisioning vagrant: "
        set +e
        vagrant reload $vagrant_type
        vagrant provision $vagrant_type
        set -e
        cd - > /dev/null
  ;;
  status)
        cd ${VAGRANT_DIR}
        vagrant status $vagrant_type | grep default | awk '{print $2}'
        cd - > /dev/null
  ;;
  ssh)
        cd ${VAGRANT_DIR}
        exec vagrant ssh $vagrant_type
  ;;
  stop)
        echo -n " *** Suspending vagrant machine: "
        cd ${VAGRANT_DIR}
        vagrant suspend $vagrant_type
        cd - > /dev/null
  ;;
  recreate)
        echo -n " *** Destroying all trace of the vagrant machine and rebuilding: "
        cd ${VAGRANT_DIR}
        vagrant halt $vagrant_type
        vagrant destroy $vagrant_type
        vagrant up $vagrant_type
        cd - > /dev/null
  ;;
  *)
  echo "Usage: "$0" <operation> <machine type>:"
  echo
  echo "       "$0" {start|reload|recreate|status|ssh|stop} {localhost|droplet}"
  exit 1
esac

exit 0
