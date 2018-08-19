<?php
/**
 * Affiliate
 *
 * PHP version 5
 *
 * @category Class
 * @package  Swagger\Client
 * @author   Swagger Codegen team
 * @link     https://github.com/swagger-api/swagger-codegen
 */

/**
 * BitMEX API
 *
 * ## REST API for the BitMEX Trading Platform  [View Changelog](/app/apiChangelog)    #### Getting Started  Base URI: [https://www.bitmex.com/api/v1](/api/v1)  ##### Fetching Data  All REST endpoints are documented below. You can try out any query right from this interface.  Most table queries accept `count`, `start`, and `reverse` params. Set `reverse=true` to get rows newest-first.  Additional documentation regarding filters, timestamps, and authentication is available in [the main API documentation](/app/restAPI).  *All* table data is available via the [Websocket](/app/wsAPI). We highly recommend using the socket if you want to have the quickest possible data without being subject to ratelimits.  ##### Return Types  By default, all data is returned as JSON. Send `?_format=csv` to get CSV data or `?_format=xml` to get XML data.  ##### Trade Data Queries  *This is only a small subset of what is available, to get you started.*  Fill in the parameters and click the `Try it out!` button to try any of these queries.  * [Pricing Data](#!/Quote/Quote_get)  * [Trade Data](#!/Trade/Trade_get)  * [OrderBook Data](#!/OrderBook/OrderBook_getL2)  * [Settlement Data](#!/Settlement/Settlement_get)  * [Exchange Statistics](#!/Stats/Stats_history)  Every function of the BitMEX.com platform is exposed here and documented. Many more functions are available.  ##### Swagger Specification  [⇩ Download Swagger JSON](swagger.json)    ## All API Endpoints  Click to expand a section.
 *
 * OpenAPI spec version: 1.2.0
 * Contact: support@bitmex.com
 * Generated by: https://github.com/swagger-api/swagger-codegen.git
 * Swagger Codegen version: 2.4.0-SNAPSHOT
 */

/**
 * NOTE: This class is auto generated by the swagger code generator program.
 * https://github.com/swagger-api/swagger-codegen
 * Do not edit the class manually.
 */

namespace Swagger\Client\Model;

use \ArrayAccess;
use \Swagger\Client\ObjectSerializer;

/**
 * Affiliate Class Doc Comment
 *
 * @category Class
 * @package  Swagger\Client
 * @author   Swagger Codegen team
 * @link     https://github.com/swagger-api/swagger-codegen
 */
class Affiliate implements ModelInterface, ArrayAccess
{
    const DISCRIMINATOR = null;

    /**
      * The original name of the model.
      *
      * @var string
      */
    protected static $swaggerModelName = 'Affiliate';

    /**
      * Array of property to type mappings. Used for (de)serialization
      *
      * @var string[]
      */
    protected static $swaggerTypes = [
        'account' => 'float',
        'currency' => 'string',
        'prev_payout' => 'float',
        'prev_turnover' => 'float',
        'prev_comm' => 'float',
        'prev_timestamp' => '\DateTime',
        'exec_turnover' => 'float',
        'exec_comm' => 'float',
        'total_referrals' => 'float',
        'total_turnover' => 'float',
        'total_comm' => 'float',
        'payout_pcnt' => 'double',
        'pending_payout' => 'float',
        'timestamp' => '\DateTime',
        'referrer_account' => 'double'
    ];

    /**
      * Array of property to format mappings. Used for (de)serialization
      *
      * @var string[]
      */
    protected static $swaggerFormats = [
        'account' => 'int64',
        'currency' => null,
        'prev_payout' => 'int64',
        'prev_turnover' => 'int64',
        'prev_comm' => 'int64',
        'prev_timestamp' => 'date-time',
        'exec_turnover' => 'int64',
        'exec_comm' => 'int64',
        'total_referrals' => 'int64',
        'total_turnover' => 'int64',
        'total_comm' => 'int64',
        'payout_pcnt' => 'double',
        'pending_payout' => 'int64',
        'timestamp' => 'date-time',
        'referrer_account' => 'double'
    ];

    /**
     * Array of property to type mappings. Used for (de)serialization
     *
     * @return array
     */
    public static function swaggerTypes()
    {
        return self::$swaggerTypes;
    }

    /**
     * Array of property to format mappings. Used for (de)serialization
     *
     * @return array
     */
    public static function swaggerFormats()
    {
        return self::$swaggerFormats;
    }

