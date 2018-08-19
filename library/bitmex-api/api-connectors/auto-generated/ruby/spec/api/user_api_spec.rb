=begin
#BitMEX API

### REST API for the BitMEX Trading Platform  [View Changelog](/app/apiChangelog)    #### Getting Started  Base URI: [https://www.bitmex.com/api/v1](/api/v1)  ##### Fetching Data  All REST endpoints are documented below. You can try out any query right from this interface.  Most table queries accept `count`, `start`, and `reverse` params. Set `reverse=true` to get rows newest-first.  Additional documentation regarding filters, timestamps, and authentication is available in [the main API documentation](/app/restAPI).  *All* table data is available via the [Websocket](/app/wsAPI). We highly recommend using the socket if you want to have the quickest possible data without being subject to ratelimits.  ##### Return Types  By default, all data is returned as JSON. Send `?_format=csv` to get CSV data or `?_format=xml` to get XML data.  ##### Trade Data Queries  *This is only a small subset of what is available, to get you started.*  Fill in the parameters and click the `Try it out!` button to try any of these queries.  * [Pricing Data](#!/Quote/Quote_get)  * [Trade Data](#!/Trade/Trade_get)  * [OrderBook Data](#!/OrderBook/OrderBook_getL2)  * [Settlement Data](#!/Settlement/Settlement_get)  * [Exchange Statistics](#!/Stats/Stats_history)  Every function of the BitMEX.com platform is exposed here and documented. Many more functions are available.  ##### Swagger Specification  [⇩ Download Swagger JSON](swagger.json)    ## All API Endpoints  Click to expand a section. 

OpenAPI spec version: 1.2.0
Contact: support@bitmex.com
Generated by: https://github.com/swagger-api/swagger-codegen.git
Swagger Codegen version: 2.4.0-SNAPSHOT

=end

require 'spec_helper'
require 'json'

