# Toggl.com CLI reporting tool

## Usage

Example invocation:
 
```
$ vendor/toggl -te7581a1373e5f723e205679f0a075aa7 -p12693340 -n30 -T
```

This would print report grouped by task for the last 30 days for a project 
with ID `12693340`. The auth token is passed via `-t` option.
 
You can save typing by storing `token` and `project_id` in the **config.php** file, e.g.:
 
```
<?php
    $toggl_token = 'e7581a1373e5f723e205679f0a075aa7';
    $project_id = '12693340';
```
## Windows support

The `toggl` script is a Linux shell script so it's not gonna run in Windows environment. I could have created a bat-file for Windows users, but that doesn't seem to be reasonable -- for example the console colors (used by this tool) will be broken too. On top of that, Microsoft has long been provided the support for the huge part of Linux known as [Windows Subsystem for Linux](https://docs.microsoft.com/en-us/windows/wsl/install-win10). It brings nice terminal app, package managers as `apt` and allows to run bash scripts **natively**. So please, use it instead.

## TODO

- Switch to https://github.com/DataMincer/task-runner to get decent options parsing.
- Add JSON and CSV output format
- Add support for multiple projects and accounts (tokens) (for teams maybe)
- You suggestions? Welcome to **Issues**
