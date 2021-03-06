<?php
/**
 * Copyright (c) 2019, Nosto Solutions Ltd
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
 * @copyright 2019 Nosto Solutions Ltd
 * @license http://opensource.org/licenses/BSD-3-Clause BSD 3-Clause
 *
 */

namespace Nosto\Test\Unit\Helper;

use Codeception\Specify;
use Codeception\TestCase\Test;
use Nosto\Helper\SerializationHelper;
use Nosto\Test\Support\MockProduct;
use Nosto\Test\Support\MockProductWithSku;
use Nosto\Test\Support\MockSku;

class SerializationHelperTest extends Test
{
    use Specify;

    /**
     * Tests that an object is serialized correctly
     */
    public function testObjectWithAltImages()
    {
        $object = new MockProduct();
        $mainImage = 'http://my.shop.com/images/test_product_image.jpg';
        $altImages = [
            $mainImage,
            'http://shop.com/product_alt.jpg',
            'http://shop.com/product_alt.jpg',
            'http://shop.com/product_alt_1.jpg',
        ];
        $object->setAlternateImageUrls($altImages);
        $object->setImageUrl($mainImage);

        $serialized = SerializationHelper::serialize($object);
        $this->assertEquals('{"url":"http:\/\/my.shop.com\/products\/test_product.html","product_id":1,"name":"Test Product","image_url":"http:\/\/my.shop.com\/images\/test_product_image.jpg","price":99.99,"list_price":110.99,"price_currency_code":"USD","availability":"InStock","categories":["\/Mens","\/Mens\/Shoes"],"description":"This is a full description","brand":"Super Brand","variation_id":"USD","supplier_cost":22.33,"inventory_level":50,"review_count":99,"rating_value":2.5,"alternate_image_urls":["http:\/\/shop.com\/product_alt.jpg","http:\/\/shop.com\/product_alt_1.jpg"],"condition":"Used","gtin":"gtin","tag1":["first"],"tag2":["second"],"tag3":["third"],"google_category":"All","skus":[],"variations":[],"date_published":"2013-03-05"}', $serialized);
    }

    /**
     * Tests that an object is serialized correctly
     */
    public function testObject()
    {
        $object = new MockProduct();
        $this->assertEquals('{"url":"http:\/\/my.shop.com\/products\/test_product.html","product_id":1,"name":"Test Product","image_url":"http:\/\/my.shop.com\/images\/test_product.jpg","price":99.99,"list_price":110.99,"price_currency_code":"USD","availability":"InStock","categories":["\/Mens","\/Mens\/Shoes"],"description":"This is a full description","brand":"Super Brand","variation_id":"USD","supplier_cost":22.33,"inventory_level":50,"review_count":99,"rating_value":2.5,"alternate_image_urls":["http:\/\/shop.com\/product_alt.jpg"],"condition":"Used","gtin":"gtin","tag1":["first"],"tag2":["second"],"tag3":["third"],"google_category":"All","skus":[],"variations":[],"date_published":"2013-03-05"}', SerializationHelper::serialize($object));
    }

    /**
     * Tests that a customer object containing inline html is serialized correctly
     */
    public function testObjectWithHtmlTags()
    {
        $object = new MockProduct();
        $object->addCustomField('customFieldOne', '<div class="customClassOne">Custom field one</div>');
        $object->addCustomField('customFieldTwo', "<div class='customClassTwo'>Custom field two</div>");
        $serializedObject = SerializationHelper::serialize($object);
        $this->assertEquals('{"url":"http:\/\/my.shop.com\/products\/test_product.html","product_id":1,"name":"Test Product","image_url":"http:\/\/my.shop.com\/images\/test_product.jpg","price":99.99,"list_price":110.99,"price_currency_code":"USD","availability":"InStock","categories":["\/Mens","\/Mens\/Shoes"],"description":"This is a full description","brand":"Super Brand","variation_id":"USD","supplier_cost":22.33,"inventory_level":50,"review_count":99,"rating_value":2.5,"alternate_image_urls":["http:\/\/shop.com\/product_alt.jpg"],"condition":"Used","gtin":"gtin","tag1":["first"],"tag2":["second"],"tag3":["third"],"google_category":"All","skus":[],"variations":[],"custom_fields":{"customFieldOne":"<div class=\"customClassOne\">Custom field one<\/div>","customFieldTwo":"<div class=\'customClassTwo\'>Custom field two<\/div>"},"date_published":"2013-03-05"}', $serializedObject);
    }

