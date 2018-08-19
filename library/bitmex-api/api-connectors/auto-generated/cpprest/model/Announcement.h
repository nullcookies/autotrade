/**
 * BitMEX API
 * ## REST API for the BitMEX Trading Platform  [View Changelog](/app/apiChangelog)    #### Getting Started  Base URI: [https://www.bitmex.com/api/v1](/api/v1)  ##### Fetching Data  All REST endpoints are documented below. You can try out any query right from this interface.  Most table queries accept `count`, `start`, and `reverse` params. Set `reverse=true` to get rows newest-first.  Additional documentation regarding filters, timestamps, and authentication is available in [the main API documentation](/app/restAPI).  *All* table data is available via the [Websocket](/app/wsAPI). We highly recommend using the socket if you want to have the quickest possible data without being subject to ratelimits.  ##### Return Types  By default, all data is returned as JSON. Send `?_format=csv` to get CSV data or `?_format=xml` to get XML data.  ##### Trade Data Queries  *This is only a small subset of what is available, to get you started.*  Fill in the parameters and click the `Try it out!` button to try any of these queries.  * [Pricing Data](#!/Quote/Quote_get)  * [Trade Data](#!/Trade/Trade_get)  * [OrderBook Data](#!/OrderBook/OrderBook_getL2)  * [Settlement Data](#!/Settlement/Settlement_get)  * [Exchange Statistics](#!/Stats/Stats_history)  Every function of the BitMEX.com platform is exposed here and documented. Many more functions are available.  ##### Swagger Specification  [⇩ Download Swagger JSON](swagger.json)    ## All API Endpoints  Click to expand a section. 
 *
 * OpenAPI spec version: 1.2.0
 * Contact: support@bitmex.com
 *
 * NOTE: This class is auto generated by the swagger code generator 2.4.0-SNAPSHOT.
 * https://github.com/swagger-api/swagger-codegen.git
 * Do not edit the class manually.
 */

/*
 * Announcement.h
 *
 * Public Announcements
 */

#ifndef IO_SWAGGER_CLIENT_MODEL_Announcement_H_
#define IO_SWAGGER_CLIENT_MODEL_Announcement_H_


#include "../ModelBase.h"

#include <cpprest/details/basic_types.h>

namespace io {
namespace swagger {
namespace client {
namespace model {

/// <summary>
/// Public Announcements
/// </summary>
class  Announcement
    : public ModelBase
{
public:
    Announcement();
    virtual ~Announcement();

    /////////////////////////////////////////////
    /// ModelBase overrides

    void validate() override;

    web::json::value toJson() const override;
    void fromJson(web::json::value& json) override;

    void toMultipart(std::shared_ptr<MultipartFormData> multipart, const utility::string_t& namePrefix) const override;
    void fromMultiPart(std::shared_ptr<MultipartFormData> multipart, const utility::string_t& namePrefix) override;

    /////////////////////////////////////////////
    /// Announcement members

    /// <summary>
    /// 
    /// </summary>
    double getId() const;
        void setId(double value);
    /// <summary>
    /// 
    /// </summary>
    utility::string_t getLink() const;
    bool linkIsSet() const;
    void unsetLink();
    void setLink(utility::string_t value);
    /// <summary>
    /// 
    /// </summary>
    utility::string_t getTitle() const;
    bool titleIsSet() const;
    void unsetTitle();
    void setTitle(utility::string_t value);
    /// <summary>
    /// 
    /// </summary>
    utility::string_t getContent() const;
    bool contentIsSet() const;
    void unsetContent();
    void setContent(utility::string_t value);
    /// <summary>
    /// 
    /// </summary>
    utility::datetime getDate() const;
    bool dateIsSet() const;
    void unsetdate();
    void setDate(utility::datetime value);

protected:
    double m_Id;
        utility::string_t m_Link;
    bool m_LinkIsSet;
    utility::string_t m_Title;
    bool m_TitleIsSet;
    utility::string_t m_Content;
    bool m_ContentIsSet;
    utility::datetime m_date;
    bool m_dateIsSet;
};

}
}
}
}

#endif /* IO_SWAGGER_CLIENT_MODEL_Announcement_H_ */
