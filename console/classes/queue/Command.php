<?php
/**
 * Created by PhpStorm.
 * User: sysadmin
 * Date: 28.11.2017
 * Time: 13:59
 */

namespace console\classes\queue;


use Symfony\Component\Process\Exception\ProcessTimedOutException;
use Symfony\Component\Process\Process;
use yii\console\ExitCode;

class Command extends \yii\queue\db\Command
{
    public $memory = '128M';
    public $timeoutProcess = 7200;

    public function options($actionID)
    {
        $options = parent::options($actionID);
        $options[] = 'memory';
        $options[] = 'timeoutProcess';

        return $options;
    }

    protected function useIsolateOption($actionID)
    {
        return parent::useIsolateOption($actionID) || in_array($actionID, ['memory', 'timeoutProcess']);
    }

    protected function handleMessage($id, $message, $ttr, $attempt)
    {
        // Executes child process
        $cmd = strtr('{php} -d memory_limit={memory} {yii} {queue}/exec "{id}" "{ttr}" "{attempt}"', [
            '{php}' => PHP_BINARY,
            '{memory}' => $this->memory,
            '{yii}' => $_SERVER['SCRIPT_FILENAME'],
            '{queue}' => $this->uniqueId,
            '{id}' => $id,
            '{ttr}' => $ttr,
            '{attempt}' => $attempt,
        ]);

        foreach ($this->getPassedOptions() as $name) {
            if (in_array($name, $this->options('exec'))) {
                $cmd .= ' --' . $name . '=' . $this->$name;
            }
        }
        if (!in_array('color', $this->getPassedOptions())) {
            $cmd .= ' --color=' . $this->isColorEnabled();
        }

        $process = new Process($cmd, null, null, $message, $ttr);
        try {
            $exitCode = $process->setTimeout($this->timeoutProcess)->run(function ($type, $buffer) {
                if ($type === Process::ERR) {
                    $this->stderr($buffer);
                } else {
                    $this->stdout($buffer);
                }
            });
        } catch (ProcessTimedOutException $error) {
            $job = $this->queue->serializer->unserialize($message);
            return $this->queue->handleError($id, $job, $ttr, $attempt, $error);
        }

        return $exitCode == ExitCode::OK;
    }

}