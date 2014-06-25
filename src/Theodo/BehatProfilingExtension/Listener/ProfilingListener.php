<?php

namespace Theodo\BehatProfilingExtension\Listener;

use Symfony\Component\Stopwatch\Stopwatch;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class ProfilingListener implements EventSubscriberInterface
{
    private $stopwatch;
    private $stopwatchEvent;
    private $eventId;

    public static function getSubscribedEvents()
    {
        return array(
            'beforeStep' => 'startStepTiming',
            'afterStep' => array(
                array('stopStepTiming', 100),
                array('printStepTime', -100),
            ),
        );
    }

    public function startStepTiming($event)
    {
        $this->eventId = spl_object_hash($event);
        $this->getStopwatch()->start($this->eventId);
    }

    public function stopStepTiming()
    {
        $this->stopwatchEvent = $this->getStopwatch()->stop($this->eventId);
    }

    public function printStepTime()
    {
        echo "\n\033[36m| ";
        echo 'Step time: ' . $this->formatTime($this->stopwatchEvent->getDuration());
        echo "\033[0m\n\n";
    }

    protected function getStopwatch()
    {
        if ($this->stopwatch === null) {
            $this->stopwatch = new Stopwatch();
        }

        return $this->stopwatch;
    }

    protected function formatTime($timeInMs)
    {
        $ms = $timeInMs % 1000;
        $timeInMs = floor($timeInMs / 1000);

        $seconds = $timeInMs % 60;
        $timeInMs = floor($timeInMs / 60);

        $minutes = $timeInMs % 60;
        $timeInMs = floor($timeInMs / 60); 

        return $minutes . "m " . $seconds . "." . $ms ."s";
    }
}
