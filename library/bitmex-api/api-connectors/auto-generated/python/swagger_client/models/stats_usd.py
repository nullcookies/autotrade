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


class StatsUSD(object):
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
        'root_symbol': 'str',
        'currency': 'str',
        'turnover24h': 'float',
        'turnover30d': 'float',
        'turnover365d': 'float',
        'turnover': 'float'
    }

    attribute_map = {
        'root_symbol': 'rootSymbol',
        'currency': 'currency',
        'turnover24h': 'turnover24h',
        'turnover30d': 'turnover30d',
        'turnover365d': 'turnover365d',
        'turnover': 'turnover'
    }

    def __init__(self, root_symbol=None, currency=None, turnover24h=None, turnover30d=None, turnover365d=None, turnover=None):  # noqa: E501
        """StatsUSD - a model defined in Swagger"""  # noqa: E501

        self._root_symbol = None
        self._currency = None
        self._turnover24h = None
        self._turnover30d = None
        self._turnover365d = None
        self._turnover = None
        self.discriminator = None

        self.root_symbol = root_symbol
        if currency is not None:
            self.currency = currency
        if turnover24h is not None:
            self.turnover24h = turnover24h
        if turnover30d is not None:
            self.turnover30d = turnover30d
        if turnover365d is not None:
            self.turnover365d = turnover365d
        if turnover is not None:
            self.turnover = turnover

    @property
    def root_symbol(self):
        """Gets the root_symbol of this StatsUSD.  # noqa: E501


        :return: The root_symbol of this StatsUSD.  # noqa: E501
        :rtype: str
        """
        return self._root_symbol

    @root_symbol.setter
    def root_symbol(self, root_symbol):
        """Sets the root_symbol of this StatsUSD.


        :param root_symbol: The root_symbol of this StatsUSD.  # noqa: E501
        :type: str
        """
        if root_symbol is None:
            raise ValueError("Invalid value for `root_symbol`, must not be `None`")  # noqa: E501

        self._root_symbol = root_symbol

    @property
    def currency(self):
        """Gets the currency of this StatsUSD.  # noqa: E501


        :return: The currency of this StatsUSD.  # noqa: E501
        :rtype: str
        """
        return self._currency

    @currency.setter
    def currency(self, currency):
        """Sets the currency of this StatsUSD.


        :param currency: The currency of this StatsUSD.  # noqa: E501
        :type: str
        """

        self._currency = currency

    @property
    def turnover24h(self):
        """Gets the turnover24h of this StatsUSD.  # noqa: E501


        :return: The turnover24h of this StatsUSD.  # noqa: E501
        :rtype: float
        """
        return self._turnover24h

    @turnover24h.setter
    def turnover24h(self, turnover24h):
        """Sets the turnover24h of this StatsUSD.


        :param turnover24h: The turnover24h of this StatsUSD.  # noqa: E501
        :type: float
        """

        self._turnover24h = turnover24h

    @property
    def turnover30d(self):
        """Gets the turnover30d of this StatsUSD.  # noqa: E501


        :return: The turnover30d of this StatsUSD.  # noqa: E501
        :rtype: float
        """
        return self._turnover30d

    @turnover30d.setter
    def turnover30d(self, turnover30d):
        """Sets the turnover30d of this StatsUSD.


        :param turnover30d: The turnover30d of this StatsUSD.  # noqa: E501
        :type: float
        """

        self._turnover30d = turnover30d

    @property
    def turnover365d(self):
        """Gets the turnover365d of this StatsUSD.  # noqa: E501


        :return: The turnover365d of this StatsUSD.  # noqa: E501
        :rtype: float
        """
        return self._turnover365d

    @turnover365d.setter
    def turnover365d(self, turnover365d):
        """Sets the turnover365d of this StatsUSD.


        :param turnover365d: The turnover365d of this StatsUSD.  # noqa: E501
        :type: float
        """

        self._turnover365d = turnover365d

    @property
    def turnover(self):
        """Gets the turnover of this StatsUSD.  # noqa: E501


        :return: The turnover of this StatsUSD.  # noqa: E501
        :rtype: float
        """
        return self._turnover

    @turnover.setter
    def turnover(self, turnover):
        """Sets the turnover of this StatsUSD.


        :param turnover: The turnover of this StatsUSD.  # noqa: E501
        :type: float
        """

        self._turnover = turnover

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
        if not isinstance(other, StatsUSD):
            return False

        return self.__dict__ == other.__dict__

    def __ne__(self, other):
        """Returns true if both objects are not equal"""
        return not self == other