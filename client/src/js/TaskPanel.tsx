import * as React from "react";
import * as API from './api/API';
import {TaskDetail, getTaskFields} from './TaskDetail';
import {Task,User,IUser} from "./models/models";
import TaskPriority from "./TaskPriority";
import {Priority} from "./metadata/Priority";
import '../css/TaskPanel.less';
import {Table} from "react-bootstrap";

interface State {
    tasks?: Task[];
    total_count?: number;
    selected_task?: Task | undefined;
    draft_title?: string;
    showComposer?: boolean;
}

interface Props {
    userID: number;
}

export default class TaskPanel extends React.Component<Props, State> {

    constructor(props: Props) {
        super(props);
        this.state = {};
    }

    public render() {
        if (typeof this.state.tasks === 'undefined') {
            this.fetchTasks();
            return <div>Loading tasks...</div>;
        }
        return (
            <div className="tk-panel">
                <div className="tk-list">
                    {/*<div className="task-composer">*/}
                        {/*<input className="task-title-draft"*/}
                               {/*value={this.state.draft_title || ''}*/}
                               {/*onChange={(event) => this.setState({draft_title: event.currentTarget.value})}*/}
                               {/*placeholder="Enter some title..."/>*/}
                        {/*<button onClick={(event) => this.setState({showComposer: true})}*/}
                            {/*disabled={!this.state.draft_title}*/}
                            {/*type="button"*/}
                            {/*className="create-button">*/}
                            {/*Compose*/}
                        {/*</button>*/}
                    {/*</div>*/}
                    <Table className="table task-table">
                        <thead>
                            <tr className="task-panel-row">
                                <th className="tk-header-owner">Owner</th>
                                <th className="tk-header-priority">Priority</th>
                                <th className="tk-header-title">Title</th>
                                <th className="tk-header-last-update">Updated</th>
                            </tr>
                        </thead>
                        <tbody>
                            {this.state.tasks.map((task) => {
                                let isSelected = task.getID() === (this.state.selected_task && this.state.selected_task.getID());
                                return (<tr className={'task-panel-row'+(isSelected ? ' selected-row' : '')}
                                            key={task.getID()}
                                            onClick={(event) => this.updateSelectedTask(task.getID(), event)}>
                                    <td className="col-owner">{task.getOwner() ? task.getOwner()!.getName() : ''}</td>
                                    <td className="col-priority">
                                        <TaskPriority priority={task.getPriority() as Priority} />
                                    </td>
                                    <td className="col-title">{task.getTitle()}</td>
                                    <td className="col-last-update">{task.getUpdatedTime()}</td>
                                </tr>);
                                })}
                        </tbody>
                    </Table>
                </div>
                <div className="selected-task-details">
                    {this.state.selected_task
                        ? <TaskDetail onTaskUpdate={(task) => this.onSelectedTaskUpdated(task)} task={this.state.selected_task} />
                        : null
                        }
                </div>
            </div>);
    }

    private fetchTasks(): void {
        API.get<IUser>(
            this.props.userID,
            'id,tasks{'+getTaskFields()+'}'
        ).then((response) => {
            let user = new User(response);
            this.setState({
                tasks: user.getTasks(),
                total_count: user.getTasksCount(),
            });
        }).catch((error) => {
            console.log(error);
        });
    }

    private updateSelectedTask(taskID: number, event: React.MouseEvent<HTMLTableRowElement>): void {
        if (this.state.selected_task && taskID === this.state.selected_task.getID()) {
            return;
        }
        if (this.state.tasks) {
            let selectedTask = this.state.tasks.find((task) => {
                return task.getID() === taskID;
            });
            this.setState({selected_task: selectedTask});
        }
    }

    private onSelectedTaskUpdated(task: Task): void {
        if (!this.state.tasks) {
            return;
        }
        for (let i = 0; i < this.state.tasks.length; i++) {
            if (this.state.tasks[i].getID() === task.getID()) {
                this.state.tasks[i] = task;
            }
        }
        this.setState({selected_task: task});
    }
}

