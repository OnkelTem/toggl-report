# Toggl.com CLI reporting tool

`toggl-report` is a command-line tool for connecting to your Toggl account and creating
a nice looking report of your "time expenses" grouped by date or task.

It was created to get the clean picture of time spent and use it then to transfer log data into RedMine, Jira - easily yet manually.

## Demo

Grouping by date:

![Report for the last 10 days grouped by date](https://user-images.githubusercontent.com/114060/75772485-1aba9a80-5d5d-11ea-9bf2-3d307316be25.png)

Grouping by task:

![Report for the last 10 days grouped by task](https://user-images.githubusercontent.com/114060/75771205-8d764680-5d5a-11ea-8b66-1193267b5179.png)

## Prerequisites

In order to use this package you need to install [Composer](https://getcomposer.org/).

## Installation

```
$ composer require onkeltem/toggl-report
```

## Usage

Example invocation:
 
```
$ vendor/bin/toggl -te7581a1373e5f723e205679f0a075aa7 -p12693340 -n30 -T
```

This would print report grouped by **task** for the last **30** days for a project
with ID **12693340**. The auth token is passed via `-t` option.

You can save typing by storing `token` and `project_id` in the **config.php** file, e.g.:
 
```
<?php
    $toggl_token = 'e7581a1373e5f723e205679f0a075aa7';
    $project_id = '12693340';
```
## Windows support

The `toggl-report` is a Linux shell script so it's not gonna run in Windows environment.
I could have created a bat-file for Windows users, but that doesn't seem to be reasonable -- for
example the console colors (used by this tool) will be broken too. On top of that, Microsoft has
long been provided the support for the huge part of Linux known
as [Windows Subsystem for Linux](https://docs.microsoft.com/en-us/windows/wsl/install-win10).
It brings nice terminal app, package managers like `apt` and allows to run bash scripts **natively**.
So please, use it instead.

## TODO

- Switch to https://github.com/DataMincer/task-runner to get decent options parsing
- Add explicit date period option (currently the end date equals present day) 
- Add JSON and CSV output format
- Add support for multiple projects and accounts (tokens) (for teams maybe)
- You suggestions? Welcome to the **Issues**
