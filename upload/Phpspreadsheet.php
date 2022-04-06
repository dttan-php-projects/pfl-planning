<?php

	include_once "./vendor/autoload.php"; 
	use PhpOffice\PhpSpreadsheet\Spreadsheet;
	use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

	class Phpspreadsheet extends Spreadsheet{

		public function __construct() {
			parent::__construct();
		}
	}

?>