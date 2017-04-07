<?php 
if(@$orders): foreach($orders as $key => $order):
	SetFillColor(300, 300, 300);
	SetTextColor(0,0,0);
	SetY(15);
	SetLeftMargin(15);
	
	// logo
	SetLeftMargin(10);
	SetY(10);
	//Image('assets/images/logo.png',10,10,15,0,'JPG');
		
	SetLeftMargin(30);
	SetXY(30,10);
	SetFont('Times','',12);
    Cell(90,6, WEBSITE_NAME,0,1,'L',0);
    Cell(90,6, STORE_ADDRESS . "," . STORE_ADDRESS2,0,1,'L',1);
    Cell(90,6, STORE_CITY . "," . STORE_STATE . " " . STORE_ZIP,0,1,'L',0);
    Cell(90,6, SUPPORT_PHONE_NUMBER,0,1,'L',0);
	
	// Invoice Header
	SetLeftMargin(160);
	SetFont('Times','',11);
	SetFillColor(187, 0, 187);
	SetXY(135,10);
	Cell(30,6, 'Invoice No.',1,1,'L',1);
	SetXY(135,16);
	Cell(30,8, @$order['order_id'],1,1,'C',0); // Invoice Number Variable
	SetXY(165,10);
	Cell(30,6, 'Order Date',1,1,'L',1);
	SetXY(165,16);
	Cell(30,8, date('m/d/Y', @$order['order_date']),1,1,'C',0); // Needs Date Variable
			
	// Bill To
	SetLeftMargin(11);
	SetFont('Times','B',10);
	SetFillColor(255, 255, 255);
	SetXY(10, 40);
	Cell(70,6, 'Bill To:',0,1,'L',0);
	SetXY(80, 40);
	Cell(70,6, 'Ship To:',0,1,'L',0);
	SetFont('Times','',10);
	SetXY(10, 45);
	Cell(70,6, @$order['contactBilling']['first_name'] . ' ' . @$order['contactBilling']['last_name'],0,1,'L',0); // Name Variable
	SetXY(80, 45);
	Cell(70,6, @$order['contactShipping']['first_name'] . ' ' . @$order['contactShipping']['last_name'],0,1,'L',0); // Name Variable

	SetXY(10, 50);
	Cell(70,6, @$order['contactBilling']['street_address'] . ' ' . @$order['contactBilling']['address_2'],0,1,'L',0); // Street Address Variable
	SetXY(80, 50);
	Cell(70,6, @$order['contactShipping']['street_address'] . ' ' . @$order['contactShipping']['address_2'],0,1,'L',0); // Street Address Variable
	
	SetXY(10, 55);
	Cell(70,6, @$order['contactBilling']['city'].', '.@$order['contactBilling']['state'].' '.@$order['contactBilling']['zip'],0,1,'L',0); // City, State, Zip Variable
	SetXY(80, 55);
	Cell(70,6, @$order['contactShipping']['city'].', '.@$order['contactShipping']['state'].' '.@$order['contactShipping']['zip'],0,1,'L',0); // City, State, Zip Variable
	
	SetXY(10, 60);
	Cell(70,6, @$order['contactBilling']['email'],0,1,'L',0); 
	SetXY(80, 60);
	Cell(70,6, @$order['contactShipping']['email'],0,1,'L',0);
	
		SetXY(10, 65);
	Cell(70,6, @$order['contactBilling']['phone'],0,1,'L',0); 
	SetXY(80, 65);
	Cell(70,6, @$order['contactShipping']['phone'],0,1,'L',0);
	
	// Amount Due and Pay Online
	SetLeftMargin(130);
	SetY(55);
	Cell(30,10, 'Amount Due:',0,1,'C',1);
	SetFillColor(187, 0, 187);
	SetXY(160, 55);
	Cell(30,10, 'Paid in Full',1,1,'C',1); // Total Amount Due Variable
	SetFillColor(255, 255, 255);
	SetXY(165, 55);
	//Cell(30,10, 'Pay Online Button',0,1,'R',1);
		
	// Main Block - Header
	SetLeftMargin(10);
	SetFillColor(187, 0, 187);
	SetY(70);
	Cell(40,5, 'Quantity',1,1,'C',1); // Date Line Item Variable
  SetLeftMargin(50);
	SetY(70);
	Cell(105,5, 'Description',1,1,'C',1); // Description Line Item Variable
	SetLeftMargin(155);
	SetY(70);
	Cell(40,5, 'Amount',1,1,'C',1);
 // Description Line Item Variable
	
	$y = 70;
	if(@$order['products']): $i = 1; foreach($order['products'] as $product):
    if($i > 35) // Too big for one page
    {
      // Close out last page
      SetLeftMargin(10);
    	SetFillColor(255, 255, 255);
    	SetFont('Times','',10);
    	$y = $y + 5;
    	SetY($y);
    	Cell(40,5, '','T',1,'C',0); // blank cell
    	SetLeftMargin(50);
    	SetY($y);
    	Cell(105,5, '','T',1,'C',0); // blank cell
    	SetLeftMargin(155);
    	SetY($y);
    	Cell(40,5, '','T',1,'R',0); // blank cell
    	
    	// Create New page
      AddPage();
      // Create Header
      SetFillColor(300, 300, 300);
    	SetTextColor(0,0,0);
    	SetY(15);
    	SetLeftMargin(15);
    	
    	// logo
    	SetLeftMargin(10);
    	SetY(10);
    	Image('assets/images/btrfly.jpg',10,10,15,0,'JPG');
    		
    	SetLeftMargin(30);
    	SetXY(30,10);
    	SetFont('Times','',12);
    	Cell(90,6, 'Butterfly Express LLC',0,1,'L',0);
    	Cell(90,6, '500 N Main Hwy, ',0,1,'L',1);
    	Cell(90,6, 'Clifton, ID 83228',0,1,'L',0);
    	Cell(90,6, '(208) 747-3021',0,1,'L',0);
    	
    	// Invoice Header
    	SetLeftMargin(160);
    	SetFont('Times','',11);
    	SetFillColor(102, 0, 102);
    	SetXY(135,10);
    	Cell(30,6, 'Invoice No.',1,1,'L',1);
    	SetXY(135,16);
    	Cell(30,8, @$order['order_id'] . ' cont...',1,1,'C',0); // Invoice Number Variable
    	SetXY(165,10);
    	Cell(30,6, 'Order Date',1,1,'L',1);
    	SetXY(165,16);
    	Cell(30,8, date('m/d/Y', @$order['order_date']),1,1,'C',0); // Needs Date Variable
    	
    	// Resume Table
    		// Main Block - Header
      	SetLeftMargin(10);
      	SetFillColor(102, 0, 102);
      	SetY(35);
      	Cell(40,5, 'Quantity',1,1,'C',1); // Date Line Item Variable
        SetLeftMargin(50);
      	SetY(35);
      	Cell(105,5, 'Description',1,1,'C',1); // Description Line Item Variable
      	SetLeftMargin(155);
      	SetY(35);
      	Cell(40,5, 'Amount',1,1,'C',1);
    	
    	
      $y = 30;
      $i = 1;
      SetLeftMargin(10);
    	SetFillColor(255, 255, 255);
    	SetFont('Times','',10);
    	$y = $y + 5;
    	SetY($y);
    	Cell(40,5, '','B',1,'C',0); // blank cell
    	SetLeftMargin(50);
    	SetY($y);
    	Cell(105,5, '','B',1,'C',0); // blank cell
    	SetLeftMargin(155);
    	SetY($y);
    	Cell(40,5, '','B',1,'R',0); // blank cell
      
    }
		// Main Block - Main
		$y = $y + 5;
		SetLeftMargin(10);
		SetFillColor(255, 255, 255);
		SetY($y);
		Cell(40,5, $product['qty'],'LR',1,'C',0); // Date Line Item Variable
		SetLeftMargin(50);
		SetY($y);
		Cell(105,5, strip_tags($product['name']),'LR',1,'L',0); // Description Line Item Variable
		SetLeftMargin(155);
		SetY($y);
		Cell(40,5, $product['price'],'LR',1,'R',0); // Description Line Item Variable
    $i++;
	endforeach; endif;
	
	$y = $y + 5;
	SetLeftMargin(10);
	SetFillColor(255, 255, 255);
	SetY($y);
	Cell(40,5, '','LR',1,'C',0); // Date Line Item Variable
	SetLeftMargin(50);
	SetY($y);
	Cell(105,5, 'Subtotal:','LR',1,'R',0); // Description Line Item Variable
	SetLeftMargin(155);
	SetY($y);
	Cell(40,5, '$'.@$order['sales_price'],'LR',1,'R',0); // Description Line Item Variable

