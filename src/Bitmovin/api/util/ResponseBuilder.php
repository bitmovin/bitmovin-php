<?php

namespace Bitmovin\api\util;

use Bitmovin\api\enum\ResponseStatus;
use Bitmovin\api\exceptions\BitmovinException;
use Bitmovin\api\model\connection\DeleteResult;
use Bitmovin\api\model\connection\DetailsResult;
use Bitmovin\api\model\connection\ErrorResponseData;
use Bitmovin\api\model\connection\ErrorResponseEnvelope;
use Bitmovin\api\model\connection\Link;
use Bitmovin\api\model\connection\ListResult;
use Bitmovin\api\model\connection\Message;
use Bitmovin\api\model\connection\ResponseData;
use Bitmovin\api\model\connection\SuccessResponseData;
use Bitmovin\api\model\connection\SuccessResponseEnvelope;
use Psr\Http\Message\ResponseInterface;

class ResponseBuilder
{

    /**
     * @param ResponseInterface $response
     * @param string            $className
     * @param string            $listName
     *
     * @param bool              $genericObject
     * @return ErrorResponseEnvelope|SuccessResponseEnvelope
     */
    public static function buildResponseEnvelope(ResponseInterface $response, $className = NULL, $listName = NULL, $genericObject = false)
    {
        $listName = is_null($listName) ? self::getListNameFromClassName($className) : $listName;
        $responseBody = $response->getBody()->getContents();
        $jsonObject = json_decode($responseBody);

        $requestId = $jsonObject->requestId;
        $status = $jsonObject->status;
        $data = NULL;
        $more = NULL;

        if (property_exists($jsonObject, 'data'))
        {
            $data = $jsonObject->data;
        }

        if (property_exists($jsonObject, 'more'))
        {
            $more = $jsonObject->more;
        }

        if ($status === ResponseStatus::SUCCESS)
        {
            $successData = static::buildSuccessData($data, $listName, $genericObject);

            return new SuccessResponseEnvelope($requestId, $successData, $more);
        }

        $errorData = static::buildErrorData($data);

        return new ErrorResponseEnvelope($requestId, $errorData, $more);
    }

    /**
     * @param                     $requestId
     * @param SuccessResponseData $successResponseData
     * @param \stdClass|NULL      $more
     *
     * @return SuccessResponseEnvelope
     */
    public static function buildSuccessResponseEnvelope($requestId, SuccessResponseData $successResponseData, \stdClass $more = NULL)
    {
        return new SuccessResponseEnvelope($requestId, $successResponseData, $more);
    }

    /**
     * @param                   $requestId
     * @param ErrorResponseData $errorResponseData
     * @param \stdClass|NULL    $more
     *
     * @return ErrorResponseEnvelope
     */
    public static function buildErrorResponseEnvelope($requestId, ErrorResponseData $errorResponseData, \stdClass $more = NULL)
    {
        return new ErrorResponseEnvelope($requestId, $errorResponseData, $more);
    }

    /**
     * @param \stdClass $data
     *
     * @return ResponseData
     */
    public static function buildData(\stdClass $data)
    {
        if (ResponseStatus::ERROR == $data->status)
        {
            return static::buildErrorData($data);
        }

        return static::buildSuccessData($data);
    }

    /**
     * @param \stdClass $successData
     * @param null      $listName
     *
     * @param bool      $genericObject
     * @return SuccessResponseData
     */
    public static function buildSuccessData(\stdClass $successData, $listName = NULL, $genericObject = false)
    {
        $messages = array();
        $result = NULL;

        if (property_exists($successData, 'result'))
        {
            $result = static::buildResult($successData->result, $listName, $genericObject);
        }

        if (property_exists($successData, 'messages'))
        {
            foreach ($successData->messages as $msg)
            {
                $messages[] = static::buildMessage($msg);
            }
        }

        return new SuccessResponseData($result, $messages);
    }

    /**
     * @param \stdClass $result
     * @param null      $listName
     * @param bool      $genericObject
     * @return DeleteResult|DetailsResult|ListResult
     */
    public static function buildResult(\stdClass $result, $listName = NULL, $genericObject = false)
    {
        if (!is_null($listName) && property_exists($result, $listName))
        {
            return static::buildListResult($result, $listName);
        }

        if ($genericObject)
        {
            return static::buildGenericObjectResult($result);
        }

        return static::buildDetailResult($result);

        //return static::buildDeleteResult($result);
    }

    /**
     * @param \stdClass $result
     *
     * @return DetailsResult
     * @throws BitmovinException
     */
    public static function buildDetailResult(\stdClass $result)
    {
        return new DetailsResult($result);
    }

    /**
     * @param \stdClass $result
     *
     * @return DetailsResult
     * @throws BitmovinException
     */
    public static function buildGenericObjectResult(\stdClass $result)
    {
        return new DetailsResult($result);
    }

    /**
     * @param \stdClass $result
     *
     * @return DeleteResult
     * @throws BitmovinException
     */
    public static function buildDeleteResult(\stdClass $result)
    {
        if (!property_exists($result, "id") || !is_string($result->id))
        {
            throw new BitmovinException("No Result ID available!");
        }

        return new DeleteResult($result);
    }

    /**
     * @param \stdClass $result
     * @param           $listName
     *
     * @return ListResult
     * @throws BitmovinException
     */
    public static function buildListResult(\stdClass $result, $listName)
    {
        if (!property_exists($result, $listName) || !is_array($result->{$listName}))
        {
            throw new BitmovinException("Unknown Result type or not found!");
        }

        return new ListResult($result->{$listName});
    }

    /**
     * @param \stdClass $errorData
     *
     * @return ErrorResponseData
     */
    public static function buildErrorData(\stdClass $errorData)
    {
        $code = null;
        $message = '';
        $developerMessage = '';
        if (property_exists($errorData, 'code'))
            $code = $errorData->code;
        if (property_exists($errorData, 'message'))
            $message = $errorData->message;
        if (property_exists($errorData, 'developerMessage'))
            $developerMessage = $errorData->developerMessage;
        /** @var Link[] $links */
        $links = array();
        /** @var Message[] $details */
        $details = array();

        if (property_exists($errorData, 'links'))
        {
            $dataLinks = $errorData->links;

            foreach ($dataLinks as $link)
            {
                $links[] = static::buildLink($link);
            }
        }

        if (property_exists($errorData, 'details'))
        {
            $dataDetails = $errorData->details;

            foreach ($dataDetails as $detail)
            {
                $details[] = static::buildMessage($detail);
            }
        }

        $responseErrorData = new ErrorResponseData($code, $message, $developerMessage);
        $responseErrorData->setLinks($links);
        $responseErrorData->setDetails($details);

        return $responseErrorData;
    }

    /**
     * @param \stdClass $linkData
     *
     * @return Link
     */
    public static function buildLink(\stdClass $linkData)
    {
        $href = $linkData->href;
        $link = new Link($href);

        if (property_exists($linkData, 'title'))
        {
            $link->setTitle($linkData->title);
        }

        return $link;
    }

    /**
     * @param \stdClass $message
     *
     * @return Message
     */
    public static function buildMessage(\stdClass $message)
    {
        $type = $message->type;
        $text = $message->text;
        $field = NULL;

        if (property_exists($message, 'field'))
        {
            $field = $message->field;
        }

        if (property_exists($message, 'links'))
        {
            $field = $message->field;
        }

        return new Message($type, $text, $field);
    }

    private static function getListNameFromClassName($className)
    {
        return strtolower(substr($className, strrpos($className, '\\') + 1)) . "s";
    }
}