# Mail Logger Plugin
Plugin for OJS 3.4 that allows debugging email sending through saving data into the custom log file
## Usage
1. Install and enable the plugin.
2. In OJS email config set `default = log`.
3. In the plugin settings set the path to the log file. 
Plugin will try to create a file by the path specified if it doesn't exist. 
By default, plugin suggests `path_to_files_dir/logs/email.logs`
