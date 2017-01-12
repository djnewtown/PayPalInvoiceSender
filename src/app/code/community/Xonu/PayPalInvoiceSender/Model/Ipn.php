<?php
/**
 * @copyright Copyright (c) 2013 Pawel Kazakow (http://xonu.de)
 */

/**
 * This file is part of PayPalInvoiceSender for Magento.
 *
 * @package     PayPalInvoiceSender
 * @copyright   Copyright (c) 2017 Newtown-Web OG (http://www.newtown.at)
 * @author      Ingo Fabbri <if@newtown.at>
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Xonu_PayPalInvoiceSender_Model_Ipn extends Mage_Paypal_Model_Ipn
{
    /**
     * Process completed payment (either full or partial)
     *
     * @param bool $skipFraudDetection
     */
    protected function _registerPaymentCapture($skipFraudDetection = false)
    {
        parent::_registerPaymentCapture($skipFraudDetection);

        $payment = $this->_order->getPayment();
        $invoice = $payment->getCreatedInvoice();

        if ($invoice) {
            $this->_sendInvoiceToCustomer($invoice);
        }
    }

    /**
     * send invoice to customer
     *
     * @param Mage_Sales_Model_Order_Invoice $invoice
     * @return $this
     */
    protected function _sendInvoiceToCustomer($invoice)
    {
        $sendInvoiceFlag = Mage::getStoreConfigFlag('paypalinvoicesender/general/send_invoice');
        if ($sendInvoiceFlag === false) {
            return $this;
        }

        if ((bool)$invoice->getEmailSent() === false) {
            try {
                $invoice->sendEmail(true);
            } catch (Exception $e) {
                Mage::logException($e);
            }
        }

        return $this;
    }
}
