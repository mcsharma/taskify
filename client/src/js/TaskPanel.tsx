import * as React from "react";
import axios from "axios";
import * as API from './api/API';
import TaskDetail from './TaskDetail';
import {Task,ITask,User,IUser} from "./models/models";

interface State {
    tasks?: Task[];
    total_count?: number;
    selected_task?: Task | undefined;
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
            return <div>{"Loading"}</div>;
        }
        return (
            <div className="tk-panel">
                <div className="tk-list">
                    <table className="table">
                        <thead>
                            <tr>
                                <th className="tk-header-owner">Owner</th>
                                <th className="tk-header-priority">Priority</th>
                                <th className="tk-header-title">Title</th>
                                <th className="tk-header-last-update">Updated</th>
                            </tr>
                        </thead>
                        <tbody>
                            {this.state.tasks.map((task) => {
                                return (<tr key={task.getID()} onClick={this.updateSelectedTask.bind(this, task.getID())}>
                                    <td className="tk-owner">{task.getOwner() ? task.getOwner()!.getName() : ''}</td>
                                    <td className="tk-priority">{task.getPriority()}</td>
                                    <td className="tk-title">{task.getTitle()}</td>
                                    <td className="tk-last-update">{task.getUpdatedTime()}</td>
                                </tr>);
                                })}
                        </tbody>
                    </table>
                </div>
                <div className="selected-task">
                    {this.state.selected_task
                        ? <TaskDetail task={this.state.selected_task} />
                        : null
                        }
                </div>
            </div>);
    }

    private fetchTasks(): void {
        API.get<IUser>(
            this.props.userID,
            'id,tasks{id,created_time,updated_time,title,description,status,owner,priority}'
        ).then((response) => {
            let user = new User(response);
            this.setState({
                tasks: user.getTasks(),
                total_count: user.getTasksCount(),
                selected_task: user.getTasks()![1]
            });
        });
    }

    private updateSelectedTask(taskID: string, event: any): void {
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
}

