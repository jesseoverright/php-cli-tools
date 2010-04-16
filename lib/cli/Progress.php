<?php
/**
 * PHP Command Line Tools
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.
 *
 * @author    James Logsdon <dwarf@girsbrain.org>
 * @copyright 2010 James Logsdom (http://girsbrain.org)
 * @license   New BSD License
 */

namespace cli;

/**
 * A more complex type of Notifier, `Progress` Notifiers always have a maxim
 * value and generally show some form of percent complete or estimated time
 * to completion along with the standard Notifier displays.
 *
 * @see cli\Notify
 */
abstract class Progress extends \cli\Notify {
	protected $_total = 0;

	/**
	 * Instantiates a Progress Notifier.
	 *
	 * @param string  $msg       The text to display next to the Notifier.
	 * @param int     $total     The total number of ticks we will be performing.
	 * @param int     $interval  The interval in milliseconds between updates.
	 * @throws \InvalidArgumentException  Thrown if `$total` is less than 0.
	 */
	public function __construct($msg, $total, $interval = 100) {
		parent::__construct($msg, $interval);
		$this->_total = (int)$total;

		if ($this->_total <= 0) {
			throw new \InvalidArgumentException('Maximum value out of range, must be positive.');
		}
	}

	/**
	 * Behaves in a similar manner to `cli\Notify::current()`, but the output
	 * is padded to match the length of `cli\Progress::total()`.
	 *
	 * @return string  The formatted and padded tick count.
	 * @see cli\Progress::total()
	 */
	public function current() {
		$size = strlen($this->total());
		return str_pad(parent::current(), $size);
	}

	/**
	 * Returns the formatted total expected ticks.
	 *
	 * @return string  The formatted total ticks.
	 */
	public function total() {
		return number_format($this->_total);
	}

	/**
	 * Calculates the estimated total time for the tick count to reach the
	 * total ticks given.
	 *
	 * @return int  The estimated total number of seconds for all ticks to be
	 *              completed. This is not the estimated time left, but total.
	 * @see cli\Notify::speed()
	 * @see cli\Notify::elapsed()
	 */
	public function estimated() {
		$speed = $this->speed();
		if (!$this->elapsed()) {
			return 0;
		}

		$estimated = round($this->_total / $speed);
		return $estimated;
	}

	/**
	 * Forces the current tick count to the total ticks given at instatiation
	 * time before passing on to `cli\Notify::finish()`.
	 */
	public function finish() {
		$this->_current = $this->_total;
		parent::finish();
	}

	/**
	 * Increments are tick counter by the given amount. If no amount is provided,
	 * the ticker is incremented by 1.
	 *
	 * @param int  $increment  The amount to increment by.
	 */
	public function increment($increment = 1) {
		$this->_current = min($this->_total, $this->_current + $increment);
	}

	/**
	 * Calculate the percentage completed.
	 *
	 * @return float  The percent completed.
	 */
	public function percent() {
		return ($this->_current / $this->_total);
	}
}