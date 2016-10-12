import {Tag, User} from "./models/models";
import ReactSelectClass = require("react-select");

export default class PrefetchedDataKeeper {

    private static allUsers: Array<User>;
    private static allTags: Array<Tag>;
    private static userTypeaheadOptions: Array<ReactSelectClass.Option>;
    private static tagTypeaheadOptions: Array<ReactSelectClass.Option>;

    public static keepAllUsers(users: Array<User>): void {
        PrefetchedDataKeeper.allUsers = users;
    }

    public static keepAllTags(tags: Array<Tag>): void {
        PrefetchedDataKeeper.allTags = tags;
    }

    public static getAllUsers(): Array<User> {
        return PrefetchedDataKeeper.allUsers;
    }

    public static getAllTags(): Array<Tag> {
        return PrefetchedDataKeeper.allTags;
    }

    public static getUserTypeaheadOptions(): Array<ReactSelectClass.Option> {
        if (PrefetchedDataKeeper.userTypeaheadOptions === void 0) {
            PrefetchedDataKeeper.userTypeaheadOptions = PrefetchedDataKeeper.allUsers.map((user) => {
                return {
                    value: user.getID(),
                    label: user.getName() || '',
                };
            });
        }
        return PrefetchedDataKeeper.userTypeaheadOptions;
    }
    public static getTagTypeaheadOptions(): Array<ReactSelectClass.Option> {
        if (PrefetchedDataKeeper.tagTypeaheadOptions === void 0) {
            PrefetchedDataKeeper.tagTypeaheadOptions = PrefetchedDataKeeper.allTags.map((tag) => {
                return {
                    value: tag.getID(),
                    label: tag.getCaption() || '',
                };
            });
        }
        return PrefetchedDataKeeper.tagTypeaheadOptions;
    }
}