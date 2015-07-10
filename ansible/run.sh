#!/usr/bin/env bash

# Python is needed for Ansible
which python
if [ $? -ne 0 ]; then
	echo 'You need to install Python ( see https://duckduckgo.com/?q=install+python )'
	exit 1
fi

# Needed to run the provisioning scripts inside the ansible directory
which ansible-playbook
if [ $? -ne 0 ]; then
	echo 'You need to install Ansible ( see http://docs.ansible.com/intro_installation.html )'
	exit 1
fi

# Use an absolute path inside the scripts
PROJECT_DIR=$(realpath ../)

ansible-playbook -vv -i inventory provision.yml --extra-vars="project_dir=$PROJECT_DIR"
