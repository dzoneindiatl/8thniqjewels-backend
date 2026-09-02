<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Http;
use Exception;
use Illuminate\Http\Client\RequestException;

class SequelShippingHelper
{
    public $baseUrl;
    protected $token;

    public function __construct() {
        $this->baseUrl = env('SEQUEL247_ENDPOINT');
        $this->token = env('SEQUEL247_TOKEN');
    }

    public function createSequelShipment($shippingDetails) {
        // $uniqueShortCode = bin2hex(random_bytes(10)); 
        // $uniqueShortCode = random_int(10); 
        $pool = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $uniqueShortCode = '';
        for ($i = 0; $i < 10; $i++) {
            $uniqueShortCode .= $pool[random_int(0, strlen($pool) - 1)];
        }
        
        $requestBody = [
            "token" => env('SEQUEL247_TOKEN'),
            "address_type" => "Business",
            "address_short_code" => $uniqueShortCode,
            "nature_of_address" => "Retail Store / Outlet",
            "gst_in" => "08AYIPS3275R1ZE",
            "business_entity_name" => "JEWELLERS",
            "address_line1" => $shippingDetails['shipping_address'],
            "address_line2" => $shippingDetails['shipping_city'],
            "pinCode" => $shippingDetails['shipping_pincode'],
            "auth_receiver_name" => $shippingDetails['shipping_customer_name'],
            "auth_receiver_phone" => $shippingDetails['shipping_phone'],
            "auth_receiver_email" => $shippingDetails['shipping_email']
        ];

        // $requestBody= '{
        //     "token": "e67faa16021a97d13c28e129a9d3e53d",
        //     "address_type": "Business",
        //     "address_short_code": "TESTADDRESS03",
        //     "nature_of_address": "Retail Store / Outlet",
        //     "gst_in": "08AYIPS3275R1ZE",
        //     "business_entity_name": "BR JEWELLERS BHILWARA",
        //     "address_line1": "01,",
        //     "address_line2": "BADE MANDIR KE Piche, BAHALA Road,Junawas",
        //     "pinCode": "311001",
        //     "auth_receiver_name": "TEST",
        //     "auth_receiver_phone": "9252498394",
        //     "auth_receiver_email": "test.test@gmail.com"
        // }';

        $requestBody = json_encode($requestBody, JSON_PRETTY_PRINT);

        try {

            $url = env('SEQUEL247_ENDPOINT') ."api/create_address";
            $ch = curl_init($url);

            // Set multiple options at once using curl_setopt_array
            curl_setopt_array($ch, [
                CURLOPT_POST => true,
                CURLOPT_POSTFIELDS => $requestBody,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_HTTPHEADER => [
                    'Content-Type: application/json',
                    'Authorization: Bearer e67faa16021a97d13c28e129a9d3e53d' // If authentication is needed
                ]
            ]);
            $response = curl_exec($ch);
            curl_close($ch);

            // $response = Http::withToken(env('SEQUEL247_TOKEN'))->withHeaders(['Content-Type' => 'application/json'])->post(env('SEQUEL247_ENDPOINT') . 'api/create_address', $requestBody);

            // FORCES Laravel to throw a RequestException if the server returns 4xx or 5xx
            return $response;

        } catch (RequestException $e) {
            // Catches standard HTTP API errors (4xx, 5xx)
            dd('API Request Error: ' . $e->getMessage());
            
        } catch (Exception $e) { 
            // Catches all other framework, SMTP, or system exceptions
            dd('General System Error: ' . $e->getMessage());
        }

    }

    public static function trackSequelShipment($docketNumber) {
        try {
            $requestBody = '{
                "token" : "'. env('SEQUEL247_TOKEN') .'",
                "docket" : "'. $docketNumber .'",
            }';
            $response = Http::withToken(env('SEQUEL247_TOKEN'))->withHeaders(['Content-Type' => 'application/json'])->post(env('SEQUEL247_ENDPOINT') . '/api/track', $requestBody);

            // FORCES Laravel to throw a RequestException if the server returns 4xx or 5xx
            $response->throw(); 

            return $response->json();

        } catch (RequestException $e) {
            // Catches standard HTTP API errors (4xx, 5xx)
            dd('API Request Error: ' . $e->getMessage());
            
        } catch (Exception $e) { 
            // Catches all other framework, SMTP, or system exceptions
            dd('General System Error: ' . $e->getMessage());
        }
    }
    
}