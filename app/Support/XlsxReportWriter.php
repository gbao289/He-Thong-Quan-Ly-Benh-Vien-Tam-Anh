<?php

namespace App\Support;

use RuntimeException;
use ZipArchive;

class XlsxReportWriter
{
    /**
     * @param array<int, string> $headers
     * @param iterable<array<int, string|int|float>> $rows
     */
    public function write(string $title, array $headers, iterable $rows, string $summary = ''): string
    {
        $path = tempnam(sys_get_temp_dir(), 'tamanh-report-');

        if ($path === false) {
            throw new RuntimeException('Không thể tạo tệp báo cáo tạm thời.');
        }

        $zip = new ZipArchive();
        if ($zip->open($path, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
            unlink($path);
            throw new RuntimeException('Không thể khởi tạo tệp Excel.');
        }

        $files = [
            '[Content_Types].xml' => $this->contentTypesXml(),
            '_rels/.rels' => $this->rootRelationshipsXml(),
            'xl/workbook.xml' => $this->workbookXml($title),
            'xl/_rels/workbook.xml.rels' => $this->workbookRelationshipsXml(),
            'xl/worksheets/sheet1.xml' => $this->worksheetXml($headers, $rows, $summary),
        ];

        foreach ($files as $name => $contents) {
            if (!$zip->addFromString($name, $contents)) {
                $zip->close();
                unlink($path);
                throw new RuntimeException('Không thể ghi dữ liệu vào tệp Excel.');
            }
        }

        if (!$zip->close()) {
            unlink($path);
            throw new RuntimeException('Không thể hoàn tất tệp Excel.');
        }

        return $path;
    }

    private function contentTypesXml(): string
    {
        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            . '<Types xmlns="http://schemas.openxmlformats.org/package/2006/content-types">'
            . '<Default Extension="rels" ContentType="application/vnd.openxmlformats-package.relationships+xml"/>'
            . '<Default Extension="xml" ContentType="application/xml"/>'
            . '<Override PartName="/xl/workbook.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet.main+xml"/>'
            . '<Override PartName="/xl/worksheets/sheet1.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.worksheet+xml"/>'
            . '</Types>';
    }

    private function rootRelationshipsXml(): string
    {
        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            . '<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">'
            . '<Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/officeDocument" Target="xl/workbook.xml"/>'
            . '</Relationships>';
    }

    private function workbookXml(string $title): string
    {
        $xml = new \XMLWriter();
        $xml->openMemory();
        $xml->startDocument('1.0', 'UTF-8');
        $xml->startElement('workbook');
        $xml->writeAttribute('xmlns', 'http://schemas.openxmlformats.org/spreadsheetml/2006/main');
        $xml->writeAttribute('xmlns:r', 'http://schemas.openxmlformats.org/officeDocument/2006/relationships');
        $xml->startElement('sheets');
        $xml->startElement('sheet');
        $xml->writeAttribute('name', mb_substr($title, 0, 31));
        $xml->writeAttribute('sheetId', '1');
        $xml->writeAttribute('r:id', 'rId1');
        $xml->endElement();
        $xml->endElement();
        $xml->endElement();
        $xml->endDocument();

        return $xml->outputMemory();
    }

    private function workbookRelationshipsXml(): string
    {
        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            . '<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">'
            . '<Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/worksheet" Target="worksheets/sheet1.xml"/>'
            . '</Relationships>';
    }

    /**
     * @param array<int, string> $headers
     * @param iterable<array<int, string|int|float>> $rows
     */
    private function worksheetXml(array $headers, iterable $rows, string $summary): string
    {
        $xml = new \XMLWriter();
        $xml->openMemory();
        $xml->startDocument('1.0', 'UTF-8');
        $xml->startElement('worksheet');
        $xml->writeAttribute('xmlns', 'http://schemas.openxmlformats.org/spreadsheetml/2006/main');
        $xml->startElement('sheetData');

        $rowNumber = 1;
        if ($summary !== '') {
            $this->writeRow($xml, [$summary], $rowNumber++);
        }
        $this->writeRow($xml, $headers, $rowNumber++);
        foreach ($rows as $row) {
            $this->writeRow($xml, $row, $rowNumber++);
        }

        $xml->endElement();
        $xml->endElement();
        $xml->endDocument();

        return $xml->outputMemory();
    }

    /**
     * @param array<int, string|int|float> $values
     */
    private function writeRow(\XMLWriter $xml, array $values, int $rowNumber): void
    {
        $xml->startElement('row');
        $xml->writeAttribute('r', (string) $rowNumber);

        foreach (array_values($values) as $columnIndex => $value) {
            $xml->startElement('c');
            $xml->writeAttribute('r', $this->columnName($columnIndex + 1) . $rowNumber);
            $xml->writeAttribute('t', 'inlineStr');
            $xml->startElement('is');
            $xml->startElement('t');
            $xml->writeAttribute('xml:space', 'preserve');
            $xml->text((string) $value);
            $xml->endElement();
            $xml->endElement();
            $xml->endElement();
        }

        $xml->endElement();
    }

    private function columnName(int $number): string
    {
        $name = '';
        while ($number > 0) {
            $number--;
            $name = chr($number % 26 + 65) . $name;
            $number = intdiv($number, 26);
        }

        return $name;
    }
}
