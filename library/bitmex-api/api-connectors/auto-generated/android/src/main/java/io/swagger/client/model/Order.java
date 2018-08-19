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

package io.swagger.client.model;

import java.math.BigDecimal;
import java.util.Date;
import io.swagger.annotations.*;
import com.google.gson.annotations.SerializedName;

/**
 * Placement, Cancellation, Amending, and History
 **/
@ApiModel(description = "Placement, Cancellation, Amending, and History")
public class Order {
  
  @SerializedName("orderID")
  private String orderID = null;
  @SerializedName("clOrdID")
  private String clOrdID = null;
  @SerializedName("clOrdLinkID")
  private String clOrdLinkID = null;
  @SerializedName("account")
  private BigDecimal account = null;
  @SerializedName("symbol")
  private String symbol = null;
  @SerializedName("side")
  private String side = null;
  @SerializedName("simpleOrderQty")
  private Double simpleOrderQty = null;
  @SerializedName("orderQty")
  private BigDecimal orderQty = null;
  @SerializedName("price")
  private Double price = null;
  @SerializedName("displayQty")
  private BigDecimal displayQty = null;
  @SerializedName("stopPx")
  private Double stopPx = null;
  @SerializedName("pegOffsetValue")
  private Double pegOffsetValue = null;
  @SerializedName("pegPriceType")
  private String pegPriceType = null;
  @SerializedName("currency")
  private String currency = null;
  @SerializedName("settlCurrency")
  private String settlCurrency = null;
  @SerializedName("ordType")
  private String ordType = null;
  @SerializedName("timeInForce")
  private String timeInForce = null;
  @SerializedName("execInst")
  private String execInst = null;
  @SerializedName("contingencyType")
  private String contingencyType = null;
  @SerializedName("exDestination")
  private String exDestination = null;
  @SerializedName("ordStatus")
  private String ordStatus = null;
  @SerializedName("triggered")
  private String triggered = null;
  @SerializedName("workingIndicator")
  private Boolean workingIndicator = null;
  @SerializedName("ordRejReason")
  private String ordRejReason = null;
  @SerializedName("simpleLeavesQty")
  private Double simpleLeavesQty = null;
  @SerializedName("leavesQty")
  private BigDecimal leavesQty = null;
  @SerializedName("simpleCumQty")
  private Double simpleCumQty = null;
  @SerializedName("cumQty")
  private BigDecimal cumQty = null;
  @SerializedName("avgPx")
  private Double avgPx = null;
  @SerializedName("multiLegReportingType")
  private String multiLegReportingType = null;
  @SerializedName("text")
  private String text = null;
  @SerializedName("transactTime")
  private Date transactTime = null;
  @SerializedName("timestamp")
  private Date timestamp = null;

  /**
   **/
  @ApiModelProperty(required = true, value = "")
  public String getOrderID() {
    return orderID;
  }
  public void setOrderID(String orderID) {
    this.orderID = orderID;
  }

  /**
   **/
  @ApiModelProperty(value = "")
  public String getClOrdID() {
    return clOrdID;
  }
  public void setClOrdID(String clOrdID) {
    this.clOrdID = clOrdID;
  }

  /**
   **/
  @ApiModelProperty(value = "")
  public String getClOrdLinkID() {
    return clOrdLinkID;
  }
  public void setClOrdLinkID(String clOrdLinkID) {
    this.clOrdLinkID = clOrdLinkID;
  }

  /**
   **/
  @ApiModelProperty(value = "")
  public BigDecimal getAccount() {
    return account;
  }
  public void setAccount(BigDecimal account) {
    this.account = account;
  }

  /**
   **/
  @ApiModelProperty(value = "")
  public String getSymbol() {
    return symbol;
  }
  public void setSymbol(String symbol) {
    this.symbol = symbol;
  }

  /**
   **/
  @ApiModelProperty(value = "")
  public String getSide() {
    return side;
  }
  public void setSide(String side) {
    this.side = side;
  }

