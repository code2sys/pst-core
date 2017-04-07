<?php

/**
 * Description of MY_Composer
 *
 * @author Rana
 */
//use appropriate namespaces

include("/var/www/demo.powersporttechnologies.com/vendor/autoload.php");

use \DTS\eBaySDK\Constants;
use \DTS\eBaySDK\Services;
use \DTS\eBaySDK\Types;
use \DTS\eBaySDK\Enums;

class ebaysdk {

    function __construct() {
        
    }

    public function pushProductToEbay() {

//ebay site selection
        $siteId = Constants\SiteIds::US;

//get ebay trading service object
        $service = new Services\TradingService(array(
            'apiVersion' => '983',
            //select sandbox or production environment
            'sandbox' => true,
            'siteId' => $siteId
        ));
        $usr_token = 'AgAAAA**AQAAAA**aAAAAA**k7CZWA**nY+sHZ2PrBmdj6wVnY+sEZ2PrA2dj6wFk4GiAJeCpwSdj6x9nY+seQ**hPYDAA**AAMAAA**58pYXe/Al5AAklWeiyoZzZ/GdVZz7I4Ze2rSB9OejaKTHGF7tF0+fkDwHvT9r+pGQ6bwDo6qN+lENnn7Z/baOHZIMe+xv0BNCYNBFw1cxGgMbOOAYP4jd6oyxcvUpKVJrUEFJiMqX533V/npXTQ03VRuQXh700oouz30DqV08gMt4QksaJkOcNz1paOmvVfdtes1ZDKnORvpUldYBoDoeJuZZxqG9u14HVnMymwOZByicT+4f3K9Ek55QDdDrLGRBx0Z3WTmWxr0OwCKPrDsBM1SKFUeOVjksG7VtI1BX72PcUeQrjPPBkpVQVDnEwHbqfyYqqfOz4klzjwq+Y/wRHSC3LPNf52G2brXQ9Xs/DXE0z1v466Hk4H8nbpltSdtXPiSo2FUZbp2OkenoyMRXSoYrHjLPgU7fnS6hGfhXXZZ+H+d+RihkrTniQmtHeZn9OZBgErRsE6wMHf307jXhiv+tMiHMcqytlR8/iV/Y6OCuN6m3TuOuaiuC/sSp59SXJWj1Yahk1cRKax2crO3tecLZJbYgSoAEjJtrdpST9KWuMg5jYbvkGxAvQ+3ckoT/bjD1+7+1GuS8eka59v4ee0d3hZJ+5jVDau5nO3u1QjBUiL1IAlyVczXsZxjpYFlgw/YyzdrYMuyzKC9FYZB33odB0ER4kIXsCQO/BvO2uzWCDkL6Tbj/iwyCEb4Rw690k5lUGeL8YJkWFtzViid1Vc3mU0aoZa/frtQu3r4vGQfE/LaNVCpFAurSxshAwqq';
//get ebay request object
        $request = new Types\AddFixedPriceItemRequestType();
//add credentials required for this very request. not every request requires it.
        $request->RequesterCredentials = new Types\CustomSecurityHeaderType();
        $request->RequesterCredentials->eBayAuthToken = $usr_token;
        /**
         * Begin creating the fixed price item.
         */
        $item = new Types\ItemType();
        /**
         * We want a multiple quantity fixed price listing.
         */
        $item->ListingType = Enums\ListingTypeCodeType::C_FIXED_PRICE_ITEM;
        $item->Quantity = 99;
        /**
         * Let the listing be automatically renewed every 30 days until cancelled.
         */
        $item->ListingDuration = Enums\ListingDurationCodeType::C_GTC;
        /**
         * The cost of the item is $19.99.
         * Note that we don't have to specify a currency as eBay will use the site id
         * that we provided earlier to determine that it will be United States Dollars (USD).
         */
        $item->StartPrice = new Types\AmountType(['value' => 500.55]);
        /**
         * Allow buyers to submit a best offer.
         */
//$item->BestOfferDetails = new Types\BestOfferDetailsType();
//$item->BestOfferDetails->BestOfferEnabled = true;
        /**
         * Automatically accept best offers of $17.99 and decline offers lower than $15.99.
         */
        $item->ListingDetails = new Types\ListingDetailsType();
//$item->ListingDetails->BestOfferAutoAcceptPrice = new Types\AmountType(['value' => 13.99]);
//$item->ListingDetails->MinimumBestOfferPrice = new Types\AmountType(['value' => 12.99]);
        /**
         * Provide a title and description and other information such as the item's location.
         * Note that any HTML in the title or description must be converted to HTML entities.
         */
        $item->Title = 'GIRISH';
        $item->Description = '<div class="desDetailTxt" id="tab_stuff">
                        ARAI VX-PRO 4 OFF ROAD MOTORCYCEL HELMET<br>
<br>
•FREE<br>
• Larger relief ports to allow more air to escape from under the larger peak to minimize lift/pulling at speed<br>
• New chin vent incorporates expanded SS mesh moved from inside to outside of shell, provides room to move the EPS chin liner forward for more room in the face area<br>
• Chin vent has mesh cap, round and smooth to resist snagging and digging in, easily removable with single screw and will break away if necessary in an impact<br>
• Inner Mud Gate that can be easily removed for more airflow<br>
• New Liner components features FCS cheek pads that have a larger surface area, wrapping under jaw for more comfort like our street helmets<br>
• New removable, replaceable, washable neck roll pad<br>
• 5mm peel away cheek pads and temple pads<br>
• New red and black material accent colors for a tri-color interior<br>
• New Emergency Cheek Pad Removal strap label design for easier use – label acts as pull tab for strap<br>
• New ventilation consists of additional top vent hole, increasing from five to six upper vents<br>
• New upper and lower rear exhaust vent cowl design to increase airflow and vacuum exhaust<br>
• Vents designed to assist holding goggle strap in center position and not slide off, up or down<br>
• Upper rear exhaust vent set design to accommodate all five shell sizes<br>
• New vent design still fragile under crash impact, but seems more durable against smaller contact<br>
• Same three basic chin intakes, two under peak intakes, six upper exhaust and two lower exhaust<br>
• New eye-port trim and material has molded rubber trim across top of eye port, with standoff ribs, that resist goggles wearing the helmet liner as well as providing small gap for airflow<br>
• Molded goggle strap guides at either side of eye port trim        </div>';
        $item->SKU = 'ABC-001';
        $item->Country = 'US';
        $item->Location = 'Beverly Hills';
        $item->PostalCode = '90210';
        /**
         * This is a required field.
         */
        $item->Currency = 'USD';
        /**
         * Display a picture with the item.
         */
        $item->PictureDetails = new Types\PictureDetailsType();
        $item->PictureDetails->GalleryType = Enums\GalleryTypeCodeType::C_GALLERY;
        $item->PictureDetails->PictureURL = [
            'http://demo.powersporttechnologies.com/productimages/62959-ARAI-VX-PRO-4-OFF-ROAD-MOTORCYCLE-HELMET.png',
            'http://demo.powersporttechnologies.com/productimages/62960-ARAI-VX-PRO-4-OFF-ROAD-MOTORCYCLE-HELMET.png',
            'http://demo.powersporttechnologies.com/productimages/62961-ARAI-VX-PRO-4-OFF-ROAD-MOTORCYCLE-HELMET.png',
            'http://demo.powersporttechnologies.com/productimages/62962-ARAI-VX-PRO-4-OFF-ROAD-MOTORCYCLE-HELMET.png',
        ];
        /**
         * List item in the Books > Audiobooks (29792) category.
         */
        $item->PrimaryCategory = new Types\CategoryType();
        $item->PrimaryCategory->CategoryID = '29792';
        /**
         * Tell buyers what condition the item is in.
         * For the category that we are listing in the value of 1000 is for Brand New.
         */
        $item->ConditionID = 1000;
        /**
         * Buyers can use one of two payment methods when purchasing the item.
         * Visa / Master Card
         * PayPal
         * The item will be dispatched within 1 business days once payment has cleared.
         * Note that you have to provide the PayPal account that the seller will use.
         * This is because a seller may have more than one PayPal account.
         */
        $item->PaymentMethods = [
            'VisaMC',
            'PayPal'
        ];
        $item->PayPalEmailAddress = 'example@example.com';
        $item->DispatchTimeMax = 1;
        /**
         * Setting up the shipping details.
         * We will use a Flat shipping rate for both domestic and international.
         */
        $item->ShippingDetails = new Types\ShippingDetailsType();
        $item->ShippingDetails->ShippingType = Enums\ShippingTypeCodeType::C_FLAT;
        /**
         * Create our first domestic shipping option.
         * Offer the Economy Shipping (1-10 business days) service at $2.00 for the first item.
         * Additional items will be shipped at $1.00.
         */
        $shippingService = new Types\ShippingServiceOptionsType();
        $shippingService->ShippingServicePriority = 1;
        $shippingService->ShippingService = 'Other';
        $shippingService->ShippingServiceCost = new Types\AmountType(['value' => 2.00]);
        $shippingService->ShippingServiceAdditionalCost = new Types\AmountType(['value' => 1.00]);
        $item->ShippingDetails->ShippingServiceOptions[] = $shippingService;
        /**
         * Create our second domestic shipping option.
         * Offer the USPS Parcel Select (2-9 business days) at $3.00 for the first item.
         * Additional items will be shipped at $2.00.
         */
        $shippingService = new Types\ShippingServiceOptionsType();
        $shippingService->ShippingServicePriority = 2;
        $shippingService->ShippingService = 'USPSParcel';
        $shippingService->ShippingServiceCost = new Types\AmountType(['value' => 3.00]);
        $shippingService->ShippingServiceAdditionalCost = new Types\AmountType(['value' => 2.00]);
        $item->ShippingDetails->ShippingServiceOptions[] = $shippingService;
        /**
         * Create our first international shipping option.
         * Offer the USPS First Class Mail International service at $4.00 for the first item.
         * Additional items will be shipped at $3.00.
         * The item can be shipped Worldwide with this service.
         */
        $shippingService = new Types\InternationalShippingServiceOptionsType();
        $shippingService->ShippingServicePriority = 1;
        $shippingService->ShippingService = 'USPSFirstClassMailInternational';
        $shippingService->ShippingServiceCost = new Types\AmountType(['value' => 4.00]);
        $shippingService->ShippingServiceAdditionalCost = new Types\AmountType(['value' => 3.00]);
        $shippingService->ShipToLocation = ['WorldWide'];
        $item->ShippingDetails->InternationalShippingServiceOption[] = $shippingService;
        /**
         * Create our second international shipping option.
         * Offer the USPS Priority Mail International (6-10 business days) service at $5.00 for the first item.
         * Additional items will be shipped at $4.00.
         * The item will only be shipped to the following locations with this service.
         * N. and S. America
         * Canada
         * Australia
         * Europe
         * Japan
         */
        $shippingService = new Types\InternationalShippingServiceOptionsType();
        $shippingService->ShippingServicePriority = 2;
        $shippingService->ShippingService = 'USPSPriorityMailInternational';
        $shippingService->ShippingServiceCost = new Types\AmountType(['value' => 5.00]);
        $shippingService->ShippingServiceAdditionalCost = new Types\AmountType(['value' => 4.00]);
        $shippingService->ShipToLocation = [
            'Americas',
            'CA',
            'AU',
            'Europe',
            'JP'
        ];
        $item->ShippingDetails->InternationalShippingServiceOption[] = $shippingService;
        /**
         * The return policy.
         * Returns are accepted.
         * A refund will be given as money back.
         * The buyer will have 14 days in which to contact the seller after receiving the item.
         * The buyer will pay the return shipping cost.
         */
        $item->ReturnPolicy = new Types\ReturnPolicyType();
        $item->ReturnPolicy->ReturnsAcceptedOption = 'ReturnsAccepted';
        $item->ReturnPolicy->RefundOption = 'MoneyBack';
        $item->ReturnPolicy->ReturnsWithinOption = 'Days_14';
        $item->ReturnPolicy->ShippingCostPaidByOption = 'Buyer';
        /**
         * Finish the request object.
         */
        $request->Item = $item;
        /**
         * Send the request.
         */
        $response = $service->addFixedPriceItem($request);
        /**
         * Output the result of calling the service operation.
         */
        if (isset($response->Errors)) {
            foreach ($response->Errors as $error) {
                printf(
                        "%s: %s\n%s\n\n", $error->SeverityCode === Enums\SeverityCodeType::C_ERROR ? 'Error' : 'Warning', $error->ShortMessage, $error->LongMessage
                );
            }
        }
        if ($response->Ack !== 'Failure') {
            printf(
                    "The item was listed to the eBay Sandbox with the Item number %s\n", $response->ItemID
            );
        }
    }

}
