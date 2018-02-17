# Photoshow and Docker

## Build and Run

Build and run Photoshow docker container running:

```bash
sudo ./runPhotoshow.bash
```

### Host directory mapping

It's also possible to run the container with a host directory mapping of `/opt/PhotoShow` container directory.

This allows to store photos and Photoshow data outside docker.

Thus it's necessary to export the `PHOTOSHOW_HOST_DIRECTORY` variable with the absolute host directory path.<br/>
Host directory path example: `/home/data/photoshow`.

The host directory must have the two following sub directories:
 * `Photos`
    * Where photos will be read and written by Photoshow.
    * Folder and sub folders must have read permission set for everyone.<br/>
      Example: `sudo chmod -R +r /home/data/photoshow/Photos` + `sudo chown -R 33 /home/data/photoshow/Phots/` 
      
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
export PHOTOSHOW_HOST_DIRECTORY="/home/data/photoshow"
sudo -E ./runPhotoshow.bash
```
