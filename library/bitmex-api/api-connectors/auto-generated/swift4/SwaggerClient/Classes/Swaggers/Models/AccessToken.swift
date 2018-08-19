//
// AccessToken.swift
//
// Generated by swagger-codegen
// https://github.com/swagger-api/swagger-codegen
//

import Foundation



public struct AccessToken: Codable {

    public var _id: String
    /** time to live in seconds (2 weeks by default) */
    public var ttl: Double?
    public var created: Date?
    public var userId: Double?

    public init(_id: String, ttl: Double?, created: Date?, userId: Double?) {
        self._id = _id
        self.ttl = ttl
        self.created = created
        self.userId = userId
    }

    public enum CodingKeys: String, CodingKey { 
        case _id = "id"
        case ttl
        case created
        case userId
    }


}
