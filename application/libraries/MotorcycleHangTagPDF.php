<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); 


class MotorcycleHangTagPDF extends TCPDF {

	public function __construct($data)
	{
		parent::__construct(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

		$this->data = $data;
		$this->offset_x = 5;
		$this->options = array(
			'font_sz_default' => 12,
			'font_sz_header' => 14,
			'font_sz_moto_name' => 20,
			'font_sz_moto_sku' => 14,
			'font_sz_moto_price' => 16,
			'font_sz_discount' => 12,
			'font_sz_details' => 12
		);
		
		initializePSTAPI();
        global $PSTAPI;
	}

	// Page footer
    public function Footer() {
        // Position at 15 mm from bottom
        $this->SetY(-15);
        // Set font
        $this->SetFont('helvetica', 'I', 8);
        // Page number
        $this->Cell(0, 10, '*Price does not include Applicable Sales Tax, .U.V.C., Title, Registration or Dealer Documentation Fee.', 0, false, 'C', 0, '', 0, false, 'T', 'M');
    }

	public function generate($download = false) {
		$pdf = $this;

		$this->_prepare();

		$header_height = $this->_drawHeader();
		$pdf->setY($header_height, false);
		$pdf->Ln(5);
		$this->_drawLeftBody();
		$pdf->setY($header_height, false);
		$this->_drawRightBody();
		$this->writeToFile($download);
	}

	protected function _prepare() {
		$pdf = $this;

		$pdf->SetCreator(PDF_CREATOR);
		$pdf->SetAuthor('PowerSport Technologies');
		$pdf->SetTitle($this->data['motorcycle']['title']);
		$pdf->SetSubject('');
		$pdf->SetKeywords('Motorcycle, Hang Tag');

		// set default header data
		$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE, PDF_HEADER_STRING);

		// set header and footer fonts
		$pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
		$pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

		// set default monospaced font
		$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

		// set margins
		$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
		$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
		$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

		// set auto page breaks
		$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

		$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

		// set font
		$pdf->SetFont('helvetica', '', 12);

		// add a page
		$pdf->AddPage();
	}

	protected function _drawHeader() {
		$pdf = $this;
		global $PSTAPI;

		$x_offset = 5;
		$page_width = $pdf->getPageWidth();
		$area_width = $page_width / 2;

		$header_bg_color = sscanf($PSTAPI->config()->getKeyValue('hang_tag_header_background_color', '#CCCCCC'), "#%02x%02x%02x");
		$header_fg_color = sscanf($PSTAPI->config()->getKeyValue('hang_tag_header_text_color', '#FFFFFF'), "#%02x%02x%02x");
		$company_logo = STORE_DIRECTORY.'/html/'.$PSTAPI->config()->getKeyValue('hang_tag_company_logo', '/assets/images/admin_logo.png');
		$company = @$this->data['address']['company'];
		$phone = @$this->data['address']['phone'];

		$pdf->setFontSize($this->options['font_sz_header']);
		$pdf->setTextColorArray($header_fg_color);
		
		list($width, $height) = getimagesize($company_logo);
		$logo_height = min(20, $height);
		$logo_width = round($width / $height * $logo_height);

		$company_text_height = $pdf->getStringHeight($area_width, $company);
		$phone_text_height = $pdf->getStringHeight($area_width, $phone);
		$header_height = 5 + $logo_height + $company_text_height + $phone_text_height + 3;

		$pdf->Rect(0, 0, $page_width, $header_height, 'F', array(), $header_bg_color);
		$pdf->Image($company_logo, $x_offset, 5, $logo_width, $logo_height, NULL, NULL, '', true, 150, '', false, false, 0, false, false, false);
		$pdf->Image($company_logo, $area_width + $x_offset, 5, $logo_width, $logo_height, NULL, NULL, '', true, 150, '', false, false, 0, false, false, false);

		
		$pdf->SetXY($x_offset, 5 + $logo_height, false);

		$pdf->MultiCell($area_width, 0, $company, 0, 'L', false, 0);
		$pdf->MultiCell($area_width, 0, $company, 0, 'L', false, 1);
		$pdf->SetX($x_offset, false);
		$pdf->Cell($area_width, 0, $phone);
		$pdf->Cell($area_width, 0, $phone);

		return $header_height;
	}

