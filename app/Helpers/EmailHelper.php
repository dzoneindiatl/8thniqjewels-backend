<?php

namespace App\Helpers;

use App\Models\User;
use App\Models\Couriers;
use App\Models\EmailTemplate;
use App\Mail\orderSuccessEmail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class EmailHelper
{



    public static function getEmailTemplate($slug, $order, $ordermain, $payment_url = null, $payment_amount = null)
    {
        $orderDetails = '<table border="1" cellpadding="5" cellspacing="0" style="border-collapse:collapse;">
                            <tr>
                                <th>Product Image</th>
                                <th>Product Name</th>
                                <th>Qty</th>
                                <th>Total Price</th>
                                <th>Combination</th>
                                <th>Pending Payment</th>
                            </tr>';

        $pendingPayment = $payment_amount ?? ($ordermain->pending_amount ?? 0);
        $combination = json_decode($order->combination ?? '', true);
        $combination = is_array($combination) ? $combination : [];
        $valueIds = [];

        foreach ($combination as $variantName => $variantValue) {
            if (is_numeric($variantName) && is_numeric($variantValue)) {
                $valueIds[] = (int) $variantValue;
                continue;
            }

            if (is_numeric($variantValue)) {
                $valueIds[] = (int) $variantValue;
            } else {
                $variantId = DB::table('variants')
                    ->where('name', $variantName)
                    ->value('id');

                if ($variantId) {
                    $valueId = DB::table('variant_values')
                        ->where('variant_id', $variantId)
                        ->where('name', $variantValue)
                        ->value('id');

                    if ($valueId) {
                        $valueIds[] = $valueId;
                    }
                }
            }
        }

        if (empty($combination) && !empty($order->product_variant_combination_id)) {
            $variantCombination = DB::table('product_variant_combinations')
                ->where('id', $order->product_variant_combination_id)
                ->first(['combination_id', 'primary_variant_value_id']);
            $combinationIds = json_decode($variantCombination->combination_id ?? '', true);
            if (is_array($combinationIds)) {
                $valueIds = array_merge($valueIds, array_filter($combinationIds, 'is_numeric'));
            }
            if (is_numeric($variantCombination->primary_variant_value_id ?? null)) {
                $valueIds[] = (int) $variantCombination->primary_variant_value_id;
            }
        }

        $variantValues = empty($valueIds)
            ? collect()
            : DB::table('variant_values')
                ->join('variants', 'variants.id', '=', 'variant_values.variant_id')
                ->whereIn('variant_values.id', array_unique($valueIds))
                ->select('variant_values.id', 'variant_values.name as value_name', 'variants.name as variant_name')
                ->get()
                ->keyBy('id');

        $combinationParts = [];
        foreach ($combination as $variantName => $variantValue) {
            if (is_numeric($variantName)) {
                $resolvedValue = $variantValues->get($variantValue);
                if ($resolvedValue) {
                    $combinationParts[] = $resolvedValue->variant_name . ': ' . $resolvedValue->value_name;
                }
            } elseif (is_numeric($variantValue)) {
                $resolvedValue = $variantValues->get($variantValue);
                $combinationParts[] = ($resolvedValue->variant_name ?? ucfirst($variantName))
                    . ': ' . ($resolvedValue->value_name ?? $variantValue);
            } else {
                $combinationParts[] = ucfirst($variantName) . ': ' . $variantValue;
            }
        }

        if (empty($combinationParts)) {
            $combinationParts = $variantValues
                ->map(function ($value) {
                    return $value->variant_name . ': ' . $value->value_name;
                })
                ->all();
        }
        $combinationText = e(implode(', ', $combinationParts));

        // Find image matching any of the variant values
        $productimage = DB::table('product_graphics')
            ->where('product_id', $order->product_id)
            ->whereIn('variant_id', $valueIds)
            ->orderBy('id', 'Asc')
            ->first();
        $track = Couriers::where('id', $order->courier_id)->first();
        $orderDetails .= '<tr>
                                <td><img src=" ' . config('constant.PRODUCT_IMAGE_URL') . '/' . @$productimage->graphic . ' " style="max-width: 85px; max-height: 120px;"></td>
                                <td>' . @$order->product->name . '</td>
                                <td>' . @$order->qty  . '</td>
                                <td>' . @$order->total . '</td>
                               <td>' .  @$combinationText . '</td>
                               <td>₹' . number_format((float) $pendingPayment, 2) . '</td>
                            </tr>';
        $orderDetails .= '</table>';
        $trackDetail = "";
        if (!empty($track->tracking_url) && !empty($order->awb_number)) {
            $trackDetail = '<div><p>Track Your Item <br><strong>Traking Url ' . $track->tracking_url . '</strong><br><strong> Traking Number ' . $order->awb_number . ' </strong></p></div>';
        }


        if (!empty($payment_url)) {
            $trackDetail = '<div><p>Your order is ready to ship. Please pay the remaining balance of ₹'
                . number_format((float) $payment_amount, 2)
                . ' using the link below:<br><a href="' . e($payment_url) . '">' . e($payment_url) . '</a></p></div>';
        }


        $user  = User::where('id', $ordermain->user_id)->first();
        $data = [
            'CUSTOMER_NAME' => $user->name,
            'ORDER_ID'      => $ordermain->order_number,
            'ORDER_DETAILS' => $orderDetails,
            'ORDER_STATUS' => $slug->name,
            'TRAKING_ORDER' => $trackDetail,
        ];
        $template = EmailHelper::getProcessedTemplate($slug, $data);

        if (!empty($user->email)) {
            Mail::to($user->email)->queue(new orderSuccessEmail($template['subject'], $template['body']));
        }
    }

    public static function getProcessedTemplate($slug, $data = [])
    {
        $newSlug = is_string($slug) ? $slug : $slug->slug;
        $template = EmailTemplate::where('slug', $newSlug)->first();
        if (!$template) {
            return ['subject' => 'No Subject', 'body' => 'Template not found'];
        }

        $search  = array_map(fn($k) => '{' . $k . '}', array_keys($data));
        $replace = array_values($data);

        $subject = str_replace($search, $replace, $template->subject);
        $body    = str_replace($search, $replace, $template->body);

        return ['subject' => $subject, 'body' => $body];
    }
}