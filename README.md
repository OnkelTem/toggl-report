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

## TODO

- Switch to https://github.com/DataMincer/task-runner to get decent options parsing.
- Add JSON and CSV output format
- Add support for multiple projects and accounts (tokens) (for teams maybe)
- You suggestions? Welcome to **Issues**
