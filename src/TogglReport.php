<?php

namespace TogglReport;

use AJT\Toggl\ReportsClient;
use AJT\Toggl\TogglClient;
use DateInterval;
use DatePeriod;
use DateTime;
use Exception;

class TogglReport {

  /** @var TogglClient */
  protected $togglClient;
  /** @var ReportsClient */
  protected $reportsClient;

  protected $workspaces = [];
  protected $projects = [];

  const USER_AGENT = 'Mozilla/5.0 (X11; Linux x86_64) Toggl/PHP';

  function __construct($toggl_token, $debug) {
    $params = ['api_key' => $toggl_token, 'debug' => $debug];
    $this->togglClient = TogglClient::factory($params);
    $this->reportsClient = ReportsClient::factory($params);
    $this->discover();
  }

  /**
   * Discovers available workspaces and projects
   */
  function discover() {
    $workspaces = $this->togglClient->getCommand('GetWorkspaces')->getResult();
    foreach($workspaces as $workspace) {
      $this->workspaces[$workspace['id']] = $workspace;
      $projects = $this->togglClient->getCommand('GetProjects', ['id' => $workspace['id']])->getResult();
      foreach($projects as $project) {
        $this->projects[$project['id']] = $project;
      }
    }
  }

  /**
   * @param $project_id
   * @param DateTime $from_date
   * @param DateTime $to_date
   * @return array
   */
  protected function retrieveTasks($project_id, $from_date, $to_date) {
    $page = 1;
    $time_slots = [];
    // Fetch data
    while (TRUE) {
      $result = $this->reportsClient->GetCommand('Details', [
        'workspace_id' => $this->projects[$project_id]['wid'],
        'project_ids' => $project_id,
        'since' => $from_date->format('Y-m-d'),
        'until' => $to_date->format('Y-m-d'),
        'user_agent' => self::USER_AGENT,
        'page' => $page++,
      ])->getResult();
      if (!empty($result['data'])) {
        $time_slots = array_merge($time_slots, $result['data']);
      } else {
        break;
      }
    }
    // Collect tasks
    $tasks = [];
    foreach ($time_slots as $ts) {
      $date = DateTime::createFromFormat('Y-m-d?H:i:sT', $ts['start']);
      $tasks[$ts['id']] = [
        'day' => $date->format('Y-m-d'),
        'timestamp' => $date->getTimestamp(),
        'name' => $ts['description'],
        'dur' => $ts['dur'] / 1000,
      ];
    }
    // Sort tasks by date
    uasort($tasks, function($task1, $task2) {
      return $task1['timestamp'] > $task2['timestamp'];
    });
    return $tasks;
  }

  /**
   * @param $project_id
   * @param $period
   * @return string
   * @throws Exception
   */
  function reportByDate($project_id, $period) {
    $to_date = new DateTime();
    $from_date = clone $to_date;
    $from_date = $from_date->sub(new DateInterval('P' . $period . 'D'));
    $tasks = $this->retrieveTasks($project_id, $from_date, $to_date);
    $report = [];
    foreach ($tasks as $task) {
      if (!isset($report[$task['day']][$task['name']])) {
        $report[$task['day']][$task['name']] = 0;
      }
      $report[$task['day']][$task['name']] +=  $task['dur'];
    }

    $period = new DatePeriod($from_date, new DateInterval('P1D'), $to_date);

    $dates = [];
    /** @var DateTime $date */
    foreach($period as $date) {
      $dates[$date->format('Y-m-d')] = in_array($date->format('D'), ['Sun', 'Sat']);
    }

    $c = new Colors();

    $total = 0;
    $output = '';
    foreach($dates as $date => $weekend) {
      $output .= $c->getColoredString($date, $weekend ? 'light_red' : 'white') . ' ';
      $output .= date('D', strtotime($date)) . "\n";
      $sub_total = 0;
      if (isset($report[$date])) {
        foreach($report[$date] as $task => $diff_sec) {
          $duration = round($diff_sec / 3600, 2);
          $sub_total += $duration;
          $output .= '  ' . $c->getColoredString(number_format($duration, 2), 'green');
          $output .= '  ' . $c->getColoredString($task, 'cyan');
          $output .= "\n";
        }
      }
      $output .= '  ' . $c->getColoredString(number_format($sub_total, 2), 'light_green') . "\n";
      $total += $sub_total;
    }

    $output .= "\n";
    $output .= $c->getColoredString('TOTAL', 'white') . "\n";
    $output .= '  ' . $c->getColoredString(number_format($total, 2), 'light_green') . "\n";

    return $output;
  }

  /**
   * @param $project_id
   * @param $period
   * @return string
   * @throws Exception
   */
  function reportByTask($project_id, $period) {
    $to_date = new DateTime();
    $from_date = clone $to_date;
    $from_date = $from_date->sub(new DateInterval('P' . $period . 'D'));
    $tasks = $this->retrieveTasks($project_id, $from_date, $to_date);
    $report = [];
    foreach ($tasks as $task) {
      if (!isset($report[$task['name']][$task['day']])) {
        $report[$task['name']][$task['day']] = 0;
      }
      $report[$task['name']][$task['day']] +=  $task['dur'];
    }

    $c = new Colors();

    $total = 0;
    $output = '';
    foreach($report as $task_name => $task_info) {
      $output .= $c->getColoredString($task_name, 'white') . "\n";
      $sub_total = 0;
      foreach($task_info as $day => $diff_sec) {
        $duration = round($diff_sec / 3600, 2);
        $sub_total += $duration;
        $output .= '  ' . $c->getColoredString(number_format($duration, 2), 'green');
        $output .= '  ' . $c->getColoredString($day, 'cyan');
        $output .= "\n";
      }
      $output .= '  ' . $c->getColoredString(number_format($sub_total, 2), 'light_green') . "\n";
      $total += $sub_total;
    }

    $output .= "\n";
    $output .= $c->getColoredString('TOTAL', 'white') . "\n";
    $output .= '  ' . $c->getColoredString(number_format($total, 2), 'light_green') . "\n";

    return $output;
  }

  function listProjects() {
    print("\n");
    print('Available projects: ' . "\n");
    foreach($this->workspaces as $wid => $workspace) {
      print('Workspaces: ' . $workspace['name'] . '[' . $wid . ']' . "\n");
      foreach($this->projects as $pid => $project) {
        if ($project['wid'] == $wid) {
          print("  " . 'Project: ' . $project['name'] . '[' . $pid . ']' . "\n");
        }
      }
    }
    return TRUE;
  }

}