  /**
   **/
  @ApiModelProperty(value = "")
  public Double getSimpleOrderQty() {
    return simpleOrderQty;
  }
  public void setSimpleOrderQty(Double simpleOrderQty) {
    this.simpleOrderQty = simpleOrderQty;
  }

  /**
   **/
  @ApiModelProperty(value = "")
  public BigDecimal getOrderQty() {
    return orderQty;
  }
  public void setOrderQty(BigDecimal orderQty) {
    this.orderQty = orderQty;
  }

  /**
   **/
  @ApiModelProperty(value = "")
  public Double getPrice() {
    return price;
  }
  public void setPrice(Double price) {
    this.price = price;
  }

  /**
   **/
  @ApiModelProperty(value = "")
  public BigDecimal getDisplayQty() {
    return displayQty;
  }
  public void setDisplayQty(BigDecimal displayQty) {
    this.displayQty = displayQty;
  }

  /**
   **/
  @ApiModelProperty(value = "")
  public Double getStopPx() {
    return stopPx;
  }
  public void setStopPx(Double stopPx) {
    this.stopPx = stopPx;
  }

  /**
   **/
  @ApiModelProperty(value = "")
  public Double getPegOffsetValue() {
    return pegOffsetValue;
  }
  public void setPegOffsetValue(Double pegOffsetValue) {
    this.pegOffsetValue = pegOffsetValue;
  }

  /**
   **/
  @ApiModelProperty(value = "")
  public String getPegPriceType() {
    return pegPriceType;
  }
  public void setPegPriceType(String pegPriceType) {
    this.pegPriceType = pegPriceType;
  }

  /**
   **/
  @ApiModelProperty(value = "")
  public String getCurrency() {
    return currency;
  }
  public void setCurrency(String currency) {
    this.currency = currency;
  }

  /**
   **/
  @ApiModelProperty(value = "")
  public String getSettlCurrency() {
    return settlCurrency;
  }
  public void setSettlCurrency(String settlCurrency) {
    this.settlCurrency = settlCurrency;
  }

  /**
   **/
  @ApiModelProperty(value = "")
  public String getOrdType() {
    return ordType;
  }
  public void setOrdType(String ordType) {
    this.ordType = ordType;
  }

  /**
   **/
  @ApiModelProperty(value = "")
  public String getTimeInForce() {
    return timeInForce;
  }
  public void setTimeInForce(String timeInForce) {
    this.timeInForce = timeInForce;
  }

  /**
   **/
  @ApiModelProperty(value = "")
  public String getExecInst() {
    return execInst;
  }
  public void setExecInst(String execInst) {
    this.execInst = execInst;
  }

  /**
   **/
  @ApiModelProperty(value = "")
  public String getContingencyType() {
    return contingencyType;
  }
  public void setContingencyType(String contingencyType) {
    this.contingencyType = contingencyType;
  }

  /**
   **/
  @ApiModelProperty(value = "")
  public String getExDestination() {
    return exDestination;
  }
  public void setExDestination(String exDestination) {
    this.exDestination = exDestination;
  }

  /**
   **/
  @ApiModelProperty(value = "")
  public String getOrdStatus() {
    return ordStatus;
  }
  public void setOrdStatus(String ordStatus) {
    this.ordStatus = ordStatus;
  }

  /**
   **/
  @ApiModelProperty(value = "")
  public String getTriggered() {
    return triggered;
  }
  public void setTriggered(String triggered) {
    this.triggered = triggered;
  }

  /**
   **/
  @ApiModelProperty(value = "")
  public Boolean getWorkingIndicator() {
    return workingIndicator;
  }
  public void setWorkingIndicator(Boolean workingIndicator) {
    this.workingIndicator = workingIndicator;
  }

  /**
   **/
  @ApiModelProperty(value = "")
  public String getOrdRejReason() {
    return ordRejReason;
  }
  public void setOrdRejReason(String ordRejReason) {
    this.ordRejReason = ordRejReason;
  }

