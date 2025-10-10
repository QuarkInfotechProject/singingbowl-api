<?php

namespace Modules\Shared\StatusCode;

class ErrorCode
{
    /* Security exceptions start */

    /**
     *When the server cannot process the request due to client error
     */
    public const BAD_REQUEST = 400;

    /**
     * When token/session has expired
     */
    public const UNAUTHORIZED = 401;

    /**
     * When payload is changed (for cases when the requested payload is different to current one)
     */
    public const INVALID_PAYLOAD = 402;

    /**
     * When the client does not have access rights to the content
     */
    public const FORBIDDEN = 403;

    /**
     * When the requested token is invalid
     */
    public const NOT_FOUND = 404;

    /**
     *
     */
    public const ENCODING_FAILED = 405;

    /**
     *
     */
    public const DECODING_FAILED = 406;

    /**
     *
     */
    public const TOKEN_EXPIRED = 407;

    /**
     * This response is sent when a request conflicts with the current state of the server.
     */
    public const CONFLICT = 409;

    /**
     * This response is sent when the requested content has been permanently deleted from server, with no forwarding address.
     */
    public const GONE = 410;

    /**
     *
     */
    public const TOO_MANY_ATTEMPTS = 429;

    /**
     * The request was well-formed but was unable to be followed due to semantic errors. (e.g., missing data).
     */
    public const UNPROCESSABLE_CONTENT = 422;

    /**
     * The user has sent too many requests in a given amount of time ("rate limiting").
     */
    public const TOO_MANY_REQUESTS = 429;

    /**
     * Error code for when merchant is not active
     */
    public const USER_NOT_ACTIVE = 101;

    /**
     * Error code for when merchant contract is expired
     */
    public const USER_NOT_FOUND = 102;

    /**
     * When user requested and current user are not the same. IDOR case
     */
    public const INVALID_USER = 103;

    /**
     * error description based on error codes
     * @var string[]
     */
    public static $errorDescription = [
        self::BAD_REQUEST => 'badRequest',
        self::UNAUTHORIZED => 'unauthorized',
        self::INVALID_PAYLOAD => 'invalidPayload',
        self::FORBIDDEN => 'forbidden',
        self::NOT_FOUND => 'invalidToken',
        self::ENCODING_FAILED => 'tokenEncodingFailed',
        self::DECODING_FAILED => 'tokenDecodingFailed',
        self::TOKEN_EXPIRED => 'tokenDecodingFailed',
        self::CONFLICT => 'tokenDecodingFailed',
        self::GONE => 'resourceNoLongerAvailable',
        self::UNPROCESSABLE_CONTENT => 'unProcessableContent',
        self::TOO_MANY_REQUESTS => 'tooManyRequests',
        self::USER_NOT_ACTIVE => 'userNotActive',
        self::USER_NOT_FOUND => 'userNotFound',
        self::INVALID_USER => 'invalidUser',
    ];
}