	protected function _drawLeftBody() {
		$pdf = $this;
		$area_width = $pdf->getPageWidth() / 2 - 10;

		$this->_drawMotorNameAndSku();
		$this->_drawPrice();
		$pdf->Ln(3);
		$this->_drawMonthlyPayment();

		$product = $this->data['motorcycle'];
		$product_details = array(
			array('Year:', $product['year']),
			array('Make:', $product['make']),
			array('Model:', $product['model'])
		);
		if (!empty($product['vin_number'])) $product_details[] = array('Vin:', $product['vin_number']);
		if (!empty($product['mileage'])) $product_details[] = array('Mileage:', $product['mileage']);
		if (!empty($product['color'])) $product_details[] = array('Color:', $product['color']);

		$specs = array();
		foreach($this->data['specs'] as $spec) {
			$specs[] = array($spec['attribute_name'], $spec['value']);
		}

		$this->_drawTable($this->data['motorcycle']['type'].' Details', $product_details, array(0.5, 0.5));
		$this->_drawTable($this->data['motorcycle']['type'].' Specifications', $specs, array(0.5, 0.5));
	}

	protected function _drawRightBody() {
		$pdf = $this;
		global $PSTAPI;
		$page_height = $pdf->GetPageHeight();
		$page_width = $pdf->GetPageWidth();
		$y = $pdf->GetY();

		$area_height = $page_height - $y;
		$area_width = $page_width/2 - 10;

		$company_logo = STORE_DIRECTORY.'/html/'.$PSTAPI->config()->getKeyValue('hang_tag_company_logo', '/assets/images/admin_logo.png');
		list($width, $height) = getimagesize($company_logo);
		$max_width = $area_width - 10;
		$max_height = $area_height / 2;
		if ($width > $max_width || $height > $max_height) {
			$ratio = MAX($width / $max_width, $height / $max_height);
			$width = $width / $ratio;
			$height = $height / $ratio;
		}

		$pdf->setTextColorArray(array(0,0,0));
		$pdf->setFont('helvetica', 'B', $this->options['font_sz_moto_name']);

		$text = $this->data['motorcycle']['title'];
		$text_height = $pdf->GetStringHeight($area_width, $text);
		$top = ($area_height - $height - $text_height) / 2;
		$pdf->Ln($top);
		$pdf->Image($company_logo, $page_width / 2 + ($page_width / 2 - $width) / 2, $pdf->GetY(), $width, $height, NULL, NULL, '', true, 150, '', false, false, 0, false, false, false);
		$pdf->Ln($height);
		$pdf->SetX($page_width / 2 + 5);
		$pdf->MultiCell($area_width, 0, $text, 0, 'C', false, 0);
	}

	protected function _drawMotorNameAndSku() {
		$pdf = $this;
		$area_width = $pdf->getPageWidth() / 2 - 10;

		$pdf->setTextColorArray(array(0,0,0));
		$pdf->setFont('helvetica', 'B');
		$pdf->setFontSize($this->options['font_sz_moto_name']);
		$pdf->setX(5, false);
		$pdf->MultiCell($area_width, 0, $this->data['motorcycle']['title'], 0, 'L', false, 1);

		$pdf->setFontSize($this->options['font_sz_moto_sku']);
		$pdf->setX(5, false);
		$pdf->Cell($width, 0, 'SKU: '.$this->data['motorcycle']['sku'], 0, 1);
		$pdf->setFont('helvetica', '');
	}

	protected function _drawPrice() {
		$pdf = $this;
		global $PSTAPI;

		$area_width = $pdf->getPageWidth() / 2 - 10;
		$active_color = sscanf($PSTAPI->config()->getKeyValue('hang_tag_monthly_payment_color', '#0000FF'), "#%02x%02x%02x");
		$inactive_color = array(0,0,0);
		$pricing_option = $this->data['pricing_option'];
		if ($pricing_option['call_for_price']) return;

		$sale_price_align = 'R';
		$pdf->setX(5, false);
		$pdf->setFontSize($this->options['font_sz_moto_price']);
		$pdf->setTextColorArray($pricing_option['show_sale_price'] ? $inactive_color : $active_color);
		if ($pricing_option['show_retail_price']) {
			if ($pricing_option['show_sale_price'])
				$pdf->SetFont('helvetica', 'D');
			$pdf->Cell($area_width / 2, 0, 'Retail Price: $'.$pricing_option['retail_price'], 0, 0);

			if ($pdf->getStringWidth('Retail Price: $'.$pricing_option['retail_price']) > $area_width / 2) {
				$pdf->Ln();
				$pdf->setX(5, false);
				$sale_price_align = '';
			}
		}

		if ($pricing_option['show_sale_price']) {
			$pdf->setTextColorArray($active_color);
			$pdf->SetFont('helvetica', '');
			$pdf->Cell($area_width / 2, 0, 'Our Price: $'.$pricing_option['sale_price'], 0, 0, $sale_price_align);

			if ($pricing_option['discounted']) {
				$pdf->Ln();
				$pdf->setX(5, false);
				$pdf->setFontSize($this->options['font_sz_discount']);
				$pdf->Cell($area_width, 0, 'Savings: $'.$pricing_option['discount'], 0, 0, $sale_price_align);
			}
		}
		$pdf->Ln();
	}

