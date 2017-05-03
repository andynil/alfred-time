<?php

require 'AlfredTime.class.php';

$alfredTime = new AlfredTime;

$query = getenv('description');
$message = '';

if (substr($query, 0, 6) === 'config') {
    $alfredTime->generateDefaultConfigurationFile();
    exec('open "' . getenv('alfred_workflow_data') . '/config.json"');
} elseif (substr($query, 0, 4) === 'sync') {
    $message = $alfredTime->syncOnlineDataToLocalCache();
} elseif (substr($query, 0, 5) === 'edit') {
    exec('open "' . getenv('alfred_workflow_data') . '/config.json"');
} elseif (substr($query, 0, 4) === 'undo') {
    $message = $alfredTime->UndoTimer();
} elseif (substr($query, 0, 6) === 'delete') {
    /**
     * For now, only handle Toggl
     */
    $timerData = json_decode(getenv('timer_data'), true);
    $message = $alfredTime->deleteTimer($timerData['id']);
} elseif (substr($query, 0, 8) === 'continue') {
    $timerData = json_decode(getenv('timer_data'), true);
    $project = ['toggl' => $timerData['pid']];
    $tags = ['toggl' => implode(', ', (empty($timerData['tags']) === true ? [] : $timerData['tags']))];
    $message = $alfredTime->startTimer($timerData['description'], $project, $tags);
} elseif (substr($query, 0, 6) === 'start ') {
    $description = substr($query, 6);

    /**
     * For now, only handle Toggl projects and tags
     */
    $project = [
        'toggl' => getenv('project_id'),
    ];

    $tag = [
        'toggl' => getenv('tag_name'),
    ];

    $message = $alfredTime->startTimer($description, $project, $tag);
} elseif (substr($query, 0, 14) === 'start_default ') {
    $description = substr($query, 14);
    $message = $alfredTime->startTimerWithDefaultOptions($description);
} elseif (substr($query, 0, 4) === 'stop') {
    $message = $alfredTime->stopRunningTimer();
}

echo $message;