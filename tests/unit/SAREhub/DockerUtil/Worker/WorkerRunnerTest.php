<?php

namespace SAREhub\DockerUtil\Worker;

use Hamcrest\Matchers;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use Mockery\MockInterface;
use PHPUnit\Framework\TestCase;
use SAREhub\Commons\Process\PcntlSignals;

class WorkerRunnerTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    /**
     * @var MockInterface | Worker
     */
    private $worker;

    /**
     * @var PcntlSignals | MockInterface
     */
    private $signals;

    /**
     * @var WorkerRunner
     */
    private $runner;

    protected function setUp()
    {
        $this->worker = \Mockery::mock(Worker::class)->shouldIgnoreMissing();
        $this->signals = \Mockery::mock(PcntlSignals::class)->shouldIgnoreMissing();
        $this->runner = new WorkerRunner($this->worker, $this->signals);
    }

    public function testCreateThenRegisterSignalHandlers()
    {
        $this->signals->expects("install")->withArgs([PcntlSignals::getDefaultInstalledSignals()]);
        $this->signals->expects("handle")->withArgs([PcntlSignals::SIGINT, Matchers::callableValue()]);
        $this->signals->expects("handle")->withArgs([PcntlSignals::SIGTERM, Matchers::callableValue()]);
        new WorkerRunner($this->worker, $this->signals);
    }

    public function testRunWhenSIGINT()
    {
        $this->signals = new PcntlSignals();
        $this->runner = new WorkerRunner($this->worker, $this->signals);

        $this->worker->expects("stop");

        $this->signals->dispatchSignal(PcntlSignals::SIGINT);
    }

    public function testRunWhenSIGTERM()
    {
        $this->signals = new PcntlSignals();
        $this->runner = new WorkerRunner($this->worker, $this->signals);

        $this->worker->expects("stop");

        $this->signals->dispatchSignal(PcntlSignals::SIGTERM);
    }

    public function testRunWhenWorkerRunningThenCheckPendingSignals()
    {
        $this->worker->expects("isRunning")->times(2)->andReturnValues([true, false]);
        $this->signals->expects("checkPendingSignals");
        $this->runner->run();
    }

    public function testRunThenWorkerStart()
    {
        $this->worker->expects("start");
        $this->runner->run();
    }

    public function testRunWhenWorkerIsRunningThenWorkerTickInLoop()
    {
        $this->worker->expects("isRunning")->times(2)->andReturnValues([true, false]);
        $this->worker->expects("tick");
        $this->runner->run();
    }
}
