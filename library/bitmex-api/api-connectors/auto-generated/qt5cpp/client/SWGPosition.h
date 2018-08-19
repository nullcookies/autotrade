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

/*
 * SWGPosition.h
 *
 * Summary of Open and Closed Positions
 */

#ifndef SWGPosition_H_
#define SWGPosition_H_

#include <QJsonObject>


#include "SWGNumber.h"
#include <QDateTime>
#include <QString>

#include "SWGObject.h"

namespace Swagger {

class SWGPosition: public SWGObject {
public:
    SWGPosition();
    SWGPosition(QString json);
    ~SWGPosition();
    void init();
    void cleanup();

    QString asJson () override;
    QJsonObject asJsonObject() override;
    void fromJsonObject(QJsonObject json) override;
    SWGPosition* fromJson(QString jsonString) override;

    SWGNumber* getAccount();
    void setAccount(SWGNumber* account);

    QString* getSymbol();
    void setSymbol(QString* symbol);

    QString* getCurrency();
    void setCurrency(QString* currency);

    QString* getUnderlying();
    void setUnderlying(QString* underlying);

    QString* getQuoteCurrency();
    void setQuoteCurrency(QString* quote_currency);

    double getCommission();
    void setCommission(double commission);

    double getInitMarginReq();
    void setInitMarginReq(double init_margin_req);

    double getMaintMarginReq();
    void setMaintMarginReq(double maint_margin_req);

    SWGNumber* getRiskLimit();
    void setRiskLimit(SWGNumber* risk_limit);

    double getLeverage();
    void setLeverage(double leverage);

    bool isCrossMargin();
    void setCrossMargin(bool cross_margin);

    double getDeleveragePercentile();
    void setDeleveragePercentile(double deleverage_percentile);

    SWGNumber* getRebalancedPnl();
    void setRebalancedPnl(SWGNumber* rebalanced_pnl);

    SWGNumber* getPrevRealisedPnl();
    void setPrevRealisedPnl(SWGNumber* prev_realised_pnl);

    SWGNumber* getPrevUnrealisedPnl();
    void setPrevUnrealisedPnl(SWGNumber* prev_unrealised_pnl);

    double getPrevClosePrice();
    void setPrevClosePrice(double prev_close_price);

    QDateTime* getOpeningTimestamp();
    void setOpeningTimestamp(QDateTime* opening_timestamp);

    SWGNumber* getOpeningQty();
    void setOpeningQty(SWGNumber* opening_qty);

    SWGNumber* getOpeningCost();
    void setOpeningCost(SWGNumber* opening_cost);

    SWGNumber* getOpeningComm();
    void setOpeningComm(SWGNumber* opening_comm);

    SWGNumber* getOpenOrderBuyQty();
    void setOpenOrderBuyQty(SWGNumber* open_order_buy_qty);

    SWGNumber* getOpenOrderBuyCost();
    void setOpenOrderBuyCost(SWGNumber* open_order_buy_cost);

    SWGNumber* getOpenOrderBuyPremium();
    void setOpenOrderBuyPremium(SWGNumber* open_order_buy_premium);

    SWGNumber* getOpenOrderSellQty();
    void setOpenOrderSellQty(SWGNumber* open_order_sell_qty);

    SWGNumber* getOpenOrderSellCost();
    void setOpenOrderSellCost(SWGNumber* open_order_sell_cost);

    SWGNumber* getOpenOrderSellPremium();
    void setOpenOrderSellPremium(SWGNumber* open_order_sell_premium);

    SWGNumber* getExecBuyQty();
    void setExecBuyQty(SWGNumber* exec_buy_qty);

    SWGNumber* getExecBuyCost();
    void setExecBuyCost(SWGNumber* exec_buy_cost);

    SWGNumber* getExecSellQty();
    void setExecSellQty(SWGNumber* exec_sell_qty);

    SWGNumber* getExecSellCost();
    void setExecSellCost(SWGNumber* exec_sell_cost);

    SWGNumber* getExecQty();
    void setExecQty(SWGNumber* exec_qty);

    SWGNumber* getExecCost();
    void setExecCost(SWGNumber* exec_cost);

    SWGNumber* getExecComm();
    void setExecComm(SWGNumber* exec_comm);

    QDateTime* getCurrentTimestamp();
    void setCurrentTimestamp(QDateTime* current_timestamp);

