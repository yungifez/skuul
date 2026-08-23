<?php

namespace App\Services\Report\Formats;

use App\Contracts\ExportFormat;
use Illuminate\Support\Collection;
use RuntimeException;
use Stringable;
use ZipArchive;

/**
 * A spreadsheet file, written without a spreadsheet library.
 *
 * An office that wants to total a column should not have to import a
 * comma-separated file first. A workbook is a zip of small XML parts, so this
 * writes those parts directly and keeps the application free of another
 * dependency.
 */
class XlsxFormat implements ExportFormat
{
    /**
     * Get the name the format is stored and chosen by.
     */
    public function key(): string
    {
        return 'xlsx';
    }

    /**
     * Get the label to show in the interface.
     */
    public function label(): string
    {
        return 'Spreadsheet (XLSX)';
    }

    /**
     * Get the file extension, without the dot.
     */
    public function extension(): string
    {
        return 'xlsx';
    }

    /**
     * Get the content type to send the file with.
     */
    public function mimeType(): string
    {
        return 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet';
    }

    /**
     * Turn the columns and rows into the bytes of one workbook.
     *
     * @param  array<int, string>  $columns
     * @param  Collection<int, array<int, mixed>>  $rows
     */
    public function render(string $title, array $columns, Collection $rows): string
    {
        $file = tempnam(sys_get_temp_dir(), 'xlsx');

        if ($file === false) {
            throw new RuntimeException('The spreadsheet could not be started.');
        }

        $zip = new ZipArchive;

        if ($zip->open($file, ZipArchive::OVERWRITE) !== true) {
            throw new RuntimeException('The spreadsheet could not be written.');
        }

        $zip->addFromString('[Content_Types].xml', $this->contentTypes());
        $zip->addFromString('_rels/.rels', $this->rootRelationships());
        $zip->addFromString('xl/workbook.xml', $this->workbook($title));
        $zip->addFromString('xl/_rels/workbook.xml.rels', $this->workbookRelationships());
        $zip->addFromString('xl/styles.xml', $this->styles());
        $zip->addFromString('xl/worksheets/sheet1.xml', $this->sheet($columns, $rows));
        $zip->close();

        $bytes = (string) file_get_contents($file);
        unlink($file);

        return $bytes;
    }

    /**
     * Build the sheet, with the headings in bold and numbers kept as numbers.
     *
     * @param  array<int, string>  $columns
     * @param  Collection<int, array<int, mixed>>  $rows
     */
    private function sheet(array $columns, Collection $rows): string
    {
        $xml = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            .'<worksheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main"><sheetData>';

        $xml .= $this->row(1, $columns, true);
        $number = 2;

        foreach ($rows as $row) {
            $xml .= $this->row($number, array_values($row), false);
            $number++;
        }

        return $xml.'</sheetData></worksheet>';
    }

    /**
     * Build one row of cells.
     *
     * @param  array<int, mixed>  $values
     */
    private function row(int $number, array $values, bool $heading): string
    {
        $xml = "<row r=\"$number\">";

        foreach ($values as $index => $value) {
            $reference = $this->columnName($index).$number;
            $style = $heading ? ' s="1"' : '';

            if ($value === null || $value === '') {
                $xml .= "<c r=\"$reference\"$style/>";

                continue;
            }

            // A number kept as a number is a number the office can total.
            if (!$heading && (is_int($value) || is_float($value))) {
                $xml .= "<c r=\"$reference\"$style><v>$value</v></c>";

                continue;
            }

            $text = htmlspecialchars($this->text($value), ENT_QUOTES | ENT_XML1);
            $xml .= "<c r=\"$reference\"$style t=\"inlineStr\"><is><t xml:space=\"preserve\">$text</t></is></c>";
        }

        return $xml.'</row>';
    }

    /**
     * Read one cell as the text a reader should see.
     */
    private function text(mixed $value): string
    {
        if (is_scalar($value)) {
            return (string) $value;
        }

        return $value instanceof Stringable ? (string) $value : '';
    }

    /**
     * Turn a zero-based column number into its spreadsheet letters.
     */
    private function columnName(int $index): string
    {
        $name = '';

        for ($number = $index + 1; $number > 0; $number = intdiv($number - 1, 26)) {
            $name = chr(65 + ($number - 1) % 26).$name;
        }

        return $name;
    }

    /**
     * Say which parts the file holds.
     */
    private function contentTypes(): string
    {
        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            .'<Types xmlns="http://schemas.openxmlformats.org/package/2006/content-types">'
            .'<Default Extension="rels" ContentType="application/vnd.openxmlformats-package.relationships+xml"/>'
            .'<Default Extension="xml" ContentType="application/xml"/>'
            .'<Override PartName="/xl/workbook.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet.main+xml"/>'
            .'<Override PartName="/xl/worksheets/sheet1.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.worksheet+xml"/>'
            .'<Override PartName="/xl/styles.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.styles+xml"/>'
            .'</Types>';
    }

    /**
     * Point the reader at the workbook.
     */
    private function rootRelationships(): string
    {
        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            .'<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">'
            .'<Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/officeDocument" Target="xl/workbook.xml"/>'
            .'</Relationships>';
    }

    /**
     * Describe the workbook and its one sheet.
     */
    private function workbook(string $title): string
    {
        // Sheet names cannot hold these characters, and stop at 31.
        $name = substr(str_replace(['\\', '/', '?', '*', '[', ']', ':'], ' ', $title), 0, 31);
        $name = htmlspecialchars($name === '' ? 'Report' : $name, ENT_QUOTES | ENT_XML1);

        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            .'<workbook xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main" '
            .'xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships">'
            ."<sheets><sheet name=\"$name\" sheetId=\"1\" r:id=\"rId1\"/></sheets>"
            .'</workbook>';
    }

    /**
     * Point the workbook at its sheet and styles.
     */
    private function workbookRelationships(): string
    {
        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            .'<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">'
            .'<Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/worksheet" Target="worksheets/sheet1.xml"/>'
            .'<Relationship Id="rId2" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/styles" Target="styles.xml"/>'
            .'</Relationships>';
    }

    /**
     * Two styles: ordinary, and the bold one the headings use.
     */
    private function styles(): string
    {
        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            .'<styleSheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main">'
            .'<fonts count="2"><font><sz val="11"/><name val="Calibri"/></font>'
            .'<font><b/><sz val="11"/><name val="Calibri"/></font></fonts>'
            .'<fills count="1"><fill><patternFill patternType="none"/></fill></fills>'
            .'<borders count="1"><border/></borders>'
            .'<cellStyleXfs count="1"><xf/></cellStyleXfs>'
            .'<cellXfs count="2"><xf xfId="0"/><xf xfId="0" fontId="1" applyFont="1"/></cellXfs>'
            .'</styleSheet>';
    }
}
