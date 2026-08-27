<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Excel_exporter Library — Menghasilkan file Excel (.xls / .xlsx XML Spreadsheet)
 * kompatibel Microsoft Excel, LibreOffice Calc, dan Google Sheets secara native
 * tanpa memerlukan ekstensi zip/vendor PHPSpreadsheet yang berat.
 */
class Excel_exporter
{
	/**
	 * Export data tabel ke format XML Excel (.xls) dengan styling header & border
	 *
	 * @param string $filename
	 * @param array  $headers   Daftar nama kolom (array 1D)
	 * @param array  $rows      Daftar baris data (array 2D)
	 * @param string $sheetname Nama worksheet
	 */
	public function download_excel($filename, array $headers, array $rows, $sheetname = 'Laporan Tahfidz')
	{
		if (substr($filename, -4) !== '.xls' && substr($filename, -5) !== '.xlsx') {
			$filename .= '.xls';
		}

		header('Content-Type: application/vnd.ms-excel; charset=UTF-8');
		header('Content-Disposition: attachment; filename="' . $filename . '"');
		header('Cache-Control: max-age=0');

		$xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
		$xml .= '<?mso-application progid="Excel.Sheet"?>' . "\n";
		$xml .= '<Workbook xmlns="urn:schemas-microsoft-com:office:spreadsheet"' . "\n";
		$xml .= ' xmlns:o="urn:schemas-microsoft-com:office:office"' . "\n";
		$xml .= ' xmlns:x="urn:schemas-microsoft-com:office:excel"' . "\n";
		$xml .= ' xmlns:ss="urn:schemas-microsoft-com:office:spreadsheet"' . "\n";
		$xml .= ' xmlns:html="http://www.w3.org/TR/REC-html40">' . "\n";

		// Styles
		$xml .= '<Styles>' . "\n";
		$xml .= ' <Style ss:ID="Default" ss:Name="Normal">' . "\n";
		$xml .= '  <Alignment ss:Vertical="Center"/>' . "\n";
		$xml .= '  <Font ss:FontName="Calibri" ss:Size="11" ss:Color="#333333"/>' . "\n";
		$xml .= ' </Style>' . "\n";

		// Header Style (Emerald background + White text)
		$xml .= ' <Style ss:ID="HeaderStyle">' . "\n";
		$xml .= '  <Alignment ss:Horizontal="Center" ss:Vertical="Center"/>' . "\n";
		$xml .= '  <Borders>' . "\n";
		$xml .= '   <Border ss:Position="Bottom" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#047857"/>' . "\n";
		$xml .= '   <Border ss:Position="Left" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#047857"/>' . "\n";
		$xml .= '   <Border ss:Position="Right" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#047857"/>' . "\n";
		$xml .= '   <Border ss:Position="Top" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#047857"/>' . "\n";
		$xml .= '  </Borders>' . "\n";
		$xml .= '  <Font ss:FontName="Calibri" ss:Size="11" ss:Bold="1" ss:Color="#FFFFFF"/>' . "\n";
		$xml .= '  <Interior ss:Color="#059669" ss:Pattern="Solid"/>' . "\n";
		$xml .= ' </Style>' . "\n";

		// Data Cell Style
		$xml .= ' <Style ss:ID="DataStyle">' . "\n";
		$xml .= '  <Borders>' . "\n";
		$xml .= '   <Border ss:Position="Bottom" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#E5E7EB"/>' . "\n";
		$xml .= '   <Border ss:Position="Left" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#E5E7EB"/>' . "\n";
		$xml .= '   <Border ss:Position="Right" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#E5E7EB"/>' . "\n";
		$xml .= '   <Border ss:Position="Top" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#E5E7EB"/>' . "\n";
		$xml .= '  </Borders>' . "\n";
		$xml .= ' </Style>' . "\n";

		// Center Cell Style
		$xml .= ' <Style ss:ID="CenterStyle">' . "\n";
		$xml .= '  <Alignment ss:Horizontal="Center" ss:Vertical="Center"/>' . "\n";
		$xml .= '  <Borders>' . "\n";
		$xml .= '   <Border ss:Position="Bottom" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#E5E7EB"/>' . "\n";
		$xml .= '   <Border ss:Position="Left" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#E5E7EB"/>' . "\n";
		$xml .= '   <Border ss:Position="Right" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#E5E7EB"/>' . "\n";
		$xml .= '   <Border ss:Position="Top" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#E5E7EB"/>' . "\n";
		$xml .= '  </Borders>' . "\n";
		$xml .= ' </Style>' . "\n";

		// Bold/Number Style
		$xml .= ' <Style ss:ID="BoldStyle">' . "\n";
		$xml .= '  <Alignment ss:Horizontal="Right" ss:Vertical="Center"/>' . "\n";
		$xml .= '  <Borders>' . "\n";
		$xml .= '   <Border ss:Position="Bottom" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#E5E7EB"/>' . "\n";
		$xml .= '   <Border ss:Position="Left" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#E5E7EB"/>' . "\n";
		$xml .= '   <Border ss:Position="Right" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#E5E7EB"/>' . "\n";
		$xml .= '   <Border ss:Position="Top" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#E5E7EB"/>' . "\n";
		$xml .= '  </Borders>' . "\n";
		$xml .= '  <Font ss:FontName="Calibri" ss:Size="11" ss:Bold="1" ss:Color="#059669"/>' . "\n";
		$xml .= ' </Style>' . "\n";

		$xml .= '</Styles>' . "\n";

		// Worksheet
		$xml .= '<Worksheet ss:Name="' . htmlspecialchars($sheetname) . '">' . "\n";
		$xml .= ' <Table>' . "\n";

		// Headers
		$xml .= '  <Row ss:Height="26">' . "\n";
		foreach ($headers as $h) {
			$xml .= '   <Cell ss:StyleID="HeaderStyle"><Data ss:Type="String">' . htmlspecialchars($h) . '</Data></Cell>' . "\n";
		}
		$xml .= '  </Row>' . "\n";

		// Data Rows
		foreach ($rows as $row) {
			$xml .= '  <Row ss:Height="20">' . "\n";
			foreach ($row as $idx => $val) {
				$val_str = (string) $val;
				$type = is_numeric($val) && !preg_match('/^0[0-9]+/', $val_str) ? 'Number' : 'String';
				$style = 'DataStyle';
				if ($type === 'Number' && $idx === count($row) - 3) {
					$style = 'BoldStyle';
				}
				$xml .= '   <Cell ss:StyleID="' . $style . '"><Data ss:Type="' . $type . '">' . htmlspecialchars($val_str) . '</Data></Cell>' . "\n";
			}
			$xml .= '  </Row>' . "\n";
		}

		$xml .= ' </Table>' . "\n";
		$xml .= '</Worksheet>' . "\n";
		$xml .= '</Workbook>';

		echo $xml;
		exit();
	}
}
