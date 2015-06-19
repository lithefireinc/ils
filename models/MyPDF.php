<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once('tcpdf/config/lang/eng.php');
require_once('tcpdf/tcpdf.php');

class MyPDF extends TCPDF{
	function generatePDF(){
		
	}
	
	public function Header(){
		parent::Header();
		
		$this->headerOne();
	}
	
	function headerOne(){
		$this->SetFont('helvetica', '', 8, '', true);
		$this->SetXY(20, 80);
		$this->SetFillColor(237, 237, 237);
		$this->Cell(70, 10, "Subject Code", 'TBLR', 0, 'C', 1);
		
		$xvalue = 90;   			
   		$this->SetXY($xvalue, 80);
   		$this->SetFillColor(237, 237, 237);						
   		$this->Cell(140, 10, "Description", 'TBLR', 0, 'C', 1);
   		
   		$xvalue = 230;   			
   		$this->SetXY($xvalue, 80);
   		$this->SetFillColor(237, 237, 237);						
   		$this->Cell(30, 10, "Units", 'TBLR', 0, 'C', 1);	
      
   		$xvalue = 260;   			
   		$this->SetXY($xvalue, 80);
   		$this->SetFillColor(237, 237, 237);						
   		$this->Cell(140, 10, "Adviser", 'TBLR', 0, 'C', 1);	
		
		$xvalue = 400;   			
   		$this->SetXY($xvalue, 80);
   		$this->SetFillColor(237, 237, 237);						
   		$this->Cell(70, 10, "Student Id", 'TBLR', 0, 'C', 1);	
		
		$xvalue = 470;   			
   		$this->SetXY($xvalue, 80);
   		$this->SetFillColor(237, 237, 237);						
   		$this->Cell(190, 10, "Student Name", 'TBLR', 0, 'C', 1);	
		
		$xvalue = 660;   			
   		$this->SetXY($xvalue, 80);
   		$this->SetFillColor(237, 237, 237);						
   		$this->Cell(190, 10, "Course", 'TBLR', 0, 'C', 1);	
   		
      	$xvalue = 850;   			
   		$this->SetXY($xvalue, 80);
   		$this->SetFillColor(237, 237, 237);						
   		$this->Cell(70, 10, "Year", 'TBLR', 0, 'C', 1);	
		
		$xvalue = 920;   			
   		$this->SetXY($xvalue, 80);
   		$this->SetFillColor(237, 237, 237);						
   		$this->Cell(70, 10, "Section", 'TBLR', 0, 'C', 1);	
	}

	function checkPageBreak($h=0, $y='', $addpage=true) {
		if ($this->empty_string($y)) {
			$y = $this->y;
		}
		$current_page = $this->page;
		if ((($y + $h) > $this->PageBreakTrigger) AND ($this->inPageBody()) AND ($this->AcceptPageBreak())) {
			if ($addpage) {
				//Automatic page break
				$x = $this->x;
				$this->AddPage($this->CurOrientation);
				$this->y = $this->tMargin;
				$oldpage = $this->page - 1;
				if ($this->rtl) {
					if ($this->pagedim[$this->page]['orm'] != $this->pagedim[$oldpage]['orm']) {
						$this->x = $x - ($this->pagedim[$this->page]['orm'] - $this->pagedim[$oldpage]['orm']);
					} else {
						$this->x = $x;
					}
				} else {
					if ($this->pagedim[$this->page]['olm'] != $this->pagedim[$oldpage]['olm']) {
						$this->x = $x + ($this->pagedim[$this->page]['olm'] - $this->pagedim[$oldpage]['olm']);
					} else {
						$this->x = $x;
					}
				}
			}
			return true;
		}
		if ($current_page != $this->page) {
			// account for columns mode
			return true;
		}
		return false;
	}
}
