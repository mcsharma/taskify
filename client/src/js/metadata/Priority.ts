import EnumBase from "./EnumBase";

export type Priority = 'high' | 'mid' | 'low' | 'urgent' | 'unspecified' | 'wishlist';

export class PriorityEnum extends EnumBase {
    static MID: Priority = "mid";
    static HIGH: Priority = "high";
    static LOW: Priority = "low";
    static URGENT: Priority = "urgent";
    static UNSPECIFIED: Priority = "unspecified";
    static WISHLIST: Priority = "wishlist";
}