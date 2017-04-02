[![Create a Toolchain](https://console.ng.bluemix.net/devops/graphics/create_toolchain_button.png)](https://console.ng.bluemix.net/devops/setup/deploy?repository=https://github.com/carlomorgenstern/piwikbluemix-test)
### Piwik on Bluemix
A minimal asset to get Piwik running on Bluemix fast.

This is a self-assembling two-stage deployment, which builds and modifies itself from the piwik source.

#### Getting Started  (Pre-requisite: [CF CLI](https://github.com/cloudfoundry/cli/releases))
- Deploy the Bluemix app via the button above.
- Clone your new repository to your local machine. E.g.:
  ```
  git clone https://github.com/<your_account>/<app_name>/
  ```
- Browse to the url for your Piwik installation and complete the multi-step setup process. During the system check phase, it is normal to see a file integrity check warning message. This is caused by:
  - deletion of composer config files, to prevent the PHP buildpack from rebuilding Piwik
  - additional files from already inserted plugin and configuration directories
  - detection of web installer tweaks to reduce end user friction
- At this point, you should login and browse to the administration section of Piwik. It is important for you to **activate** at least one plugin (e.g. SecurityInfo) to proceed with this process. This is required in order to have a fully generated **config.ini.php** prior to downloading it. The following plugins are included by default:
  - Bandwidth
  - CustomAlerts
  - LogViewer
  - PlatformsReport
  - SecurityInfo
- Your choices will be persisted within a generated file named **config.ini.php** that we will need to pull down and persist back into the repository. As an application running on a PaaS, the app's local file storage is ephemeral. Without persistence, any restart or crash/restart sequence will cause your Piwik application to revert back to the web installer sequence.
- With the new CF Diego architecture, you have to SSH into your application instance and display the file or download it via SCP. You can read up on how to do this [here](https://docs.cloudfoundry.org/devguide/deploy-apps/ssh-apps.html#other-ssh-access).
  If you are in your repository root, the command to execute would look something like this:
  ```
  $ scp -P 2222 -o User=cf:APP-GUID/0 ssh.eu-gb.bluemix.net:2222:/app/piwik/config/config.ini.php ./bluezone/configtweaks/config.ini.php
  ```
- Perform a git add, git commit and git push to persist the config.ini.php within the IBM DevOps repository. For example,
  ```
  git add -A
  git commit -m "Persisting the installer wizard generated config.ini.php"
  git push
  ```
- With this git commit, your IBM DevOps pipeline will reassemble and redeploy Piwik. Then your Piwik application will be ready for you to use.

#### How does it work?
The magic is in the .bluemix/pipeline.yml. This file configures the Bluemix toolchain "Delivery Pipeline" plugin and includes a build script, that pulls Piwik and the other code from various locations, applies tweaks and cleans itself up into a deployable asset. You may download the built asset by accessing the builder stage and "downloading all artifacts" (which can then be tweaked further and deployed manually using the CF CLI) or simply let the pipeline continue to do the assembly and deploy effort for you.

If the "Create a toolchain" feature does not work for you for whatever reason, you can also extract the two shell scripts responsible for assembly resp. deployment, that are present in the pipeline.yml file and execute them manually.

#### Installing additional plugins
If you want to add additional Piwik plugins not included within this repo, either add their download URI in **/bluezone/config.json** or put the plugin content into a named folder in **/bluezone/custom-plugins**. You are free to add as many plugin folders as you'd like within this parent **plugins** dir.  The scripts will loop through and place them within the correct location for you.
- Perform a git add, git commit and git push to persist the newly added plugins within the IBM DevOps repository. For example,
  ```
  git add -A
  git commit -m "Added myawesomeplugin to my Piwik deploy"
  git push
  ```
- With this git commit, your IBM DevOps pipeline will reassemble and redeploy Piwik. You will then need to access the administration section of Piwik and **Activate** the myawesomeplugin. Finally, you will need to refetch the config.ini.php file using techniques outlined above and persist this updated file within the repository. As you can see, it is wise to plan ahead on plugins that you'd like to use in your deploy given the overhead involved in persisting their install and activation.

#### Reference
- [Piwikstart](https://github.com/joshisa/piwikstart/)
- [Getting Started with Piwik on Bluemix](https://developer.ibm.com/bluemix/2014/07/03/getting-started-piwik-ibm-bluemix/)
