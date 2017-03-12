#
# We have two types of machines created in this vagrant file: localhost and droplet
#

# Require YAML module
require 'yaml'

# Read YAML file with box details
configuration = YAML.load_file('config.yaml')

Vagrant.require_version '>= 1.6.0'

Vagrant.configure(2) do |config|
    config.vm.hostname = configuration["hostname"]

    # multi-machine docs: https://www.vagrantup.com/docs/multi-machine/index.html
    config.vm.define "localhost" do |localhost|
        localhost.vm.box = configuration["box"]
    end

    # https://github.com/devopsgroup-io/vagrant-digitalocean
    config.vm.define "droplet" do |config|
      config.vm.provider :digital_ocean do |provider, override|
        override.ssh.private_key_path = '~/.ssh/id_rsa'
        override.vm.box = 'digital_ocean'
        override.vm.box_url = "https://github.com/devopsgroup-io/vagrant-digitalocean/raw/master/box/digital_ocean.box"
        provider.token = ENV['DO_TOKEN']
        provider.image = 'ubuntu-14-04-x64'
        provider.region = 'sgp1'
        provider.size = '512mb'
      end
    end

    # not used, but set a private network ip address anyway
    config.vm.network "private_network", ip: configuration["ip"]

    config.vm.provider configuration["provider"] do |vb|
        vb.name = configuration["name"]
        vb.memory = configuration["ram"]
        vb.gui = configuration["gui"]
        vb.cpus = configuration["cpus"]
        vb.customize ["modifyvm", :id, "--natdnshostresolver1", "on"]
    end

    if Vagrant.has_plugin?('vagrant-hostmanager')
        hosts = Array.new()
        config.hostmanager.enabled           = true
        config.hostmanager.manage_host       = true
        config.hostmanager.ignore_private_ip = false
        config.hostmanager.include_offline   = false
        config.hostmanager.aliases           = hosts
    end

    if Vagrant.has_plugin?("vagrant-cachier")
        config.cache.scope = :box
    end

    # note: 'localhost' now, could be other 'types' later. That would be better done in packer though.
    config.vm.synced_folder "./localhost", "/var/www/localhost", create: true, group: "www-data", owner: "www-data", :mount_options => ["dmode=777","fmode=666"]

    # Start the installations on the managed instanced by calling local ./scripts.
    # In a real scenario, we would handle this lot more differently (with error handling, dependencies, sequencing etc).
    # But for a prototype/weekend hack, it reads easier to see the installation sequence explicitly listed.
    config.vm.provision :shell, :path => "vagrant/install.sh"
    config.vm.provision :shell, :path => "vagrant/scripts/firewall.sh"
    config.vm.provision :shell, :path => "vagrant/scripts/mongodb.sh"
    config.vm.provision :shell, :path => "vagrant/scripts/nginx.sh"
    config.vm.provision :shell, :path => "vagrant/scripts/php.sh"
    config.vm.provision :shell, :path => "vagrant/scripts/zephir.sh"
    config.vm.provision :shell, :path => "vagrant/scripts/phalcon.sh"
    config.vm.provision :shell, :path => "vagrant/scripts/lynx.sh" # fixme
    config.vm.provision :shell, :path => "vagrant/scripts/pre-installed-projects.sh"
    config.vm.provision :shell, :path => "vagrant/scripts/nodejs.sh"
    config.vm.provision :shell, :path => "vagrant/scripts/elk.sh"
    config.vm.provision :shell, :path => "vagrant/postinstall.sh"

end