# Unit tests for SwaggerClient::UserApi
# Automatically generated by swagger-codegen (github.com/swagger-api/swagger-codegen)
# Please update as you see appropriate
describe 'UserApi' do
  before do
    # run before each test
    @instance = SwaggerClient::UserApi.new
  end

  after do
    # run after each test
  end

  describe 'test an instance of UserApi' do
    it 'should create an instance of UserApi' do
      expect(@instance).to be_instance_of(SwaggerClient::UserApi)
    end
  end

  # unit tests for user_cancel_withdrawal
  # Cancel a withdrawal.
  # @param token 
  # @param [Hash] opts the optional parameters
  # @return [Transaction]
  describe 'user_cancel_withdrawal test' do
    it 'should work' do
      # assertion here. ref: https://www.relishapp.com/rspec/rspec-expectations/docs/built-in-matchers
    end
  end

  # unit tests for user_check_referral_code
  # Check if a referral code is valid.
  # If the code is valid, responds with the referral code&#39;s discount (e.g. &#x60;0.1&#x60; for 10%). Otherwise, will return a 404.
  # @param [Hash] opts the optional parameters
  # @option opts [String] :referral_code 
  # @return [Float]
  describe 'user_check_referral_code test' do
    it 'should work' do
      # assertion here. ref: https://www.relishapp.com/rspec/rspec-expectations/docs/built-in-matchers
    end
  end

  # unit tests for user_confirm
  # Confirm your email address with a token.
  # @param token 
  # @param [Hash] opts the optional parameters
  # @return [AccessToken]
  describe 'user_confirm test' do
    it 'should work' do
      # assertion here. ref: https://www.relishapp.com/rspec/rspec-expectations/docs/built-in-matchers
    end
  end

  # unit tests for user_confirm_enable_tfa
  # Confirm two-factor auth for this account. If using a Yubikey, simply send a token to this endpoint.
  # @param token Token from your selected TFA type.
  # @param [Hash] opts the optional parameters
  # @option opts [String] :type Two-factor auth type. Supported types: &#39;GA&#39; (Google Authenticator), &#39;Yubikey&#39;
  # @return [BOOLEAN]
  describe 'user_confirm_enable_tfa test' do
    it 'should work' do
      # assertion here. ref: https://www.relishapp.com/rspec/rspec-expectations/docs/built-in-matchers
    end
  end

  # unit tests for user_confirm_withdrawal
  # Confirm a withdrawal.
  # @param token 
  # @param [Hash] opts the optional parameters
  # @return [Transaction]
  describe 'user_confirm_withdrawal test' do
    it 'should work' do
      # assertion here. ref: https://www.relishapp.com/rspec/rspec-expectations/docs/built-in-matchers
    end
  end

  # unit tests for user_disable_tfa
  # Disable two-factor auth for this account.
  # @param token Token from your selected TFA type.
  # @param [Hash] opts the optional parameters
  # @option opts [String] :type Two-factor auth type. Supported types: &#39;GA&#39; (Google Authenticator)
  # @return [BOOLEAN]
  describe 'user_disable_tfa test' do
    it 'should work' do
      # assertion here. ref: https://www.relishapp.com/rspec/rspec-expectations/docs/built-in-matchers
    end
  end

  # unit tests for user_get
  # Get your user model.
  # @param [Hash] opts the optional parameters
  # @return [User]
  describe 'user_get test' do
    it 'should work' do
      # assertion here. ref: https://www.relishapp.com/rspec/rspec-expectations/docs/built-in-matchers
    end
  end

  # unit tests for user_get_affiliate_status
  # Get your current affiliate/referral status.
  # @param [Hash] opts the optional parameters
  # @return [Affiliate]
  describe 'user_get_affiliate_status test' do
    it 'should work' do
      # assertion here. ref: https://www.relishapp.com/rspec/rspec-expectations/docs/built-in-matchers
    end
  end

  # unit tests for user_get_commission
  # Get your account&#39;s commission status.
  # @param [Hash] opts the optional parameters
  # @return [Array<UserCommission>]
  describe 'user_get_commission test' do
    it 'should work' do
      # assertion here. ref: https://www.relishapp.com/rspec/rspec-expectations/docs/built-in-matchers
    end
  end

  # unit tests for user_get_deposit_address
  # Get a deposit address.
  # @param [Hash] opts the optional parameters
  # @option opts [String] :currency 
  # @return [String]
  describe 'user_get_deposit_address test' do
    it 'should work' do
      # assertion here. ref: https://www.relishapp.com/rspec/rspec-expectations/docs/built-in-matchers
    end
  end

  # unit tests for user_get_margin
  # Get your account&#39;s margin status. Send a currency of \&quot;all\&quot; to receive an array of all supported currencies.
  # @param [Hash] opts the optional parameters
  # @option opts [String] :currency 
  # @return [Margin]
  describe 'user_get_margin test' do
    it 'should work' do
      # assertion here. ref: https://www.relishapp.com/rspec/rspec-expectations/docs/built-in-matchers
    end
  end

  # unit tests for user_get_wallet
  # Get your current wallet information.
  # @param [Hash] opts the optional parameters
  # @option opts [String] :currency 
  # @return [Wallet]
  describe 'user_get_wallet test' do
    it 'should work' do
      # assertion here. ref: https://www.relishapp.com/rspec/rspec-expectations/docs/built-in-matchers
    end
  end

  # unit tests for user_get_wallet_history
  # Get a history of all of your wallet transactions (deposits, withdrawals, PNL).
  # @param [Hash] opts the optional parameters
  # @option opts [String] :currency 
  # @return [Array<Transaction>]
  describe 'user_get_wallet_history test' do
    it 'should work' do
      # assertion here. ref: https://www.relishapp.com/rspec/rspec-expectations/docs/built-in-matchers
    end
  end

  # unit tests for user_get_wallet_summary
  # Get a summary of all of your wallet transactions (deposits, withdrawals, PNL).
  # @param [Hash] opts the optional parameters
  # @option opts [String] :currency 
  # @return [Array<Transaction>]
  describe 'user_get_wallet_summary test' do
    it 'should work' do
      # assertion here. ref: https://www.relishapp.com/rspec/rspec-expectations/docs/built-in-matchers
    end
  end

  # unit tests for user_logout
  # Log out of BitMEX.
  # @param [Hash] opts the optional parameters
  # @return [nil]
  describe 'user_logout test' do
    it 'should work' do
      # assertion here. ref: https://www.relishapp.com/rspec/rspec-expectations/docs/built-in-matchers
    end
  end

  # unit tests for user_logout_all
  # Log all systems out of BitMEX. This will revoke all of your account&#39;s access tokens, logging you out on all devices.
  # @param [Hash] opts the optional parameters
  # @return [Float]
  describe 'user_logout_all test' do
    it 'should work' do
      # assertion here. ref: https://www.relishapp.com/rspec/rspec-expectations/docs/built-in-matchers
    end
  end

  # unit tests for user_min_withdrawal_fee
  # Get the minimum withdrawal fee for a currency.
  # This is changed based on network conditions to ensure timely withdrawals. During network congestion, this may be high. The fee is returned in the same currency.
  # @param [Hash] opts the optional parameters
  # @option opts [String] :currency 
  # @return [Object]
  describe 'user_min_withdrawal_fee test' do
    it 'should work' do
      # assertion here. ref: https://www.relishapp.com/rspec/rspec-expectations/docs/built-in-matchers
    end
  end

  # unit tests for user_request_enable_tfa
  # Get secret key for setting up two-factor auth.
  # Use /confirmEnableTFA directly for Yubikeys. This fails if TFA is already enabled.
  # @param [Hash] opts the optional parameters
  # @option opts [String] :type Two-factor auth type. Supported types: &#39;GA&#39; (Google Authenticator)
  # @return [BOOLEAN]
  describe 'user_request_enable_tfa test' do
    it 'should work' do
      # assertion here. ref: https://www.relishapp.com/rspec/rspec-expectations/docs/built-in-matchers
    end
  end

  # unit tests for user_request_withdrawal
  # Request a withdrawal to an external wallet.
  # This will send a confirmation email to the email address on record, unless requested via an API Key with the &#x60;withdraw&#x60; permission.
  # @param currency Currency you&#39;re withdrawing. Options: &#x60;XBt&#x60;
  # @param amount Amount of withdrawal currency.
  # @param address Destination Address.
  # @param [Hash] opts the optional parameters
  # @option opts [String] :otp_token 2FA token. Required if 2FA is enabled on your account.
  # @option opts [Float] :fee Network fee for Bitcoin withdrawals. If not specified, a default value will be calculated based on Bitcoin network conditions. You will have a chance to confirm this via email.
  # @return [Transaction]
  describe 'user_request_withdrawal test' do
    it 'should work' do
      # assertion here. ref: https://www.relishapp.com/rspec/rspec-expectations/docs/built-in-matchers
    end
  end

  # unit tests for user_save_preferences
  # Save user preferences.
  # @param prefs 
  # @param [Hash] opts the optional parameters
  # @option opts [BOOLEAN] :overwrite If true, will overwrite all existing preferences.
  # @return [User]
  describe 'user_save_preferences test' do
    it 'should work' do
      # assertion here. ref: https://www.relishapp.com/rspec/rspec-expectations/docs/built-in-matchers
    end
  end

  # unit tests for user_update
  # Update your password, name, and other attributes.
  # @param [Hash] opts the optional parameters
  # @option opts [String] :firstname 
  # @option opts [String] :lastname 
  # @option opts [String] :old_password 
  # @option opts [String] :new_password 
  # @option opts [String] :new_password_confirm 
  # @option opts [String] :username Username can only be set once. To reset, email support.
  # @option opts [String] :country Country of residence.
  # @option opts [String] :pgp_pub_key PGP Public Key. If specified, automated emails will be sentwith this key.
  # @return [User]
  describe 'user_update test' do
    it 'should work' do
      # assertion here. ref: https://www.relishapp.com/rspec/rspec-expectations/docs/built-in-matchers
    end
  end

end