    /**
     * Array of attributes where the key is the local name,
     * and the value is the original name
     *
     * @var string[]
     */
    protected static $attributeMap = [
        'account' => 'account',
        'currency' => 'currency',
        'prev_payout' => 'prevPayout',
        'prev_turnover' => 'prevTurnover',
        'prev_comm' => 'prevComm',
        'prev_timestamp' => 'prevTimestamp',
        'exec_turnover' => 'execTurnover',
        'exec_comm' => 'execComm',
        'total_referrals' => 'totalReferrals',
        'total_turnover' => 'totalTurnover',
        'total_comm' => 'totalComm',
        'payout_pcnt' => 'payoutPcnt',
        'pending_payout' => 'pendingPayout',
        'timestamp' => 'timestamp',
        'referrer_account' => 'referrerAccount'
    ];

    /**
     * Array of attributes to setter functions (for deserialization of responses)
     *
     * @var string[]
     */
    protected static $setters = [
        'account' => 'setAccount',
        'currency' => 'setCurrency',
        'prev_payout' => 'setPrevPayout',
        'prev_turnover' => 'setPrevTurnover',
        'prev_comm' => 'setPrevComm',
        'prev_timestamp' => 'setPrevTimestamp',
        'exec_turnover' => 'setExecTurnover',
        'exec_comm' => 'setExecComm',
        'total_referrals' => 'setTotalReferrals',
        'total_turnover' => 'setTotalTurnover',
        'total_comm' => 'setTotalComm',
        'payout_pcnt' => 'setPayoutPcnt',
        'pending_payout' => 'setPendingPayout',
        'timestamp' => 'setTimestamp',
        'referrer_account' => 'setReferrerAccount'
    ];

    /**
     * Array of attributes to getter functions (for serialization of requests)
     *
     * @var string[]
     */
    protected static $getters = [
        'account' => 'getAccount',
        'currency' => 'getCurrency',
        'prev_payout' => 'getPrevPayout',
        'prev_turnover' => 'getPrevTurnover',
        'prev_comm' => 'getPrevComm',
        'prev_timestamp' => 'getPrevTimestamp',
        'exec_turnover' => 'getExecTurnover',
        'exec_comm' => 'getExecComm',
        'total_referrals' => 'getTotalReferrals',
        'total_turnover' => 'getTotalTurnover',
        'total_comm' => 'getTotalComm',
        'payout_pcnt' => 'getPayoutPcnt',
        'pending_payout' => 'getPendingPayout',
        'timestamp' => 'getTimestamp',
        'referrer_account' => 'getReferrerAccount'
    ];

    /**
     * Array of attributes where the key is the local name,
     * and the value is the original name
     *
     * @return array
     */
    public static function attributeMap()
    {
        return self::$attributeMap;
    }

    /**
     * Array of attributes to setter functions (for deserialization of responses)
     *
     * @return array
     */
    public static function setters()
    {
        return self::$setters;
    }

    /**
     * Array of attributes to getter functions (for serialization of requests)
     *
     * @return array
     */
    public static function getters()
    {
        return self::$getters;
    }

    /**
     * The original name of the model.
     *
     * @return string
     */
    public function getModelName()
    {
        return self::$swaggerModelName;
    }

    

    

    /**
     * Associative array for storing property values
     *
     * @var mixed[]
     */
    protected $container = [];

    /**
     * Constructor
     *
     * @param mixed[] $data Associated array of property values
     *                      initializing the model
     */
    public function __construct(array $data = null)
    {
        $this->container['account'] = isset($data['account']) ? $data['account'] : null;
        $this->container['currency'] = isset($data['currency']) ? $data['currency'] : null;
        $this->container['prev_payout'] = isset($data['prev_payout']) ? $data['prev_payout'] : null;
        $this->container['prev_turnover'] = isset($data['prev_turnover']) ? $data['prev_turnover'] : null;
        $this->container['prev_comm'] = isset($data['prev_comm']) ? $data['prev_comm'] : null;
        $this->container['prev_timestamp'] = isset($data['prev_timestamp']) ? $data['prev_timestamp'] : null;
        $this->container['exec_turnover'] = isset($data['exec_turnover']) ? $data['exec_turnover'] : null;
        $this->container['exec_comm'] = isset($data['exec_comm']) ? $data['exec_comm'] : null;
        $this->container['total_referrals'] = isset($data['total_referrals']) ? $data['total_referrals'] : null;
        $this->container['total_turnover'] = isset($data['total_turnover']) ? $data['total_turnover'] : null;
        $this->container['total_comm'] = isset($data['total_comm']) ? $data['total_comm'] : null;
        $this->container['payout_pcnt'] = isset($data['payout_pcnt']) ? $data['payout_pcnt'] : null;
        $this->container['pending_payout'] = isset($data['pending_payout']) ? $data['pending_payout'] : null;
        $this->container['timestamp'] = isset($data['timestamp']) ? $data['timestamp'] : null;
        $this->container['referrer_account'] = isset($data['referrer_account']) ? $data['referrer_account'] : null;
    }

