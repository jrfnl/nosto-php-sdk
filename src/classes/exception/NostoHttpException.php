<?php
/**
 * Copyright (c) 2016, Nosto Solutions Ltd
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
 * @copyright 2016 Nosto Solutions Ltd
 * @license http://opensource.org/licenses/BSD-3-Clause BSD 3-Clause
 *
 */

/**
 * Nosto exception class for http errors within the sdk.
 */
class NostoHttpException extends NostoException
{
    /**
     * @var NostoHttpResponse
     */
    private $response;

    /**
     * @var NostoHttpRequest
     */
    private $request;

    /**
     * NostoHttpException constructor.
     * @param string $message
     * @param null $code
     * @param Exception|null $previous
     * @param NostoHttpRequest|null $request
     * @param NostoHttpResponse|null $response
     */
    public function __construct(
        $message = "",
        $code = null,
        Exception $previous = null,
        NostoHttpRequest $request = null,
        NostoHttpResponse $response = null
    )
    {
        parent::__construct($message, $code, $previous);
        $this->setRequest($request);
        $this->setResponse($response);
    }

    /**
     * @return NostoHttpRequest
     */
    public function getRequest()
    {
        return $this->request;
    }

    /**
     * @param NostoHttpRequest $request
     */
    public function setRequest(NostoHttpRequest $request)
    {
        $this->request = $request;
    }

    /**
     * @return NostoHttpResponse
     */
    public function getResponse()
    {
        return $this->response;
    }

    /**
     * @param NostoHttpResponse $response
     */
    public function setResponse(NostoHttpResponse $response)
    {
        $this->response = $response;
    }
}
