<?php
class LogParser {
    public $logDir;
    public $logFiles = array();
    public $logData = array();

	/**
	 *
 	 */
	public function __construct () {
		$CFileLogRoute = new CFileLogRoute();
		$_path = $CFileLogRoute->getLogPath();

		if ( empty($_path) ) {
	        $_path = Yii::getPathOfAlias('application.runtime');
		}

		$this->logDir = $_path;
    }

	/**
	 *
	 */
	private function getLogFiles () {

		if ( $dir = opendir($this->logDir) ) {
	        while ( ( $file = readdir($dir) ) !== false ) {
				$file = $this->logDir . DIRECTORY_SEPARATOR . $file;
				if ( is_file($file) && strpos($file, '.log') !== false ) {
		            $this->logFiles[] = $file;
				}
	        }
		}
    }

	/**
	 * @return array
	 */
	public function getLogFilesAsArray () {
		$this->getLogFiles();
	
		$_newArray = array();
		foreach ( $this->logFiles AS $file ) {
	        $_newArray[] = array(
				'fileName' => $file,
				'mtime' => filemtime($file),
				'fileSize' => filesize($file),
	        );
		}
		return $_newArray;
    }

	/**
	 * @param $filePath
	 * @return array
	 */
	public function readLogFile ( $filePath ) {
		if ( !$filePath ) {
	        return array();
		}
		$this->readLogFileAsArray($filePath);
		return $this->getDataAsArray();
    }

	/**
	 * @param $filePath
	 * @return bool
	 */
	public function deleteLogFile ( $filePath ) {
		if ( is_file($filePath) ) {
	        return @unlink($filePath);
		}
		return false;
    }

	/**
	 * @param $file
	 */
	private function readLogFileAsArray ( $file ) {
		$content = file_get_contents($file);
	    $lines = explode('---', $content);
	    if ( !is_array($lines) || sizeof($lines) == 1 ) {
		    $lines = explode("\n", $content);
	    }
		$i = sizeof($this->logData);
		foreach ( $lines AS $line ) {
            ++$i;
	        $this->logData[$i] .= trim($line);
		}
    }

	/**
	 * @return array
	 */
	private function getDataAsArray () {
		$_newArray = array();
		foreach ( $this->logData AS $i => $line ) {
	        if ( preg_match('/^([0-9]{4}\/[0-9]{2}\/[0-9]{2} [0-9]{2}:[0-9]{2}:[0-9]{2}) \[([a-z]+)\] \[([A-Za-z0-9\.]+)\] (.*?)(\nStack trace:\n(.*?)\nHTTP_REFERER=(.*?))?$/si', $line, $matches) ) {
				$_newArray[] = array(
		            //'id' => $i,
		            'date' => $matches[1],
		            'eventType' => $matches[2],
		            'component' => $matches[3],
		            'description' => self::parseTrace($matches[4]),
		            'trace' => self::parseTrace($matches[6]),
					'http_referer' => $matches[7],
				);
	        }
		}

		return $_newArray;
    }

	/**
	 * @param $trace
	 * @return string
	 */
	static function parseTrace ( $trace ) {
		return str_replace("\n", '<br />', htmlspecialchars($trace));
    }
}
?>
