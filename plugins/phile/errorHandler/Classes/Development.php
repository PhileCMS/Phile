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

	protected $settings;

	public function __construct(array $settings = []) {
		$this->settings = $settings;
	}

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
		$backtrace = debug_backtrace();
		$backtrace = array_slice($backtrace, 2);
		$this->displayDeveloperOutput($errno, $errstr, $errfile, $errline, $backtrace);
	}

	/**
	 * handle PHP errors which can't be caught by error-handler
	 */
	public function handleShutdown() {
		$error = error_get_last();
		if ($error === null) {
			return;
		}
		$this->displayDeveloperOutput(
			$error['type'],
			$error['message'],
			$error['file'],
			$error['line']
		);
	}

	/**
	 * handle all exceptions
	 *
	 * @param \Exception $exception
	 *
	 * @return mixed
	 */
	public function handleException(\Exception $exception) {
		$this->displayDeveloperOutput(
			$exception->getCode(),
			$exception->getMessage(),
			$exception->getFile(),
			$exception->getLine(),
			null,
			$exception
		);
	}

	/**
	 * show a nice looking and human readable developer output
	 *
	 * @param $code
	 * @param $message
	 * @param $file
	 * @param $line
	 * @param \Exception $exception
	 */
	protected function displayDeveloperOutput($code, $message, $file, $line, array $backtrace = null, \Exception $exception = null) {
		header('HTTP/1.1 500 Internal Server Error');
		$fragment = $this->receiveCodeFragment($file,
			$line, 5, 5);
		$marker = [
			'{{base_url}}' => $this->settings['baseUrl'],
			'{{exception_message}}' => htmlspecialchars($message),
			'{{exception_code}}' => htmlspecialchars($code),
			'{{exception_file}}' => htmlspecialchars($file),
			'{{exception_line}}' => htmlspecialchars($line),
			'{{exception_fragment}}' => $fragment,
			'{{exception_class}}' => '',
			'{{exception_backtrace}}' => '',
			'{{wiki_link}}' => ($code > 0) ? '(<a href="https://github.com/PhileCMS/Phile/wiki/Exception_' . $code . '" target="_blank">Exception-Wiki</a>)' : '',
		];

		if ($exception) {
			$marker['{{exception_class}}'] = $this->linkClass(get_class($exception));
			$backtrace = $exception->getTrace();
		}

		if ($backtrace) {
			$marker['{{exception_backtrace}}'] = $this->createBacktrace($backtrace);
		}

		$tplPath = $this->settings['pluginPath'] . 'template.html';
		$template = file_get_contents($tplPath);
		echo str_replace(array_keys($marker), array_values($marker), $template);
		die();
	}

	/**
	 * creates a human readable backtrace
	 *
	 * @param array $traces
	 * @return string
	 */
	protected function createBacktrace(array $traces) {
		if (!count($traces)) {
			return '';
		}
		$backtraceCodes = [];

		foreach ($traces as $index => $step) {
			$arguments = '';
			if (isset($step['args']) && is_array($step['args'])) {
				foreach ($step['args'] as $argument) {
					$arguments .= strlen($arguments) === 0 ? '' : $this->tag('span', ', ', ['class' => 'separator']);
					if (is_object($argument)) {
						$class = 'class';
						$content = $this->linkClass(get_class($argument));
					} else {
						$class = 'others';
						$content = gettype($argument);
					}
					$arguments .= $this->tag('span', $content, ['class' => $class]);
				}
			}

			$backtrace = '';
			$backtrace .= $this->tag('span', count($traces) - $index, ['class' => 'index']);
			$backtrace .= ' ';

			if (isset($step['class'])) {
				$class = $this->linkClass($step['class']) . '<span class="divider">::</span>';
				$backtrace .= $class . $this->linkClassMethod($step['class'], $step['function']);
			} elseif (isset($step['function'])) {
				$backtrace .= $this->tag('span', $step['function'], ['class' => 'function']);
			}
			if (!empty($arguments)) {
				$backtrace .= $this->tag('span', "($arguments)", ['class' => 'funcArguments']);
			}

			if (isset($step['file'])) {
				$backtrace .= $this->receiveCodeFragment($step['file'], $step['line'], 3, 3);
			}

			$backtraceCodes[] = $this->tag('pre', $backtrace, ['class' => 'entry']);
		}

		return implode('', $backtraceCodes);
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
	protected function receiveCodeFragment($filename, $lineNumber, $linesBefore = 3, $linesAfter = 3) {
		if (!file_exists($filename)) {
			return '';
		}
		$html = $this->tag('span', $filename . ':<br/>', ['class' => 'filename']);

		$code 		= file_get_contents($filename);
		$lines 		= explode("\n", $code);

		$firstLine = $lineNumber - $linesBefore - 1;
		if ($firstLine < 0) {
			$firstLine = 0;
		}

		$lastLine = $lineNumber + $linesAfter;
		if ($lastLine > count($lines)) {
			$lastLine = count($lines);
		}

		$line		= $firstLine;
		$fragment	= '';
		while ($line < $lastLine) {
			$line++;

			$lineText = htmlspecialchars($lines[$line - 1]);
			$lineText = str_replace("\t", '&nbsp;&nbsp;', $lineText);
			$tmp = sprintf('%05d: %s <br/>', $line, $lineText);

			$class = 'row';
			if ($line === $lineNumber) {
				$class .= ' currentRow';
			}
			$fragment .= $this->tag('span', $tmp, ['class' => $class]);
		}


		$html .= $fragment;
		return $this->tag('pre', $html);
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
				$href = $this->settings['baseUrl'] . '/' . $filename;
				$class = $this->tag('a', $class, ['href' =>  $href, 'target' => '_blank']);
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
				$href = $this->settings['baseUrl'] . '/' . $filename . '#method_' . $method;
				$method = $this->tag('a', $method, ['href' =>  $href, 'target' => '_blank']);
			}
		}
		return $method;
	}

	/**
	 * create HTML-tag
	 *
	 * @param string $tag
	 * @param string $content
	 * @param array $attributes
	 * @return string
	 */
	protected function tag($tag, $content = '', array $attributes = []) {
		$html = '<' . $tag;
		foreach ($attributes as $key => $value) {
			$html .= ' ' . $key . '="' . htmlspecialchars($value) . '"';
		}
		$html .= '>' . $content . '</' . $tag . '>';
		return $html;
	}
}
