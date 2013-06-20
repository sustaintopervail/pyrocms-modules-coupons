<?php


    class PayPal_Invoice_API
    {
        const PAYPAL_REQUEST_DATA_FORMAT = "NV";     //NV/XML/JSON
        const PAYPAL_RESPONSE_DATA_FORMAT = "NV";    //NV/XML/JSON

        const LIVE_INVOICE_URL = "https://svcs.paypal.com/Invoice/";
        const SANDBOX_INVOICE_URL = "https://svcs.sandbox.paypal.com/Invoice/";

        private $mode;
        private $APPLICATION_ID;
        private $end_url;
        private $invoice_reminder_url;
        private $API_Username = "";
        private $API_Password = "";
        private $Signature    = "";
        private $business = "";


        /**
         *
         * @param $configArray
         * @internal param string $mode
         * @internal param string $API_Username
         * @internal param string $API_Password
         * @internal param string $Signature
         * @internal param string $md - live/sandbox
         */
        function __construct($configArray)
        {

            $mode = $configArray['mode'];
            $this->API_Username = $configArray['API_Username'];
            $this->API_Password = $configArray['API_Password'];
            $this->Signature = $configArray['Signature'];
            if($mode == "live")
            {
                /**
                 * Need to Supply the Application ID of Live PayPal Account
                 */
                $this->APPLICATION_ID = "";
		        $this->invoice_reminder_url = "https://www.paypal.com/us/cgi-bin/?cmd=_pay-inv&id=";
            }
            else
            {
                $this->APPLICATION_ID = "";
                $this->invoice_reminder_url = "https://www.sandbox.paypal.com/us/cgi-bin/?cmd=_pay-inv&id=";
            }

        }#__construct()

        /**
         *
         * @return string - It returns the URL
         */
        function getInvoiceURL()
        {
            if($this->mode == "live")
                return self::LIVE_INVOICE_URL;
            else
                return self::SANDBOX_INVOICE_URL;
        }

        /**
         *
         * @param string $end_point_type - End Point Type
         * @return string - It returns the complete End-Point URL [Live/Sandbox]
         */
        function getEndPoint($end_point_type)
        {
            switch($end_point_type)
            {
                case "createInvoice":
                    $end_point_url = $this->getInvoiceURL()."CreateInvoice";
                    break;
                case "sendInvoice":
                    $end_point_url = $this->getInvoiceURL()."SendInvoice";
                    break;
                case "createAndSendInvoice":
                    $end_point_url = $this->getInvoiceURL()."CreateAndSendInvoice";
                    break;
                case "updateInvoice":
                    $end_point_url = $this->getInvoiceURL()."UpdateInvoice";
                    break;
                case "getInvoiceDetails":
                    $end_point_url = $this->getInvoiceURL()."GetInvoiceDetails";
                    break;
                case "cancelInvoice":
                    $end_point_url = $this->getInvoiceURL()."CancelInvoice";
                    break;
                case "searchInvoices":
                    $end_point_url = $this->getInvoiceURL()."SearchInvoices";
                    break;
                case "markAsPaid":
                    $end_point_url = $this->getInvoiceURL()."MarkInvoiceAsPaid";
                    break;
                default:
                    $end_point_url = "";
                    break;
            }
            return $end_point_url;
        }

        /**
         * @return array - Array contains the complete setting of Paypal Invoice API Header
         */
        function getInvoiceAPIHeader()
        {
            //global $API_Username, $API_Password, $Signature;
            $headers[0] = "Content-Type: text/namevalue";               // either text/namevalue or text/xml
            $headers[1] = "X-PAYPAL-SECURITY-USERID: $this->API_Username";    //API user
            $headers[2] = "X-PAYPAL-SECURITY-PASSWORD: $this->API_Password";  //API PWD
            $headers[3] = "X-PAYPAL-SECURITY-SIGNATURE: $this->Signature";    //API Sig
            $headers[4] = "X-PAYPAL-APPLICATION-ID: {$this->APPLICATION_ID}";   //APP ID
            $headers[5] = "X-PAYPAL-REQUEST-DATA-FORMAT: ".self::PAYPAL_REQUEST_DATA_FORMAT."";           //Set Name Value Request Format
            $headers[6] = "X-PAYPAL-RESPONSE-DATA-FORMAT: ".self::PAYPAL_RESPONSE_DATA_FORMAT."";          //Set Name Value Response Format

            return $headers;
        }

        /**
         *
         * @param string $str_req - string to provide for curl request
         * @return string - Returns the curl response
         */
        function curlRequest($str_req)
        {
            // setting the curl parameters.
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $this->end_url);
            curl_setopt($ch, CURLOPT_VERBOSE, 1);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $this->getInvoiceAPIHeader());

            //curl_setopt($ch, CURLOPT_HEADER, 1); // tells curl to include headers in response, use for testing
            // turning off the server and peer verification(TrustManager Concept).
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_POST, 1);

            // setting the NVP $my_api_str as POST FIELD to curl
            curl_setopt($ch, CURLOPT_POSTFIELDS, $str_req);

            // getting response from server
            $httpResponse = curl_exec($ch);
            if(!$httpResponse)
            {
                $response = "{$this->end_url} failed: ".curl_error($ch)."(".curl_errno($ch).")";
                return $response;
            }

            return $httpResponse;
        }#end of curlRequest()

        /**
         * This function parse the curl response in formated way
         * @param string $response - curl response string
         * @return array - Formatted Array Response
         */
        function parseCurlResponse($response)
        {
            $parsed_response = array();
            foreach ($response as $i => $value)
            {
                $tmpAr = explode("=", $value);
                if(sizeof($tmpAr) > 1)
                {
                    $parsed_response[$tmpAr[0]] = urldecode($tmpAr[1]);
                }
            }
            return $parsed_response;
        }#end of parseCurlResponse()

        /**
         *
         * @param array $aryRes - Array contains formated response from Curl request
         * @return string - Error response in form of string
         */
        function formatErrorMessages($aryRes)
        {
            $strError = "";
            $strError .= "<ul>";
            $strError .= "<li><b>".@$aryRes["responseEnvelope.ack"]."</b></li>";

            $cnt = 0;
            while(true)
            {
                if(@$aryRes["error($cnt).message"] == "")
                    break;

                $strError .= "<li>".@$aryRes["error($cnt).message"]." <b>&rarr; ".@$aryRes["error($cnt).parameter(0)"]."</b>";

                if(@$aryRes["error($cnt).parameter(1)"]!="")
                    $strError .= " = <em>".@$aryRes["error($cnt).parameter(1)"]."</em>";

                $strError .= "</li>";
                $cnt++;
            }
            $strError .= "</ul>";

            return $strError;
        }
        
        /**
         * Added on [26-02-2012]
         * @param string $str - string to be trimmed
         * @param int $limit - string limit
         * @return string - trimmed string 
         */
        function adjustStringLength($str, $limit = 30)
        {
            $encode_string = urlencode($str);
            $len = strlen($encode_string);
            if( $len > $limit )
            {
                $diff = $len - $limit;
                $newlimit = $limit + $diff;
                return substr($str, 0, $limit-$diff-1);
            }
            else
                return substr($str, 0, $limit-1);
        }

        function prepareCreateInvoice($aryData, $aryItems)
        {
            $aryCreateInvoice = array();

            if(trim(@$aryData['language'])!= "")
                $aryCreateInvoice['requestEnvelope.errorLanguage']  = $aryData['language'];   //en_US        //TODO
            if(trim(@$aryData['merchantEmail'])!= "")
                $aryCreateInvoice['invoice.merchantEmail']          = $aryData['merchantEmail'];
            if(trim(@$aryData['payerEmail'])!= "")
                $aryCreateInvoice['invoice.payerEmail']             = $aryData['payerEmail'];
            if(trim(@$aryData['currencyCode'])!= "")
                $aryCreateInvoice['invoice.currencyCode']           = $aryData['currencyCode'];   //USD TODO
            if(trim(@$aryData['orderId'])!= "")
                $aryCreateInvoice['invoice.number']                 = $aryData['orderId'];

            if(trim(@$aryData['invoiceID'])!= "")
                $aryCreateInvoice['invoiceID']                 = $aryData['invoiceID'];

            if(trim(@$aryData['paymentTerms'])!= "")
                $aryCreateInvoice['invoice.paymentTerms']           = $aryData['paymentTerms'];   //[DueOnReceipt, DueOnDateSpecified, Net10, Net15, Net30, Net45]
            if(trim(@$aryData['discountPercent'])!= "")
                $aryCreateInvoice['invoice.discountPercent']        = $aryData['discountPercent'];
            if(trim(@$aryData['discountAmount'])!= "")
                $aryCreateInvoice['invoice.discountAmount']         = $aryData['discountAmount'];
            if(trim(@$aryData['invoiceTerms'])!= "")
                $aryCreateInvoice['invoice.terms']                  = $aryData['invoiceTerms'];
            if(trim(@$aryData['invoiceNote'])!= "")
                $aryCreateInvoice['invoice.note']                   = $aryData['invoiceNote'];
            if(trim(@$aryData['merchantMemo'])!= "")
                $aryCreateInvoice['invoice.merchantMemo']           = $aryData['merchantMemo'];
            if(trim(@$aryData['shippingAmount'])!= "")
                $aryCreateInvoice['invoice.shippingAmount']         = $aryData['shippingAmount'];
            if(trim(@$aryData['shippingTaxName'])!= "")
                $aryCreateInvoice['invoice.shippingTaxName']        = $aryData['shippingTaxName'];
            if(trim(@$aryData['shippingTaxRate'])!= "")
                $aryCreateInvoice['invoice.shippingTaxRate']        = $aryData['shippingTaxRate'];
            if(trim(@$aryData['logoURL'])!= "")
                $aryCreateInvoice['invoice.logoUrl']                = $aryData['logoURL'];

            if(trim(@$aryData['merchantFirstName'])!= "")
                $aryCreateInvoice['invoice.merchantInfo.firstName']     = $aryData['merchantFirstName'];
            if(trim(@$aryData['merchantLastName'])!= "")
                $aryCreateInvoice['invoice.merchantInfo.lastName']      = $aryData['merchantLastName'];
            if(trim(@$aryData['merchantBusinessName'])!= "")
                $aryCreateInvoice['invoice.merchantInfo.businessName']  = $aryData['merchantBusinessName'];
            if(trim(@$aryData['merchantPhone'])!= "")
                $aryCreateInvoice['invoice.merchantInfo.phone']         = $aryData['merchantPhone'];
            if(trim(@$aryData['merchantFax'])!= "")
                $aryCreateInvoice['invoice.merchantInfo.fax']           = $aryData['merchantFax'];
            if(trim(@$aryData['merchantWebsite'])!= "")
                $aryCreateInvoice['invoice.merchantInfo.website']       = $aryData['merchantWebsite'];
            if(trim(@$aryData['merchantCustomValue'])!= "")
                $aryCreateInvoice['invoice.merchantInfo.customValue']   = $aryData['merchantCustomValue'];

            if(trim(@$aryData['merchantLine1'])!= "")
                $aryCreateInvoice['invoice.merchantInfo.address.line1']         = $aryData['merchantLine1'];
            if(trim(@$aryData['merchantLine2'])!= "")
                $aryCreateInvoice['invoice.merchantInfo.address.line2']         = $aryData['merchantLine2'];
            if(trim(@$aryData['merchantCity'])!= "")
                $aryCreateInvoice['invoice.merchantInfo.address.city']          = $aryData['merchantCity'];
            if(trim(@$aryData['merchantState'])!= "")
                $aryCreateInvoice['invoice.merchantInfo.address.state']         = $aryData['merchantState'];
            if(trim(@$aryData['merchantPostalCode'])!= "")
                $aryCreateInvoice['invoice.merchantInfo.address.postalCode']    = $aryData['merchantPostalCode'];
            if(trim(@$aryData['merchantCountryCode'])!= "")
                $aryCreateInvoice['invoice.merchantInfo.address.countryCode']   = $aryData['merchantCountryCode'];

            if(trim(@$aryData['billingFirstName'])!= "")
                $aryCreateInvoice['invoice.billingInfo.firstName']      = $aryData['billingFirstName'];
            if(trim(@$aryData['billingLastName'])!= "")
                $aryCreateInvoice['invoice.billingInfo.lastName']       = $aryData['billingLastName'];
            if(trim(@$aryData['billingBusinessName'])!= "")
                $aryCreateInvoice['invoice.billingInfo.businessName']   = $aryData['billingBusinessName'];
            if(trim(@$aryData['billingPhone'])!= "")
                $aryCreateInvoice['invoice.billingInfo.phone']          = $aryData['billingPhone'];
            if(trim(@$aryData['billingFax'])!= "")
                $aryCreateInvoice['invoice.billingInfo.fax']            = $aryData['billingFax'];
            if(trim(@$aryData['billingWebsite'])!= "")
                $aryCreateInvoice['invoice.billingInfo.website']        = $aryData['billingWebsite'];
            if(trim(@$aryData['billingCustomValue'])!= "")
                $aryCreateInvoice['invoice.billingInfo.customValue']    = $aryData['billingCustomValue'];

            if(trim(@$aryData['billingLine1'])!= "")
                $aryCreateInvoice['invoice.billingInfo.address.line1']          = $aryData['billingLine1'];
            if(trim(@$aryData['billingLine2'])!= "")
                $aryCreateInvoice['invoice.billingInfo.address.line2']          = $aryData['billingLine2'];
            if(trim(@$aryData['billingCity'])!= "")
                $aryCreateInvoice['invoice.billingInfo.address.city']           = $aryData['billingCity'];
            if(trim(@$aryData['billingState'])!= "")
                $aryCreateInvoice['invoice.billingInfo.address.state']          = $aryData['billingState'];
            if(trim(@$aryData['billingPostalCode'])!= "")
                $aryCreateInvoice['invoice.billingInfo.address.postalCode']     = $aryData['billingPostalCode'];
            if(trim(@$aryData['billingCountryCode'])!= "")
                $aryCreateInvoice['invoice.billingInfo.address.countryCode']    = $aryData['billingCountryCode'];

            if(trim(@$aryData['shippingFirstName'])!= "")
                $aryCreateInvoice['invoice.shippingInfo.firstName']     = $aryData['shippingFirstName'];
            if(trim(@$aryData['shippingLastName'])!= "")
                $aryCreateInvoice['invoice.shippingInfo.lastName']      = $aryData['shippingLastName'];
            if(trim(@$aryData['shippingBusinessName'])!= "")
                $aryCreateInvoice['invoice.shippingInfo.businessName']  = $aryData['shippingBusinessName'];
            if(trim(@$aryData['shippingPhone'])!= "")
                $aryCreateInvoice['invoice.shippingInfo.phone']         = $aryData['shippingPhone'];
            if(trim(@$aryData['shippingFax'])!= "")
                $aryCreateInvoice['invoice.shippingInfo.fax']           = $aryData['shippingFax'];
            if(trim(@$aryData['shippingWebsite'])!= "")
                $aryCreateInvoice['invoice.shippingInfo.website']       = $aryData['shippingWebsite'];
            if(trim(@$aryData['shippingCustomValue'])!= "")
                $aryCreateInvoice['invoice.shippingInfo.customValue']   = $aryData['shippingCustomValue'];


            if(trim(@$aryData['shippingLine1'])!= "")
                $aryCreateInvoice['invoice.shippingInfo.address.line1']         = $aryData['shippingLine1'];
            if(trim(@$aryData['shippingLine2'])!= "")
                $aryCreateInvoice['invoice.shippingInfo.address.line2']         = $aryData['shippingLine2'];
            if(trim(@$aryData['shippingCity'])!= "")
                $aryCreateInvoice['invoice.shippingInfo.address.city']          = $aryData['shippingCity'];
            if(trim(@$aryData['shippingState'])!= "")
                $aryCreateInvoice['invoice.shippingInfo.address.state']         = $aryData['shippingState'];
            if(trim(@$aryData['shippingPostalCode'])!= "")
                $aryCreateInvoice['invoice.shippingInfo.address.postalCode']    = $aryData['shippingPostalCode'];
            if(trim(@$aryData['shippingCountryCode'])!= "")
                $aryCreateInvoice['invoice.shippingInfo.address.countryCode']   = $aryData['shippingCountryCode'];        //US TODO


            $nLoop = count($aryItems);
            for($cnt=0;$cnt<$nLoop;$cnt++)
            {
                if(trim(@$aryItems[$cnt]['name'])!= "")
                    $aryCreateInvoice["invoice.itemList.item($cnt).name"]       = $this->adjustStringLength($aryItems[$cnt]['item_name']);
                if(trim(@$aryItems[$cnt]['item_description'])!= "")
                    $aryCreateInvoice["invoice.itemList.item($cnt).description"]= $this->adjustStringLength($aryItems[$cnt]['item_description'],1000);
                #if(trim(@$aryData['$aryItems[$cnt]['date']'])!= "")
                    #$aryCreateInvoice["invoice.itemList.item($cnt).date"]       = $aryItems[$cnt]['date'];
                if(trim(@$aryItems[$cnt]['quantity'])!= "")
                    $aryCreateInvoice["invoice.itemList.item($cnt).quantity"]   = $this->adjustStringLength($aryItems[$cnt]['item_quantity']);
                if(trim(@$aryItems[$cnt]['unitprice'])!= "")
                    $aryCreateInvoice["invoice.itemList.item($cnt).unitPrice"]  = $this->adjustStringLength($aryItems[$cnt]['item_unitprice']);
                if(trim(@$aryItems[$cnt]['taxName'])!= "")
                    $aryCreateInvoice["invoice.itemList.item($cnt).taxName"]    = $aryItems[$cnt]['taxName'];
                if(trim(@$aryItems[$cnt]['taxRate'])!= "")
                    $aryCreateInvoice["invoice.itemList.item($cnt).taxRate"]    = $aryItems[$cnt]['taxRate'];
            }

            if(trim(@$aryData['invoiceDate'])!= "")
                $aryCreateInvoice['invoice.invoiceDate']            = urldecode($aryData['invoiceDate']);     //2011-12-31T05:38:48Z
            if(trim(@$aryData['dueDate'])!= "")
                $aryCreateInvoice['invoice.dueDate']                = urldecode($aryData['dueDate']);

//            $reqstr .= "&invoice.invoiceDate={$aryData['invoiceDate']}";
//            $reqstr .= "&invoice.dueDate={$aryData['dueDate']}";

            $request_string = http_build_query( $aryCreateInvoice );


            return $request_string;
        }#end of prepareCreateInvoice()

        function doCreateInvoice($aryData, $aryItems)
        {
            $this->end_url = $this->getEndPoint("createInvoice");
            $strCreateInvoice = $this->prepareCreateInvoice($aryData, $aryItems);
            $response = $this->curlRequest($strCreateInvoice);
            $aryRresponse = explode("&", $response);
            $response = $this->parseCurlResponse($aryRresponse);
            return $response;
        }#doCreateInvoice()

        function prepareSendInvoice($invoiceID)
        {
            $arySendInvoice = array();

            $arySendInvoice['requestEnvelope.errorLanguage'] = "en_US";
            $arySendInvoice['invoiceID'] = $invoiceID;

            $request_string = http_build_query( $arySendInvoice );
            return $request_string;
        }

        function doSendInvoice($invoiceID)
        {
            $this->end_url = $this->getEndPoint("sendInvoice");
            $strSendInvoice = $this->prepareSendInvoice($invoiceID);
            $response = $this->curlRequest($strSendInvoice);
            $aryRresponse = explode("&", $response);
            $response = $this->parseCurlResponse($aryRresponse);
            return $response;
        }

        function doCreateAndSendInvoice($aryData, $aryItems)
        {
            $this->end_url = $this->getEndPoint("createAndSendInvoice");
            $strCreateAndSendInvoice = $this->prepareCreateInvoice($aryData, $aryItems);
            $response = $this->curlRequest($strCreateAndSendInvoice);

            $aryRresponse = explode("&", $response);
            $response = $this->parseCurlResponse($aryRresponse);
            return $response;
        }

        function doUpdateInvoice($aryData, $aryItems)
        {
            $this->end_url = $this->getEndPoint("updateInvoice");
            $strUpdateInvoice = $this->prepareCreateInvoice($aryData, $aryItems);
            $response = $this->curlRequest($strUpdateInvoice);
            $aryRresponse = explode("&", $response);
            $response = $this->parseCurlResponse($aryRresponse);
            return $response;
        }

        function prepareGetInvoiceDetail($invoiceID)
        {
            $arySendInvoice = array();

            $arySendInvoice['requestEnvelope.errorLanguage'] = "en_US";
            $arySendInvoice['invoiceID'] = $invoiceID;

            $request_string = http_build_query( $arySendInvoice );
            return $request_string;
        }

        function doGetInvoiceDetail($invoiceID)
        {
            $this->end_url = $this->getEndPoint("getInvoiceDetails");
            $strGetInvoiceDetail = $this->prepareGetInvoiceDetail($invoiceID);
            $response = $this->curlRequest($strGetInvoiceDetail);
            $aryRresponse = explode("&", $response);
            $response = $this->parseCurlResponse($aryRresponse);
            return $response;
        }

        function prepareCancelInvoice($aryData)
        {
            $aryCancelInvoice = array();

            $aryCancelInvoice['requestEnvelope.errorLanguage'] = "en_US";
            $aryCancelInvoice['invoiceID'] = $aryData['response_invoice_id'];
            $aryCancelInvoice['subject']   = $aryData['email_subject'];
            $aryCancelInvoice['noteForPayer'] = $aryData['email_body'];
            $aryCancelInvoice['sendCopyToMerchant'] = "true";

            $request_string = http_build_query( $aryCancelInvoice );
            return $request_string;

        }

        function doCancelInvoice($aryData)
        {
            $this->end_url = $this->getEndPoint("cancelInvoice");
            $strCancelInvoice = $this->prepareCancelInvoice($aryData);
            $response = $this->curlRequest($strCancelInvoice);
            $aryRresponse = explode("&", $response);
            $response = $this->parseCurlResponse($aryRresponse);
            return $response;
        }

        function prepareSearchInvoice($aryData)
        {
            $arySearchInvoice = array();

            if(trim(@$aryData['language'])!= "")
                $arySearchInvoice['requestEnvelope.errorLanguage'] = $aryData['language'];  //en_US
            if(trim(@$aryData['merchantEmail'])!= "")
                $arySearchInvoice['merchantEmail'] = $aryData['merchantEmail'];
            if(trim(@$aryData['pane_no'])!= "")
                $arySearchInvoice['page'] = $aryData['pane_no'];
            if(trim(@$aryData['page_records'])!= "")
                $arySearchInvoice['pageSize'] = $aryData['page_records'];

            if(trim(@$aryData['email'])!= "")
                $arySearchInvoice['parameters.email'] = $aryData['email'];
            if(trim(@$aryData['recipient_name'])!= "")
                $arySearchInvoice['parameters.recipientName'] = $aryData['recipient_name'];
            if(trim(@$aryData['business_name'])!= "")
                $arySearchInvoice['parameters.businessName'] = $aryData['business_name'];
            if(trim(@$aryData['invoice_number'])!= "")
                $arySearchInvoice['parameters.invoiceNumber'] = $aryData['invoice_number'];

            if(trim(@$aryData['status'])!= "")
                $arySearchInvoice['parameters.status'] = $aryData['status'];///

            if(trim(@$aryData['lower_amount'])!= "")
                $arySearchInvoice['parameters.lowerAmount'] = $aryData['lower_amount'];
            if(trim(@$aryData['upper_amount'])!= "")
                $arySearchInvoice['parameters.uppderAmount'] = $aryData['upper_amount'];
            if(trim(@$aryData['currency_code'])!= "")
                $arySearchInvoice['parameters.currencyCode'] = $aryData['currency_code'];
            if(trim(@$aryData['memo'])!= "")
                $arySearchInvoice['parameters.memo'] = $aryData['memo'];
            if(trim(@$aryData['origin'])!= "")
                $arySearchInvoice['parameters.origin'] = $aryData['origin'];

            if(trim(@$aryData['invoice_start_date'])!= "")
                $arySearchInvoice['parameters.invoiceDate.startDate'] = urldecode($aryData['invoice_start_date']);
            if(trim(@$aryData['invoice_end_date'])!= "")
                $arySearchInvoice['parameters.invoiceDate.endDate'] = urldecode($aryData['invoice_end_date']);

            if(trim(@$aryData['due_start_date'])!= "")
                $arySearchInvoice['parameters.dueDate.startDate'] = urldecode($aryData['due_start_date']);
            if(trim(@$aryData['due_end_date'])!= "")
                $arySearchInvoice['parameters.dueDate.endDate'] = urldecode($aryData['due_end_date']);

            if(trim(@$aryData['payment_start_date'])!= "")
                $arySearchInvoice['parameters.paymentDate.startDate'] = urldecode($aryData['payment_start_date']);
            if(trim(@$aryData['payment_end_date'])!= "")
                $arySearchInvoice['parameters.paymentDate.endDate'] = urldecode($aryData['payment_end_date']);

            if(trim(@$aryData['creation_start_date'])!= "")
                $arySearchInvoice['parameters.creationDate.startDate'] = urldecode($aryData['creation_start_date']);
            if(trim(@$aryData['creation_end_date'])!= "")
                $arySearchInvoice['parameters.creationDate.endDate'] = urldecode($aryData['creation_end_date']);

            $request_string = http_build_query( $arySearchInvoice );


            return $request_string;
        }

        function doSearchInvoice($aryData)
        {
            $this->end_url = $this->getEndPoint("searchInvoices");
            $strSearchDetail = $this->prepareSearchInvoice($aryData);
            $response = $this->curlRequest($strSearchDetail);
            $aryRresponse = explode("&", $response);
            $response = $this->parseCurlResponse($aryRresponse);
            return $response;
        }

        function prepareMarkAsPaid($aryData)
        {
            $aryCancelInvoice = array();

            $aryCancelInvoice['requestEnvelope.errorLanguage'] = "en_US";
            $aryCancelInvoice['invoiceID'] = $aryData['response_invoice_id'];
            #$aryCancelInvoice['payment']   = $aryData['payment'];
            $aryCancelInvoice['payment.date']   = date(DATE_ATOM, strtotime($aryData['payment_date']));
            $aryCancelInvoice['payment.note'] = $aryData['payment_note'];
            $aryCancelInvoice['payment.method'] = $aryData['method'];

            $request_string = http_build_query( $aryCancelInvoice );
            return $request_string;
        }

        function doMarkAsPaid($aryData)
        {
            $this->end_url = $this->getEndPoint("markAsPaid");
            $strMarkAsPaid = $this->prepareMarkAsPaid($aryData);
            $response = $this->curlRequest($strMarkAsPaid);
            $aryRresponse = explode("&", $response);
            $response = $this->parseCurlResponse($aryRresponse);
            return $response;
        }
        
        function invoiceReminderHTML($aryData)
        {
            if(count($aryData)<9)
                return false;

            $content = '
                            <div id=":oi" class="ii gt adP adO">
                              <div id=":oj">
                                <div>
                                  <div>
                                    <div class="adM"></div>
                                    <table border="0" cellpadding="0" cellspacing="0" style="font:11px Verdana,Arial,Helvetica,sans-serif;color:#333" width="580">
                                      <tbody>
                                        <tr valign="top">
                                          <td colspan="3"><table border="0" cellpadding="0" cellspacing="0" width="100%" style="margin-bottom:10px">
                                              <tbody>
                                                <tr valign="bottom">
                                                  <td align="left" height="75" width="253" style="padding-left:13px"><img src="https://www.internationalcheckout.com/IC2007logo.jpg" border="0" alt="Payments by PayPal"> </td>
                                                  <td align="right"><div style="padding:0 4px 4px 0;margin:0 4px 0 0;font:italic 12px arial;color:#757575;line-height:15px">Powered by</div></td>
                                                  <td align="left" width="89" style="padding-right:13px"><img align="center" height="20" src="http://images.paypal.com/en_US/i/logo/logo_paypal_76w_20h.gif" border="0" alt="PayPal"> </td>
                                                </tr>
                                                <tr>
                                                  <td></td>
                                                </tr>
                                              </tbody>
                                            </table></td>
                                        </tr>
                                        <tr>
                                          <td colspan="3"><img height="13" src="http://images.paypal.com/en_US/i/scr/scr_emailTopCorners_580wx13h.gif" border="0" style="vertical-align:bottom" alt=""></td>
                                        </tr>
                                        <tr>
                                          <td width="12" style="background:url(\'http://i/scr/scr_emailLeftBorder_13wx1h.gif\') left repeat-y;border-left:1px solid #ddd"><img src="http://images.paypal.com/en_US/i/scr/pixel.gif" border="0" alt=""></td>
                                          <td style="width:530px;word-wrap:break-word;padding:12px;margin:0" width="530">
                                            <p>'.$aryData['reminder_msg'].'</p>
                                            <br>
                                            <br>
                                            <table border="0" cellpadding="0" cellspacing="0" style="margin:0 0 10px 0;font:11px Verdana,Arial,Helvetica,sans-serif">
                                              <tbody>
                                                <tr>
                                                  <td bgcolor="#ffa822" style="margin:0;padding:1px 10px;border:1px solid #bfbfbf;border-right-color:#908d8d;border-bottom-color:#908d8d"><a style="text-decoration:none;color:#000000" href="'.$this->invoice_reminder_url.$aryData['invoice_id'].'" target="_blank">Pay Invoice</a></td>
                                                </tr>
                                              </tbody>
                                            </table>
                                            <br>
                                            <table style="font:12px Verdana,Arial,Helvetica,sans-serif">
                                              <tbody>
                                                <tr>
                                                  <td>Pay with </td>
                                                  <td><div><img border="0" src="http://images.paypal.com/en_US/i/logo/logo_ccVisa.gif" alt="Visa "><u></u><img border="0" src="http://images.paypal.com/en_US/i/logo/logo_ccMC.gif" alt="Mastercard "><u></u><img border="0" src="http://images.paypal.com/en_US/i/logo/logo_ccAmex.gif" alt="American Express "><u></u> <img border="0" src="http://images.paypal.com/en_US/i/logo/logo_ccDiscover.gif" alt="Discover "><u></u><img border="0" src="http://images.paypal.com/en_US/i/logo/PayPal_mark_37x23.gif" alt="PayPal "><u></u></div></td>
                                                </tr>
                                              </tbody>
                                            </table>
                                            <p>To view the details of this invoice or send '.$aryData['sender_name'].' your payment, you can also copy and paste this link into your web browser: <a href="'.$this->invoice_reminder_url.$aryData['invoice_id'].'" target="_blank">https://www.paypal.com/us/cgi-<wbr>bin/?cmd=_pay-inv&amp;id=INV2-<wbr>'.$aryData['invoice_id'].'</a></p>
                                            <h4><span style="font:bold 11px Verdana,Arial,Helvetica,sans-serif;color:#333">Summary of this invoice</span></h4>
                                            <table cellpadding="0" cellspacing="0" style="border-top:1px solid #eeeeee;border-left:1px solid #eeeeee;border-bottom:1px solid #eeeeee;width:100%;font:11px Verdana,Helvetica,sans-serif;color:#333" summary="Summary of your invoice">
                                              <tbody>
                                                <tr>
                                                  <td align="right" style="background-color:#eeeeee;padding:5px 10px 0 0" width="30%" valign="top" rowspan="2">Sent to</td>
                                                  <td style="padding:5px 0 0 10px">'.$aryData['name'].'</td>
                                                </tr>
                                                <tr>
                                                  <td style="padding:5px 0 0 10px"><a href="mailto:'.$aryData['email'].'" target="_blank">'.$aryData['email'].'</a></td>
                                                </tr>
                                                <tr>
                                                  <td align="right" style="background-color:#eeeeee;padding:5px 10px 0 0" width="30%" valign="top" rowspan="2">Sent from</td>
                                                  <td style="padding:5px 0 0 10px">'.$aryData['sender_name'].'</td>
                                                </tr>
                                                <tr>
                                                  <td style="padding:5px 0 0 10px"><a href="mailto:'.$aryData['sender_email'].'" target="_blank">'.$aryData['sender_email'].'</a></td>
                                                </tr>
                                                <tr>
                                                  <td align="right" style="background-color:#eeeeee;padding:5px 10px 0 0" valign="top" width="30%">Invoice number</td>
                                                  <td style="padding:5px 0 0 10px">'.$aryData['order_id'].'</td>
                                                </tr>
                                                <tr>
                                                  <td align="right" style="background-color:#eeeeee;padding:5px 10px 0 0" valign="top" width="30%">Date payment is due</td>
                                                  <td style="padding:5px 0 0 10px">'.$aryData['due_date'].'</td>
                                                </tr>
                                                <tr>
                                                  <td align="right" style="background-color:#eeeeee;padding:5px 10px 5px 0" valign="top" width="30%">Amount</td>
                                                  <td style="padding:5px 0 5px 10px">$'.$aryData['amount'].' USD</td>
                                                </tr>
                                              </tbody>
                                            </table>
                                            <p></p></td>
                                          <td width="12" style="background:url(\'http://i/scr/scr_emailRightBorder_13wx1h.gif\') left repeat-y;border-right:1px solid #ddd"><img src="http://images.paypal.com/en_US/i/scr/pixel.gif" border="0" alt=""></td>
                                        </tr>
                                        <tr>
                                          <td colspan="3"><img height="13" src="http://images.paypal.com/en_US/i/scr/scr_emailBottomCorners_580wx13h.gif" border="0" alt=""></td>
                                        </tr>
                                      </tbody>
                                    </table>
                                    <table border="0" cellpadding="0" cellspacing="0" style="padding-top:20px;font:10px Verdana,Arial,Helvetica,sans-serif;color:#333" width="580">
                                      <tbody>
                                        <tr>
                                          <td><div style="margin:5px 0;padding:0"><a href="https://www.paypal.com/us/cgi-bin/helpweb?cmd=_help" target="_blank">Help Center</a><span style="color:#ccc"> | </span><a href="https://www.paypal.com/us/security" target="_blank">Security Center</a></div></td>
                                        </tr>
                                      </tbody>
                                    </table>
                                  </div>
                                </div>
                              </div>
                              <div class="yj6qo"></div>
                            </div>
                ';
            return $content;
        }

    }#end of class


?>