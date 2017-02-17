<?php
/**
 * Created by PhpStorm.
 * User: dmoser
 * Date: 10.02.17
 * Time: 09:29
 */

namespace Bitmovin\api\resource\outputs;


use Bitmovin\api\model\outputs\GenericS3Output;

class GenericS3OutputResource extends OutputResource
{
    /**
     * @param GenericS3Output $output
     *
     * @return GenericS3Output
     * @throws \Bitmovin\api\exceptions\BitmovinException
     */
    public function create(GenericS3Output $output)
    {
        return parent::createOutput($output);
    }

    /**
     * @param GenericS3Output $output
     *
     * @return GenericS3Output
     * @throws \Bitmovin\api\exceptions\BitmovinException
     */
    public function delete(GenericS3Output $output)
    {
        return parent::deleteOutput($output);
    }

    /**
     * @param GenericS3Output $output
     *
     * @return GenericS3Output
     * @throws \Bitmovin\api\exceptions\BitmovinException
     */
    public function get(GenericS3Output $output)
    {
        return parent::getOutput($output);
    }

    /**
     * @param integer $offset
     * @param integer $limit
     * @return GenericS3Output[]
     */
    public function listPage($offset = 0, $limit = 25)
    {
        /** @noinspection PhpIncompatibleReturnTypeInspection */
        return $this->listResourcePage($offset, $limit);
    }

    /**
     * @param $outputId
     *
     * @return GenericS3Output
     * @throws \Bitmovin\api\exceptions\BitmovinException
     */
    public function getById($outputId)
    {
        return parent::getOutputById($outputId);
    }

    /**
     * @param $outputId
     *
     * @return GenericS3Output
     * @throws \Bitmovin\api\exceptions\BitmovinException
     */
    public function deleteById($outputId)
    {
        return parent::deleteOutputById($outputId);
    }
}