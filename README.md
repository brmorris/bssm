Brad's Toy Service Manager
==========================

This is my toy service manager using some tech the author has been wanting to play around with.
This is a purely a learning exercise for the author. The idea was to gain some experience with
these technologies:

  - the PHP language
  - Phalcon
  - Micro REST APIs with Phalcon
  - Volt
  - MongoDB
  - Nginx, especially in relation to running PHP apps within it
  - multi-machine Vagrant files

# Overview

Here's a quick explanation of what this tool does.

Briefly, this tool provides an API and simple web UI on a set of "Services" that run on the local machine.
Each Service has a number of "Operations", which are commands that run on the service. For example, the
'nginx' service might have a 'reload' operation. There is an API to 'execute' these operations and also
lookup the results of past Executions of Operations.

## Services.json

Services are currently defined in the local `./Services.json` file. This contains the data
for what Services and Operations are currently defined. There is no support for updating the
data in Services.json via a REST API - the defined Services and Operations are GET/read-only.

## REST API

See the "API documentation tab" for usage of the API.

# Directory structure and Key files

## Structure

  - the `./localhost` directory contains the Phalcon application.
  - the `./vagrant` directory contains the phalcon files
  - notably, `./vagrant/scripts` contains the installation scripts called by the Vagrant file

## Key files

  - `./run-vagrant.sh` is a wrapper on managing local and remote vagrant images
  - `./localhost/services.json` contains the definition of the Services and Operations used by the app.
  - `./Vagrantfile` is the uh, Vagrant file

# Vagrant

## Getting Started

### Local machine

1. Download and install [VirtualBox](https://www.virtualbox.org/)
2. Download and install [Vagrant](http://www.vagrantup.com/)
3. Clone project and run `./run-instance.sh start localhost`

### Digital Ocean Droplet

1. Download and install [VirtualBox](https://www.virtualbox.org/)
2. Download and install [Vagrant](http://www.vagrantup.com/)
3. Set the DO_ENV to your DO API token
4. Clone project and run `./run-instance.sh start droplet`
5. Check the DO web UI for the new

### Vagrant Plugins

You probably need to install some Vagrant plugins.

vagrant host manager plugin:

```bash
vagrant plugin install vagrant-hostmanager
```

and vagrant cachier (to cache shared packages installation):

```bash
vagrant plugin install vagrant-cachier
```
### DO machine

```bash
vagrant plugin install vagrant-digitalocean
```

# Machine / instance management

A convenience script is at `./run-vagrant.sh` that can be used to manage local
and remote vagrant instances.

This script does what you would expect with the following usage statement:

```
$ ./run-vagrant.sh

Usage: ./run-vagrant.sh <operation> <instance type>:

       ./run-vagrant.sh {start|reload|recreate|status|ssh|stop} {localhost|droplet}
```

## Vagrant Commands

To see the vagrant commands used to manage these instances, check the source of `./run-vagrant.sh`.

##  Software:

Default vm parameters:

```yaml
name: phalcon2-dev
hostname: vm.local
box: ubuntu/trusty64
provider: virtualbox
gui: false
ram: 512
cpus: 1
ip: 10.10.10.150
projects-folder: "~/projects"
```

# Notes

## MongoDB

List and show collections:
```
vagrant@brads-sandbox:~$ mongo services
MongoDB shell version: 2.6.12
connecting to: services
> show collections
logs
system.indexes
> db.logs.find() #
> db.logs.find({"_id": ObjectId("58c38fb4c99c687f92682673")})
```

TODO
----

Next steps:

  - add unit tests (!)
  - use redis to lock a service during an operation run
  - configure Executions data table for filtering
  - support richer querying of Executions: eg allow filtering
    by category /
  - rate limiting
  - save date created and client data (like IP, UA) in mongodb

License
-------

This project is open-sourced software licensed under the MIT License.