    /**
     * Show all the invalid properties with reasons.
     *
     * @return array invalid properties with reasons
     */
    public function listInvalidProperties()
    {
        $invalidProperties = [];

        if ($this->container['account'] === null) {
            $invalidProperties[] = "'account' can't be null";
        }
        if ($this->container['currency'] === null) {
            $invalidProperties[] = "'currency' can't be null";
        }
        return $invalidProperties;
    }

    /**
     * Validate all the properties in the model
     * return true if all passed
     *
     * @return bool True if all properties are valid
     */
    public function valid()
    {
        return count($this->listInvalidProperties()) === 0;
    }


    /**
     * Gets account
     *
     * @return float
     */
    public function getAccount()
    {
        return $this->container['account'];
    }

    /**
     * Sets account
     *
     * @param float $account account
     *
     * @return $this
     */
    public function setAccount($account)
    {
        $this->container['account'] = $account;

        return $this;
    }

    /**
     * Gets currency
     *
     * @return string
     */
    public function getCurrency()
    {
        return $this->container['currency'];
    }

    /**
     * Sets currency
     *
     * @param string $currency currency
     *
     * @return $this
     */
    public function setCurrency($currency)
    {
        $this->container['currency'] = $currency;

        return $this;
    }

    /**
     * Gets prev_payout
     *
     * @return float
     */
    public function getPrevPayout()
    {
        return $this->container['prev_payout'];
    }

    /**
     * Sets prev_payout
     *
     * @param float $prev_payout prev_payout
     *
     * @return $this
     */
    public function setPrevPayout($prev_payout)
    {
        $this->container['prev_payout'] = $prev_payout;

        return $this;
    }

    /**
     * Gets prev_turnover
     *
     * @return float
     */
    public function getPrevTurnover()
    {
        return $this->container['prev_turnover'];
    }

    /**
     * Sets prev_turnover
     *
     * @param float $prev_turnover prev_turnover
     *
     * @return $this
     */
    public function setPrevTurnover($prev_turnover)
    {
        $this->container['prev_turnover'] = $prev_turnover;

        return $this;
    }

    /**
     * Gets prev_comm
     *
     * @return float
     */
    public function getPrevComm()
    {
        return $this->container['prev_comm'];
    }

    /**
     * Sets prev_comm
     *
     * @param float $prev_comm prev_comm
     *
     * @return $this
     */
    public function setPrevComm($prev_comm)
    {
        $this->container['prev_comm'] = $prev_comm;

        return $this;
    }

    /**
     * Gets prev_timestamp
     *
     * @return \DateTime
     */
    public function getPrevTimestamp()
    {
        return $this->container['prev_timestamp'];
    }

    /**
     * Sets prev_timestamp
     *
     * @param \DateTime $prev_timestamp prev_timestamp
     *
     * @return $this
     */
    public function setPrevTimestamp($prev_timestamp)
    {
        $this->container['prev_timestamp'] = $prev_timestamp;

        return $this;
    }

    /**
     * Gets exec_turnover
     *
     * @return float
     */
    public function getExecTurnover()
    {
        return $this->container['exec_turnover'];
    }

    /**
     * Sets exec_turnover
     *
     * @param float $exec_turnover exec_turnover
     *
     * @return $this
     */
    public function setExecTurnover($exec_turnover)
    {
        $this->container['exec_turnover'] = $exec_turnover;

        return $this;
    }

    /**
     * Gets exec_comm
     *
     * @return float
     */
    public function getExecComm()
    {
        return $this->container['exec_comm'];
    }

