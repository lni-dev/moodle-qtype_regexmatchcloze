# Regexmatch Cloze

## Moodle Installation
References for installation on Windows and Ubuntu are given below.
### Windows
- https://download.moodle.org/windows/
    - Download and Extract Zip
    - Start Moodle Server using `Start Moodle.exe`
    - Stop Moodle Server using `Stop Moodle.exe`

### Ubuntu
- Step-by-Step Guide: https://docs.moodle.org/404/en/Step-by-step_Installation_Guide_for_Ubuntu

## Regexmatch Cloze Installation Using Git (for development)
To install using git for the latest version (the master branch), type this command in the
`<moodle-installation>/question/type` folder of your Moodle install:
```
git clone --recurse-submodules https://github.com/lni-dev/moodle-qtype_regexmatchcloze.git regexmatchcloze
```
After the installation the moodle administration website `Website Administration` must be visited.

## Regexmatch Cloze Installation/Update (for normal use)
The Zip-File can be installed through the moodle Administration site:
`Site administration` > `Plugins` > `Install plugins`.
More Information about installing plugins can be found
[here](https://docs.moodle.org/404/en/Installing_plugins).

## IDE
The following (example) IDEs can be used to edit the code.
### PHPStorm
- Download: https://www.jetbrains.com/phpstorm/download/
    - Note: Student/Teacher license must only be used for non-commercial educational purposes.
        - (including conducting academic research or providing educational services)
        - See https://www.jetbrains.com/legal/docs/toolbox/license_educational/
        - Get license: https://www.jetbrains.com/shop/eform/students
- Select php executable: `Settings` -> `PHP` -> `CLI Interpreter` -> `...` -> `+` -> `Other Local...` -> `PHP executable`
    - If Moodle is already installed, you can use the PHP of the moodle installation
- Check out the complete `<moodle-installation>` folder as project

### Eclipse
- https://docs.moodle.org/dev/Setting_up_Eclipse
- Check out the complete `<moodle-installation>` folder as project


## Development Notes and Troubleshooting
Some notes and troubleshooting occurred during development

### Course backup remains on pending
This is a problem, if moodle is installed locally. Asynchronous backups must be disabled.
This can be done in the moodle administration site:
`Site administration` > `Courses` > `Asynchronous backups`

### Creating a new version
Some steps must be taken if a new version if the plugin should be released.
1. Increase the plugin-version in the file `version.php`. More information can be found in the specified file.
2. If any changes regarding the database were taken (e.g. a column/table added or removed), this can be
   done in `db/update.php`. The required code can mostly be generated using the moodle XMLDB Editor. More Information
   about the XMLDB Editor can be found [here](https://moodledev.io/general/development/tools/xmldb).
3. Create the new Zip for the Plugin: Zip the contents of the Plugins, so that the following structure is created:
   ```
   regexmatchcloze.zip
    | - db
      | - ...
    | - ...
    | - version.php
   ```
    - The `.git` and `.idea` folder should not be added to the zip file.

### supported PHP Version and Moodle Version
Make sure, that the Plugin supports the lowest PHP version possible for your supported
moodle version. (Set the PHP Language Level to that version)
The supported moodle version must be set in `version.php.
- [Required PHP Version](https://docs.moodle.org/404/en/PHP)
- [Moodle Versions](https://moodledev.io/general/releases)

### database errors after installation
- Check the state of the required databases using the Moodle Adminer Plugin
  (`Site administration` > `Server` > `Moodle Adminer`)
  and "repair" the database using a customized version with a `update.php`, which
  will repair the database. It is possible, that simply allowing `update.php` to run again
  may fix the problem.

### Uninstalling the plugin
To uninstall the plugin no questions of this plugin may exist. To achieve this, all entries in
the table `mdl_question` where the variable `qtype` is `regexmatchcloze` must be deleted. The Plugin
[Moodle Adminer](https://moodle.org/plugins/local_adminer) can be used to easily do this.

## Development Links with useful information
Additional advice can be found here:
- https://github.com/marcusgreen/moodle-qtype_TEMPLATE/wiki
- Backup/Restore: https://moodle.org/mod/forum/discuss.php?d=397659
