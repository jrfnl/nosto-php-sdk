<?php
/**
 * Copyright (c) 2015, Nosto Solutions Ltd
 * All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without modification,
 * are permitted provided that the following conditions are met:
 *
 * 1. Redistributions of source code must retain the above copyright notice,
 * this list of conditions and the following disclaimer.
 *
 * 2. Redistributions in binary form must reproduce the above copyright notice,
 * this list of conditions and the following disclaimer in the documentation
 * and/or other materials provided with the distribution.
 *
 * 3. Neither the name of the copyright holder nor the names of its contributors
 * may be used to endorse or promote products derived from this software without
 * specific prior written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND
 * ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED
 * WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE
 * DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT HOLDER OR CONTRIBUTORS BE LIABLE FOR
 * ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES
 * (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
 * LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON
 * ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
 * (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS
 * SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 *
 * @author Nosto Solutions Ltd <contact@nosto.com>
 * @copyright 2015 Nosto Solutions Ltd
 * @license http://opensource.org/licenses/BSD-3-Clause BSD 3-Clause
 */

/**
 * Order collection for historical data exports.
 * Supports only items implementing "NostoOrderInterface".
 */
class NostoExportCollectionOrder extends NostoOrderCollection implements NostoExportCollectionInterface
{
    /**
     * @inheritdoc
     */
    public function getJson()
    {
        /** @var NostoFormatterDate $dateFormatter */
        $dateFormatter = Nosto::formatter('date');
        /** @var NostoFormatterPrice $priceFormatter */
        $priceFormatter = Nosto::formatter('price');

        $array = array();
        /** @var NostoOrderInterface $item */
        foreach ($this->getArrayCopy() as $item) {
            $data = array(
                'order_number' => $item->getOrderNumber(),
                'order_status_code' => $item->getOrderStatus()->getCode(),
                'order_status_label' => $item->getOrderStatus()->getLabel(),
                'created_at' => $dateFormatter->format($item->getCreatedDate()),
                'buyer' => array(),
                'payment_provider' => $item->getPaymentProvider(),
                'purchased_items' => array(),
            );
            foreach ($item->getPurchasedItems() as $orderItem) {
                $data['purchased_items'][] = array(
                    'product_id' => $orderItem->getProductId(),
                    'quantity' => (int)$orderItem->getQuantity(),
                    'name' => $orderItem->getName(),
                    'unit_price' => $priceFormatter->format($orderItem->getUnitPrice()),
                    'price_currency_code' => $orderItem->getCurrency()->getCode(),
                );
            }
            // Add optional order reference if set.
            if ($item->getExternalOrderRef()) {
                $data['external_order_ref'] = $item->getExternalOrderRef();
            }
            // Add optional order status history if set.
            if ($item->getOrderStatuses() !== array()) {
                $dateFormat = new NostoDateFormat(NostoDateFormat::ISO_8601);
                $statuses = array();
                foreach ($item->getOrderStatuses() as $status) {
                    if ($status->getCreatedAt()) {
                        if (!isset($statuses[$status->getCode()])) {
                            $statuses[$status->getCode()] = array();
                        }
                        $statuses[$status->getCode()][] = $dateFormatter->format(
                            $status->getCreatedAt(),
                            $dateFormat
                        );
                    }
                }
                if (count($statuses) > 0) {
                    $data['order_statuses'] = $statuses;
                }
            }
            // Add optional buyer info.
            if ($item->getBuyerInfo()->getFirstName()) {
                $data['buyer']['first_name'] = $item->getBuyerInfo()->getFirstName();
            }
            if ($item->getBuyerInfo()->getLastName()) {
                $data['buyer']['last_name'] = $item->getBuyerInfo()->getLastName();
            }
            if ($item->getBuyerInfo()->getEmail()) {
                $data['buyer']['email'] = $item->getBuyerInfo()->getEmail();
            }

            $array[] = $data;
        }

        return json_encode($array);
    }
}