    SWGNumber* getCurrentQty();
    void setCurrentQty(SWGNumber* current_qty);

    SWGNumber* getCurrentCost();
    void setCurrentCost(SWGNumber* current_cost);

    SWGNumber* getCurrentComm();
    void setCurrentComm(SWGNumber* current_comm);

    SWGNumber* getRealisedCost();
    void setRealisedCost(SWGNumber* realised_cost);

    SWGNumber* getUnrealisedCost();
    void setUnrealisedCost(SWGNumber* unrealised_cost);

    SWGNumber* getGrossOpenCost();
    void setGrossOpenCost(SWGNumber* gross_open_cost);

    SWGNumber* getGrossOpenPremium();
    void setGrossOpenPremium(SWGNumber* gross_open_premium);

    SWGNumber* getGrossExecCost();
    void setGrossExecCost(SWGNumber* gross_exec_cost);

    bool isIsOpen();
    void setIsOpen(bool is_open);

    double getMarkPrice();
    void setMarkPrice(double mark_price);

    SWGNumber* getMarkValue();
    void setMarkValue(SWGNumber* mark_value);

    SWGNumber* getRiskValue();
    void setRiskValue(SWGNumber* risk_value);

    double getHomeNotional();
    void setHomeNotional(double home_notional);

    double getForeignNotional();
    void setForeignNotional(double foreign_notional);

    QString* getPosState();
    void setPosState(QString* pos_state);

    SWGNumber* getPosCost();
    void setPosCost(SWGNumber* pos_cost);

    SWGNumber* getPosCost2();
    void setPosCost2(SWGNumber* pos_cost2);

    SWGNumber* getPosCross();
    void setPosCross(SWGNumber* pos_cross);

    SWGNumber* getPosInit();
    void setPosInit(SWGNumber* pos_init);

    SWGNumber* getPosComm();
    void setPosComm(SWGNumber* pos_comm);

    SWGNumber* getPosLoss();
    void setPosLoss(SWGNumber* pos_loss);

    SWGNumber* getPosMargin();
    void setPosMargin(SWGNumber* pos_margin);

    SWGNumber* getPosMaint();
    void setPosMaint(SWGNumber* pos_maint);

    SWGNumber* getPosAllowance();
    void setPosAllowance(SWGNumber* pos_allowance);

    SWGNumber* getTaxableMargin();
    void setTaxableMargin(SWGNumber* taxable_margin);

    SWGNumber* getInitMargin();
    void setInitMargin(SWGNumber* init_margin);

    SWGNumber* getMaintMargin();
    void setMaintMargin(SWGNumber* maint_margin);

    SWGNumber* getSessionMargin();
    void setSessionMargin(SWGNumber* session_margin);

    SWGNumber* getTargetExcessMargin();
    void setTargetExcessMargin(SWGNumber* target_excess_margin);

    SWGNumber* getVarMargin();
    void setVarMargin(SWGNumber* var_margin);

    SWGNumber* getRealisedGrossPnl();
    void setRealisedGrossPnl(SWGNumber* realised_gross_pnl);

    SWGNumber* getRealisedTax();
    void setRealisedTax(SWGNumber* realised_tax);

    SWGNumber* getRealisedPnl();
    void setRealisedPnl(SWGNumber* realised_pnl);

    SWGNumber* getUnrealisedGrossPnl();
    void setUnrealisedGrossPnl(SWGNumber* unrealised_gross_pnl);

    SWGNumber* getLongBankrupt();
    void setLongBankrupt(SWGNumber* long_bankrupt);

    SWGNumber* getShortBankrupt();
    void setShortBankrupt(SWGNumber* short_bankrupt);

    SWGNumber* getTaxBase();
    void setTaxBase(SWGNumber* tax_base);

    double getIndicativeTaxRate();
    void setIndicativeTaxRate(double indicative_tax_rate);

    SWGNumber* getIndicativeTax();
    void setIndicativeTax(SWGNumber* indicative_tax);

    SWGNumber* getUnrealisedTax();
    void setUnrealisedTax(SWGNumber* unrealised_tax);

    SWGNumber* getUnrealisedPnl();
    void setUnrealisedPnl(SWGNumber* unrealised_pnl);

