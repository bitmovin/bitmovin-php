<?php

namespace Bitmovin\api\resource;

use Bitmovin\api\AbstractHttpClient;
use Bitmovin\api\exceptions\BitmovinException;
use Bitmovin\api\model\AbstractModel;
use Bitmovin\api\model\ModelInterface;
use Bitmovin\api\util\ResponseBuilder;
use Doctrine\Common\Annotations\AnnotationRegistry;
use JMS\Serializer\Naming\IdenticalPropertyNamingStrategy;
use JMS\Serializer\Naming\SerializedNameAnnotationStrategy;
use JMS\Serializer\SerializerBuilder;
use JMS\Serializer\SerializerInterface;
use Psr\Http\Message\ResponseInterface;

abstract class AbstractResource extends AbstractHttpClient
{
    /** @var string */
    private $baseUri;
    /** @var string Namespace\ClassName */
    private $className;
    /** @var  string */
    private $listName = null;
    /** @var SerializerInterface */
    private $serializer;

    /** @var  string */
    private $apiKey;

    /**
     * AbstractResource constructor.
     *
     * @param        $baseUri
     * @param string $className
     * @param string $listName
     * @param        $apiKey
     * @param string $version
     * @param string $endpointUrl
     */
    public function __construct($baseUri, $className, $listName, $apiKey, $version = "v1", $endpointUrl = "https://api.bitmovin.com")
    {
        parent::__construct($apiKey, $version, $endpointUrl);

        $this->apiKey = $apiKey;

        AnnotationRegistry::registerLoader('class_exists');
        $propertyNamingStrategy = new SerializedNameAnnotationStrategy(new IdenticalPropertyNamingStrategy());
        $this->serializer = SerializerBuilder::create()->setPropertyNamingStrategy($propertyNamingStrategy)
            ->build();

        $this->baseUri = trim($baseUri, "/");
        $this->className = $className;
        $this->listName = $listName;
    }

    /**
     * @return string
     */
    protected function getApiKey()
    {
        return $this->apiKey;
    }

    /**
     * @param ModelInterface $model
     * @param string         $uri
     * @param string         $serializationClassName
     * @return AbstractModel
     * @throws BitmovinException
     */
    protected function postResource(ModelInterface $model, $uri, $serializationClassName)
    {
        try
        {
            $jsonString = $this->getSerializer()->serialize($model, 'json');
            $response = $this->postRequest($uri, $jsonString);
            return $this->buildResourceFromResponseCustomClass($response, $serializationClassName);
        }
        catch (BitmovinException $e)
        {
            throw $this->updateException($e);
        }
    }

    /**
     * @param string $uri
     * @param string $serializationClassName
     * @return mixed
     * @throws BitmovinException
     * @internal param string $resourceId
     *
     */
    protected function getResourceObject($uri, $serializationClassName)
    {
        try
        {
            $response = $this->getRequest($uri);
            return $this->buildResourceFromResponseCustomClass($response, $serializationClassName, true);
        }
        catch (BitmovinException $e)
        {
            throw $this->updateException($e);
        }
    }

    /**
     * @param string $uri
     * @param string $serializationClassName
     * @return AbstractModel
     * @throws BitmovinException
     * @internal param string $resourceId
     *
     */
    protected function getResourceCustomClass($uri, $serializationClassName)
    {
        try
        {
            $response = $this->getRequest($uri);
            return $this->buildResourceFromResponseCustomClass($response, $serializationClassName);
        }
        catch (BitmovinException $e)
        {
            throw $this->updateException($e);
        }
    }

    /**
     * @param ModelInterface $model
     *
     * @return AbstractModel
     * @throws BitmovinException
     */
    protected function createResource(ModelInterface $model)
    {
        try
        {
            $jsonString = $this->getSerializer()->serialize($model, 'json');
            $response = $this->postRequest($this->getBaseUri(), $jsonString);
            return $this->buildResourceFromResponse($response);
        }
        catch (BitmovinException $e)
        {
            throw $this->updateException($e);
        }
    }

    /**
     * @param string $resourceId
     *
     * @return AbstractModel
     * @throws BitmovinException
     */
    protected function getResource($resourceId)
    {
        try
        {
            $response = $this->getRequest($this->getBaseUri() . "/" . $resourceId);
            return $this->buildResourceFromResponse($response);
        }
        catch (BitmovinException $e)
        {
            throw $this->updateException($e);
        }
    }

