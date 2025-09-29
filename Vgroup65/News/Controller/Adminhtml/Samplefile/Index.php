<?php
namespace Vgroup65\News\Controller\Adminhtml\Samplefile;

use Vgroup65\News\Controller\Adminhtml\FileExport;

class Index extends FileExport
{
    public function execute()
    {
        /* @var Vgroup65\News\Helper\Data */
        $imageHelper = $this->helper;
        $basePath = $imageHelper->getBaseDir();

        if (!file_exists($basePath)):
            try {
                mkdir($basePath, 0775, true);
            } catch (\Exception $e) {
                $this->messageManager->addError(
                    'issue with directory permission, please set permission of the News folder in the media directory'
                );
                $this->_redirect('news/import/index');
                return;
            }
        endif;

        $heading = [
            __('News Id'),
            __('News Title'),
            __('URL Identifier'),
            __('Category ID'),
            __('Store Name'),
            __('Image Url'),
            __('Description'),
            __('Status'),
            __('Publish Date')
        ];
         
        if (!file_exists($basePath)):
            mkdir($basePath, 0775, true);
        endif;
         
        $outputFile = $basePath . "/SampleFile.csv";
        $handle = fopen($outputFile, 'w');

        // ? fixed
        fputcsv($handle, $heading, ',', '"', '\\');
         
        $content = [
            __(''),
            __('Title'),
            __('urlIdentifier'),
            __(''),
            __('Default Store View'),
            __(''),
            __('description'),
            __('Enabled'),
            __('01/22/2018  7:42:38 AM')
        ];

        // ? fixed
        fputcsv($handle, $content, ',', '"', '\\');

        $this->downloadCsv($outputFile);
    }
 
    public function downloadCsv($file)
    {
        if (file_exists($file)) {
            //set appropriate headers
            header('Content-Description: File Transfer');
            header('Content-Type: application/csv');
            header('Content-Disposition: attachment; filename=' . basename($file));
            header('Expires: 0');
            header('Cache-Control: must-revalidate');
            header('Pragma: public');
            header('Content-Length: ' . filesize($file));
            ob_clean();
            flush();
            readfile($file);
            unlink($file);
        }
    }
}