    /**
     * Sets exec_comm
     *
     * @param float $exec_comm exec_comm
     *
     * @return $this
     */
    public function setExecComm($exec_comm)
    {
        $this->container['exec_comm'] = $exec_comm;

        return $this;
    }

    /**
     * Gets total_referrals
     *
     * @return float
     */
    public function getTotalReferrals()
    {
        return $this->container['total_referrals'];
    }

    /**
     * Sets total_referrals
     *
     * @param float $total_referrals total_referrals
     *
     * @return $this
     */
    public function setTotalReferrals($total_referrals)
    {
        $this->container['total_referrals'] = $total_referrals;

        return $this;
    }

    /**
     * Gets total_turnover
     *
     * @return float
     */
    public function getTotalTurnover()
    {
        return $this->container['total_turnover'];
    }

    /**
     * Sets total_turnover
     *
     * @param float $total_turnover total_turnover
     *
     * @return $this
     */
    public function setTotalTurnover($total_turnover)
    {
        $this->container['total_turnover'] = $total_turnover;

        return $this;
    }

    /**
     * Gets total_comm
     *
     * @return float
     */
    public function getTotalComm()
    {
        return $this->container['total_comm'];
    }

    /**
     * Sets total_comm
     *
     * @param float $total_comm total_comm
     *
     * @return $this
     */
    public function setTotalComm($total_comm)
    {
        $this->container['total_comm'] = $total_comm;

        return $this;
    }

    /**
     * Gets payout_pcnt
     *
     * @return double
     */
    public function getPayoutPcnt()
    {
        return $this->container['payout_pcnt'];
    }

    /**
     * Sets payout_pcnt
     *
     * @param double $payout_pcnt payout_pcnt
     *
     * @return $this
     */
    public function setPayoutPcnt($payout_pcnt)
    {
        $this->container['payout_pcnt'] = $payout_pcnt;

        return $this;
    }

    /**
     * Gets pending_payout
     *
     * @return float
     */
    public function getPendingPayout()
    {
        return $this->container['pending_payout'];
    }

    /**
     * Sets pending_payout
     *
     * @param float $pending_payout pending_payout
     *
     * @return $this
     */
    public function setPendingPayout($pending_payout)
    {
        $this->container['pending_payout'] = $pending_payout;

        return $this;
    }

    /**
     * Gets timestamp
     *
     * @return \DateTime
     */
    public function getTimestamp()
    {
        return $this->container['timestamp'];
    }

    /**
     * Sets timestamp
     *
     * @param \DateTime $timestamp timestamp
     *
     * @return $this
     */
    public function setTimestamp($timestamp)
    {
        $this->container['timestamp'] = $timestamp;

        return $this;
    }

    /**
     * Gets referrer_account
     *
     * @return double
     */
    public function getReferrerAccount()
    {
        return $this->container['referrer_account'];
    }

    /**
     * Sets referrer_account
     *
     * @param double $referrer_account referrer_account
     *
     * @return $this
     */
    public function setReferrerAccount($referrer_account)
    {
        $this->container['referrer_account'] = $referrer_account;

        return $this;
    }
    /**
     * Returns true if offset exists. False otherwise.
     *
     * @param integer $offset Offset
     *
     * @return boolean
     */
    public function offsetExists($offset)
    {
        return isset($this->container[$offset]);
    }

    /**
     * Gets offset.
     *
     * @param integer $offset Offset
     *
     * @return mixed
     */
    public function offsetGet($offset)
    {
        return isset($this->container[$offset]) ? $this->container[$offset] : null;
    }

    /**
     * Sets value based on offset.
     *
     * @param integer $offset Offset
     * @param mixed   $value  Value to be set
     *
     * @return void
     */
    public function offsetSet($offset, $value)
    {
        if (is_null($offset)) {
            $this->container[] = $value;
        } else {
            $this->container[$offset] = $value;
        }
    }

    /**
     * Unsets offset.
     *
     * @param integer $offset Offset
     *
     * @return void
     */
    public function offsetUnset($offset)
    {
        unset($this->container[$offset]);
    }

    /**
     * Gets the string presentation of the object
     *
     * @return string
     */
    public function __toString()
    {
        if (defined('JSON_PRETTY_PRINT')) { // use JSON pretty print
            return json_encode(
                ObjectSerializer::sanitizeForSerialization($this),
                JSON_PRETTY_PRINT
            );
        }

        return json_encode(ObjectSerializer::sanitizeForSerialization($this));
    }
}

