<?php

namespace SAREhub\DockerUtil\Worker;

use SAREhub\Commons\Process\PcntlSignals;

/**
 * Basic worker runner.
 */
class WorkerRunner {
	
	private $worker;
	
	/**
	 * @var PcntlSignals
	 */
	private $signals;
	
	private function __construct(Worker $worker, PcntlSignals $signals) {
		$this->worker = $worker;
		$this->signals = $signals;
		$this->installSignals();
	}
	
	private function installSignals() {
		$callback = function () { $this->stop(); };
		$this->signals->handle(PcntlSignals::SIGTERM, $callback);
	}
	
	public static function create(Worker $worker, PcntlSignals $signals): WorkerRunner {
		return new self($worker, $signals);
	}
	
	/**
	 * Starts worker and calls worker tick in loop until worker is running.
	 */
	public function run() {
		$this->getWorker()->start();
		while ($this->getWorker()->isRunning()) {
			$this->signals->checkPendingSignals();
			$this->getWorker()->tick();
		}
	}
	
	private function stop() {
		$this->getWorker()->stop();
	}
	
	private function getWorker(): Worker {
		return $this->worker;
	}
}