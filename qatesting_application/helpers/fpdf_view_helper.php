<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

if (!function_exists('pdf_init'))
{
	function pdf_init($pdf_lib)
	{
		// Get an instance to CI		
		$CI =& get_instance();
		// Load PDF Library
		$CI->load->library($pdf_lib, NULL, 'PDF');
		return TRUE;
  }
}

if (!function_exists('AddFont'))
{
	function AddFont($family, $style=NULL, $file=NULL)
	{
		$CI =& get_instance();
		$CI->PDF->AddFont($family, $style, $file);
  }
}

if (!function_exists('AddLink'))
{
	function AddLink()
	{
		$CI =& get_instance();
		return $CI->PDF->AddLink();
  }
}

if (!function_exists('AddPage'))
{
	function AddPage($orientation=NULL, $size=NULL)
	{
		$CI =& get_instance();
		$CI->PDF->AddPage($orientation, $size);
  }
}

if (!function_exists('AliasNbPages'))
{
	function AliasNbPages($alias=NULL)
	{
		$CI =& get_instance();
		$CI->PDF->AliasNbPages($alias);
  }
}

if (!function_exists('Cell'))
{
	function Cell($w, $h=0, $txt='', $border=0, $ln=0, $align='', $fill=FALSE, $link=NULL)
	{
		$CI =& get_instance();
		$CI->PDF->Cell($w, $h, $txt, $border, $ln, $align, $fill, $link);
  }
}

if (!function_exists('GetStringWidth'))
{
	function GetStringWidth($s)
	{
		$CI =& get_instance();
		return $CI->PDF->GetStringWidth($s);
  }
}

if (!function_exists('GetX'))
{
	function GetX()
	{
		$CI =& get_instance();
		return $CI->PDF->GetX();
  }
}

if (!function_exists('GetY'))
{
	function GetY()
	{
		$CI =& get_instance();
		return $CI->PDF->GetY();
  }
}

if (!function_exists('Image'))
{
	function Image($file, $x=NULL, $y=NULL, $w=0, $h=0, $type=NULL, $link=NULL)
	{
		$CI =& get_instance();
		$CI->PDF->Image($file, $x, $y, $w, $h, $type, $link);
  }
}

if (!function_exists('Line'))
{
	function Line($x1, $y1, $x2, $y2)
	{
		$CI =& get_instance();
		$CI->PDF->Line($x1, $y1, $x2, $y2);
  }
}

if (!function_exists('Link'))
{
	function Link($x, $y, $w, $h, $link)
	{
		$CI =& get_instance();
		$CI->PDF->Link($x, $y, $w, $h, $link);
  }
}

if (!function_exists('Ln'))
{
	function Ln($h=NULL)
	{
		$CI =& get_instance();
		$CI->PDF->Ln($h);
  }
}

if (!function_exists('MultiCell'))
{
	function MultiCell($w, $h, $txt, $border=0, $align='J', $fill=FALSE)
	{
		$CI =& get_instance();
		$CI->PDF->MultiCell($w, $h, $txt, $border, $align, $fill);
  }
}

if (!function_exists('PageNo'))
{
	function PageNo()
	{
		$CI =& get_instance();
		$CI->PDF->PageNo();
  }
}

if (!function_exists('Rect'))
{
	function Rect($x, $y, $w, $h, $style='')
	{
		$CI =& get_instance();
		$CI->PDF->Rect($x, $y, $w, $h, $style);
  }
}

if (!function_exists('SetDrawColor'))
{
	function SetDrawColor($r, $g=NULL, $b=NULL)
	{
		$CI =& get_instance();
		$CI->PDF->SetDrawColor($r, $g, $b);
  }
}

if (!function_exists('SetFillColor'))
{
	function SetFillColor($r, $g=NULL, $b=NULL)
	{
		$CI =& get_instance();
		$CI->PDF->SetFillColor($r, $g, $b);
  }
}

if (!function_exists('SetFont'))
{
	function SetFont($family='', $style='', $size=NULL)
	{
		$CI =& get_instance();
		$CI->PDF->SetFont($family, $style, $size);
  }
}

if (!function_exists('SetFontSize'))
{
	function SetFontSize($size)
	{
		$CI =& get_instance();
		$CI->PDF->SetFontSize($size);
  }
}

if (!function_exists('SetLeftMargin'))
{
	function SetLeftMargin($margin)
	{
		$CI =& get_instance();
		$CI->PDF->SetLeftMargin($margin);
  }
}

if (!function_exists('SetLineWidth'))
{
	function SetLineWidth($width)
	{
		$CI =& get_instance();
		$CI->PDF->SetLineWidth($width);
  }
}

if (!function_exists('SetLink'))
{
	function SetLink($link, $y=0, $page=-1)
	{
		$CI =& get_instance();
		$CI->PDF->SetLink($link, $y, $page);
  }
}

if (!function_exists('SetMargins'))
{
	function SetMargins($left, $top, $right=NULL)
	{
		$CI =& get_instance();
		$CI->PDF->SetMargins($left, $top, $right);
  }
}

if (!function_exists('SetRightMargin'))
{
	function SetRightMargin($margin)
	{
		$CI =& get_instance();
		$CI->PDF->SetRightMargin($margin);
  }
}

if (!function_exists('SetTextColor'))
{
	function SetTextColor($r, $g=NULL, $b=NULL)
	{
		$CI =& get_instance();
		$CI->PDF->SetTextColor($r, $g, $b);
  }
}

if (!function_exists('SetTopMargin'))
{
	function SetTopMargin($margin)
	{
		$CI =& get_instance();
		$CI->PDF->SetTopMargin($margin);
  }
}

if (!function_exists('SetX'))
{
	function SetX($x)
	{
		$CI =& get_instance();
		$CI->PDF->SetX($x);
  }
}

if (!function_exists('SetXY'))
{
	function SetXY($x, $y)
	{
		$CI =& get_instance();
		$CI->PDF->SetXY($x, $y);
  }
}

if (!function_exists('SetY'))
{
	function SetY($y)
	{
		$CI =& get_instance();
		$CI->PDF->SetY($y);
  }
}

if (!function_exists('Text'))
{
	function Text($x, $y, $txt)
	{
		$CI =& get_instance();
		$CI->PDF->Text($x, $y, $txt);
  }
}

if (!function_exists('Write'))
{
	function Write($h, $txt, $link=NULL)
	{
		$CI =& get_instance();
		$CI->PDF->Write($h, $txt, $link=NULL);
  }
}

/* End of file fpdf_view_helper.php */
/* Location: SHAREDAPPPATH/helpers/_fw/fpdf_view_helper.php */
