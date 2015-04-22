<?php

class Billmate_Bankpay_Model_Adminhtml_Comment{
    public function getCommentText(){ //this method must exits. It returns the text for the comment
        $lang = explode('_',Mage::getStoreConfig('general/locale/code'));
        $langCode = ($lang[0] == 'sv') ? $lang[0] : 'en';
        return '<img src="'.Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA).'billmate/images/'.$langCode.'/bankpay.png"/>';
    }
}
