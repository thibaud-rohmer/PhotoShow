# Readme

## Introduction

This script deploys PhotoShow to a Docker container just like the previous deployment script. 
The difference is that this script uses Ansible to specify the deployment steps instead of with a custom bash script.

## Usage

Run ``run.sh`` in the ``ansible`` directory and follow the instructions. You will be prompted in case there are 
missing dependencies.

## How it works

A build directory is created in ``/tmp`` and the directory in it's current state is rsynced to that build directory.
Then the generated key is copied, the variables in configuration files are filled and copied. In addition to the usual
configuration files also pre-generated xml files for the account configuration are put in place so the container is 
ready immediately for use.

## Prerequisites

The build scripts check for these dependencies and point the user to the installation instructions on the websites where
these tools are distributed from.

 - Python (to run Ansible)
 - Ansible
 - Docker
 - Docker Compose
