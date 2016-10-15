import * as React from "react";
import {Task, User, Tag, ITask} from './models/models';
import '../css/TaskDetail.less';
import TaskActivity from "./TaskActivity";
import * as API from './api/API';
import Toggle from "./Toggle";
import {PriorityEnum, Priority} from './metadata/Priority';

import PrefetchedDataKeeper from "./PrefechedDataKeeper";
import classNames = require("classnames");
import ReactSelectClass = require("react-select");
type Option = ReactSelectClass.Option;

interface Props {
    task: Task;
    onTaskUpdate: (task: Task) => void
}

// Absence of a field in state will mean that it hasn't been edited yet.
interface State {
    title?: string;
    status?: string; // TODO see if we can define enum type in ts
    owner?: Option | null;
    description?: string;
    tags?: Option[];
    subscribers?: Option[];
    priority?: Priority;
    latestSavedTask?: Task; // Latest copy on the server.
}

export function getTaskFields(): string {
    return 'id,created_time,updated_time,title,description,status,owner,priority,tags,subscribers,activities{' +
        'id,task,actor,changed,old_title,new_title,old_description,new_description,new_status,old_priority,new_priority,' +
        'added_tags,removed_tags,added_subscribers,removed_subscribers,old_owner,new_owner}';
}

export class TaskDetail extends React.Component<Props, State> {

    constructor(props: Props) {
        super(props);
        this.state = TaskDetail.prepareInitialState(props);
    }

    componentWillReceiveProps(newProps: Props) {
        this.state = TaskDetail.prepareInitialState(newProps);
    }

    private static prepareInitialState(props: Props): State {
        return {
            tags: props.task.getTags()!.map(tag => {
                return {
                    value: tag.getID(),
                    label: tag.getCaption() || '',
                };
            }),
            subscribers: props.task.getSubscribers()!.map(user => {
                return {
                    value: user.getID(),
                    label: user.getName() || '',
                };
            }),
            owner: props.task.getOwner() ? {
                value: props.task.getOwner()!.getID(),
                label: props.task.getOwner()!.getName() || ''
            } : null
        };
    }

    public render() {
        var priorityOptions = PriorityEnum.getAll<string>().map((priority) => {
            return {
                value: priority,
                label: priority
            };
        });
        let savedTask = this.getSavedTask(),
            hasChange = this.hasChange();

        return (
            <div className={classNames("task-detail")}>
                <div className={"task-headers" + (hasChange ? ' pending-change' : '')}>
                    <div className="task-headers-inner">
                        <div className="top-row">
                            <input className="task-title"
                                   value={this.getTitle()}
                                   onChange={(event) => this.onTitleChange(event)}
                            />
                            <button
                                disabled={!hasChange}
                                type="button"
                                className="save-button"
                                onClick={(event) => this.onSaveClick(event)}>Save
                            </button>
                        </div>
                        <div className="task-field">
                            <Toggle active={this.getStatus() === 'open'}
                                    onChange={(active) => this.onStatusChange(active)}
                            />
                        </div>
                        <div className="task-field">
                            <div className="name">ID:</div>
                            <div>{savedTask.getID()}</div>
                        </div>
                        <div className="task-field owner">
                            <div className="name">Owner:</div>
                            <ReactSelectClass options={PrefetchedDataKeeper.getUserTypeaheadOptions()}
                                    value={this.getOwner()}
                                    placeholder="owner"
                                    autosize={true}
                                    onChange={(option: Option) => this.onOwnerChange(option)}
                            />
                        </div>
                        <div className="task-field priority">
                            <div className="name">Priority:</div>
                            <ReactSelectClass
                                searchable={false}
                                clearable={false}
                                value={this.getPriority()}
                                options={priorityOptions}
                                onChange={(option: Option) => option ? this.onPriorityChange(option.value as Priority) : null}
                            />
                        </div>
                        <div className="task-field description">
                            <div className="name">Description:</div>
                            <textarea className="content" value={this.getDescription()}
                                      onChange={(event) => this.onDescriptionChange(event)}
                            />
                        </div>
                        <div className="task-field tags">
                            <div className="name">Tags:</div>
                            <ReactSelectClass multi={true}
                                    clearable={false}
                                    options={PrefetchedDataKeeper.getTagTypeaheadOptions()}
                                    value={this.state.tags}
                                    placeholder="Add some tags.."
                                    onChange={(options: Option[]) => options ? this.onTagsChange(options) : null}
                            />
                        </div>
                        <div className="task-field subscribers">
                            <div className="name">Subscribers</div>
                            <ReactSelectClass multi={true}
                                    clearable={false}
                                    options={PrefetchedDataKeeper.getUserTypeaheadOptions()}
                                    value={this.state.subscribers}
                                    placeholder="Add subscribers.."
                                    onChange={(options: Option[]) => options ? this.onSubscribersChange(options) : null}
                            />
                        </div>
                    </div>
                </div>
                <div className="task-activities">
                    {savedTask.getActivities() && savedTask.getActivities()!.map((activity) => {
                        return <TaskActivity key={activity.getID()} activity={activity}/>;
                    }).reverse()}
                </div>
            </div>
        );
    }

