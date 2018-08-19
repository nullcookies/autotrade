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
 * SWGNotification.h
 *
 * Account Notifications
 */

#ifndef SWGNotification_H_
#define SWGNotification_H_

#include <QJsonObject>


#include "SWGNumber.h"
#include <QDateTime>
#include <QString>

#include "SWGObject.h"

namespace Swagger {

class SWGNotification: public SWGObject {
public:
    SWGNotification();
    SWGNotification(QString json);
    ~SWGNotification();
    void init();
    void cleanup();

    QString asJson () override;
    QJsonObject asJsonObject() override;
    void fromJsonObject(QJsonObject json) override;
    SWGNotification* fromJson(QString jsonString) override;

    SWGNumber* getId();
    void setId(SWGNumber* id);

    QDateTime* getDate();
    void setDate(QDateTime* date);

    QString* getTitle();
    void setTitle(QString* title);

    QString* getBody();
    void setBody(QString* body);

    SWGNumber* getTtl();
    void setTtl(SWGNumber* ttl);

    QString* getType();
    void setType(QString* type);

    bool isClosable();
    void setClosable(bool closable);

    bool isPersist();
    void setPersist(bool persist);

    bool isWaitForVisibility();
    void setWaitForVisibility(bool wait_for_visibility);

    QString* getSound();
    void setSound(QString* sound);


    virtual bool isSet() override;

private:
    SWGNumber* id;
    bool m_id_isSet;

    QDateTime* date;
    bool m_date_isSet;

    QString* title;
    bool m_title_isSet;

    QString* body;
    bool m_body_isSet;

    SWGNumber* ttl;
    bool m_ttl_isSet;

    QString* type;
    bool m_type_isSet;

    bool closable;
    bool m_closable_isSet;

    bool persist;
    bool m_persist_isSet;

    bool wait_for_visibility;
    bool m_wait_for_visibility_isSet;

    QString* sound;
    bool m_sound_isSet;

};

}

#endif /* SWGNotification_H_ */
