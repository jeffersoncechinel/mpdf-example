<?php

namespace JC\Mpdf\components;

use mPDF;

class PdfManager
{
    public $pdf;
    public $content;
    public $isCanvas;

    public function __construct($content)
    {
        $this->content = $content;
    }

    public function createPdf()
    {
        $footer = '';
        $html = $this->content;

        /*if (!defined('_MPDF_TTFONTPATH')) {
             define('_MPDF_TTFONTPATH', Yii::getAlias('@frontend/assets/src/fonts/'));
         }*/

        $canvasBreakLine = null;

        if ($this->isCanvas) {
            $canvasBreakLine = '<hr>';
        }

        $mpdf = new mPDF('utf-8', 'A4', 0, 'proximanova', 10, 10, 10, 10, 10, 5);
        $mpdf->shrink_tables_to_fit = 1;
        $mpdf->setAutoBottomMargin = 'pad';
        //$mpdf->setFooter('Page {PAGENO}');
        $mpdf->SetHTMLFooter(
            '
                <div style="width: 100%; text-align: right; font-size: 9px;">{PAGENO}</div>
                <div style="width: 100%; text-align: left; font-size: 9px;">' .
            $footer . $canvasBreakLine . '  
                </div>'
        );
        //$mpdf->SetDisplayMode('fullpage');

        $fontdata = [
            'proximanova' => [
                'R' => 'proximanova-regular.ttf',
                'B' => 'proximanova-bold.ttf',
            ],
            'arizonia' => [
                'R' => 'Arizonia-Regular.ttf',
            ],
            'pacifico' => [
                'R' => 'Pacifico-Regular.ttf',
            ],
        ];

        foreach ($fontdata as $f => $fs) {
            // add to fontdata array
            $mpdf->fontdata[$f] = $fs;

            // add to available fonts array
            foreach (['R', 'B', 'I', 'BI'] as $style) {
                if (isset($fs[$style]) && $fs[$style]) {
                    // warning: no suffix for regular style! hours wasted: 2
                    $mpdf->available_unifonts[] = $f . trim($style, 'R');
                }
            }
        }

        $mpdf->default_available_fonts = $mpdf->available_unifonts;

        $chunks = str_split($html, 1000000);
        foreach ($chunks as $chunk) {
            $mpdf->WriteHTML($chunk);
        }

        $this->pdf = $mpdf;

        return $this;
    }

    public function saveAs($file)
    {
        $this->pdf->Output($file, 'F');
    }

    public function output($name = '')
    {
        return $this->pdf->Output($name, 'I');
    }

    public function renderPdf()
    {
        return $this->outputBuffer();
    }

    public function outputBuffer($name = '')
    {
        return $this->pdf->Output($name, 'S');
    }

    protected function footerTemplate($code)
    {
        $footerTemplate = '
         <table width="100%">
            <tr>
                <td width="33%" style="font-size: 11px;">{CODE}</td>
            </tr>
         </table>';

        $footerTemplate = str_replace('{CODE}', $code, $footerTemplate);

        return $footerTemplate;
    }
}
