<?php
/**
 * Created by PhpStorm.
 * User: jesper
 * Date: 2015-01-19
 * Time: 16:45
 */ 
class Billmate_Common_Model_Observer extends Mage_Core_Model_Abstract
{

    public function adminSystemConfigChangedSectionBillmate()
    {

    }

    public function redirectToCancelUrl(Varien_Event_Observer $observer)
    {
        $controllerAction = $observer->getEvent()->getControllerAction();
        $session = Mage::getSingleton('checkout/session');
        if($session->getRebuildCart()){
            Mage::log('rebuild');
            $order = Mage::getModel('sales/order');
            $message = 'Order canceled by user';
            $order_id = $session->getLastRealOrderId();
            $order->loadByIncrementId($order_id);

            if (!$order->isCanceled() && !$order->hasInvoices()) {
                $order->cancel();
                $order->addStatusToHistory(Mage_Sales_Model_Order::STATE_CANCELED, $message);
                $order->save();

                // Rollback stock
                // Mage::helper('billmatecardpay')->rollbackStockItems($order);
            }

            //$session->setQuoteId($session->getBillmateQuoteId(true));
            if ($quoteId = $session->getLastQuoteId()) {
                $quote = Mage::getModel('sales/quote')->load($quoteId);
                if ($quote->getId()) {
                    $quote->setIsActive(true)->save();
                    $session->setQuoteId($quoteId);
                }

                $quoteItems = $quote->getAllItems();
                if( sizeof( $quoteItems ) <=0 ){
                    $items = $order->getAllItems();
                    if( $items ){
                        foreach( $items as $item ){
                            $product1 = Mage::getModel('catalog/product')->load($item->getProductId());
                            $qty = $item->getQtyOrdered();
                            $quote->addProduct($product1, $qty);
                        }
                    }else{
                        $quote->setIsActive(false)->save();
                        $this->_redirect('/');
                    }
                    $quote->setReservedOrderId(null);
                    $quote->collectTotals()->save();
                }
            }
        }
    }


}