  /**
   **/
  @ApiModelProperty(value = "")
  public Double getSimpleLeavesQty() {
    return simpleLeavesQty;
  }
  public void setSimpleLeavesQty(Double simpleLeavesQty) {
    this.simpleLeavesQty = simpleLeavesQty;
  }

  /**
   **/
  @ApiModelProperty(value = "")
  public BigDecimal getLeavesQty() {
    return leavesQty;
  }
  public void setLeavesQty(BigDecimal leavesQty) {
    this.leavesQty = leavesQty;
  }

  /**
   **/
  @ApiModelProperty(value = "")
  public Double getSimpleCumQty() {
    return simpleCumQty;
  }
  public void setSimpleCumQty(Double simpleCumQty) {
    this.simpleCumQty = simpleCumQty;
  }

  /**
   **/
  @ApiModelProperty(value = "")
  public BigDecimal getCumQty() {
    return cumQty;
  }
  public void setCumQty(BigDecimal cumQty) {
    this.cumQty = cumQty;
  }

  /**
   **/
  @ApiModelProperty(value = "")
  public Double getAvgPx() {
    return avgPx;
  }
  public void setAvgPx(Double avgPx) {
    this.avgPx = avgPx;
  }

  /**
   **/
  @ApiModelProperty(value = "")
  public String getMultiLegReportingType() {
    return multiLegReportingType;
  }
  public void setMultiLegReportingType(String multiLegReportingType) {
    this.multiLegReportingType = multiLegReportingType;
  }

  /**
   **/
  @ApiModelProperty(value = "")
  public String getText() {
    return text;
  }
  public void setText(String text) {
    this.text = text;
  }

  /**
   **/
  @ApiModelProperty(value = "")
  public Date getTransactTime() {
    return transactTime;
  }
  public void setTransactTime(Date transactTime) {
    this.transactTime = transactTime;
  }

  /**
   **/
  @ApiModelProperty(value = "")
  public Date getTimestamp() {
    return timestamp;
  }
  public void setTimestamp(Date timestamp) {
    this.timestamp = timestamp;
  }


