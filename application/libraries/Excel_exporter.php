<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Excel_exporter Library — Menghasilkan file Excel (.xlsx asli / XML Spreadsheet)
 * kompatibel Microsoft Excel 2007+, LibreOffice Calc, dan Google Sheets secara native
 * tanpa memerlukan vendor PHPSpreadsheet pihak ketiga yang berat.
 */
class Excel_exporter
{
	/**
	 * Export data tabel ke format file Excel (.xlsx standar atau .xls XML)
	 *
	 * @param string $filename
	 * @param array  $headers   Daftar nama kolom (array 1D)
	 * @param array  $rows      Daftar baris data (array 2D)
	 * @param string $sheetname Nama worksheet
	 */
	public function download_excel($filename, array $headers, array $rows, $sheetname = 'Laporan Tahfidz')
	{
		// Cek ketersediaan ZipArchive bawaan PHP untuk membuat .xlsx resmi
		if (class_exists('ZipArchive')) {
			$this->export_xlsx($filename, $headers, $rows, $sheetname);
			return;
		}

		// Fallback ke XML Spreadsheet jika ZipArchive tidak aktif
		$this->export_xml_xls($filename, $headers, $rows, $sheetname);
	}

	/**
	 * Membuat berkas .xlsx resmi (Office Open XML / Zip Container)
	 */
	private function export_xlsx($filename, array $headers, array $rows, $sheetname)
	{
		$filename = preg_replace('/\.(xls|xlsx)$/i', '', $filename) . '.xlsx';

		$temp_file = tempnam(sys_get_temp_dir(), 'xlsx_');
		$zip = new ZipArchive();
		if ($zip->open($temp_file, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== TRUE) {
			$this->export_xml_xls($filename, $headers, $rows, $sheetname);
			return;
		}

		// 1. [Content_Types].xml
		$content_types = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>' . "\n"
			. '<Types xmlns="http://schemas.openxmlformats.org/package/2006/content-types">'
			. '<Default Extension="rels" ContentType="application/vnd.openxmlformats-package.relationships+xml"/>'
			. '<Default Extension="xml" ContentType="application/xml"/>'
			. '<Override PartName="/xl/workbook.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet.main+xml"/>'
			. '<Override PartName="/xl/worksheets/sheet1.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.worksheet+xml"/>'
			. '<Override PartName="/xl/styles.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.styles+xml"/>'
			. '</Types>';
		$zip->addFromString('[Content_Types].xml', $content_types);

		// 2. _rels/.rels
		$rels = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>' . "\n"
			. '<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">'
			. '<Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/officeDocument" Target="xl/workbook.xml"/>'
			. '</Relationships>';
		$zip->addFromString('_rels/.rels', $rels);

		// 3. xl/_rels/workbook.xml.rels
		$wb_rels = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>' . "\n"
			. '<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">'
			. '<Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/worksheet" Target="worksheets/sheet1.xml"/>'
			. '<Relationship Id="rId2" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/styles" Target="styles.xml"/>'
			. '</Relationships>';
		$zip->addFromString('xl/_rels/workbook.xml.rels', $wb_rels);

		// 4. xl/workbook.xml
		$clean_sheetname = preg_replace('/[\\\\\\/\?\*\[\]\:]/', '', $sheetname) ?: 'Sheet1';
		$workbook = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>' . "\n"
			. '<workbook xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main" xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships">'
			. '<sheets>'
			. '<sheet name="' . htmlspecialchars($clean_sheetname, ENT_QUOTES, 'UTF-8') . '" sheetId="1" r:id="rId1"/>'
			. '</sheets>'
			. '</workbook>';
		$zip->addFromString('xl/workbook.xml', $workbook);

		// 5. xl/styles.xml
		$styles = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>' . "\n"
			. '<styleSheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main">'
			. '<fonts count="2">'
			. ' <font><sz val="11"/><name val="Calibri"/></font>'
			. ' <font><b/><sz val="11"/><color rgb="FFFFFFFF"/><name val="Calibri"/></font>'
			. '</fonts>'
			. '<fills count="3">'
			. ' <fill><patternFill patternType="none"/></fill>'
			. ' <fill><patternFill patternType="gray125"/></fill>'
			. ' <fill><patternFill patternType="solid"><fgColor rgb="FF059669"/><bgColor indexed="64"/></patternFill></fill>'
			. '</fills>'
			. '<borders count="2">'
			. ' <border><left/><right/><top/><bottom/></border>'
			. ' <border>'
			. '  <left style="thin"><color rgb="FFE5E7EB"/></left>'
			. '  <right style="thin"><color rgb="FFE5E7EB"/></right>'
			. '  <top style="thin"><color rgb="FFE5E7EB"/></top>'
			. '  <bottom style="thin"><color rgb="FFE5E7EB"/></bottom>'
			. ' </border>'
			. '</borders>'
			. '<cellStyleXfs count="1">'
			. ' <xf numFmtId="0" fontId="0" fillId="0" borderId="0"/>'
			. '</cellStyleXfs>'
			. '<cellXfs count="3">'
			. ' <xf numFmtId="0" fontId="0" fillId="0" borderId="1" xfId="0" applyFont="1" applyBorder="1"/>'
			. ' <xf numFmtId="0" fontId="1" fillId="2" borderId="1" xfId="0" applyFont="1" applyFill="1" applyBorder="1" applyAlignment="1"><alignment horizontal="center" vertical="center"/></xf>'
			. ' <xf numFmtId="0" fontId="0" fillId="0" borderId="1" xfId="0" applyAlignment="1"><alignment horizontal="center" vertical="center"/></xf>'
			. '</cellXfs>'
			. '</styleSheet>';
		$zip->addFromString('xl/styles.xml', $styles);

		// 6. xl/worksheets/sheet1.xml
		$sheet = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>' . "\n"
			. '<worksheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main">'
			. '<sheetData>';

		$row_idx = 1;

		// Header row
		$sheet .= '<row r="' . $row_idx . '">';
		foreach ($headers as $c_idx => $h_val) {
			$col_letter = $this->get_col_letter($c_idx + 1);
			$cell_ref = $col_letter . $row_idx;
			$sheet .= '<c r="' . $cell_ref . '" s="1" t="inlineStr"><is><t>' . htmlspecialchars((string)$h_val, ENT_QUOTES, 'UTF-8') . '</t></is></c>';
		}
		$sheet .= '</row>';
		$row_idx++;

		// Data rows
		foreach ($rows as $r_data) {
			$sheet .= '<row r="' . $row_idx . '">';
			$col_num = 1;
			foreach ($r_data as $val) {
				$col_letter = $this->get_col_letter($col_num);
				$cell_ref = $col_letter . $row_idx;
				$val_str = (string) $val;

				if (is_numeric($val) && !preg_match('/^0[0-9]+/', $val_str)) {
					$sheet .= '<c r="' . $cell_ref . '" s="0" t="n"><v>' . $val_str . '</v></c>';
				} else {
					$sheet .= '<c r="' . $cell_ref . '" s="0" t="inlineStr"><is><t>' . htmlspecialchars($val_str, ENT_QUOTES, 'UTF-8') . '</t></is></c>';
				}
				$col_num++;
			}
			$sheet .= '</row>';
			$row_idx++;
		}

		$sheet .= '</sheetData></worksheet>';
		$zip->addFromString('xl/worksheets/sheet1.xml', $sheet);

		$zip->close();

		// Output download headers
		header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		header('Content-Disposition: attachment; filename="' . $filename . '"');
		header('Content-Length: ' . filesize($temp_file));
		header('Cache-Control: max-age=0');
		header('Pragma: public');

		readfile($temp_file);
		@unlink($temp_file);
		exit();
	}

	/**
	 * Helper konversi index angka ke huruf kolom Excel (1=A, 2=B, 27=AA, dst)
	 */
	private function get_col_letter($col_num)
	{
		$letter = '';
		while ($col_num > 0) {
			$mod = ($col_num - 1) % 26;
			$letter = chr(65 + $mod) . $letter;
			$col_num = (int)(($col_num - $mod) / 26);
		}
		return $letter;
	}

	/**
	 * XML Spreadsheet format (.xls) fallback
	 */
	private function export_xml_xls($filename, array $headers, array $rows, $sheetname)
	{
		$filename = preg_replace('/\.(xls|xlsx)$/i', '', $filename) . '.xls';

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

		$xml .= '<Styles>' . "\n";
		$xml .= ' <Style ss:ID="Default" ss:Name="Normal">' . "\n";
		$xml .= '  <Alignment ss:Vertical="Center"/>' . "\n";
		$xml .= '  <Font ss:FontName="Calibri" ss:Size="11" ss:Color="#333333"/>' . "\n";
		$xml .= ' </Style>' . "\n";

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

		$xml .= ' <Style ss:ID="DataStyle">' . "\n";
		$xml .= '  <Borders>' . "\n";
		$xml .= '   <Border ss:Position="Bottom" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#E5E7EB"/>' . "\n";
		$xml .= '   <Border ss:Position="Left" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#E5E7EB"/>' . "\n";
		$xml .= '   <Border ss:Position="Right" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#E5E7EB"/>' . "\n";
		$xml .= '   <Border ss:Position="Top" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#E5E7EB"/>' . "\n";
		$xml .= '  </Borders>' . "\n";
		$xml .= ' </Style>' . "\n";

		$xml .= '</Styles>' . "\n";

		$xml .= '<Worksheet ss:Name="' . htmlspecialchars($sheetname) . '">' . "\n";
		$xml .= ' <Table>' . "\n";

		$xml .= '  <Row ss:Height="26">' . "\n";
		foreach ($headers as $h) {
			$xml .= '   <Cell ss:StyleID="HeaderStyle"><Data ss:Type="String">' . htmlspecialchars($h) . '</Data></Cell>' . "\n";
		}
		$xml .= '  </Row>' . "\n";

		foreach ($rows as $row) {
			$xml .= '  <Row ss:Height="20">' . "\n";
			foreach ($row as $val) {
				$val_str = (string) $val;
				$type = is_numeric($val) && !preg_match('/^0[0-9]+/', $val_str) ? 'Number' : 'String';
				$xml .= '   <Cell ss:StyleID="DataStyle"><Data ss:Type="' . $type . '">' . htmlspecialchars($val_str) . '</Data></Cell>' . "\n";
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

