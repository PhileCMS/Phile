<?php
/**
 * The Development Error Handler
 */

namespace Phile\Plugin\Phile\ErrorHandler;
use Phile\ServiceLocator\ErrorHandlerInterface;
use Phile\Core\Utility;

/**
 * Class Development
 *
 * this is the development error handler for PhileCMS
 * inspired by the debug exception handler of TYPO3 we create this handler.
 * due to incompatibility of the two licenses (GPL and MIT) we have written
 * the entire code again. we thank the core team of TYPO3 for the great idea.
 *
 */
class Development implements ErrorHandlerInterface {
	/**
	 * handle the error
	 *
	 * @param int    $errno
	 * @param string $errstr
	 * @param string $errfile
	 * @param int    $errline
	 * @param array  $errcontext
	 *
	 * @return boolean
	 */
	public function handleError($errno, $errstr, $errfile, $errline, array $errcontext) {
		$this->displayDeveloperOutput(new \Exception("[{$errno}] {$errstr} in {$errfile} on line {$errline}", $errno));
	}

	/**
	 * handle all exceptions
	 *
	 * @param \Exception $exception
	 *
	 * @return mixed
	 */
	public function handleException(\Exception $exception) {
		$this->displayDeveloperOutput($exception);
	}

	/**
	 * show a nice looking and human readable developer output
	 * @param \Exception $exception
	 */
	protected function displayDeveloperOutput(\Exception $exception) {
		header('HTTP/1.1 500 Internal Server Error');
		$marker					= array(
			'{{base_url}}'				=> Utility::getBaseUrl(),
			'{{exception_message}}'		=> htmlspecialchars($exception->getMessage()),
			'{{exception_code}}'		=> htmlspecialchars($exception->getCode()),
			'{{exception_file}}'		=> htmlspecialchars($exception->getFile()),
			'{{exception_line}}'		=> htmlspecialchars($exception->getLine()),
			'{{exception_class}}'		=> $this->linkClass(get_class($exception)),
			'{{exception_backtrace}}'	=> $this->createBacktrace($exception->getTrace()),
			'{{wiki_link}}'				=> ($exception->getCode() > 0) ? '(<a href="https://github.com/PhileCMS/Phile/wiki/Exception_' . $exception->getCode() . '" target="_blank">Exception-Wiki</a>)' : '',

		);
		$template				= file_get_contents(Utility::resolveFilePath('MOD:phile/errorHandler/template.html'));
		echo str_replace(array_keys($marker), array_values($marker), $template);
	}

	/**
	 * creates a human readable backtrace
	 *
	 * @param array $traces
	 *
	 * @return string
	 */
	protected function createBacktrace(array $traces) {
		$backtraceCode = '';
		if (count($traces)) {
			foreach ($traces as $index => $step) {
				$arguments = '';
				if (isset($step['args']) && is_array($step['args'])) {
					foreach ($step['args'] as $argument) {
						$arguments .= strlen($arguments) === 0 ? '' : '<span class="separator">, </span>';
						if (is_object($argument)) {
							$arguments .= '<span class="class">' . $this->linkClass(get_class($argument)) . '</span>';
						} else {
							$arguments .= '<span class="others">' . gettype($argument) . '</span>';
						}
					}
				}

				$backtraceCode .= '<pre class="entry">';
				$backtraceCode .= '<span class="index">' . (count($traces) - $index) . '</span> ';

				if (isset($step['class'])) {
					$class = $this->linkClass($step['class']) . '<span class="divider">::</span>';
					$backtraceCode .= $class . $this->linkClassMethod($step['class'], $step['function']);
				} elseif (isset($step['function'])) {
					$backtraceCode .= '<span class="function">' . $step['function'] . '</span>';
				}
				$backtraceCode .= '<span class="funcArguments">(' . $arguments . ')</span>';

				if (isset($step['file'])) {
					$backtraceCode .= $this->receiveCodeFragment($step['file'], $step['line'], 3, 3);
				}

				$backtraceCode .= '</pre>';
			}
		}
		return $backtraceCode;
	}

	/**
	 * receive a code fragment from file
	 *
	 * @param $filename
	 * @param $lineNumber
	 * @param $linesBefore
	 * @param $linesAfter
	 *
	 * @return string
	 */
	protected function receiveCodeFragment($filename, $lineNumber, $linesBefore, $linesAfter) {
		if (!file_exists($filename)) {
			return '';
		}
		$code 		= file_get_contents($filename);
		$lines 		= explode("\n", $code);

		$firstLine	= ($lineNumber > $linesBefore) ? $lineNumber - $linesBefore : 0;
		$lastLine	= ($lineNumber < (count($lines) - $linesAfter)) ? $lineNumber + $linesAfter : count($lines) + 1;

		$line		= $firstLine-1;
		$fragment	= '';
		while ($line < $lastLine) {
			$line++;
			$tmp = sprintf('%05d', $line) . ': ' . str_replace("\t", '&nbsp;&nbsp;', $lines[$line - 1]) . '<br/>';
			if ($line === $lineNumber) {
				$tmp = '<span class="currentRow">' . $tmp . '</span>';
			}
			$fragment .= $tmp;
		}
		return '<div class="filename">' . $filename . ':</div><pre>' . $fragment . '</pre>';
	}

	/**
	 * link the class name to the API or return the class name
	 * @param $class
	 *
	 * @return string
	 */
	protected function linkClass($class) {
		if (strpos($class, 'Phile\\') === 0) {
			$filename = 'docs/classes/'.str_replace('\\', '.', $class).'.html';
			if (file_exists(Utility::resolveFilePath($filename))) {
				$class = '<a href="'.Utility::getBaseUrl().'/'.$filename.'" target="_blank">'.$class.'</a>';
			}
		}
		return $class;
	}

	/**
	 * link the method name to the API or return the method name
	 * @param $class
	 * @param $method
	 *
	 * @return string
	 */
	protected function linkClassMethod($class, $method) {
		if (strpos($class, 'Phile\\') === 0) {
			$filename = 'docs/classes/'.str_replace('\\', '.', $class).'.html';
			if (file_exists(Utility::resolveFilePath($filename))) {
				$method = '<a href="'.Utility::getBaseUrl().'/'.$filename.'#method_'.$method.'" target="_blank">'.$method.'</a>';
			}
		}
		return $method;
	}
}