    private hasChange() {
        var savedTask = this.getSavedTask();
        return this.state.title !== void 0 && this.state.title !== savedTask.getTitle() ||
            this.state.status !== void 0 && this.state.status !== savedTask.getStatus() ||
            this.state.priority !== void 0 && this.state.priority !== savedTask.getPriority() ||
            this.state.description !== void 0 && this.state.description !== (savedTask.getDescription() || '') ||
            this.hasOwnerChanged() ||
            this.haveTagsChanged() ||
            this.haveSubscribersChanged();
    }

    private hasOwnerChanged() {
        if (!!this.getSavedTask().getOwner()) {
            return !this.state.owner ||
                this.state.owner.value !== this.getSavedTask().getOwner()!.getID();
        }
        return !!this.state.owner;
    }

    private haveTagsChanged(): boolean {
        let currentTags = _.reduce(this.state.tags || [], (set: any, tag: Option) => {
                set[tag.value] = 1;
                return set;
            }, {}),
            savedTags = _.reduce(this.getSavedTask().getTags() || [], (set: any, tag: Tag) => {
                set[tag.getID()] = 1;
                return set;
            }, {});
        return !_.isEqual(currentTags, savedTags);
    }

    private haveSubscribersChanged(): boolean {
        let currentSubscribers = _.reduce(this.state.subscribers || [], (set: any, user: Option) => {
                set[user.value] = 1;
                return set;
            }, {}),
            savedSubscribers = _.reduce(this.getSavedTask().getSubscribers() || [], (set: any, user: User) => {
                set[user.getID()] = 1;
                return set;
            }, {});
        return !_.isEqual(currentSubscribers, savedSubscribers);
    }

    private onTitleChange(event: React.FormEvent<HTMLInputElement>) {
        this.setState({title: event.currentTarget.value});
    }

    private onStatusChange(active: boolean) {
        this.setState({status: active ? 'open' : 'closed'});
    }

    private onOwnerChange(option: Option) {
        this.setState({owner: option});
    }

    private onPriorityChange(priority: Priority) {
        this.setState({priority: priority});
    }

    private onDescriptionChange(event: React.FormEvent<HTMLTextAreaElement>) {
        this.setState({description: event.currentTarget.value});
    }

    private onTagsChange(options: Option[]) {
        this.setState({tags: options});
    }

    private onSubscribersChange(options: Option[]) {
        this.setState({subscribers: options});
    }

    private onSaveClick(event: React.MouseEvent<HTMLButtonElement>) {
        if (!this.hasChange()) {
            return;
        }
        let params = new Map();
        var savedTask = this.state.latestSavedTask || this.props.task;
        if (this.state.title &&
            this.state.title !== savedTask.getTitle()) {
            params.set('title', this.state.title);
        }
        if (this.state.description !== void 0 &&
            this.state.description !== savedTask.getDescription()) {
            params.set('description', this.state.description);
        }
        if (this.state.status !== void 0 &&
            this.state.status !== savedTask.getStatus()) {
            params.set('status', this.state.status);
        }
        if (this.state.priority !== void 0 &&
            this.state.priority !== savedTask.getPriority()) {
            params.set('priority', this.state.priority);
        }

        if (this.hasOwnerChanged()) {
            // pass 0 to clear the owner
            params.set('owner_id', this.state.owner ? this.state.owner.value : 0);
        }
        if (this.haveTagsChanged()) {
            params.set('tags', JSON.stringify(this.state.tags!.map(option => option.value)));
        }
        if (this.haveSubscribersChanged()) {
            params.set('subscribers', JSON.stringify(this.state.subscribers!.map(option => option.value)))
        }

        // TODO correctly handle the failure
        API.post(this.props.task.getID().toString(), params)
            .then(() => this.fetchLatestTask())
            .then((taskJson) => this.updateLatestTask(taskJson));
    }

    private fetchLatestTask() {
        return API.get<ITask>(
            this.props.task.getID(),
            getTaskFields()
        );
    }

    private updateLatestTask(taskJson: ITask) {
        this.props.onTaskUpdate(new Task(taskJson));
        // TODO update the data in the list as well once task is updated.
    }

    private getTitle() {
        if (this.state.title !== void 0) {
            return this.state.title;
        }
        return this.getSavedTask().getTitle();
    }

    private getDescription() {
        if (this.state.description !== void 0) {
            return this.state.description;
        }
        if (this.getSavedTask().getDescription() !== void 0) {
            return this.getSavedTask().getDescription();
        }
        return '';
    }

    private getOwner(): Option {
        return this.state.owner as Option;
    }

    private getStatus() {
        return this.state.status || this.getSavedTask().getStatus();
    }

    private getPriority() {
        return this.state.priority || this.getSavedTask().getPriority();
    }

    private getSavedTask() {
        return this.state.latestSavedTask || this.props.task;
    }
}
