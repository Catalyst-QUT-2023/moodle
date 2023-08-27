                                 .-..-.
   _____                         | || |
  /____/-.---_  .---.  .---.  .-.| || | .---.
  | |  _   _  |/  _  \/  _  \/  _  || |/  __ \
  * | | | | | || |_| || |_| || |_| || || |___/
    |_| |_| |_|\_____/\_____/\_____||_|\_____

Other site customisation outside of "/local/" directory
=======================================================

Local language pack modifications
---------------------------------
Moodle supports other type of local customisation of standard language
packs. If you want to create your own language pack based on another
language create new dataroot directory with "_local" suffix, for example
following file with content changes string "Login" to "Sign in":
moodledata/lang/en_local
<?php
  $string['login'] = 'Sign in';

See also http://docs.moodle.org/en/Language_editing


Custom script injection
-----------------------
Very old customisation option that allows you to modify scripts by injecting
code right after the require 'config.php' call.

This setting is enabled by manually setting $CFG->customscripts variable
in config.php script. The value is expected to be full path to directory
with the same structure as dirroot. Please note this hack only affects
files that actually include the config.php!

Examples:
* disable one specific moodle page without code modification
* alter page parameters on the fly