$y = $y + 5;
	SetLeftMargin(10);
	SetFillColor(255, 255, 255);
	SetY($y);
	Cell(40,5, '','LR',1,'C',0); // Date Line Item Variable
	SetLeftMargin(50);
	SetY($y);
	Cell(105,5, 'Shipping:','LR',1,'R',0); // Description Line Item Variable
	SetLeftMargin(155);
	SetY($y);
	Cell(40,5, '$'.@$order['shipping'],'LR',1,'R',0); // Description Line Item Variable
	
$y = $y + 5;
	SetLeftMargin(10);
	SetFillColor(255, 255, 255);
	SetY($y);
	Cell(40,5, '','LR',1,'C',0); // Date Line Item Variable
	SetLeftMargin(50);
	SetY($y);
	Cell(105,5, 'Tax:','LR',1,'R',0); // Description Line Item Variable
	SetLeftMargin(155);
	SetY($y);
	Cell(40,5, '$'.@$order['tax'],'LR',1,'R',0); // Description Line Item Variable
	
	SetLeftMargin(10);
	SetFillColor(255, 255, 255);
	SetFont('Times','B',10);
	$y = $y + 5;
	SetY($y);
	Cell(40,5, '','T',1,'C',0); // blank cell
	SetLeftMargin(50);
	SetY($y);
	Cell(105,5, '','T',1,'C',0); // blank cell
	SetLeftMargin(155);
	SetY($y);
	Cell(40,5, 'Total:  $'.number_format(@$order['sales_price'] + @$order['shipping'] + @$order['tax'], 2, '.', ','),'T',1,'R',0); // Date Line Item Variable
	
	/*
// Main Block - Footer
	//terms details
	$y = $y + 10;
	SetLeftMargin(10);
	SetY($y);
	SetFillColor(255, 255, 255);
	SetFont('Times','',10);
	MultiCell(0, 5, 'This cell is for us to put lost of technical jargon that no one every reads but makes the lawyers happy.' ,0 , 1 , 0);

	    $y = $y + 15;
	SetLeftMargin(10);
	SetY($y);
	SetFillColor(255, 255, 255);
  MultiCell(0, 5, 'All the best, The Butterfly Express Team' ,0 , C , 0);
*/
  if(@$orders[$key + 1])
    AddPage();
  	endforeach; endif;
		
?>