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
 * Class PagSeguroPreApprovalParser
 */
class PagSeguroPreApprovalParser extends PagSeguroPaymentParser
{

    /***
     * @param $payment PagSeguroPaymentRequest
     * @return mixed
     */
    public static function getData($payment)
    {
		
        $data = parent::getData($payment);

        if($payment->getPreApprovalCharge() != null){
            $data["preApprovalCharge"] = $payment->getPreApprovalCharge();
        };

        if($payment->getPreApprovalName() != null){
            $data["preApprovalName"] = $payment->getPreApprovalName();
        };

        if($payment->getPreApprovalDetails() != null){
            $data["preApprovalDetails"] = $payment->getPreApprovalDetails();
        };

        if($payment->getPreApprovalAmountPerPayment() != null){
            $data["preApprovalAmountPerPayment"] = $payment->getPreApprovalAmountPerPayment();
        };

        if($payment->getPreApprovalPeriod() != null){
            $data["preApprovalPeriod"] = $payment->getPreApprovalPeriod();
        };

        if($payment->getPreApprovalDayOfWeek() != null){
            $data["preApprovalDayOfWeek"] = $payment->getPreApprovalDayOfWeek();
        };

        if($payment->getPreApprovalInitialDate() != null){
            $data["preApprovalInitialDate"] = $payment->getPreApprovalInitialDate();
        };

        if($payment->getPreApprovalFinalDate() != null){
            $data["preApprovalFinalDate"] = $payment->getPreApprovalFinalDate();
        };

        if($payment->getPreApprovalMaxAmountPerPeriod() != null){
            $data["preApprovalMaxAmountPerPeriod"] = $payment->getPreApprovalMaxAmountPerPeriod();
        };

        if($payment->getPreApprovalMaxTotalAmount() != null){
            $data["preApprovalMaxTotalAmount"] = $payment->getPreApprovalMaxTotalAmount();
        };

        if($payment->getReviewURL() != null){
            $data["reviewURL"] = $payment->getReviewURL();
        };
		
        return $data;
    }

    /***
     * @param $str_xml
     * @return PagSeguroParserData Success
     */
    public static function readSuccessXml($str_xml)
    {
        $parser = new PagSeguroXmlParser($str_xml);
        $data = $parser->getResult('preApprovalRequest');
        $paymentParserData = new PagSeguroParserData();
        $paymentParserData->setCode($data['code']);
        $paymentParserData->setRegistrationDate($data['date']);
        return $paymentParserData;
    }
	
	/***
     * @param $str_xml
     * @return PagSeguroParserData Success
     */
    public static function readPreApproval($str_xml)
    {
		$parser = new PagSeguroXmlParser($str_xml);
		$data = $parser->getResult('preApproval');
        $preApproval = new PagSeguroPreApproval();
        
		// <transaction> <lastEventDate>
        if (isset($data["lastEventDate"])) {
            $preApproval->setLastEventDate($data["lastEventDate"]);
        }

        // <transaction> <date>
        if (isset($data["date"])) {
            $preApproval->setDate($data["date"]);
        }

        // <transaction> <code>
        if (isset($data["code"])) {
            $preApproval->setCode($data["code"]);
        }

        // <transaction> <reference>
        if (isset($data["reference"])) {
            $preApproval->setReference($data["reference"]);
        }

        // <transaction> <status>
        if (isset($data["status"]))
            $preApproval->setStatus(new PagSeguroPreApprovalStatus($data["status"]));
			
		if (isset($data["sender"])) {

            // <transaction> <sender>
            $sender = new PagSeguroSender();

            // <transaction> <sender> <name>
            if (isset($data["sender"]["name"])) {
                $sender->setName($data["sender"]["name"]);
            }

            // <transaction> <sender> <email>
            if (isset($data["sender"]["email"])) {
                $sender->setEmail($data["sender"]["email"]);
            }

            if (isset($data["sender"]["phone"])) {

                // <transaction> <sender> <phone>
                $phone = new PagSeguroPhone();

                // <transaction> <sender> <phone> <areaCode>
                if (isset($data["sender"]["phone"]["areaCode"])) {
                    $phone->setAreaCode($data["sender"]["phone"]["areaCode"]);
                }

                // <transaction> <sender> <phone> <number>
                if (isset($data["sender"]["phone"]["number"])) {
                    $phone->setNumber($data["sender"]["phone"]["number"]);
                }

                $sender->setPhone($phone);
            }

            // <transaction><sender><documents>
            if (isset($data["sender"]['documents']) && is_array($data["sender"]['documents'])) {

                $documents = $data["sender"]['documents'];
                if (count($documents) > 0) {
                    foreach ($documents as $document) {
                        $sender->addDocument($document['type'], $document['value']);
                    }
                }
            }

            $preApproval->setSender($sender);
        }
		
		return $preApproval;
    }
	
	/***
     * @param $str_xml
     * @return PagSeguroParserData Success
     */
    public static function readCancelXml($str_xml)
    {
        $parser = new PagSeguroXmlParser($str_xml);
        $data = $parser->getResult('result');
        $paymentParserData = new PagSeguroParserData();
        $paymentParserData->setCode(null); // PreApproval API does not send code on cancel requests
        $paymentParserData->setRegistrationDate($data['date']);
        return $paymentParserData;
    }
}
