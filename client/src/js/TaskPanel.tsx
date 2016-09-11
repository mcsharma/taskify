import * as React from "react";
import axios from "axios";
import * as API from './api/API';
import TaskRow from './TaskRow';
import {Task,ITask} from "./models/models";
import {IUser} from "./models/models";
import {User} from "./models/models";

interface State {
    tasks?: Task[];
    total_count?: number;
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
        } else {
            return (
                <div>
                    {this.state.tasks.map((task) => {
                        return <TaskRow key={task.getID()} task={task}/>;
                        })}
                </div>);
        }
    }

    private fetchTasks(): void {
        API.get<IUser>(
            this.props.userID,
            'id,tasks{id,created_time,updated_time,title,description,status,owner}'
        ).then((response) => {
            let user = new User(response);
            this.setState({
                tasks: user.getTasks(),
                total_count: user.getTasksCount()
            });
        });
    }
}