    /**
     * Tests that a collection with objects is serialized correctly
     */
    public function testObjectCollection()
    {
        $collection = new \Nosto\Object\Product\ProductCollection();
        $collection->append(new MockProduct());
        $this->assertEquals('[{"url":"http:\/\/my.shop.com\/products\/test_product.html","product_id":1,"name":"Test Product","image_url":"http:\/\/my.shop.com\/images\/test_product.jpg","price":99.99,"list_price":110.99,"price_currency_code":"USD","availability":"InStock","categories":["\/Mens","\/Mens\/Shoes"],"description":"This is a full description","brand":"Super Brand","variation_id":"USD","supplier_cost":22.33,"inventory_level":50,"review_count":99,"rating_value":2.5,"alternate_image_urls":["http:\/\/shop.com\/product_alt.jpg"],"condition":"Used","gtin":"gtin","tag1":["first"],"tag2":["second"],"tag3":["third"],"google_category":"All","skus":[],"variations":[],"date_published":"2013-03-05"}]', SerializationHelper::serialize($collection));
    }

    /**
     * Tests that an array with objects is serialized correctly
     */
    public function testObjectArray()
    {
        $array = [new MockProduct()];
        $this->assertEquals('[{"url":"http:\/\/my.shop.com\/products\/test_product.html","product_id":1,"name":"Test Product","image_url":"http:\/\/my.shop.com\/images\/test_product.jpg","price":99.99,"list_price":110.99,"price_currency_code":"USD","availability":"InStock","categories":["\/Mens","\/Mens\/Shoes"],"description":"This is a full description","brand":"Super Brand","variation_id":"USD","supplier_cost":22.33,"inventory_level":50,"review_count":99,"rating_value":2.5,"alternate_image_urls":["http:\/\/shop.com\/product_alt.jpg"],"condition":"Used","gtin":"gtin","tag1":["first"],"tag2":["second"],"tag3":["third"],"google_category":"All","skus":[],"variations":[],"date_published":"2013-03-05"}]', SerializationHelper::serialize($array));
    }

    /**
     * Tests that a simple array is serialized correctly
     */
    public function testSimpleArray()
    {
        $array = ['one', 1, 'two', 2];
        $this->assertEquals('["one",1,"two",2]', SerializationHelper::serialize($array));
    }

    /**
     * Tests that custom fields are not converted to snake case
     */
    public function testObjectWithCustomFields()
    {
        $object = new MockProduct();
        $object->addCustomField('shouldNotBeSnakeCase', 'value');
        $this->assertEquals('{"url":"http:\/\/my.shop.com\/products\/test_product.html","product_id":1,"name":"Test Product","image_url":"http:\/\/my.shop.com\/images\/test_product.jpg","price":99.99,"list_price":110.99,"price_currency_code":"USD","availability":"InStock","categories":["\/Mens","\/Mens\/Shoes"],"description":"This is a full description","brand":"Super Brand","variation_id":"USD","supplier_cost":22.33,"inventory_level":50,"review_count":99,"rating_value":2.5,"alternate_image_urls":["http:\/\/shop.com\/product_alt.jpg"],"condition":"Used","gtin":"gtin","tag1":["first"],"tag2":["second"],"tag3":["third"],"google_category":"All","skus":[],"variations":[],"custom_fields":{"shouldNotBeSnakeCase":"value"},"date_published":"2013-03-05"}', SerializationHelper::serialize($object));
    }

    /**
     * Tests that custom fields with scandic characters are preserved
     */
    public function testObjectWithScandicCustomFields()
    {
        $object = new MockProduct();
        $object->addCustomField('spesialCäåös', 'value');
        $this->assertEquals('{"url":"http:\/\/my.shop.com\/products\/test_product.html","product_id":1,"name":"Test Product","image_url":"http:\/\/my.shop.com\/images\/test_product.jpg","price":99.99,"list_price":110.99,"price_currency_code":"USD","availability":"InStock","categories":["\/Mens","\/Mens\/Shoes"],"description":"This is a full description","brand":"Super Brand","variation_id":"USD","supplier_cost":22.33,"inventory_level":50,"review_count":99,"rating_value":2.5,"alternate_image_urls":["http:\/\/shop.com\/product_alt.jpg"],"condition":"Used","gtin":"gtin","tag1":["first"],"tag2":["second"],"tag3":["third"],"google_category":"All","skus":[],"variations":[],"custom_fields":{"spesialC\u00e4\u00e5\u00f6s":"value"},"date_published":"2013-03-05"}', SerializationHelper::serialize($object));
    }

