# coding: utf-8

"""
    BitMEX API

    ## REST API for the BitMEX Trading Platform  [View Changelog](/app/apiChangelog)    #### Getting Started  Base URI: [https://www.bitmex.com/api/v1](/api/v1)  ##### Fetching Data  All REST endpoints are documented below. You can try out any query right from this interface.  Most table queries accept `count`, `start`, and `reverse` params. Set `reverse=true` to get rows newest-first.  Additional documentation regarding filters, timestamps, and authentication is available in [the main API documentation](/app/restAPI).  *All* table data is available via the [Websocket](/app/wsAPI). We highly recommend using the socket if you want to have the quickest possible data without being subject to ratelimits.  ##### Return Types  By default, all data is returned as JSON. Send `?_format=csv` to get CSV data or `?_format=xml` to get XML data.  ##### Trade Data Queries  *This is only a small subset of what is available, to get you started.*  Fill in the parameters and click the `Try it out!` button to try any of these queries.  * [Pricing Data](#!/Quote/Quote_get)  * [Trade Data](#!/Trade/Trade_get)  * [OrderBook Data](#!/OrderBook/OrderBook_getL2)  * [Settlement Data](#!/Settlement/Settlement_get)  * [Exchange Statistics](#!/Stats/Stats_history)  Every function of the BitMEX.com platform is exposed here and documented. Many more functions are available.  ##### Swagger Specification  [⇩ Download Swagger JSON](swagger.json)    ## All API Endpoints  Click to expand a section.   # noqa: E501

    OpenAPI spec version: 1.2.0
    Contact: support@bitmex.com
    Generated by: https://github.com/swagger-api/swagger-codegen.git
"""


import pprint
import re  # noqa: F401

import six


class Funding(object):
    """NOTE: This class is auto generated by the swagger code generator program.

    Do not edit the class manually.
    """

    """
    Attributes:
      swagger_types (dict): The key is attribute name
                            and the value is attribute type.
      attribute_map (dict): The key is attribute name
                            and the value is json key in definition.
    """
    swagger_types = {
        'timestamp': 'datetime',
        'symbol': 'str',
        'funding_interval': 'datetime',
        'funding_rate': 'float',
        'funding_rate_daily': 'float'
    }

    attribute_map = {
        'timestamp': 'timestamp',
        'symbol': 'symbol',
        'funding_interval': 'fundingInterval',
        'funding_rate': 'fundingRate',
        'funding_rate_daily': 'fundingRateDaily'
    }

    def __init__(self, timestamp=None, symbol=None, funding_interval=None, funding_rate=None, funding_rate_daily=None):  # noqa: E501
        """Funding - a model defined in Swagger"""  # noqa: E501

        self._timestamp = None
        self._symbol = None
        self._funding_interval = None
        self._funding_rate = None
        self._funding_rate_daily = None
        self.discriminator = None

        self.timestamp = timestamp
        self.symbol = symbol
        if funding_interval is not None:
            self.funding_interval = funding_interval
        if funding_rate is not None:
            self.funding_rate = funding_rate
        if funding_rate_daily is not None:
            self.funding_rate_daily = funding_rate_daily

    @property
    def timestamp(self):
        """Gets the timestamp of this Funding.  # noqa: E501


        :return: The timestamp of this Funding.  # noqa: E501
        :rtype: datetime
        """
        return self._timestamp

    @timestamp.setter
    def timestamp(self, timestamp):
        """Sets the timestamp of this Funding.


        :param timestamp: The timestamp of this Funding.  # noqa: E501
        :type: datetime
        """
        if timestamp is None:
            raise ValueError("Invalid value for `timestamp`, must not be `None`")  # noqa: E501

        self._timestamp = timestamp

    @property
    def symbol(self):
        """Gets the symbol of this Funding.  # noqa: E501


        :return: The symbol of this Funding.  # noqa: E501
        :rtype: str
        """
        return self._symbol

    @symbol.setter
    def symbol(self, symbol):
        """Sets the symbol of this Funding.


        :param symbol: The symbol of this Funding.  # noqa: E501
        :type: str
        """
        if symbol is None:
            raise ValueError("Invalid value for `symbol`, must not be `None`")  # noqa: E501

        self._symbol = symbol

    @property
    def funding_interval(self):
        """Gets the funding_interval of this Funding.  # noqa: E501


        :return: The funding_interval of this Funding.  # noqa: E501
        :rtype: datetime
        """
        return self._funding_interval

    @funding_interval.setter
    def funding_interval(self, funding_interval):
        """Sets the funding_interval of this Funding.


        :param funding_interval: The funding_interval of this Funding.  # noqa: E501
        :type: datetime
        """

        self._funding_interval = funding_interval

    @property
    def funding_rate(self):
        """Gets the funding_rate of this Funding.  # noqa: E501


        :return: The funding_rate of this Funding.  # noqa: E501
        :rtype: float
        """
        return self._funding_rate

    @funding_rate.setter
    def funding_rate(self, funding_rate):
        """Sets the funding_rate of this Funding.


        :param funding_rate: The funding_rate of this Funding.  # noqa: E501
        :type: float
        """

        self._funding_rate = funding_rate

    @property
    def funding_rate_daily(self):
        """Gets the funding_rate_daily of this Funding.  # noqa: E501


        :return: The funding_rate_daily of this Funding.  # noqa: E501
        :rtype: float
        """
        return self._funding_rate_daily

    @funding_rate_daily.setter
    def funding_rate_daily(self, funding_rate_daily):
        """Sets the funding_rate_daily of this Funding.


        :param funding_rate_daily: The funding_rate_daily of this Funding.  # noqa: E501
        :type: float
        """

        self._funding_rate_daily = funding_rate_daily

    def to_dict(self):
        """Returns the model properties as a dict"""
        result = {}

        for attr, _ in six.iteritems(self.swagger_types):
            value = getattr(self, attr)
            if isinstance(value, list):
                result[attr] = list(map(
                    lambda x: x.to_dict() if hasattr(x, "to_dict") else x,
                    value
                ))
            elif hasattr(value, "to_dict"):
                result[attr] = value.to_dict()
            elif isinstance(value, dict):
                result[attr] = dict(map(
                    lambda item: (item[0], item[1].to_dict())
                    if hasattr(item[1], "to_dict") else item,
                    value.items()
                ))
            else:
                result[attr] = value

        return result

    def to_str(self):
        """Returns the string representation of the model"""
        return pprint.pformat(self.to_dict())

    def __repr__(self):
        """For `print` and `pprint`"""
        return self.to_str()

    def __eq__(self, other):
        """Returns true if both objects are equal"""
        if not isinstance(other, Funding):
            return False

        return self.__dict__ == other.__dict__

    def __ne__(self, other):
        """Returns true if both objects are not equal"""
        return not self == other