	protected function _drawMonthlyPayment() {
		$pricing_option = $this->data['pricing_option'];
		if (!$pricing_option['show_monthly_payment'])
			return;

		$pdf = $this;
		global $PSTAPI;

		$area_width = $pdf->getPageWidth() / 2 - 10;
		$bg_color = sscanf($PSTAPI->config()->getKeyValue('hang_tag_monthly_payment_color', '#0000FF'), "#%02x%02x%02x");
		
		$text = $pricing_option['payment_text'].': '.$pricing_option['monthly_payment'].'/mo*';
		$html = $pricing_option['payment_text'].': '.$pricing_option['monthly_payment'].'/mo<sup>*</sup>';
		$pdf->setFontSize($this->options['font_sz_moto_price']);
		$pdf->setTextColorArray(array(255, 255, 255));
		$text_width = $pdf->getStringWidth($text) + 4;
		$text_height = $pdf->getStringHeight($area_width, $text);
		$y = $pdf->GetY();
		$x = 5;
		$pdf->SetFillColorArray($bg_color);
		$pdf->polygon(array(
			$x, $y,
			$x + 2, $y,
			$x + 5, $y + 6,
			$x + 2, $y + 12,
			$x, $y + 12,
			$x + 3, $y + 6
		), 'F');

		$pdf->polygon(array(
			$x + 4, $y,
			$x + 4 + $text_width, $y,
			$x + 4 + $text_width + 3, $y + 6,
			$x + 4 + $text_width, $y + 12,
			$x + 4, $y + 12,
			$x + 4 + 3, $y + 6
		), 'F');
		// $pdf->Cell($text_width, 0, $text, 0, 1, 'C');
		$pdf->WriteHTMLCell($text_width, 15, $x + 4 + 3, $y + 2, $text);
		// $pdf->Cell($text_width, 0, $text, 0, 1, 'C', false, '', 0, true, 'C', 'C');
	}

	protected function _drawTable($title, $rows, $col_weights) {
		$pdf = $this;
		$area_width = $pdf->getPageWidth() / 2 - 10;

		$pdf->SetFillColorArray(array(238,238,238));
		$pdf->setTextColorArray(array(0,0,0));
		$pdf->SetFont('helvetica', 'B', $this->options['font_sz_details']);
		$pdf->Ln();
		$pdf->SetX(5, false);
		$pdf->Cell($area_width, 0, $title, 0, 1);
		$pdf->SetFont('helvetica', '');
		
		for ($i = 0; $i < count($rows); $i++) {
			$this->_drawTableRow($rows[$i], $col_weights, $area_width, ($i % 2) == 0);
		}
	}

	protected function _drawTableRow($row, $col_weights, $row_width, $fill) {
		$pdf = $this;
		$pdf->SetX(5, false);
		$row_height = $this->_getRowHeight($row, $col_weights, $row_width);
		for ($i = 0; $i < count($col_weights); $i ++) {
			$width = round($row_width * $col_weights[$i]);

			$pdf->MultiCell($width, row_height, $row[$i], 0, 'L', $fill, 0);
			// $pdf->Cell($width, $row_height, $row[$i], 0, 0, 'L', $fill, '', 0, true, 'T', 'M');
			// $pdf->Cell($width, $row_height, $row[$i]);
		}
		$pdf->Ln();
	}

	protected function _getRowHeight($row, $col_weights, $row_width) {
		$pdf = $this;
		$max_height = 0;
		for ($i = 0; $i < count($col_weights); $i ++) {
			$width = round($row_width * $col_weights[$i]);
			$height = $pdf->GetStringHeight($width, $row[$i]);
			$max_height = max($height, $max_height);
		}
		return $max_height;
	}

	protected function writeToFile($download = false) {
		$pdf = $this;
        $pdf->Output(STORE_DIRECTORY.'/hang_tag.pdf', $download ? 'I' : 'F');
	}

}

/* End of file poreport.php */
/* Location: libraries/poreport.php */