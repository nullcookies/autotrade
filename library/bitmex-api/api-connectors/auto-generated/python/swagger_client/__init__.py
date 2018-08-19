# coding: utf-8

# flake8: noqa

"""
    BitMEX API

    ## REST API for the BitMEX Trading Platform  [View Changelog](/app/apiChangelog)    #### Getting Started  Base URI: [https://www.bitmex.com/api/v1](/api/v1)  ##### Fetching Data  All REST endpoints are documented below. You can try out any query right from this interface.  Most table queries accept `count`, `start`, and `reverse` params. Set `reverse=true` to get rows newest-first.  Additional documentation regarding filters, timestamps, and authentication is available in [the main API documentation](/app/restAPI).  *All* table data is available via the [Websocket](/app/wsAPI). We highly recommend using the socket if you want to have the quickest possible data without being subject to ratelimits.  ##### Return Types  By default, all data is returned as JSON. Send `?_format=csv` to get CSV data or `?_format=xml` to get XML data.  ##### Trade Data Queries  *This is only a small subset of what is available, to get you started.*  Fill in the parameters and click the `Try it out!` button to try any of these queries.  * [Pricing Data](#!/Quote/Quote_get)  * [Trade Data](#!/Trade/Trade_get)  * [OrderBook Data](#!/OrderBook/OrderBook_getL2)  * [Settlement Data](#!/Settlement/Settlement_get)  * [Exchange Statistics](#!/Stats/Stats_history)  Every function of the BitMEX.com platform is exposed here and documented. Many more functions are available.  ##### Swagger Specification  [⇩ Download Swagger JSON](swagger.json)    ## All API Endpoints  Click to expand a section.   # noqa: E501

    OpenAPI spec version: 1.2.0
    Contact: support@bitmex.com
    Generated by: https://github.com/swagger-api/swagger-codegen.git
"""


from __future__ import absolute_import

# import apis into sdk package
from swagger_client.api.api_key_api import APIKeyApi
from swagger_client.api.announcement_api import AnnouncementApi
from swagger_client.api.chat_api import ChatApi
from swagger_client.api.execution_api import ExecutionApi
from swagger_client.api.funding_api import FundingApi
from swagger_client.api.instrument_api import InstrumentApi
from swagger_client.api.insurance_api import InsuranceApi
from swagger_client.api.leaderboard_api import LeaderboardApi
from swagger_client.api.liquidation_api import LiquidationApi
from swagger_client.api.notification_api import NotificationApi
from swagger_client.api.order_api import OrderApi
from swagger_client.api.order_book_api import OrderBookApi
from swagger_client.api.position_api import PositionApi
from swagger_client.api.quote_api import QuoteApi
from swagger_client.api.schema_api import SchemaApi
from swagger_client.api.settlement_api import SettlementApi
from swagger_client.api.stats_api import StatsApi
from swagger_client.api.trade_api import TradeApi
from swagger_client.api.user_api import UserApi

# import ApiClient
from swagger_client.api_client import ApiClient
from swagger_client.configuration import Configuration
# import models into sdk package
from swagger_client.models.api_key import APIKey
from swagger_client.models.access_token import AccessToken
from swagger_client.models.affiliate import Affiliate
from swagger_client.models.announcement import Announcement
from swagger_client.models.chat import Chat
from swagger_client.models.chat_channel import ChatChannel
from swagger_client.models.connected_users import ConnectedUsers
from swagger_client.models.error import Error
from swagger_client.models.error_error import ErrorError
from swagger_client.models.execution import Execution
from swagger_client.models.funding import Funding
from swagger_client.models.index_composite import IndexComposite
from swagger_client.models.inline_response200 import InlineResponse200
from swagger_client.models.inline_response2001 import InlineResponse2001
from swagger_client.models.instrument import Instrument
from swagger_client.models.instrument_interval import InstrumentInterval
from swagger_client.models.insurance import Insurance
from swagger_client.models.leaderboard import Leaderboard
from swagger_client.models.liquidation import Liquidation
from swagger_client.models.margin import Margin
from swagger_client.models.notification import Notification
from swagger_client.models.order import Order
from swagger_client.models.order_book_l2 import OrderBookL2
from swagger_client.models.position import Position
from swagger_client.models.quote import Quote
from swagger_client.models.settlement import Settlement
from swagger_client.models.stats import Stats
from swagger_client.models.stats_history import StatsHistory
from swagger_client.models.stats_usd import StatsUSD
from swagger_client.models.trade import Trade
from swagger_client.models.trade_bin import TradeBin
from swagger_client.models.transaction import Transaction
from swagger_client.models.user import User
from swagger_client.models.user_commission import UserCommission
from swagger_client.models.user_preferences import UserPreferences
from swagger_client.models.wallet import Wallet
from swagger_client.models.x_any import XAny
