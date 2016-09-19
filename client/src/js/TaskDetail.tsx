import * as React from "react";
import {Task, User, Tag, ITask} from './models/models';
import '../css/TaskDetail.less';
import TaskActivity from "./TaskActivity";
import * as API from './api/API';

interface Props {
    task: Task;
}

// Absence of a field in state will mean that it hasn't been edited yet.
interface State {
    title?: string;
    status?: string; // TODO see if we can define enum type in ts
    owner?: User;
    description?: string;
    tags?: Tag[];
    subscribers?: User[];
    priority?: string;
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
                    onClick={(event) => this.onSaveClick(event)}>Save</button>
                </div>
                <div className="task-field">
                    Status:
                    <input value={this.getStatus()}
                           onChange={(event) => this.onStatusChange(event)}
                    />
                </div>
                <div className="task-field">
                    ID: {savedTask.getID()}
                </div>
                <div className="task-field">
                    Owner:
                    <input defaultValue={savedTask.getOwner()!.getName()}/>
                </div>
                <div className="task-field">
                    Priority:
                    <input value={this.getPriority()}
                           onChange={(event) => this.onPriorityChange(event)}
                    />
                </div>
                <div className="task-field task-description">
                    <div>Description:</div>
                    <textarea value={this.getDescription()}
                           onChange={(event) => this.onDescriptionChange(event)}
                    />
                </div>
                {savedTask.getTags() ?
                    <div style={{marginTop: '10px'}}>
                        Tags: {savedTask.getTags()!.map((tag) => {
                        return <span key={tag.getID()} style={{
                              padding: '3px',
                              margin: '3px',
                              border: '1px solid lightgreen',
                              borderRadius: '2px'
                          }}>{tag.getCaption()}</span>;
                    })}
                    </div>: null
                }
                {savedTask.getSubscribers() ?
                    <div style={{marginTop: '10px'}}>
                        Subscribers: {savedTask.getSubscribers()!.map((subscriber) => {
                        return <span key={subscriber.getID()} style={{
                              padding: '3px',
                              margin: '3px',
                              border: '1px solid lightblue',
                              borderRadius: '2px'
                          }}>{subscriber.getName()}</span>;
                    })}
                    </div>: null
                }
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

    private onStatusChange(event: React.FormEvent<HTMLInputElement>) {
        this.setState({status: event.currentTarget.value});
    }

    private onPriorityChange(event: React.FormEvent<HTMLInputElement>) {
        this.setState({priority: event.currentTarget.value});
    }

    private onDescriptionChange(event: React.FormEvent<HTMLTextAreaElement>) {
        this.setState({description: event.currentTarget.value});
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
}

