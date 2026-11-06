<?php

namespace Tests\Unit;

use App\Support\XlsxReportWriter;
use PHPUnit\Framework\TestCase;
use ZipArchive;

class XlsxReportWriterTest extends TestCase
{
    public function test_it_creates_an_excel_workbook_with_safe_string_cells(): void
    {
        $path = (new XlsxReportWriter())->write(
            'Báo cáo bệnh nhân',
            ['Họ và tên', 'Ghi chú'],
            [['Nguyễn Văn A', '=1+1']]
        );

        try {
            $archive = new ZipArchive();
            $this->assertSame(true, $archive->open($path));

            $sheet = $archive->getFromName('xl/worksheets/sheet1.xml');
            $archive->close();

            $this->assertIsString($sheet);
            $this->assertStringContainsString('Nguyễn Văn A', $sheet);
            $this->assertStringContainsString('=1+1', $sheet);
            $this->assertStringContainsString('t="inlineStr"', $sheet);
        } finally {
            unlink($path);
        }
    }
}
