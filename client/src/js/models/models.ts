// NOTE: Mark a field non-optional if this is a field we will always fetch
// AND will always be present (i.e. have a non-null value) in the response.
import * as _ from 'lodash';

export interface INode {
    id: number;
    created_time?: string;
    updated_time?: string;
}

export interface IEdge<T extends INode> {
    total_count: number;
    nodes: T[];
}

export interface ITask extends INode {
    title?: string;
    status?: string; // TODO check enum type in TS
    description?: string;
    priority?: string;
    owner?: IUser;
    creator?: IUser;
    tags?: IEdge<ITag>;
    subscribers?: IEdge<IUser>;
    activities?: IEdge<IActivity>;
}

export interface IUser extends INode {
    name?: string;
    email?: string;
    tasks?: IEdge<ITask>;
    created_tasks?: IEdge<ITask>;
    activities?: IEdge<IActivity>;
}

export interface ITag extends INode {
    caption?: string;
    description?: string;
    creator?: IUser;
}

export interface IActivity extends INode {
    actor: IUser;
    task: ITask;
    changed: string;
    old_title?: string;
    new_title?: string;
    old_description?: string;
    new_description?: string;
    new_status?: string;
    old_priority?: string;
    new_priority?: string;
    old_owner?: IUser;
    new_owner?: IUser;
    added_tags?: ITag[];
    removed_tags?: ITag[];
    added_subscribers?: IUser[];
    removed_subscribers?: IUser[];
}

export class NodeBase<T extends INode> {

    protected json: T;

    constructor(json: T) {
        this.json = json;
    }

    getID() {
        return this.json.id;
    }

    getCreatedTime() {
        return this.json.created_time;
    }

    getUpdatedTime() {
        return this.json.updated_time;
    }

    getJson() {
        return this.json;
    }
}

export class User extends NodeBase<IUser> {

    private createdTasks: Task[]|undefined;
    private tasks: Task[]|undefined;
    private activities: Activity[]|undefined;

    constructor(json: IUser) {
        super(json);
        if (json.created_tasks) {
            this.createdTasks = json.created_tasks.nodes.map((taskJson) => {
                return new Task(taskJson);
            });
        }
        if (json.tasks) {
            this.tasks = json.tasks.nodes.map((taskJson) => {
                return new Task(taskJson);
            });
        }
        if (json.activities) {
            this.activities = json.activities.nodes.map((activityJson) => {
                return new Activity(activityJson);
            });
        }
    }

    getName() {
        return this.json.name;
    }

    getEmail() {
        return this.json.email;
    }

    getActivities() {
        return this.activities;
    }

    getActivitiesCount() {
        return _.get(this.json.activities, 'total_count') as number;
    }

    getCreatedTasks() {
        return this.createdTasks;
    }

    getCreatedTasksCount() {
        return _.get(this.json.created_tasks, 'total_count') as number;
    }

    getTasks() {
        return this.tasks;
    }

    getTasksCount(): number {
        return _.get(this.json.tasks, 'total_count') as number;
    }
}

export class Task extends NodeBase<ITask> {

    private creator: User|undefined;
    private owner: User|undefined;
    private tags: Tag[]|undefined;
    private subscribers: User[]|undefined;
    private activities: Activity[]|undefined;

    constructor(json: ITask) {
        super(json);
        if (this.json.creator) {
            this.creator = new User(this.json.creator);
        }
        if (this.json.owner) {
            this.owner = new User(this.json.owner);
        }
        if (json.tags) {
            this.tags = json.tags.nodes.map((tagJson) => {
                return new Tag(tagJson);
            });
        }
        if (json.subscribers) {
            this.subscribers = json.subscribers.nodes.map((userJson) => {
                return new User(userJson);
            });
        }
        if (json.activities) {
            this.activities = json.activities.nodes.map((activityJson) => {
                return new Activity(activityJson);
            });
        }
    }

    getStatus() {
        return this.json.status;
    }

    getTitle() {
        return this.json.title;
    }

    getDescription() {
        return this.json.description;
    }

    getPriority() {
        return this.json.priority;
    }

    getSubscribers() {
        return this.subscribers;
    }

    getSubscribersCount() {
        return _.get(this.json.subscribers, 'total_count') as number;
    }

    getTags() {
        return this.tags;
    }

    getTagsCount() {
        return _.get(this.json.tags, 'total_count') as number;
    }

    getActivities() {
        return this.activities;
    }

    getActivitiesCount() {
        return _.get(this.json.activities, 'total_count') as number;
    }

    getOwner() {
        return this.owner;
    }

    getCreator() {
        return this.creator;
    }

    setTitle(title: string) {
        this.json.title = title;
    }
}

export class Tag extends NodeBase<ITag> {

    private creator: User|undefined;

    constructor(json: ITag) {
        super(json);
        if (this.json.creator) {
            this.creator = new User(this.json.creator);
        }
    }

    getCaption() {
        return this.json.caption;
    }

    getDescription() {
        return this.json.description;
    }

    getCreator() {
        return this.creator;
    }
}

export class Activity extends NodeBase<IActivity> {

    private actor: User;
    private task: Task;
    private oldOwner: User|undefined;
    private newOwner: User|undefined;
    private addedTags: Tag[];
    private removedTags: Tag[];
    private addedSubscribers: User[];
    private removedSubscribers: User[];

    constructor(json: IActivity) {
        super(json);
        this.actor = new User(json.actor);
        this.task = new Task(json.task);
        if (json.old_owner) {
            this.oldOwner = new User(json.old_owner);
        }
        if (json.new_owner) {
            this.newOwner = new User(json.new_owner);
        }
        if (json.added_tags) {
            this.addedTags = json.added_tags.map((tagJson) => {
                return new Tag(tagJson);
            });
        }
        if (json.removed_tags) {
            this.removedTags = json.removed_tags.map((tagJson) => {
                return new Tag(tagJson);
            });
        }
        if (json.added_subscribers) {
            this.addedSubscribers = json.added_subscribers.map((userJson) => {
                return new User(userJson);
            });
        }
        if (json.removed_subscribers) {
            this.removedSubscribers = json.removed_subscribers.map((userJson) => {
                return new User(userJson);
            });
        }
    }

    getChanged() {
        return this.json.changed;
    }

    getOldTitle() {
        return this.json.old_title;
    }

    getNewTitle() {
        return this.json.new_title;
    }

    getOldDescription() {
        return this.json.old_description;
    }

    getNewDescription() {
        return this.json.new_description;
    }

    getNewStatus() {
        return this.json.new_status;
    }

    getOldPriority() {
        return this.json.old_priority;
    }

    getNewPriority() {
        return this.json.new_priority;
    }

    getOldOwner() {
        return this.oldOwner;
    }

    getNewOwner() {
        return this.newOwner;
    }

    getAddedTags() {
        return this.addedTags;
    }

    getRemovedTags() {
        return this.removedTags;
    }

    getAddedSubscribers() {
        return this.addedSubscribers;
    }

    getRemovedSubscribers() {
        return this.removedSubscribers;
    }

    getActor() {
        return this.actor;
    }

    getTask() {
        return this.task;
    }
}