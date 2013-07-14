<?php

class Ebate_Mailersoft_Helper_Data extends Mage_Core_Helper_Abstract {

    /**
     * Force Download Code block in your controller
     *
     * @param string $fileName
     * @param string $content set to null to avoid starting output, $contentLength should be set explicitly in that case
     * @param string $contentType
     * @param int $contentLength explicit content length, if strlen($content) isn't applicable
     * @return Mage_Adminhtml_Controller_Action
     */
    public function _prepareDownloadResponse($fileName, $content, $contentType = 'application/octet-stream', $contentLength = null) {
        $session = Mage::getSingleton('admin/session');
        if ($session->isFirstPageAfterLogin()) {
            $this->_redirect($session->getUser()->getStartupPageUrl());
            return $this;
        }
        Mage::app()->getResponse()
                ->setHttpResponseCode(200)
                ->setHeader('Charset', 'utf-8')
                ->setHeader('Pragma', 'public', true)
                ->setHeader('Cache-Control', 'must-revalidate, post-check=0, pre-check=0', true)
                ->setHeader('Content-type', $contentType, true)
                ->setHeader('Content-Length', is_null($contentLength) ? strlen($content) : $contentLength)
                ->setHeader('Content-Disposition', 'attachment; filename=' . $fileName)
                ->setHeader('Last-Modified', date('r'));
        if (!is_null($content)) {
            Mage::app()->getResponse()->setBody($content);
        }
        return $this;
    }

}