    double getUnrealisedPnlPcnt();
    void setUnrealisedPnlPcnt(double unrealised_pnl_pcnt);

    double getUnrealisedRoePcnt();
    void setUnrealisedRoePcnt(double unrealised_roe_pcnt);

    double getSimpleQty();
    void setSimpleQty(double simple_qty);

    double getSimpleCost();
    void setSimpleCost(double simple_cost);

    double getSimpleValue();
    void setSimpleValue(double simple_value);

    double getSimplePnl();
    void setSimplePnl(double simple_pnl);

    double getSimplePnlPcnt();
    void setSimplePnlPcnt(double simple_pnl_pcnt);

    double getAvgCostPrice();
    void setAvgCostPrice(double avg_cost_price);

    double getAvgEntryPrice();
    void setAvgEntryPrice(double avg_entry_price);

    double getBreakEvenPrice();
    void setBreakEvenPrice(double break_even_price);

    double getMarginCallPrice();
    void setMarginCallPrice(double margin_call_price);

    double getLiquidationPrice();
    void setLiquidationPrice(double liquidation_price);

    double getBankruptPrice();
    void setBankruptPrice(double bankrupt_price);

    QDateTime* getTimestamp();
    void setTimestamp(QDateTime* timestamp);

    double getLastPrice();
    void setLastPrice(double last_price);

    SWGNumber* getLastValue();
    void setLastValue(SWGNumber* last_value);


    virtual bool isSet() override;

private:
    SWGNumber* account;
    bool m_account_isSet;

    QString* symbol;
    bool m_symbol_isSet;

    QString* currency;
    bool m_currency_isSet;

    QString* underlying;
    bool m_underlying_isSet;

    QString* quote_currency;
    bool m_quote_currency_isSet;

    double commission;
    bool m_commission_isSet;

    double init_margin_req;
    bool m_init_margin_req_isSet;

    double maint_margin_req;
    bool m_maint_margin_req_isSet;

    SWGNumber* risk_limit;
    bool m_risk_limit_isSet;

    double leverage;
    bool m_leverage_isSet;

    bool cross_margin;
    bool m_cross_margin_isSet;

    double deleverage_percentile;
    bool m_deleverage_percentile_isSet;

    SWGNumber* rebalanced_pnl;
    bool m_rebalanced_pnl_isSet;

    SWGNumber* prev_realised_pnl;
    bool m_prev_realised_pnl_isSet;

    SWGNumber* prev_unrealised_pnl;
    bool m_prev_unrealised_pnl_isSet;

    double prev_close_price;
    bool m_prev_close_price_isSet;

    QDateTime* opening_timestamp;
    bool m_opening_timestamp_isSet;

    SWGNumber* opening_qty;
    bool m_opening_qty_isSet;

    SWGNumber* opening_cost;
    bool m_opening_cost_isSet;

    SWGNumber* opening_comm;
    bool m_opening_comm_isSet;

    SWGNumber* open_order_buy_qty;
    bool m_open_order_buy_qty_isSet;

    SWGNumber* open_order_buy_cost;
    bool m_open_order_buy_cost_isSet;

    SWGNumber* open_order_buy_premium;
    bool m_open_order_buy_premium_isSet;

    SWGNumber* open_order_sell_qty;
    bool m_open_order_sell_qty_isSet;

    SWGNumber* open_order_sell_cost;
    bool m_open_order_sell_cost_isSet;

    SWGNumber* open_order_sell_premium;
    bool m_open_order_sell_premium_isSet;

    SWGNumber* exec_buy_qty;
    bool m_exec_buy_qty_isSet;

    SWGNumber* exec_buy_cost;
    bool m_exec_buy_cost_isSet;

    SWGNumber* exec_sell_qty;
    bool m_exec_sell_qty_isSet;

    SWGNumber* exec_sell_cost;
    bool m_exec_sell_cost_isSet;

    SWGNumber* exec_qty;
    bool m_exec_qty_isSet;

    SWGNumber* exec_cost;
    bool m_exec_cost_isSet;

    SWGNumber* exec_comm;
    bool m_exec_comm_isSet;

    QDateTime* current_timestamp;
    bool m_current_timestamp_isSet;

    SWGNumber* current_qty;
    bool m_current_qty_isSet;

    SWGNumber* current_cost;
    bool m_current_cost_isSet;

