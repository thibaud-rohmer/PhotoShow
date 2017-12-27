# PhotoShow and Docker

To have PhotoShow running with Docker it's necessary to:
 * Build the PhotoShow image which will be used to run a container
 * Create a container based on image built
 
More details about [Docker options](https://www.docker.com)

## Build

Build PhotoShow image with the following command:

```bash
sudo docker build -t photoshow .
```

## Create and run a dedicated PhotoShow container

When PhotoShow image has been built, the following command allows to create and run a dedicated Photoshow container named `photoshow_1`:

```bash
sudo docker run --name photoshow_1 -p 8080:80 -d -i -t photoshow
```

PhotoShow is now available on: http://localhost:8080/"

### Stop container

PhotoShow container can be stopped with:

```bash
docker stop photoshow_1
```

### Remove container

PhotoShow container can be removed with:

```bash
docker rm photoshow_1
```

## Run container with host directory mapping

It's also possible to run the container with a host directory mapping of `/opt/PhotoShow` container directory.

This allows to store photos and Photoshow data outside docker.
Host directory path example: `/home/data/photoshow`.

The host directory must have the two following sub directories:
 * `Photos`
    * Where photos will be read by Photoshow.
    * Folder and sub folders must have read permission set for everyone.<br/>
      Example: `sudo chmod -R +r /home/data/photoshow/Photos`.
 * `generated`
    * Where Photoshow will store its internal data.
    * Folder and sub folders must be owned by UID `33` which is user: `www-data` in container and owner permissions set to: `rwx`.<br/>
      Example:
```bash
sudo chown -R 33 /home/data/photoshow/generated
sudo chmod -R u+rwx /home/data/photoshow/generated
```

Once host directory is configured run:

```bash
sudo docker run --name photoshow_2 -v "/home/data/photoshow:/opt/PhotoShow" -p 8080:80 -d -i -t photoshow
```

## Logs

If you want access to logs, a volume can be map to container directory: `/var/log`