  @Override
  public boolean equals(Object o) {
    if (this == o) {
      return true;
    }
    if (o == null || getClass() != o.getClass()) {
      return false;
    }
    Order order = (Order) o;
    return (this.orderID == null ? order.orderID == null : this.orderID.equals(order.orderID)) &&
        (this.clOrdID == null ? order.clOrdID == null : this.clOrdID.equals(order.clOrdID)) &&
        (this.clOrdLinkID == null ? order.clOrdLinkID == null : this.clOrdLinkID.equals(order.clOrdLinkID)) &&
        (this.account == null ? order.account == null : this.account.equals(order.account)) &&
        (this.symbol == null ? order.symbol == null : this.symbol.equals(order.symbol)) &&
        (this.side == null ? order.side == null : this.side.equals(order.side)) &&
        (this.simpleOrderQty == null ? order.simpleOrderQty == null : this.simpleOrderQty.equals(order.simpleOrderQty)) &&
        (this.orderQty == null ? order.orderQty == null : this.orderQty.equals(order.orderQty)) &&
        (this.price == null ? order.price == null : this.price.equals(order.price)) &&
        (this.displayQty == null ? order.displayQty == null : this.displayQty.equals(order.displayQty)) &&
        (this.stopPx == null ? order.stopPx == null : this.stopPx.equals(order.stopPx)) &&
        (this.pegOffsetValue == null ? order.pegOffsetValue == null : this.pegOffsetValue.equals(order.pegOffsetValue)) &&
        (this.pegPriceType == null ? order.pegPriceType == null : this.pegPriceType.equals(order.pegPriceType)) &&
        (this.currency == null ? order.currency == null : this.currency.equals(order.currency)) &&
        (this.settlCurrency == null ? order.settlCurrency == null : this.settlCurrency.equals(order.settlCurrency)) &&
        (this.ordType == null ? order.ordType == null : this.ordType.equals(order.ordType)) &&
        (this.timeInForce == null ? order.timeInForce == null : this.timeInForce.equals(order.timeInForce)) &&
        (this.execInst == null ? order.execInst == null : this.execInst.equals(order.execInst)) &&
        (this.contingencyType == null ? order.contingencyType == null : this.contingencyType.equals(order.contingencyType)) &&
        (this.exDestination == null ? order.exDestination == null : this.exDestination.equals(order.exDestination)) &&
        (this.ordStatus == null ? order.ordStatus == null : this.ordStatus.equals(order.ordStatus)) &&
        (this.triggered == null ? order.triggered == null : this.triggered.equals(order.triggered)) &&
        (this.workingIndicator == null ? order.workingIndicator == null : this.workingIndicator.equals(order.workingIndicator)) &&
        (this.ordRejReason == null ? order.ordRejReason == null : this.ordRejReason.equals(order.ordRejReason)) &&
        (this.simpleLeavesQty == null ? order.simpleLeavesQty == null : this.simpleLeavesQty.equals(order.simpleLeavesQty)) &&
        (this.leavesQty == null ? order.leavesQty == null : this.leavesQty.equals(order.leavesQty)) &&
        (this.simpleCumQty == null ? order.simpleCumQty == null : this.simpleCumQty.equals(order.simpleCumQty)) &&
        (this.cumQty == null ? order.cumQty == null : this.cumQty.equals(order.cumQty)) &&
        (this.avgPx == null ? order.avgPx == null : this.avgPx.equals(order.avgPx)) &&
        (this.multiLegReportingType == null ? order.multiLegReportingType == null : this.multiLegReportingType.equals(order.multiLegReportingType)) &&
        (this.text == null ? order.text == null : this.text.equals(order.text)) &&
        (this.transactTime == null ? order.transactTime == null : this.transactTime.equals(order.transactTime)) &&
        (this.timestamp == null ? order.timestamp == null : this.timestamp.equals(order.timestamp));
  }

  @Override
  public int hashCode() {
    int result = 17;
    result = 31 * result + (this.orderID == null ? 0: this.orderID.hashCode());
    result = 31 * result + (this.clOrdID == null ? 0: this.clOrdID.hashCode());
    result = 31 * result + (this.clOrdLinkID == null ? 0: this.clOrdLinkID.hashCode());
    result = 31 * result + (this.account == null ? 0: this.account.hashCode());
    result = 31 * result + (this.symbol == null ? 0: this.symbol.hashCode());
    result = 31 * result + (this.side == null ? 0: this.side.hashCode());
    result = 31 * result + (this.simpleOrderQty == null ? 0: this.simpleOrderQty.hashCode());
    result = 31 * result + (this.orderQty == null ? 0: this.orderQty.hashCode());
    result = 31 * result + (this.price == null ? 0: this.price.hashCode());
    result = 31 * result + (this.displayQty == null ? 0: this.displayQty.hashCode());
    result = 31 * result + (this.stopPx == null ? 0: this.stopPx.hashCode());
    result = 31 * result + (this.pegOffsetValue == null ? 0: this.pegOffsetValue.hashCode());
    result = 31 * result + (this.pegPriceType == null ? 0: this.pegPriceType.hashCode());
    result = 31 * result + (this.currency == null ? 0: this.currency.hashCode());
    result = 31 * result + (this.settlCurrency == null ? 0: this.settlCurrency.hashCode());
    result = 31 * result + (this.ordType == null ? 0: this.ordType.hashCode());
    result = 31 * result + (this.timeInForce == null ? 0: this.timeInForce.hashCode());
    result = 31 * result + (this.execInst == null ? 0: this.execInst.hashCode());
    result = 31 * result + (this.contingencyType == null ? 0: this.contingencyType.hashCode());
    result = 31 * result + (this.exDestination == null ? 0: this.exDestination.hashCode());
    result = 31 * result + (this.ordStatus == null ? 0: this.ordStatus.hashCode());
    result = 31 * result + (this.triggered == null ? 0: this.triggered.hashCode());
    result = 31 * result + (this.workingIndicator == null ? 0: this.workingIndicator.hashCode());
    result = 31 * result + (this.ordRejReason == null ? 0: this.ordRejReason.hashCode());
    result = 31 * result + (this.simpleLeavesQty == null ? 0: this.simpleLeavesQty.hashCode());
    result = 31 * result + (this.leavesQty == null ? 0: this.leavesQty.hashCode());
    result = 31 * result + (this.simpleCumQty == null ? 0: this.simpleCumQty.hashCode());
    result = 31 * result + (this.cumQty == null ? 0: this.cumQty.hashCode());
    result = 31 * result + (this.avgPx == null ? 0: this.avgPx.hashCode());
    result = 31 * result + (this.multiLegReportingType == null ? 0: this.multiLegReportingType.hashCode());
    result = 31 * result + (this.text == null ? 0: this.text.hashCode());
    result = 31 * result + (this.transactTime == null ? 0: this.transactTime.hashCode());
    result = 31 * result + (this.timestamp == null ? 0: this.timestamp.hashCode());
    return result;
  }

