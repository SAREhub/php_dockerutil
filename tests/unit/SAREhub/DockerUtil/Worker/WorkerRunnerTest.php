<?php

namespace SAREhub\DockerUtil\Worker;

use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use Mockery\Mock;
use PHPUnit\Framework\TestCase;
use SAREhub\Commons\Process\PcntlSignals;

class WorkerRunnerTest extends TestCase {
	use MockeryPHPUnitIntegration;
	
	/**
	 * @var Mock | mixed
	 */
	private $worker;
	/**
	 * @var PcntlSignals
	 */
	private $signals;
	/**
	 * @var WorkerRunner
	 */
	private $runner;
	
	protected function setUp() {
		$this->worker = \Mockery::mock(Worker::class)->shouldIgnoreMissing();
		$this->signals = PcntlSignals::create(false);
		$this->runner = WorkerRunner::create($this->worker, $this->signals);
	}
	
	public function testCreateThenHasHandlerFor_SIGTERM() {
		$handler = $this->signals->getHandlersForSignal(PcntlSignals::SIGTERM)[PcntlSignals::DEFAULT_NAMESPACE];
		$this->assertNotNull($handler);
	}
	
	public function testSIGTERM_ThenStop() {
		$this->worker->shouldReceive('stop')->once();
		$this->signals->dispatchSignal(PcntlSignals::SIGTERM);
	}
	
	public function testRunWhenWorkerRunningThenPcntlSignalsCheckPendingSignals() {
		$this->signals = \Mockery::mock(PcntlSignals::class)->shouldIgnoreMissing();
		$this->worker->shouldReceive('isRunning')->andReturnValues([true, true, false]);
		$this->signals->shouldReceive('checkPendingSignals')->twice();
		$this->runner = WorkerRunner::create($this->worker, $this->signals);
		$this->runner->run();
	}
	
	public function testRunThenWorkerStart() {
		$this->worker->shouldReceive('start')->once();
		$this->runner->run();
	}
	
	public function testRunWhenWorkerIsRunningThenWorkerTickInLoop() {
		$this->worker->shouldReceive('isRunning')->andReturnValues([true, true, false]);
		$this->worker->shouldReceive('tick')->twice();
		$this->runner->run();
	}
}
