<?php
class Brand {
	
	/**
	 * brand unique identificatin
	 * @var String
	 */
	private $id;
	
	/**
	 * brand name
	 * @var String
	 */
	private $name;
	
	/**
	 * array with all the programmes
	 * @var array
	 */
	private $programmes;
	
	/**
	 * Basic constructor
	 * @param string $id unique id
	 * @param string $name brand name
	 */
	public function __construct($id, $name) {
		$this->id = $id;
		$this->name = $name;
		$this->programmes = array();
	}
	
	/**
	 * Adds a programme to the brand
	 */
	public function addProgramme($programme) {
		$this->programmes[] = $programme;	
	}
	
	public function getId() {
		return $this->id;
	}
	
	public function getName() {
		return $this->name;
	
	}
	public function getProgrammes() {
		return $this->programmes;
	}
	
	public function __toString() {
		return $this->name."\n".print_r($this->programmes, true);
	}
}

class Programme {
	/**
	 * Programme name
	 * @var String
	 */
	private $name;
	
	/**
	 * programme duration in seconds
	 * @var Integer
	 */
	private $duration; 
	
	/**
	 * Default constructor
	 * @param String $name programme name
	 * @param int $duration programme duration in seconds
	 */
	public function __construct($name, $duration) {
		$this->name = $name;
		$this->duration = $duration;
	}
	
	public function getName() {
		return $this->name;
	}
	/**
	 * Returns the programme duration
	 * @param boolean formated if true, then a string with the duration in HH:MM:SS format is returned
	 */
	public function getDuration($formated=false) {
		if (!$formated) {
			return $this->duration;
		} else {
			$hours = (int)($this->duration / 3600);
			$minutes = (int)(($this->duration % 3600) / 60);
			$seconds = ($this->duration % 3600) % 60;
			return sprintf("%02d:%02d:%02d", $hours, $minutes, $seconds); 
		}
	}
	
} 
?>