  @Override
  public String toString()  {
    StringBuilder sb = new StringBuilder();
    sb.append("class Order {\n");
    
    sb.append("  orderID: ").append(orderID).append("\n");
    sb.append("  clOrdID: ").append(clOrdID).append("\n");
    sb.append("  clOrdLinkID: ").append(clOrdLinkID).append("\n");
    sb.append("  account: ").append(account).append("\n");
    sb.append("  symbol: ").append(symbol).append("\n");
    sb.append("  side: ").append(side).append("\n");
    sb.append("  simpleOrderQty: ").append(simpleOrderQty).append("\n");
    sb.append("  orderQty: ").append(orderQty).append("\n");
    sb.append("  price: ").append(price).append("\n");
    sb.append("  displayQty: ").append(displayQty).append("\n");
    sb.append("  stopPx: ").append(stopPx).append("\n");
    sb.append("  pegOffsetValue: ").append(pegOffsetValue).append("\n");
    sb.append("  pegPriceType: ").append(pegPriceType).append("\n");
    sb.append("  currency: ").append(currency).append("\n");
    sb.append("  settlCurrency: ").append(settlCurrency).append("\n");
    sb.append("  ordType: ").append(ordType).append("\n");
    sb.append("  timeInForce: ").append(timeInForce).append("\n");
    sb.append("  execInst: ").append(execInst).append("\n");
    sb.append("  contingencyType: ").append(contingencyType).append("\n");
    sb.append("  exDestination: ").append(exDestination).append("\n");
    sb.append("  ordStatus: ").append(ordStatus).append("\n");
    sb.append("  triggered: ").append(triggered).append("\n");
    sb.append("  workingIndicator: ").append(workingIndicator).append("\n");
    sb.append("  ordRejReason: ").append(ordRejReason).append("\n");
    sb.append("  simpleLeavesQty: ").append(simpleLeavesQty).append("\n");
    sb.append("  leavesQty: ").append(leavesQty).append("\n");
    sb.append("  simpleCumQty: ").append(simpleCumQty).append("\n");
    sb.append("  cumQty: ").append(cumQty).append("\n");
    sb.append("  avgPx: ").append(avgPx).append("\n");
    sb.append("  multiLegReportingType: ").append(multiLegReportingType).append("\n");
    sb.append("  text: ").append(text).append("\n");
    sb.append("  transactTime: ").append(transactTime).append("\n");
    sb.append("  timestamp: ").append(timestamp).append("\n");
    sb.append("}\n");
    return sb.toString();
  }
}
