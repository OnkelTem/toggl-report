<?php

use TogglReport\TogglReport;

$toggl_token = '';
$project_id = '';
$debug = FALSE;

@include dirname(__FILE__) . '/config.php';

/**
 * Params:
 *  -t  token
 *  -p  project_id
 *  -n  number of days to report (default: 1)
 *  -T  group report by tasks, otherwise groups by date
 */
$options = getopt('t::p::n::T');

// Get the token
if (isset($options['t'])) {
  $toggl_token = $options['t'];
}
(bool) $toggl_token || fwrite(STDERR, 'Token not configured') && die();

$toggl = new TogglReport($toggl_token, $debug);

// Get the project
if (isset($options['p'])) {
  $project_id = $options['p'];
}
(bool) $project_id || fwrite(STDERR, 'Project not configured' . "\n") && $toggl->listProjects() && die();

// Get the report period
$period = isset($options['n']) ? (int) $options['n'] : 1;

echo isset($options['T'])
  ? $toggl->reportByTask($project_id, $period)
  : $toggl->reportByDate($project_id, $period);
