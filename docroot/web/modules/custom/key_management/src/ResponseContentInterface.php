<?php

namespace Drupal\key_management;

interface ResponseContentInterface {
    /**
     * Returns the HTTP status code of the response.
     * 
     * @return int
     */
    public function getResponseStatusCode();

    /**
     * Returns the response content data.
     * 
     * @return array
     */
    public function getResponseContent();

    /**
     * Returns TRUE if the request was not processed successfully
     *  
    */
    public function isError();

    /**
     * Returns the error message in case of errors.
     * 
     * @return string
     */
    public function getErrorMessage();

    /**
     * Returns the response content set by the constructor raw.
     * 
     * @return mixed
     */
    public function getRawContent();

    /**
     * Updates the response content.
     * 
     * @param mixed content.
     * 
     * @return void
     */
    public function updateContent($content);
}