    SWGNumber* current_comm;
    bool m_current_comm_isSet;

    SWGNumber* realised_cost;
    bool m_realised_cost_isSet;

    SWGNumber* unrealised_cost;
    bool m_unrealised_cost_isSet;

    SWGNumber* gross_open_cost;
    bool m_gross_open_cost_isSet;

    SWGNumber* gross_open_premium;
    bool m_gross_open_premium_isSet;

    SWGNumber* gross_exec_cost;
    bool m_gross_exec_cost_isSet;

    bool is_open;
    bool m_is_open_isSet;

    double mark_price;
    bool m_mark_price_isSet;

    SWGNumber* mark_value;
    bool m_mark_value_isSet;

    SWGNumber* risk_value;
    bool m_risk_value_isSet;

    double home_notional;
    bool m_home_notional_isSet;

    double foreign_notional;
    bool m_foreign_notional_isSet;

    QString* pos_state;
    bool m_pos_state_isSet;

    SWGNumber* pos_cost;
    bool m_pos_cost_isSet;

    SWGNumber* pos_cost2;
    bool m_pos_cost2_isSet;

    SWGNumber* pos_cross;
    bool m_pos_cross_isSet;

    SWGNumber* pos_init;
    bool m_pos_init_isSet;

    SWGNumber* pos_comm;
    bool m_pos_comm_isSet;

    SWGNumber* pos_loss;
    bool m_pos_loss_isSet;

    SWGNumber* pos_margin;
    bool m_pos_margin_isSet;

    SWGNumber* pos_maint;
    bool m_pos_maint_isSet;

    SWGNumber* pos_allowance;
    bool m_pos_allowance_isSet;

    SWGNumber* taxable_margin;
    bool m_taxable_margin_isSet;

    SWGNumber* init_margin;
    bool m_init_margin_isSet;

    SWGNumber* maint_margin;
    bool m_maint_margin_isSet;

    SWGNumber* session_margin;
    bool m_session_margin_isSet;

    SWGNumber* target_excess_margin;
    bool m_target_excess_margin_isSet;

    SWGNumber* var_margin;
    bool m_var_margin_isSet;

    SWGNumber* realised_gross_pnl;
    bool m_realised_gross_pnl_isSet;

    SWGNumber* realised_tax;
    bool m_realised_tax_isSet;

    SWGNumber* realised_pnl;
    bool m_realised_pnl_isSet;

    SWGNumber* unrealised_gross_pnl;
    bool m_unrealised_gross_pnl_isSet;

    SWGNumber* long_bankrupt;
    bool m_long_bankrupt_isSet;

    SWGNumber* short_bankrupt;
    bool m_short_bankrupt_isSet;

    SWGNumber* tax_base;
    bool m_tax_base_isSet;

    double indicative_tax_rate;
    bool m_indicative_tax_rate_isSet;

    SWGNumber* indicative_tax;
    bool m_indicative_tax_isSet;

    SWGNumber* unrealised_tax;
    bool m_unrealised_tax_isSet;

    SWGNumber* unrealised_pnl;
    bool m_unrealised_pnl_isSet;

    double unrealised_pnl_pcnt;
    bool m_unrealised_pnl_pcnt_isSet;

    double unrealised_roe_pcnt;
    bool m_unrealised_roe_pcnt_isSet;

    double simple_qty;
    bool m_simple_qty_isSet;

    double simple_cost;
    bool m_simple_cost_isSet;

    double simple_value;
    bool m_simple_value_isSet;

    double simple_pnl;
    bool m_simple_pnl_isSet;

    double simple_pnl_pcnt;
    bool m_simple_pnl_pcnt_isSet;

    double avg_cost_price;
    bool m_avg_cost_price_isSet;

    double avg_entry_price;
    bool m_avg_entry_price_isSet;

    double break_even_price;
    bool m_break_even_price_isSet;

    double margin_call_price;
    bool m_margin_call_price_isSet;

    double liquidation_price;
    bool m_liquidation_price_isSet;

    double bankrupt_price;
    bool m_bankrupt_price_isSet;

    QDateTime* timestamp;
    bool m_timestamp_isSet;

    double last_price;
    bool m_last_price_isSet;

    SWGNumber* last_value;
    bool m_last_value_isSet;

};

}

#endif /* SWGPosition_H_ */
