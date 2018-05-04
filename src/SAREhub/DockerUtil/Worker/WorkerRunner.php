<?php

namespace SAREhub\DockerUtil\Worker;

use SAREhub\Commons\Process\PcntlSignals;

/**
 * Basic worker runner.
 */
class WorkerRunner
{

    /**
     * @var Worker
     */
    private $worker;

    /**
     * @var PcntlSignals
     */
    private $signals;

    public function __construct(Worker $worker, PcntlSignals $signals)
    {
        $this->worker = $worker;
        $this->signals = $signals;
        $this->installSignals();
    }

    private function installSignals()
    {
        $this->getSignals()->install(PcntlSignals::getDefaultInstalledSignals());
        $handler = [$this, "stop"];
        $this->getSignals()->handle(PcntlSignals::SIGINT, $handler);
        $this->getSignals()->handle(PcntlSignals::SIGTERM, $handler);
    }

    /**
     * Starts worker and calls worker tick in loop until worker is running.
     * @throws \Exception
     */
    public function run()
    {
        $this->getWorker()->start();
        while ($this->getWorker()->isRunning()) {
            $this->tick();
        }
    }

    /**
     * @throws \Exception
     */
    private function tick()
    {
        $this->getSignals()->checkPendingSignals();
        $this->getWorker()->tick();
    }

    /**
     * @throws \Exception
     * Internal use only
     */
    public function stop()
    {
        $this->getWorker()->stop();
    }

    private function getWorker(): Worker
    {
        return $this->worker;
    }

    private function getSignals(): PcntlSignals
    {
        return $this->signals;
    }
}