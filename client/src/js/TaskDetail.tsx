import * as React from "react";
import {Task, User, Tag, ITask} from './models/models';
import '../css/TaskDetail.less';
import TaskActivity from "./TaskActivity";
import * as API from './api/API';
import Toggle from "./Toggle";
import * as Select from 'react-select';
import {PriorityEnum,Priority} from './metadata/Priority';

// Be sure to include styles at some point, probably during your bootstrapping
import 'react-select/dist/react-select.css';
import Option = ReactSelectClass.Option;
import ReactSelectClass = require("react-select");

interface Props {
    task: Task;
}

// Absence of a field in state will mean that it hasn't been edited yet.
interface State {
    title?: string;
    status?: string; // TODO see if we can define enum type in ts
    owner?: User;
    description?: string;
    tags?: Option[];
    subscribers?: Option[];
    priority?: Priority;
    latestSavedTask?: Task; // Latest copy on the server.
}

export function getTaskFields(): string {
    return 'id,created_time,updated_time,title,description,status,owner,priority,tags,subscribers,activities{'+
      'id,task,actor,changed,old_title,new_title,old_description,new_description,new_status,old_priority,new_priority' +
        '}';
}

export class TaskDetail extends React.Component<Props, State> {

    constructor(props: Props) {
        super(props);
        this.state = {};
    }

    componentWillReceiveProps(newProps: Props) {
        this.state = {};
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
            <div className="task-detail"
                 style={{backgroundColor: hasChange ? 'lightyellow' : 'whitesmoke'}}>
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
                <div className="task-field">
                    <div className="name">Owner:</div>
                    <input defaultValue={savedTask.getOwner()!.getName()}/>
                </div>
                <div className="task-field priority">
                    <div className="name">Priority:</div>
                    <Select
                        searchable={false}
                        clearable={false}
                        value={this.getPriority()}
                        options={priorityOptions}
                        onChange={(option: Option) => option ? this.onPriorityChange(option.value as Priority) : null}
                    />
                </div>
                <div className="task-field description">
                    <div className="name">Description:</div>
                    <textarea value={this.getDescription()}
                              onChange={(event) => this.onDescriptionChange(event)}
                    />
                </div>
                <Select multi={true}
                        clearable={false}
                        options={this.getTags()}
                        value={this.getTags()}
                        placeholder="Add some tags.."
                        onChange={(options: Option[]) => options ? this.onTagsChange(options) : null}
                />
                <Select multi={true}
                        clearable={false}
                        options={this.getSubscribers()}
                        value={this.getSubscribers()}
                        placeholder="Add subscribers.."
                        onChange={(options: Option[]) => options ? this.onSubscribersChange(options) : null}
                />
                {savedTask.getActivities() ?
                    <div style={{marginTop: '10px'}}>
                        <div>Activities:</div>
                        {savedTask.getActivities()!.map((activity) => {
                            return <TaskActivity key={activity.getID()} activity={activity}/>;
                        }).reverse()}
                    </div>: null
                }
            </div>
        );
    }

    private hasChange() {
        var savedTask = this.getSavedTask();
        return this.state.title !== void 0 && this.state.title !== savedTask.getTitle() ||
            this.state.status !== void 0 && this.state.status !== savedTask.getStatus() ||
            this.state.priority != void 0 && this.state.priority !== savedTask.getPriority() ||
            this.state.description != void 0 && this.state.description !== savedTask.getDescription();
    }

    private onTitleChange(event: React.FormEvent<HTMLInputElement>) {
        this.setState({title: event.currentTarget.value});
    }

    private onStatusChange(active: boolean) {
        this.setState({status: active ? 'open' : 'closed'});
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

        // TODO correctly handle the failure
        API.post(this.props.task.getID(), params)
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
        this.setState({latestSavedTask: new Task(taskJson)});
        // TODO update the data in the list as well once task is updated.
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

    private getSavedTask() {
        return this.state.latestSavedTask || this.props.task;
    }

    private getTitle() {
        if (this.state.title !== void 0) {
            return this.state.title;
        }
        return this.getSavedTask().getTitle();
    }

    private getStatus() {
        return this.state.status || this.getSavedTask().getStatus();
    }

    private getPriority() {
        return this.state.priority || this.getSavedTask().getPriority();
    }

    private getTags(): Option[] {
        if (this.state.tags !== void 0) {
            return this.state.tags;
        }
        return this.getSavedTask().getTags()!.map((tag) => {
            return {
                value: tag.getID(),
                label: tag.getCaption() || '',
            };
        });
    }

    private getSubscribers(): Option[] {
        if (this.state.subscribers !== void 0) {
            return this.state.subscribers;
        }
        return this.getSavedTask().getSubscribers()!.map((user) => {
            return {
                value: user.getID(),
                label: user.getName() || '',
            };
        });
    }

}