    /**
     * Tests that an object with key that contains special characters is serialized correctly
     */
    public function testObjectWithSpecialCharacters()
    {
        $object = new MockProduct();
        $object->addCustomField('key.with.\special?char s*', 'åäöø');
        $this->assertEquals('{"url":"http:\/\/my.shop.com\/products\/test_product.html","product_id":1,"name":"Test Product","image_url":"http:\/\/my.shop.com\/images\/test_product.jpg","price":99.99,"list_price":110.99,"price_currency_code":"USD","availability":"InStock","categories":["\/Mens","\/Mens\/Shoes"],"description":"This is a full description","brand":"Super Brand","variation_id":"USD","supplier_cost":22.33,"inventory_level":50,"review_count":99,"rating_value":2.5,"alternate_image_urls":["http:\/\/shop.com\/product_alt.jpg"],"condition":"Used","gtin":"gtin","tag1":["first"],"tag2":["second"],"tag3":["third"],"google_category":"All","skus":[],"variations":[],"custom_fields":{"key.with.\\\special?char s*":"\u00e5\u00e4\u00f6\u00f8"},"date_published":"2013-03-05"}', SerializationHelper::serialize($object));
    }

    /**
     * Tests that an object SKUs are serialized to HTML correctly
     */
    public function testObjectWithSkus()
    {
        $object = new MockProductWithSku();
        $this->assertEquals('{"url":"http:\/\/my.shop.com\/products\/test_product.html","product_id":1,"name":"Test Product","image_url":"http:\/\/my.shop.com\/images\/test_product.jpg","price":99.99,"list_price":110.99,"price_currency_code":"USD","availability":"InStock","categories":["\/Mens","\/Mens\/Shoes"],"description":"This is a full description","brand":"Super Brand","variation_id":"USD","supplier_cost":22.33,"inventory_level":50,"review_count":99,"rating_value":2.5,"alternate_image_urls":["http:\/\/shop.com\/product_alt.jpg"],"condition":"Used","gtin":"gtin","tag1":["first"],"tag2":["second"],"tag3":["third"],"google_category":"All","skus":[{"id":100,"name":"Test Product","price":99.99,"list_price":110.99,"url":"http:\/\/my.shop.com\/products\/test_product.html","image_url":"http:\/\/my.shop.com\/images\/test_product.jpg","gtin":"gtin","availability":"InStock","inventory_level":20},{"id":100,"name":"Test Product","price":99.99,"list_price":110.99,"url":"http:\/\/my.shop.com\/products\/test_product.html","image_url":"http:\/\/my.shop.com\/images\/test_product.jpg","gtin":"gtin","availability":"InStock","custom_fields":{"noSnakeCase":"value"},"inventory_level":20},{"id":100,"name":"Test Product","price":99.99,"list_price":110.99,"url":"http:\/\/my.shop.com\/products\/test_product.html","image_url":"http:\/\/my.shop.com\/images\/test_product.jpg","gtin":"gtin","availability":"InStock","custom_fields":{"\u00e5\u00e4\u00f6":"\u00e5\u00e4\u00f6"},"inventory_level":20}],"variations":[],"date_published":"2013-03-05"}', SerializationHelper::serialize($object));
    }

    /**
     * Tests that an object SKUs are serialized to HTML correctly
     */
    public function testSkuIsSanitized()
    {
        $object = new MockSku();
        $object = $object->sanitize();
        $this->assertEquals('{"id":100,"name":"Test Product","price":99.99,"list_price":110.99,"url":"http:\/\/my.shop.com\/products\/test_product.html","image_url":"http:\/\/my.shop.com\/images\/test_product.jpg","gtin":"gtin","availability":"InStock"}', SerializationHelper::serialize($object));
    }
}
