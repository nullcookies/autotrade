//
// Notification.swift
//
// Generated by swagger-codegen
// https://github.com/swagger-api/swagger-codegen
//

import Foundation


/** Account Notifications */

public struct Notification: Codable {

    public enum ModelType: String, Codable { 
        case success = "success"
        case error = "error"
        case info = "info"
    }
    public var _id: Double?
    public var date: Date
    public var title: String
    public var body: String
    public var ttl: Double
    public var type: ModelType?
    public var closable: Bool?
    public var persist: Bool?
    public var waitForVisibility: Bool?
    public var sound: String?

    public init(_id: Double?, date: Date, title: String, body: String, ttl: Double, type: ModelType?, closable: Bool?, persist: Bool?, waitForVisibility: Bool?, sound: String?) {
        self._id = _id
        self.date = date
        self.title = title
        self.body = body
        self.ttl = ttl
        self.type = type
        self.closable = closable
        self.persist = persist
        self.waitForVisibility = waitForVisibility
        self.sound = sound
    }

    public enum CodingKeys: String, CodingKey { 
        case _id = "id"
        case date
        case title
        case body
        case ttl
        case type
        case closable
        case persist
        case waitForVisibility
        case sound
    }


}

