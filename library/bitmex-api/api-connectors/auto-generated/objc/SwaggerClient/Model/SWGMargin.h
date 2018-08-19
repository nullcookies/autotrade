#import <Foundation/Foundation.h>
#import "SWGObject.h"

/**
* BitMEX API
* ## REST API for the BitMEX Trading Platform  [View Changelog](/app/apiChangelog)    #### Getting Started  Base URI: [https://www.bitmex.com/api/v1](/api/v1)  ##### Fetching Data  All REST endpoints are documented below. You can try out any query right from this interface.  Most table queries accept `count`, `start`, and `reverse` params. Set `reverse=true` to get rows newest-first.  Additional documentation regarding filters, timestamps, and authentication is available in [the main API documentation](/app/restAPI).  *All* table data is available via the [Websocket](/app/wsAPI). We highly recommend using the socket if you want to have the quickest possible data without being subject to ratelimits.  ##### Return Types  By default, all data is returned as JSON. Send `?_format=csv` to get CSV data or `?_format=xml` to get XML data.  ##### Trade Data Queries  *This is only a small subset of what is available, to get you started.*  Fill in the parameters and click the `Try it out!` button to try any of these queries.  * [Pricing Data](#!/Quote/Quote_get)  * [Trade Data](#!/Trade/Trade_get)  * [OrderBook Data](#!/OrderBook/OrderBook_getL2)  * [Settlement Data](#!/Settlement/Settlement_get)  * [Exchange Statistics](#!/Stats/Stats_history)  Every function of the BitMEX.com platform is exposed here and documented. Many more functions are available.  ##### Swagger Specification  [⇩ Download Swagger JSON](swagger.json)    ## All API Endpoints  Click to expand a section. 
*
* OpenAPI spec version: 1.2.0
* Contact: support@bitmex.com
*
* NOTE: This class is auto generated by the swagger code generator program.
* https://github.com/swagger-api/swagger-codegen.git
* Do not edit the class manually.
*/





@protocol SWGMargin
@end

@interface SWGMargin : SWGObject


@property(nonatomic) NSNumber* account;

@property(nonatomic) NSString* currency;

@property(nonatomic) NSNumber* riskLimit;

@property(nonatomic) NSString* prevState;

@property(nonatomic) NSString* state;

@property(nonatomic) NSString* action;

@property(nonatomic) NSNumber* amount;

@property(nonatomic) NSNumber* pendingCredit;

@property(nonatomic) NSNumber* pendingDebit;

@property(nonatomic) NSNumber* confirmedDebit;

@property(nonatomic) NSNumber* prevRealisedPnl;

@property(nonatomic) NSNumber* prevUnrealisedPnl;

@property(nonatomic) NSNumber* grossComm;

@property(nonatomic) NSNumber* grossOpenCost;

@property(nonatomic) NSNumber* grossOpenPremium;

@property(nonatomic) NSNumber* grossExecCost;

@property(nonatomic) NSNumber* grossMarkValue;

@property(nonatomic) NSNumber* riskValue;

@property(nonatomic) NSNumber* taxableMargin;

@property(nonatomic) NSNumber* initMargin;

@property(nonatomic) NSNumber* maintMargin;

@property(nonatomic) NSNumber* sessionMargin;

@property(nonatomic) NSNumber* targetExcessMargin;

@property(nonatomic) NSNumber* varMargin;

@property(nonatomic) NSNumber* realisedPnl;

@property(nonatomic) NSNumber* unrealisedPnl;

@property(nonatomic) NSNumber* indicativeTax;

@property(nonatomic) NSNumber* unrealisedProfit;

@property(nonatomic) NSNumber* syntheticMargin;

@property(nonatomic) NSNumber* walletBalance;

@property(nonatomic) NSNumber* marginBalance;

@property(nonatomic) NSNumber* marginBalancePcnt;

@property(nonatomic) NSNumber* marginLeverage;

@property(nonatomic) NSNumber* marginUsedPcnt;

@property(nonatomic) NSNumber* excessMargin;

@property(nonatomic) NSNumber* excessMarginPcnt;

@property(nonatomic) NSNumber* availableMargin;

@property(nonatomic) NSNumber* withdrawableMargin;

@property(nonatomic) NSDate* timestamp;

@property(nonatomic) NSNumber* grossLastValue;

@property(nonatomic) NSNumber* commission;

@end
