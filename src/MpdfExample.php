<?php

namespace JC\Mpdf;

use JC\Mpdf\components\PdfManager;

class MpdfExample
{

    public static function create()
    {
        $template = file_get_contents(__DIR__ . '/templates/template.html');
        $template = str_replace('{{NAME}}', 'Jefferson Cechinel', $template);

        $pdfMgr = new PdfManager($template);

        if (!$pdf = @$pdfMgr->createPdf()) {
            throw new \Exception('Error creating pdf file.');
        }

        @$pdf->saveAs(__DIR__ . '/output/template.pdf');
    }

}
