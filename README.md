[![Deploy to Bluemix](https://bluemix.net/deploy/button.png)](https://bluemix.net/deploy?repository=https://github.com/carlomorgenstern/piwikbluemix)
### Piwik on Bluemix
A minimal asset to get Piwik running on Bluemix fast.
Forked and modified from [here.](https://github.com/joshisa/piwikstart)

This is a self-assembling two-stage deployment, which builds and modifies itself from the piwik source.

#### Getting Started  (Pre-requisite: [CF CLI](https://github.com/cloudfoundry/cli/releases))
- Deploy the Bluemix app via the button above.
- Clone your new repository to your local machine.
  ```
  git clone https://hub.jazz.net/git/<your_id>/<app_name>/
  ```
- Browse to the url for your Piwik installation and complete the multi-step setup process. During the system check phase, it is normal to see a file integrity check warning message. This is caused by:
  - movement of composer config files
  - missing .gitignore files
  - detection of web installer tweaks to reduce end user friction
  - 403 blocked access for the piwik.php test because of default security hardening
- At this point, you should login and browse to the administration section of Piwik. It is important for you to **activate** at least one plugin (e.g. SecurityInfo) to proceed with this process. This is required in order to have a fully generated **config.ini.php** prior to downloading it. **NOTE**: To encourage better security practices, the deploy is configured to only allow login via **HTTPS** and will force redirect any attempts to access the deploy via NON-SSL. The following plugins are included by default:
  - SecurityInfo
  - PerformanceInfo
  - SimpleSysMon
  - PlatformsReport
  - CustomAlerts
  - SimplePageBuilder
- Your choices will be persisted within a generated file named **config.ini.php** that we will need to pull down and persist back into the repository. As an application running on a PaaS, the app's local file storage is ephemeral. Without persistence, any restart or crash/restart sequence will cause your Piwik application to revert back to the web installer sequence.
- Within the terminal, browse to the root dir of your local cloned repo and execute a command similar to:
  ```
  $ cf files <replace_me_with_app_name> /app/fetchConfig.sh | sed -e '1,3d' > fetchConfig.sh
  $ chmod +x fetchConfig.sh
  $ ./fetchConfig.sh
  ```
- This pulls down a helper bash script named fetchConfig.sh. Your app's name will already be populated. This script helps you (repeatedly) persist the config file in the expected location. It also will **DEACTIVATE** some of the Example Plugins that are deemed a performance+security vulnerability.
- Perform a git add, git commit and git push to persist the config.ini.php within the IBM DevOps repository. For example,
  ```
  git add -A
  git commit -m "Persisting the installer wizard generated config.ini.php"
  git push
  ```
- With this git commit, your IBM DevOps pipeline will reassemble and redeploy Piwik. Then your Piwik application will be ready for you to use.

#### How does it work?
The magic is in the .bluemix/pipeline.yml. This file configures the Bluemix DevOps pipeline and includes a build script, that pulls Piwik and the other code from various locations, applies tweaks and cleans itself up into a deployable asset. You may download the built asset by accessing the builder stage and "downloading all artifacts" (which can then be tweaked further and deployed manually using the CF CLI) or simply let the pipeline continue to do the assembly and deploy effort for you.

If the Deploy to Bluemix feature does not work for you for whatever reason, you can also extract the two shell scripts responsible for assembly resp. deployment, that are present in the pipeline.yml file and execute them manually.

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