    /**
     * @param string $resourceId
     *
     * @return AbstractModel
     * @throws BitmovinException
     */
    protected function deleteResource($resourceId)
    {
        try
        {
            $response = $this->deleteRequest($this->getBaseUri() . "/" . $resourceId);

            return $this->buildResourceFromResponse($response);
        }
        catch (BitmovinException $e)
        {
            throw $this->updateException($e);
        }
    }

    /**
     * @param integer $offset
     * @param integer $limit
     * @return \Bitmovin\api\model\AbstractModel[]
     * @throws BitmovinException
     */
    protected function listResourcePage($offset, $limit)
    {
        try
        {
            $offset = max(0, intval($offset));
            $limit = max(0, min(100, intval($limit)));

            $response = $this->listRequest($this->getBaseUri(), $offset, $limit);
            $items = $this->buildResourcesFromArrayResponse($response);
            return $items;
        }
        catch (BitmovinException $e)
        {
            throw $this->updateException($e);
        }
    }

    /**
     * @param ResponseInterface $response
     *
     * @return AbstractModel
     */
    protected function buildResourceFromResponse(ResponseInterface $response)
    {
        $responseEnvelope = ResponseBuilder::buildResponseEnvelope($response, $this->getClassName(), $this->getListName());
        $result = $responseEnvelope->getData()->getResult();

        $serializationClassName = $this->getClassName();
        $jsonResult = json_encode($result->getContent());

        /** @var AbstractModel $deserializedContent */
        $deserializedContent = $this->getSerializer()->deserialize(
            $jsonResult, $serializationClassName, 'json'
        );
        $deserializedContent->setResponseEnvelope($responseEnvelope);

        return $deserializedContent;
    }

    /**
     * @param ResponseInterface $response
     *
     * @param  string           $serializationClassName
     * @param bool              $genericObject
     * @return AbstractModel
     */
    protected function buildResourceFromResponseCustomClass(ResponseInterface $response, $serializationClassName, $genericObject = false)
    {
        $responseEnvelope = ResponseBuilder::buildResponseEnvelope($response, $this->getClassName(), $this->getListName(), $genericObject);
        $result = $responseEnvelope->getData()->getResult();

        $jsonResult = json_encode($result->getContent());

        /** @var AbstractModel $deserializedContent */
        $deserializedContent = $this->getSerializer()->deserialize(
            $jsonResult, $serializationClassName, 'json'
        );
        $deserializedContent->setResponseEnvelope($responseEnvelope);

        return $deserializedContent;
    }

    /**
     * @param ResponseInterface $response
     *
     * @return \Bitmovin\api\model\AbstractModel[]
     */
    private function buildResourcesFromArrayResponse(ResponseInterface $response)
    {
        $responseEnvelope = ResponseBuilder::buildResponseEnvelope($response, $this->getClassName(), $this->getListName());
        $result = $responseEnvelope->getData()->getResult();

        $serializationClassName = $this->getClassName();

        $jsonResult = json_encode($result->getContent());

        /** @var AbstractModel[] $deserializedContentArray */
        $deserializedContentArray = $this->getSerializer()
            ->deserialize($jsonResult, "array<" . $serializationClassName . ">", 'json');

        return $deserializedContentArray;
    }

    /**
     * @return string
     */
    private function getBaseUri()
    {
        return $this->baseUri;
    }

    /**
     * @return string
     */
    protected function getListName()
    {
        return $this->listName;
    }

    /** string */
    private function getClassName()
    {
        return $this->className;
    }

    /**
     * @return \JMS\Serializer\SerializerInterface
     */
    private function getSerializer()
    {
        return $this->serializer;
    }

    /**
     * @param BitmovinException $e
     *
     * @return BitmovinException
     */
    private function updateException(BitmovinException $e)
    {
        $apiResponse = $e->getApiResponse();

        if (!is_null($apiResponse))
        {
            $errorResponse = ResponseBuilder::buildResponseEnvelope($apiResponse);
            $message = $errorResponse->getData()->getMessage();
            $developerMessage = $errorResponse->getData()->getDeveloperMessage();

            return new BitmovinException($message, $e->getCode(), $e, $developerMessage);
        }

        return $e;
    }
}