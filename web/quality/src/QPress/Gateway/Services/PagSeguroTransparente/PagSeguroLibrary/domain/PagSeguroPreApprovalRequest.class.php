<?php
/**
 * 2007-2014 [PagSeguro Internet Ltda.]
 *
 * NOTICE OF LICENSE
 *
 *Licensed under the Apache License, Version 2.0 (the "License");
 *you may not use this file except in compliance with the License.
 *You may obtain a copy of the License at
 *
 *http://www.apache.org/licenses/LICENSE-2.0
 *
 *Unless required by applicable law or agreed to in writing, software
 *distributed under the License is distributed on an "AS IS" BASIS,
 *WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 *See the License for the specific language governing permissions and
 *limitations under the License.
 *
 *  @author    André da Silva Medeiros <andre@swdesign.net.br>
 *  @copyright 2007-2014 PagSeguro Internet Ltda.
 *  @license   http://www.apache.org/licenses/LICENSE-2.0
 */

/***
 * Represents a preApproval request
 */
class PagSeguroPreApprovalRequest extends PagSeguroPaymentRequest
{

    private $reviewURL;
    private $preApprovalMaxTotalAmount;
    private $preApprovalMaxAmountPerPeriod;
    private $preApprovalFinalDate;
    private $preApprovalInitialDate;
    private $preApprovalDayOfMonth;
    private $preApprovalDayOfWeek;
    private $preApprovalPeriod;
    private $preApprovalAmountPerPayment;
    private $preApprovalDetails;
    private $preApprovalName;
    private $preApprovalCharge;

    /***
     * Sets the redirect URL
     *
     * Uri to where the PagSeguro payment page should redirect the user after the payment information is processed.
     * Typically this is a confirmation page on your web site.
     *
     * @param String $redirectURL
     */
    public function setReviewURL($reviewURL)
    {
        $this->reviewURL = $this->verifyURLTest($reviewURL);
    }

    /***
     * Sets the preApprovalMaxTotalAmount for this pre approval
     * @param double $value
     */
    public function setPreApprovalMaxTotalAmount($value) {
        $this->preApprovalMaxTotalAmount = $value;
    }
    
    /***
     * Sets the preApprovalMaxAmountPerPeriod for this pre approval
     * @param double $value
     */
    public function setPreApprovalMaxAmountPerPeriod($value) {
        $this->preApprovalMaxAmountPerPeriod = $value;
    }
    
    /***
     * Sets the preApprovalFinalDate for this pre approval
     * @param ISODate $date
     */
    public function setPreApprovalFinalDate($date) {
        $this->preApprovalFinalDate = $date;
    }
    
    /***
     * Sets the preApprovalInitialDate for this pre approval
     * @param ISODate $date
     */
    public function setPreApprovalInitialDate($date) {
        $this->preApprovalInitialDate = $date;
    }
    
    /***
     * Sets the preApprovalDayOfMonth for this pre approval
     * @param Number $day
     */
    public function setPreApprovalDayOfMonth($day) {
        $this->preApprovalDayOfMonth = $day;
    }
    
    /***
     * Sets the preApprovalDayOfWeek for this pre approval
     * @param String $day
     */
    public function setPreApprovalDayOfWeek($day) {
        $this->preApprovalDayOfWeek = $day;
    }
    
    /***
     * Sets the preApprovalPeriod for this pre approval
     * @param String $period
     */
    public function setPreApprovalPeriod($period) {
        $this->preApprovalPeriod = $period;
    }
    
    /***
     * Sets the preApprovalAmountPerPayment value for the recurrent payment
     * @param double $value
     */
    public function setPreApprovalAmountPerPayment($value) {
        $this->preApprovalAmountPerPayment = $value;
    }
    
    /***
     * Sets the preApprovalDetails for the transaction
     * @param String $details
     */
    public function setPreApprovalDetails($details) {
        $this->preApprovalDetails = $details;
    }
    
    /***
     * Sets the preApprovalName (title) for the transaction
     * @param String $name
     */
    public function setPreApprovalName($name) {
        $this->preApprovalName = $name;
    }
    
    /***
     * Sets the preApprovalCharge type (auto, manual)
     * @param String $type
     */
    public function setPreApprovalCharge($type) {
        $this->preApprovalCharge = $type;
    }
    
    public function doPreApproval(PagSeguroCredentials $credentials, $onlyCheckoutCode = false)
    {
        return PagSeguroPreApprovalService::createPreApprovalRequest($credentials, $this);
    }
}
